<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hasil extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		if($this->session->userdata('whs_logged')==true){
			if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
				
			}else{
				redirect(site_url('informasi-data-umum'));
			}
		}else{
			$this->session->set_flashdata('error', "Your session has expired!");
			redirect(site_url(''));
		}
	}
	public function index()
	{
		$data["title"] = "Hasil Skor Tipelogi";
		$data["provinsi"] = $this->mdb->getdatawhere("m_provinsi");
		$data["content"] = $this->load->view('v_hasil_skor_detail', $data, true);
		$this->load->view('v_header', $data);
	}
	public function get_hasil_table()
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
		<th>Skor</th>
		<th>Detail</th>
		</tr>
		</thead>
		<tbody>
		SMF;

		$arr = array(
			"A" => ["sekda","Sekretariat Daerah"],
			"B" => ["sekdprd","Sekretariat DPRD"],
			"C" => ["inspektorat","Inspektorat"],
			"D" => ["dinas","Dinas"],
			"E" => ["badan","Badan"],
		);
		if($role == "kabupaten"){
			$arr["F"] = ["kecamatan","Kecamatan"];
		}
		foreach ($arr as $k => $v) {
			if($k == "A" || $k == "B" || $k == "C"){
				$skor = $this->cari_skor($role, $v[0], $tahun, $kode_provinsi, $kode_kabupaten, null);
			}else{
				$skor = "-";
			}
			$btn = "";
			if($skor <> "-"){
				if($skor <> ">800 (tipe A)"){
					$btn = <<<SMF
					<button type="button" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="" class="btn btn-success me-1 mb-1 dl-skor-btn"><i class="bi bi-download"></i></button>
					SMF;
				}
				$btn .= <<<SMF
				<button type="button" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="" class="btn btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-eye"></i></button>
				SMF;
			}
			
			$html .= <<<SMF
			<tr>
			<td class="fw-bold">{$k}.</td>
			<td class="fw-bold">{$v[1]}</td>
			<td class="text-center">{$skor}</td>
			<td class="text-center">{$btn}</td>
			</tr>
			SMF;
			if($k == "D" || $k == "E"){
				$dataBadan = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => $v[0], "tipe_daerah" => $role, "tahun" => $tahun]);
				foreach ($dataBadan as $ka => $va) {
					$skorSub = $this->cari_skor($role, $v[0], $tahun, $kode_provinsi, $kode_kabupaten, $va->id_badan);
					$naming = $va->kode_badan.". " .$va->nama_badan;
					if($va->parent <> ""){
						$naming = "<small><small>{$va->kode_parent}. {$va->parent}</small></small><br>".$naming;
					}
					$btn = "";
					if($skorSub <> "-"){
						if($skorSub <> ">800 (tipe A)"){
							$btn = <<<SMF
							<button type="button" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->id_badan}" class="btn btn-success me-1 mb-1 dl-skor-btn"><i class="bi bi-download"></i></button>
							SMF;
						}
						$btn .= <<<SMF
						<button type="button" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->id_badan}" class="btn btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-eye"></i></button>
						SMF;
					}
					$html .= <<<SMF
					<tr>
					<td></td>
					<td>{$naming}</td>
					<td class="text-center">{$skorSub}</td>
					<td class="text-center">{$btn}</td>
					</tr>
					SMF;
				}
			}
			if($k == "F"){
				$dataBadan = $this->mdb->getdatawhere("m_kecamatan", ["kode_kabupaten" => $kode_kabupaten]);
				foreach ($dataBadan as $ka => $va) {
					$skorSub = $this->cari_skor($role, $v[0], $tahun, $kode_provinsi, $kode_kabupaten, $va->kode_kecamatan);
					$naming = $va->nama_kecamatan;
					$btn = "";
					if($skorSub <> "-"){
						if($skorSub <> ">800 (tipe A)"){
							$btn = <<<SMF
							<button type="button" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->kode_kecamatan}" class="btn btn-success me-1 mb-1 dl-skor-btn"><i class="bi bi-download"></i></button>
							SMF;
						}
						$btn .= <<<SMF
						<button type="button" data-role="{$role}" data-var="{$v[0]}" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->kode_kecamatan}" class="btn btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-eye"></i></button>
						SMF;
					}
					$html .= <<<SMF
					<tr>
					<td></td>
					<td>{$naming}</td>
					<td class="text-center">{$skorSub}</td>
					<td class="text-center">{$btn}</td>
					</tr>
					SMF;
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
	private function cari_skor($tipe_daerah, $tipe_variable, $tahun, $kode_provinsi, $kode_kabupaten, $id_badan)
	{
		$data = [];
		$badan = [];
		if($tipe_daerah == "provinsi"){
			if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
				$dataTeknis = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "tipe_variable" => $tipe_variable]);
			}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
				$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $id_badan, "tahun" => $tahun]);
				$dataTeknis = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan]);
			}
			$dataUmum = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_soal" => "umum", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi]);
		}else{
			if($tipe_variable == "sekda" || $tipe_variable == "sekdprd" || $tipe_variable == "inspektorat"){
				$dataTeknis = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable]);
			}elseif($tipe_variable == "badan" || $tipe_variable == "dinas"){
				$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $id_badan, "tahun" => $tahun]);
				$dataTeknis = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan]);
			}else{
				$dataTeknis = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_soal" => "teknis", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten, "tipe_variable" => $tipe_variable, "id_badan" => $id_badan]);
			}
			$dataUmum = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_soal" => "umum", "tipe_daerah" => $tipe_daerah, "tahun" => $tahun, "kode_provinsi" => $kode_provinsi, "kode_kabupaten" => $kode_kabupaten]);
		}
		$skor = "-";
		if($dataTeknis && $dataUmum){
			$skorVal = (($dataTeknis->skor * 0.8)+($dataUmum->skor * 0.2)) * $dataUmum->perkalian;
			$skor = number_format($skorVal, 1);
			$kategori = "";
			if($skorVal <= 300){
				$kategori = "setingkat seksi/subbidang";
			}elseif($skorVal <= 400){
				$kategori = "setingkat bidang";
			}elseif($skorVal <= 600){
				$kategori = "tipe C";
			}elseif($skorVal <= 800){
				$kategori = "tipe B";
			}else{
				$kategori = "tipe A";
			}
			$skor = $skor . " ($kategori)";
		}
		if($badan){
			if($badan->tipe_penilaian == "terisi"){
				$skor = ">800 (tipe A)";
			}
		}
		return $skor;
	}
}
