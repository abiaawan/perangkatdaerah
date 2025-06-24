<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kunci extends CI_Controller {
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
		$data["title"] = "Buka Pengisian Data";
		$data["provinsi"] = $this->mdb->getdatawhere("m_provinsi");
		$data["content"] = $this->load->view('v_kunci_data', $data, true);
		$this->load->view('v_header', $data);
	}
	public function get_kunci_table()
	{
		$role = $this->input->post("role");
		$tahun = $this->input->post("tahun");
		$kode_provinsi = $this->input->post("provinsi");
		$kode_kabupaten = $this->input->post("kabupaten");
		$html = <<<SMF
		<div class="table-responsive mb-2">
		<table class="table table-bordered mb-0 border">
		<thead class="text-center">
		<tr>
		<th colspan="2">Nomenklatur Perangkat Daerah</th>
		<th>Variable Umum</th>
		<th>Variable Teknis</th>
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
			$btnUmum = "";
			$btnTeknis = "";
			if($k == "A" || $k == "B" || $k == "C"){
				$skor = $this->cari_skor($role, $v[0], $tahun, $kode_provinsi, $kode_kabupaten, null);
				if($skor[0] == true){
					$btnUmum = <<<SMF
					<button type="button" data-role="{$role}" data-var="" data-soal="umum" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="" data-badan="" class="btn btn-primary btn-sm me-1 mb-1 unlock-btn btn-umum"><i class="bi bi-unlock"></i></button>
					SMF;
				}
				if($skor[1] == true){
					$btnTeknis = <<<SMF
					<button type="button" data-role="{$role}" data-var="{$v[0]}" data-soal="teknis" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="" class="btn btn-primary btn-sm me-1 mb-1 unlock-btn"><i class="bi bi-unlock"></i></button>
					SMF;
				}
			}
			
			
			$html .= <<<SMF
			<tr>
			<td class="fw-bold">{$k}.</td>
			<td class="fw-bold">{$v[1]}</td>
			<td class="text-center">{$btnUmum}</td>
			<td class="text-center">{$btnTeknis}</td>
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
					

					$btnUmum = "";
					$btnTeknis = "";
					if($skorSub[0] == true){
						$btnUmum = <<<SMF
						<button type="button" data-role="{$role}" data-var="" data-soal="umum" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="" class="btn btn-primary btn-sm me-1 mb-1 unlock-btn btn-umum"><i class="bi bi-unlock"></i></button>
						SMF;
					}
					if($skorSub[1] == true){
						$btnTeknis = <<<SMF
						<button type="button" data-role="{$role}" data-var="{$v[0]}" data-soal="teknis" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->id_badan}" class="btn btn-primary btn-sm me-1 mb-1 unlock-btn"><i class="bi bi-unlock"></i></button>
						SMF;
					}

					$html .= <<<SMF
					<tr>
					<td></td>
					<td>{$naming}</td>
					<td class="text-center">{$btnUmum}</td>
					<td class="text-center">{$btnTeknis}</td>
					</tr>
					SMF;
				}
			}
			if($k == "F"){
				$dataBadan = $this->mdb->getdatawhere("m_kecamatan", ["kode_kabupaten" => $kode_kabupaten]);
				foreach ($dataBadan as $ka => $va) {
					$skorSub = $this->cari_skor($role, $v[0], $tahun, $kode_provinsi, $kode_kabupaten, $va->kode_kecamatan);
					$naming = $va->nama_kecamatan;
					$btnUmum = "";
					$btnTeknis = "";
					if($skorSub[0] == true){
						$btnUmum = <<<SMF
						<button type="button" data-role="{$role}" data-var="" data-soal="umum" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="" class="btn btn-primary btn-sm me-1 mb-1 unlock-btn btn-umum"><i class="bi bi-unlock"></i></button>
						SMF;
					}
					if($skorSub[1] == true){
						$btnTeknis = <<<SMF
						<button type="button" data-role="{$role}" data-var="{$v[0]}" data-soal="teknis" data-tahun="{$tahun}" data-prov="{$kode_provinsi}" data-kab="{$kode_kabupaten}" data-badan="{$va->id_badan}" class="btn btn-primary btn-sm me-1 mb-1 unlock-btn"><i class="bi bi-unlock"></i></button>
						SMF;
					}
					$html .= <<<SMF
					<tr>
					<td></td>
					<td>{$naming}</td>
					<td class="text-center">{$btnUmum}</td>
					<td class="text-center">{$btnTeknis}</td>
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
		$skorUmum = false;
		$skorTeknis = false;
		
		if($dataUmum){
			$skorUmum = true;
		}
		if($dataTeknis){
			$skorTeknis = true;
		}
		if($badan){
			if($badan->tipe_penilaian == "terisi"){
				$skorUmum = false;
				$skorTeknis = false;
			}
		}
		return [$skorUmum, $skorTeknis];
	}
	public function buka_kunci()
	{
		$where = array(
			"tahun" => $this->input->post("tahun"),
			"tipe_daerah" => $this->input->post("role"),
			"kode_provinsi" => $this->input->post("provinsi"),
			"tipe_soal" => $this->input->post("soal")
		);
		if($this->input->post("role") == "kabupaten"){
			$where["kode_kabupaten"] = $this->input->post("kabupaten");
		}
		if($this->input->post("soal") == "teknis"){
			$where["tipe_variable"] = $this->input->post("var");
			if($this->input->post("var") == "dinas" || $this->input->post("var") == "badan"){
				$where["id_badan"] = $this->input->post("id_badan");
			}
			if($this->input->post("var") == "kecamatan"){
				$where["kode_kecamatan"] = $this->input->post("id_badan");
			}
		}
		$this->mdb->putdatawhere("tb_status_jawaban", $where, ["status" => "draft"]);
		$this->mdb->deletedata("tb_skor", $where);
	}
	public function tahun()
	{
		$data["title"] = "Tahun Pengisian Data";
		$data["data_tahun"] = $this->mdb->getrowdatawhere("m_tahun_pengisian", null, ["updated_date" => "desc"]);
		$data["content"] = $this->load->view('v_tahun_data', $data, true);
		$this->load->view('v_header', $data);
	}
	public function send_tahun()
	{
		$data = array(
			"tahun" => $this->input->post("tahun"),
			"updated_date" => date("Y-m-d H:i:s")
		);
		$this->mdb->postdata("m_tahun_pengisian", $data);
		$this->session->set_flashdata('success', "Berhasil mengubah tahun pengisian data!");
		redirect(site_url('tahun-pengisian-data'));

	}
}
