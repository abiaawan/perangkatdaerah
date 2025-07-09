<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		if($this->session->userdata('whs_logged')==true){
			if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin" || $this->session->userdata('whs_role')=="kl" || $this->session->userdata('whs_role')=="provinsi"){
				
			}else{
				redirect(site_url('informasi-data-umum'));
			}
		}else{
			$last = $this->uri->total_segments();
			$last_uri = $this->uri->segment($last);

			if($last_uri == "approval"){
				$this->session->set_flashdata('error', "Please log in to continue!");
				$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				redirect(site_url('')."?r=".urlencode($actual_link));
			}else{
				$this->session->set_flashdata('error', "Your session has expired!");
				redirect(site_url(''));	
			}
		}
	}
	public function index()
	{
		$data["title"] = "Approval";
		if($this->session->userdata("whs_role") <> "provinsi"){
			$data["provinsi"] = $this->mdb->getdatawhere("m_provinsi");
		}else{
			$data["provinsi"] = $this->mdb->getdatawhere("m_provinsi", ["kode_provinsi" =>$this->session->userdata("whs_kode_provinsi")]);
			$data["kabupaten"] = $this->mdb->getdatawhere("m_kabupaten", ["kode_provinsi" =>$this->session->userdata("whs_kode_provinsi")]);
		}
		$data["content"] = $this->load->view('v_approval', $data, true);
		$this->load->view('v_header', $data);
	}
	public function get_approval()
	{
		$role = $this->input->post("role");
		$tahun = $this->input->post("tahun");
		$kode_provinsi = $this->input->post("provinsi");
		$kode_kabupaten = $this->input->post("kabupaten");
		$html = <<<SMF
		<div class="table-responsive mb-2">
		<table class="table table-bordered mb-0 border w-100">
		<thead class="text-center">
		<tr>
		<th></th>
		<th>Nomenklatur Perangkat Daerah</th>
		<th>Variable Umum</th>
		<th>Aksi</th>
		<th>Variable Teknis</th>
		<th>Aksi</th>
		</tr>
		</thead>
		<tbody>
		SMF;

		
		$arr = [];
		$arr["A"] = ["sekda","Sekretariat Daerah"];
		$arr["B"] = ["sekdprd","Sekretariat DPRD"];
		$arr["C"] = ["inspektorat","Inspektorat"];
		$arr["D"] = ["dinas","Dinas"];
		$arr["E"] = ["badan","Badan"];


		if($this->session->userdata('whs_role')<>"kl"){
			if($role == "kabupaten"){
				$arr["F"] = ["kecamatan","Kecamatan"];
			}
		}
		foreach ($arr as $k => $v) {
			if($k == "A" || $k == "B" || $k == "C"){
				$statusArr = $this->cari_status($role, $v[0], $tahun, $kode_provinsi, $kode_kabupaten, null);
			}else{
				$statusArr = "-";
			}
			$btn = "";
			$btn2 = "";
			$status = "";
			$status2 = "";
			if(is_array($statusArr)){
				$status = $this->status_text($statusArr[0],$statusArr[2]);
				$status2 = $this->status_text($statusArr[1],$statusArr[3]);

				if(!in_array(0, $statusArr[0])){
					$btn .= <<<SMF
					<button type="button" data-title="{$v[1]}" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="" data-tipe="umum" class="btn btn-sm btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-file-earmark-check"></i></button>
					SMF;
				}
				
				if(!in_array(0, $statusArr[1])){
					$btn2 .= <<<SMF
					<button type="button" data-title="{$v[1]}" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="" data-tipe="teknis" class="btn btn-sm btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-file-earmark-check"></i></button>
					SMF;
				}
				
			}
			$canApprove = false;
			if($this->session->userdata('whs_role')=="kl"){
				$klAppr = $this->cari_kl_badan($role, $v[0], $tahun, null, $this->session->userdata('whs_id_kl'));
				if($klAppr === true){
					$canApprove = true;
				}
			}else{
				$canApprove = true;
			}
			$header = "";
			if($canApprove === true){
				if($status === ""){
					$header = <<<SMF
					<tr>
					<td class="fw-bold">{$k}.</td>
					<td class="fw-bold">{$v[1]}</td>
					<td class="text-center">{$status}</td>
					<td class="text-center">{$btn}</td>
					<td class="text-center">{$status2}</td>
					<td class="text-center">{$btn2}</td>
					</tr>
					SMF;
				}else{
					$html .= <<<SMF
					<tr>
					<td class="fw-bold">{$k}.</td>
					<td class="fw-bold">{$v[1]}</td>
					<td class="text-center">{$status}</td>
					<td class="text-center">{$btn}</td>
					<td class="text-center">{$status2}</td>
					<td class="text-center">{$btn2}</td>
					</tr>
					SMF;
				}
			}
			if($k == "D" || $k == "E"){
				$dataBadan = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => $v[0], "tipe_daerah" => $role, "tahun" => $tahun]);
				foreach ($dataBadan as $ka => $va) {
					$html .= $header;
					$header = "";
					$statusArr = $this->cari_status($role, $v[0], $tahun, $kode_provinsi, $kode_kabupaten, $va->id_badan);
					$naming = $va->kode_badan.". " .$va->nama_badan;
					$naming2 = ucwords(strtolower($va->nama_badan));
					if($va->parent <> ""){
						$naming = "<small><small>{$va->kode_parent}. {$va->parent}</small></small><br>".$naming;
						$naming2 = ucwords(strtolower("$va->parent $naming2"));
					}
					$naming3 = $v[0] == "dinas" ? "Dinas" : "";
					$btn = "";
					$btn2 = "";
					$status = "";
					$status2 = "";
					if(is_array($statusArr)){
						$status = $this->status_text($statusArr[0],$statusArr[2]);
						$status2 = $this->status_text($statusArr[1],$statusArr[3]);
						if(!in_array(0, $statusArr[0])){
							$btn .= <<<SMF
							<button type="button" data-title="{$naming3} {$naming2}" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->id_badan}" data-tipe="umum" class="btn btn-sm btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-file-earmark-check"></i></button>
							SMF;
						}

						if(!in_array(0, $statusArr[1])){
							$btn2 .= <<<SMF
							<button type="button" data-title="{$naming3} {$naming2}" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->id_badan}" data-tipe="teknis" class="btn btn-sm btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-file-earmark-check"></i></button>
							SMF;
						}
					}
					$canApprove = false;
					if($this->session->userdata('whs_role')=="kl"){
						$klAppr = $this->cari_kl_badan($role, $v[0], $tahun, $va->id_badan, $this->session->userdata('whs_id_kl'));
						if($klAppr === true){
							$canApprove = true;
						}
					}else{
						$canApprove = true;
					}
					if($canApprove === true){
						$html .= <<<SMF
						<tr>
						<td></td>
						<td>{$naming}</td>
						<td class="text-center">{$status}</td>
						<td class="text-center">{$btn}</td>
						<td class="text-center">{$status2}</td>
						<td class="text-center">{$btn2}</td>
						</tr>
						SMF;
					}
				}
			}
			if($k == "F"){
				$dataBadan = $this->mdb->getdatawhere("m_kecamatan", ["kode_kabupaten" => $kode_kabupaten]);
				foreach ($dataBadan as $ka => $va) {
					$html .= $header;
					$header = "";
					$statusArr = $this->cari_status($role, $v[0], $tahun, $kode_provinsi, $kode_kabupaten, $va->kode_kecamatan);
					$naming = $va->nama_kecamatan;
					$naming2 = ucwords(strtolower($naming));
					$btn = "";
					$btn2 = "";
					$status = "";
					$status2 = "";
					if(is_array($statusArr)){
						$status = $this->status_text($statusArr[0],$statusArr[2]);
						$status2 = $this->status_text($statusArr[1],$statusArr[3]);
						if(!in_array(0, $statusArr[0])){
							$btn .= <<<SMF
							<button type="button" data-title="Kecamatan {$naming2}" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->kode_kecamatan}" data-tipe="umum" class="btn btn-sm btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-file-earmark-check"></i></button>
							SMF;
						}
						if(!in_array(0, $statusArr[1])){
							$btn2 .= <<<SMF
							<button type="button" data-title="Kecamatan {$naming2}" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->kode_kecamatan}" data-tipe="teknis" class="btn btn-sm btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-file-earmark-check"></i></button>
							SMF;
						}
					}
					$canApprove = false;
					if($this->session->userdata('whs_role')=="kl"){
						$klAppr = $this->cari_kl_badan($role, $v[0], $tahun, null, $this->session->userdata('whs_id_kl'));
						if($klAppr === true){
							$canApprove = true;
						}
					}else{
						$canApprove = true;
					}
					if($canApprove === true){
						$html .= <<<SMF
						<tr>
						<td></td>
						<td>{$naming}</td>
						<td class="text-center">{$status}</td>
						<td class="text-center">{$btn}</td>
						<td class="text-center">{$status2}</td>
						<td class="text-center">{$btn2}</td>
						</tr>
						SMF;
					}
				}
			}
		}
		$html .= <<<SMF
		</tbody>
		</table>
		</div>
		SMF;
		echo $html;
	}
	private function status_text($arr, $comm)
	{
		$approver = "";
		$html = "";
		$badge = "";
		foreach ($arr as $k => $v) {
			if($v <> 4){
				$rejected = "";
				$rejected2 = "";
				$rejected3 = "";
				if($k == 0){
					$approver = "Kemendagri";
				}elseif($k == 1){
					$approver = "K/L Terkait";
				}elseif($k == 2){
					$approver = "Provinsi";
				}

				if($v == 0){
					$txt = "Belum Submit";
					$badge = "secondary";
					return '<div class="mx-0 mt-0 mb-1 p-0"><span class="badge bg-'.$badge.'">'.$txt.'</span></div>';
				}elseif($v == 1){
					$txt = "Approved by";
					$badge = "success";
				}elseif($v == 2){
					$rejected = " rejected-span";
					$rejected2 = ' data-comment="'.htmlspecialchars($comm[$k]).'"';
					$rejected3 = '<i class="bi bi-info-circle"></i> ';
					$txt = "Rejected by";
					$badge = "danger";
					return '<div class="mx-0 mt-0 mb-1 p-0'.$rejected.'"'.$rejected2.'><span class="badge bg-'.$badge.'">'.$rejected3.$txt.' '.$approver.'</span></div>';
				}elseif($v == 3){
					$txt = "Menunggu Approval";
					$badge = "info";
				}
				$html .= '<div class="mx-0 mt-0 mb-1 p-0"><span class="badge bg-'.$badge.'">'.$txt.' '.$approver.'</span></div>';
			}
		}
		return $html;
	}
	public function view_approval()
	{
		$tipeVar = $this->input->post("tipe_var");
		$tipeSoal = $this->input->post("tipe_soal");
		$idBadan = $this->input->post("id_badan");

		$role = $this->input->post("role");
		$tahun = $this->input->post("tahun");
		$kode_provinsi = $this->input->post("provinsi");
		$kode_kabupaten = $this->input->post("kabupaten");
		if($role=="provinsi"){
			$dataGeo = $this->mdb->getrowdatawhere("m_provinsi", ["kode_provinsi" => $kode_provinsi]);
			$nama_provinsi = $dataGeo->nama_provinsi;
			$nama_kabupaten = "";
		}elseif($role=="kabupaten"){
			$dataGeo = $this->mdb->getrowdatawhere("m_provinsi", ["kode_provinsi" => $kode_provinsi]);
			$dataGeo2 = $this->mdb->getrowdatawhere("m_kabupaten", ["kode_kabupaten" => $kode_kabupaten]);
			$nama_provinsi = $dataGeo->nama_provinsi;
			$nama_kabupaten = $dataGeo2->nama_kabupaten;
		}



		$namaDaerah = $role == "provinsi" ? "Provinsi ". $nama_provinsi : $nama_kabupaten;

		$whereVarSoal = [
			"tipe_daerah" => $role,
			"tahun" => $tahun
		];
		$whereVarSoal["tipe_soal"] = "umum";
		$varSoal = $this->mdb->getrowdatawhere("tb_variable_soal", $whereVarSoal);
		if($tipeVar == "dinas" || $tipeVar == "badan"){
			$whereVarSoal["id_badan"] = $idBadan;
			$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan, "tahun" => $tahun]);
			if($badan){
				if($badan->tipe_penilaian == "terisi"){
					echo "100";
					die;
				}
			}
		}
		$whereVarSoal["tipe_soal"] = "teknis";
		$whereVarSoal["tipe_variable"] = $tipeVar;
		$varSoalTeknis = $this->mdb->getrowdatawhere("tb_variable_soal", $whereVarSoal);
		if(!$varSoal || !$varSoalTeknis){
			echo "404";
			die;
		}

		$whereSoal = [
			"kode_soal" => $varSoal->kode_soal, 
			"tipe_soal" => "umum", 
			"tipe_daerah" => $role,
			"tahun" => $tahun
		];
		$whereSoalTeknis = [
			"kode_soal" => $varSoalTeknis->kode_soal, 
			"tipe_soal" => "teknis", 
			"tipe_daerah" => $role,
			"tahun" => $tahun
		];
		$whereJawaban = [
			"kode_kabupaten" => $kode_kabupaten, 
			"kode_provinsi" => $kode_provinsi, 
			"tipe_daerah" => $role,
			"tipe_soal" => "umum",
			"tahun" => $tahun
		];
		$whereJawabanTeknis = [
			"kode_kabupaten" => $kode_kabupaten, 
			"kode_provinsi" => $kode_provinsi, 
			"tipe_daerah" => $role,
			"tipe_soal" => "teknis",
			"tipe_variable" => $tipeVar, 
			"tahun" => $tahun
		];
		$tipeVarName = "";
		$badanName = "";
		if($tipeVar == "dinas" || $tipeVar == "badan"){
			$whereJawabanTeknis["id_badan"] = $idBadan;
			$namaBadanTemp = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan, "tahun" => $tahun]);
			if($namaBadanTemp->parent == ""){
				$badanName = ucwords(strtolower($namaBadanTemp->nama_badan))." ";
			}else{
				$badanName = ucwords(strtolower("{$namaBadanTemp->parent} {$namaBadanTemp->nama_badan}"))." ";
			}

			$tipeVarName = ucwords($tipeVar);
		}elseif($tipeVar == "kecamatan"){
			$whereJawabanTeknis["kode_kecamatan"] = $idBadan;
			$badanName = ucwords(strtolower($this->mdb->getrowdatawhere("m_kecamatan", ["kode_kecamatan" => $idBadan])->nama_kecamatan))." ";
			$tipeVarName = ucwords($tipeVar);
		}else{
			if($tipeVar == "sekda"){
				$tipeVarName = "Sekretariat Daerah";
			}elseif($tipeVar == "sekdprd"){
				$tipeVarName = "Sekretariat DPRD";			
			}else{
				$tipeVarName = "Inspektorat";			
			}
		}
		$namaDaerah = ucwords(strtolower($namaDaerah));
		$soal = $this->mdb->getdatawhere("m_soal", $whereSoal);
		$soalTeknis = $this->mdb->getdatawhere("m_soal", $whereSoalTeknis);
		$jawaban = $this->mdb->getdatawhere("tb_jawaban", $whereJawaban);
		$jawabanTeknis = $this->mdb->getdatawhere("tb_jawaban", $whereJawabanTeknis);
		

		if($tipeSoal == "umum"){
			$values = [];
			$check = [];
			$x = 0;

			foreach ($soal as $k => $v) {
				$ans = "a";
				$val = 0;
				$upload = "";
				foreach(range('a','e') as $i) {
					$ansRow = $this->find_answer($jawaban, $v->id_soal, $i);
					if($ansRow[0] == true) {
						$ans = $i;
						$val = $ansRow[1];
						$upload = $ansRow[2];
						break;
					}
				}
				$x++;
				$values[] = [
					'x' => "{$v->no}.",
					'soal' => "{$v->soal}",
					'upload' => "{$upload}",
					'jawaban' => number_format($val,0,",","."),
					'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
				];
			}
			$html = '<div class="text-center h5">Faktor Umum</div><div class="table-responsive mb-2"><table class="table table-bordered mb-0 border"><thead class="text-center"><tr><th width="5%">No</th><th width="70%">Indikator & Kelas Interval</th><th width="10%">Skor</th><th>Lampiran</th></tr></thead><tbody>';
			$kodeDaerah = $role == "provinsi" ? $kode_provinsi : $kode_kabupaten;
			foreach ($values as $k => $v) {
				$html .= <<<SMF
				<tr><td class="text-center">{$v['x']}</td><td><p class="m-0">{$v['soal']}</p>
				<p class="m-0">{$v['jawaban']}</p>
				</td><td class="text-center">{$v['skor']}</td>
				</td><td class="text-center"><button type="button" class="btn btn-success view-btn" data-daerah="{$role}" data-kodedaerah="{$kodeDaerah}" data-file="{$v['upload']}"><i class="bi bi-eye"></i> Lihat</button></td></tr>
				SMF;
			}
			$html .= "</tbody></table></div>";
		}
		if($tipeSoal == "teknis"){
			$values = [];
			$check = [];
			$x = 0;
			foreach ($soalTeknis as $k => $v) {
				$ans = "a";
				$val = 0;
				$upload = "";
				foreach(range('a','e') as $i) {
					$ansRow = $this->find_answer($jawabanTeknis, $v->id_soal, $i);
					if($ansRow[0] == true) {
						$ans = $i;
						$val = $ansRow[1];
						$upload = $ansRow[2];
						break;
					}
				}
				$x++;
				$values[] = [
					'x' => "{$v->no}.",
					'soal' => "{$v->soal}",
					'upload' => "{$upload}",
					'jawaban' => number_format($val,0,",","."),
					'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
				];
			}
			$html = '<div class="text-center h5">Faktor Teknis</div><div class="table-responsive mb-2"><table class="table table-bordered mb-0 border"><thead class="text-center"><tr><th width="5%">No</th><th width="70%">Indikator & Kelas Interval</th><th width="10%">Skor</th><th>Lampiran</th></tr></thead><tbody>';
			$kodeDaerah = $role == "provinsi" ? $kode_provinsi : $kode_kabupaten;
			foreach ($values as $k => $v) {
				$html .= <<<SMF
				<tr><td class="text-center">{$v['x']}</td><td><p class="m-0">{$v['soal']}</p>
				<p class="m-0">{$v['jawaban']}</p>
				</td><td class="text-center">{$v['skor']}</td>
				</td><td class="text-center"><button type="button" class="btn btn-success view-btn" data-daerah="{$role}" data-kodedaerah="{$kodeDaerah}" data-file="{$v['upload']}"><i class="bi bi-eye"></i> Lihat</button></td></tr>
				SMF;
			}
			$html .= "</tbody></table></div>";
		}
		$statusArr = $this->cari_status($role, $tipeVar, $tahun, $kode_provinsi, $kode_kabupaten, $idBadan)[$tipeSoal == "umum" ? 0 : 1];
		$canApprove = false;
		if(in_array(3, $statusArr)){
			if($statusArr[0] == 3 && $this->session->userdata('whs_role') == "admin"){
				$canApprove = true;
			}elseif($statusArr[1] == 3 && $this->session->userdata('whs_role') == "kl"){
				$canApprove = true;
			}elseif($statusArr[2] == 3 && $this->session->userdata('whs_role') == "provinsi"){
				$canApprove = true;
			}
		}
		if($canApprove == true){
			$html .= '<div class="text-center mt-2">';
			$html .= <<<SMF
			<button type="button" data-role="{$role}" data-var="{$tipeVar}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$idBadan}" data-tipe="{$tipeSoal}" class="btn btn-success me-1 mb-1 approve-btn"><i class="bi bi-check-lg"></i> Approve</button>
			<button type="button" data-role="{$role}" data-var="{$tipeVar}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$idBadan}" data-tipe="{$tipeSoal}" class="btn btn-danger me-1 mb-1 reject-btn"><i class="bi bi-x-lg"></i> Reject</button>
			SMF;
			$html .= "</div>";
		}
		echo $html;
	}
	private function find_answer($answer, $id_soal, $option)
	{
		foreach ($answer as $k => $v) {
			if($v->id_soal == $id_soal){
				if($v->jawaban == $option){
					return [true, $v->value, $v->upload];
				}
			}
		}
		return [false, 0, ""];
	}

	private function cari_kl_badan($tipe_daerah, $tipe_variable, $tahun, $id_badan, $id_kl)
	{
		if($tipe_daerah == "provinsi"){
			if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
				$dataTeknis = $this->mdb->getrowdatawhere("tb_kl_badan", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "tipe_variable" => $tipe_variable, "id_kl" => $id_kl]);
			}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
				$dataTeknis = $this->mdb->getrowdatawhere("tb_kl_badan", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "tipe_variable" => $tipe_variable, "id_kl" => $id_kl, "id_badan" => $id_badan]);
			}
		}else{
			if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
				$dataTeknis = $this->mdb->getrowdatawhere("tb_kl_badan", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "tipe_variable" => $tipe_variable, "id_kl" => $id_kl]);
			}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
				$dataTeknis = $this->mdb->getrowdatawhere("tb_kl_badan", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "tipe_variable" => $tipe_variable, "id_kl" => $id_kl, "id_badan" => $id_badan]);
			}else{
				$dataTeknis = $this->mdb->getrowdatawhere("tb_kl_badan", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "tipe_variable" => $tipe_variable, "id_kl" => $id_kl]);
			}
		}
		if($dataTeknis){
			return true;
		}else{
			return false;
		}
	}
	private function cari_status($tipe_daerah, $tipe_variable, $tahun, $kode_provinsi, $kode_kabupaten, $id_badan)
	{
		$data = [];
		$badan = [];
		if($tipe_daerah == "provinsi"){
			if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
				$dataTeknis = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "tipe_variable" => $tipe_variable]);
			}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
				$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $id_badan, "tahun" => $tahun]);
				$dataTeknis = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan]);
			}
			$dataUmum = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "umum", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi]);
		}else{
			if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
				$dataTeknis = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable]);
			}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
				$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $id_badan, "tahun" => $tahun]);
				$dataTeknis = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan]);
			}else{
				$dataTeknis = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable, "kode_kecamatan" => $id_badan]);
			}
			$dataUmum = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "umum", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten]);
		}
		$status = [0,1,2,3];
		if($dataUmum){
			$status[0] = [$dataUmum->approval_kementerian, $dataUmum->approval_kl, $dataUmum->approval_provinsi];
			$status[2] = [$dataUmum->comment_kementerian, $dataUmum->comment_kl, $dataUmum->comment_provinsi];
		}else{
			$status[0] = [0,0,0];
			$status[2] = ["","",""];
		}
		if($dataTeknis){
			$status[1] = [$dataTeknis->approval_kementerian, $dataTeknis->approval_kl, $dataTeknis->approval_provinsi];
			$status[3] = [$dataTeknis->comment_kementerian, $dataTeknis->comment_kl, $dataTeknis->comment_provinsi];
		}else{
			$status[1] = [0,0,0];
			$status[3] = ["","",""];
		}
		if($badan){
			if($badan->tipe_penilaian == "terisi"){
				$status = "terisi";
			}
		}
		return $status;
	}
	public function approve()
	{
		$tipe_variable = $this->input->post("tipe_var");
		$tipe_soal = $this->input->post("tipe_soal");
		$id_badan = $this->input->post("id_badan");

		$tipe_daerah = $this->input->post("role");
		$tahun = $this->input->post("tahun");
		$kode_provinsi = $this->input->post("provinsi");
		$kode_kabupaten = $this->input->post("kabupaten");
		$data = [];

		if($this->session->userdata('whs_role') == "kl"){
			$klAppr = $this->cari_kl_badan($tipe_daerah, $tipe_variable, $tahun, $id_badan, $this->session->userdata('whs_id_kl'));
			if($klAppr === false){
				die;
			}
		}
		if($this->session->userdata('whs_role') == "provinsi"){
			if($tipe_daerah == "provinsi"){
				die;
			}
			if($kode_provinsi <> $this->session->userdata("whs_kode_provinsi")){
				die;
			}
		}

		$statusArr = $this->cari_status($tipe_daerah, $tipe_variable, $tahun, $kode_provinsi, $kode_kabupaten, $id_badan)[$tipe_soal == "umum" ? 0 : 1];


		if($statusArr[0] == 3 && $this->session->userdata('whs_role') == "admin"){
			$data["approval_kementerian"] = 1;
		}elseif($statusArr[1] == 3 && $this->session->userdata('whs_role') == "kl"){
			$data["approval_kl"] = 1;
		}elseif($statusArr[2] == 3 && $this->session->userdata('whs_role') == "provinsi"){
			$data["approval_provinsi"] = 1;
		}

		if($tipe_daerah == "provinsi"){
			if($tipe_soal == "umum"){
				$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi];
			}else{
				if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "tipe_variable" => $tipe_variable];
				}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan];
				}
			}
		}else{
			if($tipe_soal == "umum"){
				$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten];
			}else{
				if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable];
				}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan];
				}else{
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable, "kode_kecamatan" => $id_badan];
				}
			}
		}

		$this->mdb->putdatawhere("tb_status_jawaban", $where, $data);

		$sendTo = [];
		if($tipe_daerah == "provinsi"){
			$dataProv = $this->mdb->getrowdatawhere("m_provinsi", ["kode_provinsi" => $kode_provinsi]);
			$naming = ucwords(strtolower("Provinsi {$dataProv->nama_provinsi}"));
			$provs = $this->mdb->getdatawhere("m_user", ["kode_provinsi" => $kode_provinsi, "role" => "provinsi"]);
			foreach ($provs as $k => $v) {
				if($v->email){
					$sendTo[$v->email] = $v->name;
				}
			}
		}else{
			$dataKab = $this->mdb->getrowdatawhere("m_kabupaten", ["kode_kabupaten" => $kode_kabupaten]);
			$naming = ucwords(strtolower($dataProv->nama_kabupaten));
			$kabs = $this->mdb->getdatawhere("m_user", ["kode_kabupaten" => $kode_kabupaten, "role" => "kabupaten"]);
			foreach ($kabs as $k => $v) {
				if($v->email){
					$sendTo[$v->email] = $v->name;
				}
			}
		}
		$secName = "";
		if($this->session->userdata('whs_role') == "admin"){
			$secName = "Kementerian Dalam Negeri";
		}elseif($this->session->userdata('whs_role') == "kl"){
			$secName = ucwords(strtolower($this->session->userdata('whs_nama_kl')));
		}elseif($this->session->userdata('whs_role') == "provinsi"){
			$secName = ucwords(strtolower($this->session->userdata('whs_nama_provinsi')));
		}else{
			$secName = ucwords($this->session->userdata('whs_role'));
		}
		$secName = $this->session->userdata("whs_name") . " ($secName)"; 


		if($tipe_soal == "teknis"){
			$title = preg_replace('/[^a-zA-Z :]/', '', $this->input->post("title")) . " " . $naming." Variable Teknis";	
		}else{
			$title = $naming . " Variable Umum";
		}

		$varName = "";
		if($tipe_variable == "sekda"){
			$varName = "sekretariat-daerah";
		}elseif($tipe_variable == "sekdprd"){
			$varName = "sekretariat-dprd";
		}elseif($tipe_variable == "inspektorat"){
			$varName = "inspektorat";
		}elseif($tipe_variable == "dinas"){
			$varName = "dinas";
		}elseif($tipe_variable == "badan"){
			$varName = "badan";
		}elseif($tipe_variable == "kecamatan"){
			$varName = "kecamatan";
		}
		$url = base_url("variable/").$varName;

		$this->mdb->approve_approval_request([$sendTo, $secName, $title, $url]);

		$statusArr = $this->cari_status($tipe_daerah, $tipe_variable, $tahun, $kode_provinsi, $kode_kabupaten, $id_badan)[$tipe_soal == "umum" ? 0 : 1];
		if(!in_array(0, $statusArr) && !in_array(2, $statusArr) && !in_array(3, $statusArr)){
			$jawaban = $this->mdb->getdatawhere("tb_jawaban", $where, null, ["id_soal" => "asc"]);
			foreach ($jawaban as $k => $v) {
				$searchSoal[] = $v->id_soal;
				$soalJawab[$v->id_soal] = $v->jawaban;
				$soalJawabValue[] = $v->value;
			}
			$nilai = 0;
			$searchNilai = $this->mdb->getdatawhere("m_soal", null, null, null, ["id_soal", $searchSoal]);
			foreach ($searchNilai as $k => $v) {
				$skor = (($v->bobot / 100) * $v->{"skala_{$soalJawab[$v->id_soal]}"});
				$nilai += $skor;
			}
			if($tipe_soal == "umum"){
				$perkalian = $this->pengkalian_wilayah($this->session->userdata('whs_kode_provinsi'), $this->session->userdata('whs_kode_kabupaten'));
			}else{
				$perkalian = $this->pengkalian_wilayah($this->session->userdata('whs_kode_provinsi'), $this->session->userdata('whs_kode_kabupaten'), $tipe_variable, $id_badan);
			}
			$skor = $where;
			$skor["skor"] = $nilai;
			$skor["id_kategori_perkalian"] = $perkalian->id_kategori_perkalian;
			$skor["updated_date"] = date("Y-m-d H:i:s");
			$search = $this->mdb->getrowdatawhere("tb_skor", $where);
			if(!$search){
				$this->mdb->postdata("tb_skor", $skor);
			}else{
				$this->mdb->putdatawhere("tb_skor", $where, $skor);
			}
			if($tipe_soal == "umum"){
				if (isset($soalJawabValue[0]) && isset($soalJawabValue[1]) && $soalJawabValue[1] != 0) {
					$kepadatan = $soalJawabValue[0] / $soalJawabValue[1];
				} else {
					$kepadatan = "";
				}
				$dataInformasi = array(
					"penduduk" => $soalJawabValue[0] ?? "",
					"kepadatan" => $kepadatan,
					"luas" => $soalJawabValue[1] ?? "",
					"apbd" => $soalJawabValue[2] ?? "",
					"kode_provinsi" => $kode_provinsi,
					"kode_kabupaten" => $kode_kabupaten ?? 0,
					"tipe_daerah" => $tipe_daerah,
					"tahun" => $tahun,
					"updated_date" => date("Y-m-d H:i:s")
				);
				$whereInformasi = ["tipe_daerah" => $tipe_daerah, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tahun" => $tahun];
				$search2 = $this->mdb->getrowdatawhere("tb_informasi_tematik", $whereInformasi);
				if($search2){
					$this->mdb->putdatawhere("tb_informasi_tematik", $whereInformasi, $dataInformasi);
				}else{
					$this->mdb->postdata("tb_informasi_tematik", $dataInformasi);
				}
			}
		}
	}
	public function reject()
	{
		$tipe_variable = $this->input->post("tipe_var");
		$tipe_soal = $this->input->post("tipe_soal");
		$id_badan = $this->input->post("id_badan");

		$tipe_daerah = $this->input->post("role");
		$tahun = $this->input->post("tahun");
		$kode_provinsi = $this->input->post("provinsi");
		$kode_kabupaten = $this->input->post("kabupaten");
		$data = [];

		if($this->session->userdata('whs_role') == "kl"){
			$klAppr = $this->cari_kl_badan($tipe_daerah, $tipe_variable, $tahun, $id_badan, $this->session->userdata('whs_id_kl'));
			if($klAppr === false){
				die;
			}
		}
		if($this->session->userdata('whs_role') == "provinsi"){
			if($tipe_daerah == "provinsi"){
				die;
			}
			if($kode_provinsi <> $this->session->userdata("whs_kode_provinsi")){
				die;
			}
		}
		

		$statusArr = $this->cari_status($tipe_daerah, $tipe_variable, $tahun, $kode_provinsi, $kode_kabupaten, $id_badan)[$tipe_soal == "umum" ? 0 : 1];


		if($statusArr[0] == 3 && $this->session->userdata('whs_role') == "admin"){
			$data["approval_kementerian"] = 2;
			$data["comment_kementerian"] = $this->input->post("comment");
		}elseif($statusArr[1] == 3 && $this->session->userdata('whs_role') == "kl"){
			$data["approval_kl"] = 2;
			$data["comment_kl"] = $this->input->post("comment");
		}elseif($statusArr[2] == 3 && $this->session->userdata('whs_role') == "provinsi"){
			$data["approval_provinsi"] = 2;
			$data["comment_provinsi"] = $this->input->post("comment");
		}
		$data["status"] = "draft";

		if($tipe_daerah == "provinsi"){
			if($tipe_soal == "umum"){
				$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi];
			}else{
				if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "tipe_variable" => $tipe_variable];
				}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan];
				}
			}
		}else{
			if($tipe_soal == "umum"){
				$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten];
			}else{
				if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable];
				}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan];
				}else{
					$where = ["tipe_soal" => $tipe_soal, "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable, "kode_kecamatan" => $id_badan];
				}
			}
		}

		$this->mdb->putdatawhere("tb_status_jawaban", $where, $data);

		$sendTo = [];
		if($tipe_daerah == "provinsi"){
			$dataProv = $this->mdb->getrowdatawhere("m_provinsi", ["kode_provinsi" => $kode_provinsi]);
			$naming = ucwords(strtolower("Provinsi {$dataProv->nama_provinsi}"));
			$provs = $this->mdb->getdatawhere("m_user", ["kode_provinsi" => $kode_provinsi, "role" => "provinsi"]);
			foreach ($provs as $k => $v) {
				if($v->email){
					$sendTo[$v->email] = $v->name;
				}
			}
		}else{
			$dataKab = $this->mdb->getrowdatawhere("m_kabupaten", ["kode_kabupaten" => $kode_kabupaten]);
			$naming = ucwords(strtolower($dataProv->nama_kabupaten));
			$kabs = $this->mdb->getdatawhere("m_user", ["kode_kabupaten" => $kode_kabupaten, "role" => "kabupaten"]);
			foreach ($kabs as $k => $v) {
				if($v->email){
					$sendTo[$v->email] = $v->name;
				}
			}
		}
		
		$secName = "";
		if($this->session->userdata('whs_role') == "admin"){
			$secName = "Kementerian Dalam Negeri";
		}elseif($this->session->userdata('whs_role') == "kl"){
			$secName = ucwords(strtolower($this->session->userdata('whs_nama_kl')));
		}elseif($this->session->userdata('whs_role') == "provinsi"){
			$secName = ucwords(strtolower($this->session->userdata('whs_nama_provinsi')));
		}else{
			$secName = ucwords($this->session->userdata('whs_role'));
		}
		$secName = $this->session->userdata("whs_name") . " ($secName)"; 

		if($tipe_soal == "teknis"){
			$title = preg_replace('/[^a-zA-Z :]/', '', $this->input->post("title")) . " " . $naming." Variable Teknis";	
		}else{
			$title = $naming . " Variable Umum";
		}
		$varName = "";
		if($tipe_variable == "sekda"){
			$varName = "sekretariat-daerah";
		}elseif($tipe_variable == "sekdprd"){
			$varName = "sekretariat-dprd";
		}elseif($tipe_variable == "inspektorat"){
			$varName = "inspektorat";
		}elseif($tipe_variable == "dinas"){
			$varName = "dinas";
		}elseif($tipe_variable == "badan"){
			$varName = "badan";
		}elseif($tipe_variable == "kecamatan"){
			$varName = "kecamatan";
		}
		$url = base_url("variable/").$varName;

		$this->mdb->reject_approval_request([$sendTo, $secName, $title, $this->input->post("comment"), $url]);
	}
	private function pengkalian_wilayah($id, $idkab=null, $var=null, $idBadan=null)
	{
		if($var == "sekdprd"){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "x"]);
		}
		if($var == "badan" || $var == "dinas"){
			$dataBadan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
			if($dataBadan){
				$arr = ["X", "U", "BB", "CC"];
				if(in_array($dataBadan->kode_badan, $arr)){
					return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "x"]);
				}
			}else{
				return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "x"]);
			}
		}
		$kode_kota = ['1171', '1172', '1173', '1174', '1175', '1271', '1272', '1273', '1274', '1275', '1276', '1277', '1278', '1371', '1372', '1373', '1374', '1375', '1376', '1471', '1472', '1473', '1571', '1572', '1671', '1672', '1673', '1674', '1771', '1871', '1971', '2171', '2172', '3171', '3172', '3173', '3174', '3175', '3271', '3272', '3273', '3274', '3275', '3276', '3277', '3278', '3279', '3371', '3372', '3373', '3374', '3375', '3376', '3471', '3571', '3572', '3573', '3574', '3575', '3576', '3577', '3578', '3671', '3672', '3673', '3674', '5171', '5271', '5272', '6171', '6172', '6271', '6371', '6372', '6471', '6472', '6474', '6571', '7171', '7172', '7173', '7174', '7271', '7371', '7372', '7373', '7471', '7472', '7571', '8171', '8172', '8271', '8272', '9171', '9271', '9471'];
		$kode_bps_kategori_f = ['6101', '6102', '6105', '6107', '6108', '6411', '6501', '6504', '5303', '5305', '5306', '5321', '9403', '9420', '9501', '9502', '9708'];
		$kode_bps_kategori_e = ['1301', '1410', '1901', '1902', '1903', '1904', '1905', '1906', '1971', '2101', '2102', '2103', '2104', '2105', '2171', '2172', '3101', '5105', '5201', '5202', '5203', '5204', '5205', '5206', '5207', '5208', '5271', '5272', '5301', '5302', '5303', '5304', '5305', '5306', '5307', '5308', '5309', '5310', '5311', '5312', '5313', '5314', '5315', '5316', '5317', '5318', '5319', '5320', '5321', '5371', '7103', '7104', '7108', '7201', '7211', '7301', '7309', '7407', '7412', '8101', '8102', '8103', '8104', '8105', '8106', '8107', '8108', '8109', '8171', '8172', '8201', '8202', '8203', '8204', '8205', '8206', '8207', '8208', '8271', '8272', '9201', '9408', '9409', '9427'];
		$kode_bps_kategori_g = ['1101', '1108', '1214', '1218', '1301', '1409', '1410', '1703', '1813', '1901', '1902', '1905', '2101', '2102', '2103', '2105', '2171', '3529', '5207', '5208', '5303', '5307', '5310', '5314', '5320', '6101', '6405', '6504', '7103', '7104', '7201', '7203', '7206', '7407', '7505', '8101', '8102', '8105', '8107', '8108', '8205', '8207', '9201', '9409', '9419'];

		$sumatera = ["11","12","13","14","15","16","17","18","19"];
		$kepriau = ["21"];
		$jawa = ["31","32","33","34","35","36"];
		$bali = ["51"];
		$nusa = ["52","53"];
		$maluku = ["81", "82"];
		$kalimantan = ["61","62","63","64","65"];
		$sulawesi = ["71","72","73","74","75","76"];
		$papua = ["91", "92", "94", "95", "96", "97"];

		if(in_array($idkab, $kode_kota)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "b"]);
		}elseif(in_array($idkab, $kode_bps_kategori_g)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "g"]);
		}elseif(in_array($idkab, $kode_bps_kategori_f)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "f"]);
		}elseif(in_array($id, $kepriau)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "f"]);
		}elseif(in_array($idkab, $kode_bps_kategori_e)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "e"]);
		}elseif(in_array($id, $papua)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "d"]);
		}elseif(in_array($id, $nusa) || in_array($id, $maluku)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "c"]);
		}elseif(in_array($id, $sumatera) || in_array($id, $kalimantan) || in_array($id, $sulawesi)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "b"]);
		}elseif(in_array($id, $jawa) || in_array($id, $bali)){
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "a"]);
		}else{
			return $this->mdb->getrowdatawhere("m_kategori_perkalian", ["kode" => "x"]);
		}
	}
}
