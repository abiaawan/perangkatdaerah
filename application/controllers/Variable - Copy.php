<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;
class Variable extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		if($this->session->userdata('whs_logged')==true){
			// if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			// 	redirect(site_url('dashboard'));
			// }
		}else{
			$this->session->set_flashdata('error', "Your session has expired!");
			redirect(site_url(''));
		}
	}
	public function index()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}else{
			redirect(site_url('informasi-data-umum'));
		}
		die;
	}
	public function add($url)
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		// echo json_encode($_POST);die;
		$data["title"] = "Add Variable";
		$kodeSoal = [];
		$tipeVar = $this->input->post("tipe_variable");
		if(!$tipeVar){
			redirect(site_url("/variable/$url"));
		}
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = ucwords($this->session->userdata('whs_role'));
		if($this->input->post("tipe_soal_plus")){
			if(str_contains($this->input->post("tipe_soal_plus"), "_")){
				$jenisSoalPlus = explode("_", $this->input->post("tipe_soal_plus"));
				$jenisSoal = $jenisSoalPlus[0];
				$idBadan = $jenisSoalPlus[1];
			}else{
				$jenisSoal = $this->input->post("tipe_soal");
			}
		}else{
			$jenisSoal = $this->input->post("tipe_soal");
		}

		$data["tipe_var"] = $tipeVar;
		$data["url"] = $url;
		$where = [
			"tahun" => $this->session->userdata('whs_tahun'),
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
			"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
			"tipe_soal" => $jenisSoal,
			"tipe_variable" => $tipeVar
		];

		$subtitlePrefix = [
			"sekda" => "Sekretariat Daerah",
			"sekdprd" => "Sekretariat DPRD",
			"inspektorat" => "Inspektorat",
			"dinas" => "Dinas",
			"badan" => "Badan",
			"kecamatan" => "Kecamatan"
		];

		if (in_array($tipeVar, ["sekda", "sekdprd", "inspektorat"])) {
			$data["subtitle"] = ucwords(strtolower($subtitlePrefix[$tipeVar] . " $tipeDaerah $namaDaerah"));
			$data["id_badan"] = 0;
			$whereVarSoal = $where;
			unset($whereVarSoal["kode_kabupaten"]);
			unset($whereVarSoal["kode_provinsi"]);
			
			if($jenisSoal == "umum"){
				unset($whereVarSoal["tipe_variable"]);
			}
			$varSoal = $this->mdb->getrowdatawhere("tb_variable_soal", $whereVarSoal);
		} else if (in_array($tipeVar, ["dinas", "badan", "kecamatan"])) {
			$whereVarSoal = $where;
			$where["id_badan"] = $idBadan;
			$whereVarSoal["id_badan"] = $idBadan;

			if ($tipeVar == "kecamatan") {
				unset($whereVarSoal["id_badan"]);
				if($jenisSoal == "umum"){
					unset($whereVarSoal["tipe_variable"]);
				}else{
					$where["kode_kecamatan"] = $idBadan;
				}
				$dataBadan = $this->mdb->getrowdatawhere("m_kecamatan", ["kode_kecamatan" => $idBadan]);
				$data["subsubtitle"] = ucwords(strtolower($dataBadan->nama_kecamatan));
			} else {
				$dataBadan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
				$data["subsubtitle"] = ucwords(strtolower($dataBadan->nama_badan));
				if($jenisSoal == "umum"){
					unset($whereVarSoal["id_badan"]);
					unset($whereVarSoal["tipe_variable"]);
				}
			}

			$data["subtitle"] = ucwords(strtolower($subtitlePrefix[$tipeVar] . " $tipeDaerah $namaDaerah"));
			$data["id_badan"] = $idBadan;

			unset($whereVarSoal["kode_kabupaten"]);
			unset($whereVarSoal["kode_provinsi"]);
			$varSoal = $this->mdb->getrowdatawhere("tb_variable_soal", $whereVarSoal);
		}

		if (!$varSoal) {
			echo "Soal belum dibuat";
			die;
		}

		$data["soal"] = $this->mdb->getdatawhere("m_soal", [
			"kode_soal" => $varSoal->kode_soal,
			"tipe_soal" => $jenisSoal,
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"tahun" => $this->session->userdata('whs_tahun')
		]);

		$jawabanWhere = $where;
		if($jenisSoal == "umum"){
			$data["header"] = "Faktor Umum";
			unset($jawabanWhere["tipe_variable"]);
			unset($where["tipe_variable"]);
			unset($jawabanWhere["id_badan"]);
			unset($where["id_badan"]);
		}else{
			$data["header"] = "Faktor Teknis";
			if ($tipeVar == "kecamatan") {
				$jawabanWhere["kode_kecamatan"] = $idBadan;
				unset($jawabanWhere["id_badan"]);
				unset($where["id_badan"]);
			}
		}
		$data["jawaban"] = $this->mdb->getdatawhere("tb_jawaban", $jawabanWhere);
		// echo json_encode($where);die;
		$status_jawaban = $this->mdb->getrowdatawhere("tb_status_jawaban", $where);
		if(!$status_jawaban){
			$status_jawaban = new stdClass();
			$status_jawaban->{"status"} = "-";
		}

		$data["status_jawaban"] = $status_jawaban;
		$data["tipe_soal"] = $jenisSoal;
		$data["content"] = $this->load->view('v_lembar_soal', $data, true);
		$this->load->view('v_header', $data);

	}
	public function delete_file()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$tipeVar = $this->input->post("tipe_var");
		$id = $this->input->post("id");
		$data = array(
			"id_soal" => $id,
			"tahun" => $this->session->userdata('whs_tahun'),
			"tipe_variable" => $tipeVar,
			"tipe_soal" => $this->input->post("tipe_soal"),
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
			"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
			"updated_date" => date("Y-m-d H:i:s")
		);
		if($tipeVar == "sekda" || $tipeVar == "sekdprd" || $tipeVar == "inspektorat"){
			$where = [
				"id_soal" => $id,
				"tipe_variable" => $tipeVar,
				"tahun" => $this->session->userdata('whs_tahun'),
				"tipe_soal" => $this->input->post("tipe_soal"),
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
				"tipe_daerah" => $this->session->userdata('whs_role')
			];
		}elseif($tipeVar == "dinas" || $tipeVar == "badan"){
			$data["id_badan"] = $this->input->post("id_badan");
			$where = [
				"id_soal" => $id,
				"tipe_variable" => $tipeVar,
				"tahun" => $this->session->userdata('whs_tahun'),
				"tipe_soal" => $this->input->post("tipe_soal"),
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"id_badan" => $this->input->post("id_badan")
			];
			if($this->input->post("tipe_soal") == "umum"){
				unset($where["id_badan"]);
			}
		}else{
			$data["kode_kecamatan"] = $this->input->post("id_badan");
			$where = [
				"id_soal" => $id,
				"tipe_variable" => $tipeVar,
				"tahun" => $this->session->userdata('whs_tahun'),
				"tipe_soal" => $this->input->post("tipe_soal"),
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"kode_kecamatan" => $this->input->post("id_badan")
			];
			if($this->input->post("tipe_soal") == "umum"){
				unset($where["kode_kecamatan"]);
			}
		}
		if($this->input->post("tipe_soal") == "umum"){
			$data["tipe_variable"] = "";
			$where["tipe_variable"] = "";
		}
		$search = $this->mdb->getrowdatawhere("tb_jawaban", $where);
		if($search){
			if($search->upload){
				$data["upload"] = "";
				$tipeDaerah = $this->session->userdata('whs_role');
				$kodeDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_kode_provinsi') : $this->session->userdata('whs_kode_kabupaten');
				$path = "public/$tipeDaerah/$kodeDaerah/$search->upload";
				$this->mdb->putdatawhere("tb_jawaban", $where, $data, NULL, [$path]);
			}
		}
		if($data && $where){
			$data["status"] = "draft";
			$where["tipe_soal"] = $this->input->post("tipe_soal");
			unset($where["id_soal"]);
			unset($data["id_soal"]);
			unset($data["upload"]);
			$search = $this->mdb->getrowdatawhere("tb_status_jawaban", $where);
			if(!$search){
				$this->mdb->postdata("tb_status_jawaban", $data);
			}else{
				$this->mdb->putdatawhere("tb_status_jawaban", $where, $data);
			}
		}
	}
	public function upload_file()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$tipeVar = $this->input->post("tipe_var");
		$id = $this->input->post("id");
		$data = array(
			"id_soal" => $id,
			"tahun" => $this->session->userdata('whs_tahun'),
			"tipe_variable" => $tipeVar,
			"tipe_soal" => $this->input->post("tipe_soal"),
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
			"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
			"updated_date" => date("Y-m-d H:i:s")
		);
		if($tipeVar == "sekda" || $tipeVar == "sekdprd" || $tipeVar == "inspektorat"){
			$where = [
				"id_soal" => $id,
				"tipe_variable" => $tipeVar,
				"tahun" => $this->session->userdata('whs_tahun'),
				"tipe_soal" => $this->input->post("tipe_soal"),
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
				"tipe_daerah" => $this->session->userdata('whs_role')
			];
		}elseif($tipeVar == "dinas" || $tipeVar == "badan"){
			$data["id_badan"] = $this->input->post("id_badan");
			$where = [
				"id_soal" => $id,
				"tipe_variable" => $tipeVar,
				"tahun" => $this->session->userdata('whs_tahun'),
				"tipe_soal" => $this->input->post("tipe_soal"),
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"id_badan" => $this->input->post("id_badan")
			];
			if($this->input->post("tipe_soal") == "umum"){
				unset($where["id_badan"]);
			}
		}else{
			$data["kode_kecamatan"] = $this->input->post("id_badan");
			$where = [
				"id_soal" => $id,
				"tipe_variable" => $tipeVar,
				"tahun" => $this->session->userdata('whs_tahun'),
				"tipe_soal" => $this->input->post("tipe_soal"),
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"kode_kecamatan" => $this->input->post("id_badan")
			];
			if($this->input->post("tipe_soal") == "umum"){
				unset($where["kode_kecamatan"]);
			}
		}
		if($this->input->post("tipe_soal") == "umum"){
			$data["tipe_variable"] = "";
			$where["tipe_variable"] = "";
		}
		$this->load->library('upload');
		$fileName = $_FILES["file"]['name'];
		$upload = null;
		$storeName = "";
		if($fileName){
			$fileNameCmps = explode(".", $fileName);
			$fileExtension = strtolower(end($fileNameCmps));
			$storeName = $id."_".rand(1111111111,9999999999).".".$fileExtension;
			$tipeDaerah = $this->session->userdata('whs_role');
			$kodeDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_kode_provinsi') : $this->session->userdata('whs_kode_kabupaten');
			$path = "public/$tipeDaerah/$kodeDaerah";
			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
			$config['upload_path'] = $path;
			$config['allowed_types'] = 'pdf';
			$config['file_name'] = $storeName;
			$config['max_size'] = 2048;
			$upload = ["file",$config];
			$data["upload"] = $storeName;
		}else{
			http_response_code(500);
			die;
		}
		$search = $this->mdb->getrowdatawhere("tb_jawaban", $where);
		if(!$search){
			$this->mdb->postdata("tb_jawaban", $data, $upload);
		}else{
			$remove = null;
			if($search->upload <> ""){
				$remove = [$path."/".$search->upload];
			}
			$this->mdb->putdatawhere("tb_jawaban", $where, $data, $upload, $remove);
		}
		if($data && $where){
			$data["status"] = "draft";
			$where["tipe_soal"] = $this->input->post("tipe_soal");
			unset($where["id_soal"]);
			unset($data["upload"]);
			unset($data["id_soal"]);
			$search = $this->mdb->getrowdatawhere("tb_status_jawaban", $where);
			if(!$search){
				$this->mdb->postdata("tb_status_jawaban", $data);
			}else{
				$this->mdb->putdatawhere("tb_status_jawaban", $where, $data);
			}
		}
		echo $storeName;

	}
	public function send_variable()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$tipeVar = $this->input->post("tipe_var");
		$soalJawab = [];
		$searchSoal = [];
		if(!$this->input->post("ans")){
			die;
		}
		foreach ($this->input->post("ans") as $k => $v) {
			$ans = "";
			$ansData = $this->mdb->getrowdatawhere("m_soal", ["id_soal" => $k]);
			$ansBarray = explode("-", $ansData->jawaban_b);
			$ansCarray = explode("-", $ansData->jawaban_c);
			$ansDarray = explode("-", $ansData->jawaban_d);
			if($v <= $ansData->jawaban_a){
				$ans = "a";
			}elseif($v >= $ansBarray[0] && $v <= $ansBarray[1]){
				$ans = "b";
			}elseif($v >= $ansCarray[0] && $v <= $ansCarray[1]){
				$ans = "c";
			}elseif($v >= $ansDarray[0] && $v <= $ansDarray[1]){
				$ans = "d";
			}else{
				$ans = "e";
			}
			$data = array(
				"id_soal" => $k,
				"jawaban" => $ans,
				"value" => $v,
				"tahun" => $this->session->userdata('whs_tahun'),
				"tipe_variable" => $tipeVar,
				"tipe_soal" => $this->input->post("tipe_soal"),
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
				"updated_date" => date("Y-m-d H:i:s")
			);
			if($tipeVar == "sekda" || $tipeVar == "sekdprd" || $tipeVar == "inspektorat"){
				$where = [
					"id_soal" => $k,
					"tipe_variable" => $tipeVar,
					"tahun" => $this->session->userdata('whs_tahun'),
					"tipe_soal" => $this->input->post("tipe_soal"),
					"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
					"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
					"tipe_daerah" => $this->session->userdata('whs_role')
				];
			}elseif($tipeVar == "dinas" || $tipeVar == "badan"){
				$data["id_badan"] = $this->input->post("id_badan");
				$where = [
					"id_soal" => $k,
					"tipe_variable" => $tipeVar,
					"tahun" => $this->session->userdata('whs_tahun'),
					"tipe_soal" => $this->input->post("tipe_soal"),
					"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
					"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"id_badan" => $this->input->post("id_badan")
				];
				if($this->input->post("tipe_soal") == "umum"){
					unset($where["id_badan"]);
				}
			}else{
				$data["kode_kecamatan"] = $this->input->post("id_badan");
				$where = [
					"id_soal" => $k,
					"tipe_variable" => $tipeVar,
					"tahun" => $this->session->userdata('whs_tahun'),
					"tipe_soal" => $this->input->post("tipe_soal"),
					"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
					"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"kode_kecamatan" => $this->input->post("id_badan")
				];
				if($this->input->post("tipe_soal") == "umum"){
					unset($where["kode_kecamatan"]);
				}
			}
			if($this->input->post("tipe_soal") == "umum"){
				$data["tipe_variable"] = "";
				$where["tipe_variable"] = "";
			}
			$soalJawab[$k] = $ans;
			$searchSoal[] = $k;
			$search = $this->mdb->getrowdatawhere("tb_jawaban", $where);
			if(!$search){
				$this->mdb->postdata("tb_jawaban", $data);
			}else{
				$this->mdb->putdatawhere("tb_jawaban", $where, $data);
			}

		}
		if($data && $where){
			if($this->input->post("submit_type") == "draft"){
				$data["status"] = "draft";
				$where["tipe_soal"] = $this->input->post("tipe_soal");
			}else{
				$data["status"] = "submit";
				$where["tipe_soal"] = $this->input->post("tipe_soal");
			}
			unset($where["id_soal"]);
			unset($data["id_soal"]);
			unset($data["jawaban"]);
			unset($data["value"]);
			$search = $this->mdb->getrowdatawhere("tb_status_jawaban", $where);
			if(!$search){
				$this->mdb->postdata("tb_status_jawaban", $data);
			}else{
				$this->mdb->putdatawhere("tb_status_jawaban", $where, $data);
			}
			if($this->input->post("submit_type") == "submit"){
				unset($data["status"]);
				$perkalian = $this->pengkalian_wilayah($this->session->userdata('whs_kode_provinsi'), $this->session->userdata('whs_kode_kabupaten'));
				$nilai = 0;
				$searchNilai = $this->mdb->getdatawhere("m_soal", null, null, null, ["id_soal", $searchSoal]);
				$ArrInformasi = [];
				$outputNilai = [];
				foreach ($searchNilai as $k => $v) {
					$skor = (($v->bobot / 100) * $v->{"skala_{$soalJawab[$v->id_soal]}"});
					$nilai += $skor;
					$outputNilai[$v->id_soal] = [];
					$outputNilai[$v->id_soal]["indikator"] = $v->soal;
					$ArrInformasi[] = $v->{"jawaban_{$soalJawab[$v->id_soal]}"};
					$outputNilai[$v->id_soal]["skor"] = $skor;
				}
				$data["skor"] = $nilai;

				$data["id_kategori_perkalian"] = $perkalian->id_kategori_perkalian;
				$search = $this->mdb->getrowdatawhere("tb_skor", $where);
				if(!$search){
					$this->mdb->postdata("tb_skor", $data);
				}else{
					$this->mdb->putdatawhere("tb_skor", $where, $data);
				}
				echo json_encode($outputNilai);
			}

		}

	}
	private function pengkalian_wilayah($id, $idkab=null)
	{
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

		if(in_array($idkab, $kode_bps_kategori_g)){
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
	public function sekda()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "sekda";
		$data["data_status"]["teknis"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_variable" => "sekda", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$data["data_status"]["umum"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "umum", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Sekretariat Daerah $tipeDaerah $namaDaerah"));
		$data["url"] = "sekretariat-daerah";
		$data["content"] = $this->load->view('v_pilih_variable', $data, true);
		$this->load->view('v_header', $data);
	}
	public function sekdprd()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "sekdprd";
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$data["data_status"]["teknis"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_variable" => "sekdprd", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$data["data_status"]["umum"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "umum", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = "Sekretariat DPRD " . ucwords(strtolower("$tipeDaerah $namaDaerah"));
		$data["url"] = "sekretariat-dprd";
		$data["content"] = $this->load->view('v_pilih_variable', $data, true);
		$this->load->view('v_header', $data);
	}
	public function inspektorat()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "inspektorat";
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$data["data_status"]["teknis"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_variable" => "inspektorat", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$data["data_status"]["umum"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "umum", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Inspektorat $tipeDaerah $namaDaerah"));
		$data["url"] = "inspektorat";
		$data["content"] = $this->load->view('v_pilih_variable', $data, true);
		$this->load->view('v_header', $data);
	}
	public function dinas()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "dinas";
		$data["data_variable"] = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => "dinas", "tipe_daerah" => $this->session->userdata('whs_role')]);

		$data["data_status"]["teknis"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_variable" => "dinas", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$data["data_status"]["umum"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "umum", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);

		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Dinas $tipeDaerah $namaDaerah"));
		$data["url"] = "dinas";
		$data["content"] = $this->load->view('v_pilih_variable_detail', $data, true);
		$this->load->view('v_header', $data);
	}
	public function badan()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "badan";
		$data["data_variable"] = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => "badan", "tipe_daerah" => $this->session->userdata('whs_role')]);
		$data["data_status"]["teknis"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_variable" => "badan", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$data["data_status"]["umum"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "umum", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Badan $tipeDaerah $namaDaerah"));
		$data["url"] = "badan";
		$data["content"] = $this->load->view('v_pilih_variable_detail', $data, true);
		$this->load->view('v_header', $data);
	}
	public function kecamatan()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "kecamatan";
		$data["data_variable"] = $this->mdb->getdatawhere("m_kecamatan", ["kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$data["data_status"]["teknis"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_soal" => "teknis", "tipe_variable" => "kecamatan", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$data["data_status"]["umum"] = $this->mdb->getrowdatawhere("tb_status_jawaban", ["tipe_soal" => "umum", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Kecamatan $tipeDaerah $namaDaerah"));
		$data["url"] = "kecamatan";
		$data["content"] = $this->load->view('v_pilih_variable_detail', $data, true);
		$this->load->view('v_header', $data);
	}
	public function view_skor()
	{
		$tipeVar = $this->input->post("tipe_var");
		$idBadan = $this->input->post("id_badan");
		if($this->session->userdata('whs_role') == "provinsi" || $this->session->userdata('whs_role') == "kabupaten"){
			$role = $this->session->userdata('whs_role');
			$tahun = $this->session->userdata('whs_tahun');
			$kode_provinsi = $this->session->userdata('whs_kode_provinsi');
			$kode_kabupaten = $this->session->userdata('whs_kode_kabupaten');
			$nama_provinsi = $this->session->userdata('whs_nama_provinsi');
			$nama_kabupaten = $this->session->userdata('whs_nama_kabupaten');
		}else{
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
			$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
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
			$namaBadanTemp = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
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
			}elseif($tipeVar == "sekda"){
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
		if(!$jawaban || !$jawabanTeknis){
			echo "204";
			die;
		}
		$values = [];
		$check = [];
		$x = 0;

		foreach ($soal as $k => $v) {
			$ans = "a";
			foreach(range('a','e') as $i) {
				if($this->find_answer($jawaban, $v->id_soal, $i) == true) {
					$check[$i] = "■";
					$ans = $i;
				}else{
					$check[$i] = "☐";
				}
			}
			$x++;
			$values[] = [
				'x' => "{$v->no}.",
				'soal' => "{$v->soal}",
				'jawaban' => "{$this->find_answer_value($jawaban, $v->id_soal)}",
				'jawaban_a' => "{$check['a']} {$v->jawaban_a}",
				'jawaban_b' => "{$check['b']} {$v->jawaban_b}",
				'jawaban_c' => "{$check['c']} {$v->jawaban_c}",
				'jawaban_d' => "{$check['d']} {$v->jawaban_d}",
				'jawaban_e' => "{$check['e']} {$v->jawaban_e}",
				'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}
		$html = '<div class="text-center h5">Faktor Umum</div><div class="table-responsive mb-2"><table class="table table-bordered mb-0 border"><thead class="text-center"><tr><th>No</th><th>Indikator & Kelas Interval</th><th>Skor</th></tr></thead><tbody>';
		foreach ($values as $k => $v) {
			$html .= <<<SMF
			<tr><td class="text-center">{$v['x']}</td><td><p class="m-0">{$v['soal']}</p>
			<p class="m-0">{$v['jawaban']}</p>
			<p class="m-0">{$v['jawaban_a']}</p>
			<p class="m-0">{$v['jawaban_b']}</p>
			<p class="m-0">{$v['jawaban_c']}</p>
			<p class="m-0">{$v['jawaban_d']}</p>
			<p class="m-0">{$v['jawaban_e']}</p>
			</td><td class="text-center">{$v['skor']}</td></tr>
			SMF;
		}
		$html .= "</tbody></table></div>";

		$values = [];
		$check = [];
		foreach ($soalTeknis as $k => $v) {
			$ans = "a";
			foreach(range('a','e') as $i) {
				if($this->find_answer($jawabanTeknis, $v->id_soal, $i) == true) {
					$check[$i] = "■";
					$ans = $i;
				}else{
					$check[$i] = "☐";
				}
			}
			$x++;
			$values[] = [
				'x' => "{$v->no}.",
				'soal' => "{$v->soal}",
				'jawaban' => "{$this->find_answer_value($jawabanTeknis, $v->id_soal)}",
				'jawaban_a' => "{$check['a']} {$v->jawaban_a}",
				'jawaban_b' => "{$check['b']} {$v->jawaban_b}",
				'jawaban_c' => "{$check['c']} {$v->jawaban_c}",
				'jawaban_d' => "{$check['d']} {$v->jawaban_d}",
				'jawaban_e' => "{$check['e']} {$v->jawaban_e}",
				'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}
		$html .= '<div class="text-center h5">Faktor Teknis</div><div class="table-responsive mb-2"><table class="table table-bordered mb-0 border"><thead class="text-center"><tr><th>No</th><th>Indikator & Kelas Interval</th><th>Skor</th></tr></thead><tbody>';
		foreach ($values as $k => $v) {
			$html .= <<<SMF
			<tr><td class="text-center">{$v['x']}</td><td><p class="m-0">{$v['soal']}</p>
			<p class="m-0">{$v['jawaban']}</p>
			<p class="m-0">{$v['jawaban_a']}</p>
			<p class="m-0">{$v['jawaban_b']}</p>
			<p class="m-0">{$v['jawaban_c']}</p>
			<p class="m-0">{$v['jawaban_d']}</p>
			<p class="m-0">{$v['jawaban_e']}</p>
			</td><td class="text-center">{$v['skor']}</td></tr>
			SMF;
		}
		$html .= "</tbody></table></div>";

		$skor = '<div class="">';
		$title = "$tipeVarName $namaDaerah {$badanName}";
		$skorDataUmum = $this->mdb->getrowdatawhere("tb_skor", $whereJawaban);
		$skorDataTeknis = $this->mdb->getrowdatawhere("tb_skor", $whereJawabanTeknis);
		if($skorDataUmum && $skorDataTeknis){
			$perkalian = $this->mdb->getrowdatawhere("m_kategori_perkalian", ["id_kategori_perkalian" => $skorDataUmum->id_kategori_perkalian]);
			$skorUmum = $skorDataUmum ? $skorDataUmum->skor : 0;
			$skorTeknis = $skorDataTeknis->skor;
			if($skorUmum == 0){
				$skor .= <<<SMF
				<p>$title mempunyai Nilai Tipelogi -. dengan Nilai Skor : </p>
				<p class="m-0">1. Variable Umum: (Variable Umum belum diisi)</p>
				<p class="m-0">2. Variable Teknis: {$skorTeknis}</p>
				<p class="m-0">Pengkalian Wilayah: {$perkalian->kategori}</p>
				<p>Total Skor: (Variable Umum belum diisi) (-)</p>
				SMF;
			}else{
				$skorInt = (($skorTeknis * 0.8)+($skorUmum * 0.2)) * $perkalian->perkalian;
				$totalSkor = number_format($skorInt, 1);
				$kategori = "";
				if($skorInt <= 300){
					$kategori = "setingkat seksi/subbidang";
				}elseif($skorInt <= 400){
					$kategori = "setingkat bidang";
				}elseif($skorInt <= 600){
					$kategori = "tipe C";
				}elseif($skorInt <= 800){
					$kategori = "tipe B";
				}else{
					$kategori = "tipe A";
				}
				$skor .= <<<SMF
				<p>$title mempunyai Nilai Tipelogi $kategori. dengan Nilai Skor : </p>
				<p class="m-0">1. Variable Umum: {$skorUmum}</p>
				<p class="m-0">2. Variable Teknis: {$skorTeknis}</p>
				<p>Pengkalian Wilayah: {$perkalian->kategori}</p>
				<p class="m-0">Total Skor: $totalSkor ($kategori)</p>
				SMF;
			}
		}
		$skor .= "</div>";
		echo $html.$skor;
	}
	public function download_variable_all()
	{
		$tipeVar = $_GET["tipe_var"];
		$idBadan = $_GET["id_badan"];
		if($this->session->userdata('whs_role') == "provinsi" || $this->session->userdata('whs_role') == "kabupaten"){
			$role = $this->session->userdata('whs_role');
			$tahun = $this->session->userdata('whs_tahun');
			$kode_provinsi = $this->session->userdata('whs_kode_provinsi');
			$kode_kabupaten = $this->session->userdata('whs_kode_kabupaten');
			$nama_provinsi = $this->session->userdata('whs_nama_provinsi');
			$nama_kabupaten = $this->session->userdata('whs_nama_kabupaten');
		}else{
			$role = $_GET["role"];
			$tahun = $_GET["tahun"];
			$kode_provinsi = $_GET["provinsi"];
			$kode_kabupaten = $_GET["kabupaten"];
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
		}
		$this->download_pdf_all($tipeVar, $idBadan, $role, $tahun, $kode_provinsi, $kode_kabupaten, $nama_provinsi, $nama_kabupaten);
	}
	public function download_pdf_all($tipeVar, $idBadan, $role, $tahun, $kode_provinsi, $kode_kabupaten, $nama_provinsi, $nama_kabupaten)
	{
		$namaDaerah = $role == "provinsi" ? "Provinsi ". $nama_provinsi : $nama_kabupaten;

		$whereVarSoal = [
			"tipe_daerah" => $role,
			"tahun" => $tahun
		];
		$whereVarSoal["tipe_soal"] = "umum";
		$varSoal = $this->mdb->getrowdatawhere("tb_variable_soal", $whereVarSoal);
		if($tipeVar == "dinas" || $tipeVar == "badan"){
			$whereVarSoal["id_badan"] = $idBadan;
		}
		$whereVarSoal["tipe_soal"] = "teknis";
		$whereVarSoal["tipe_variable"] = $tipeVar;
		$varSoalTeknis = $this->mdb->getrowdatawhere("tb_variable_soal", $whereVarSoal);

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
			$namaBadanTemp = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
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
			}elseif($tipeVar == "sekda"){
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
		$values = [];
		$check = [];

		foreach ($soal as $k => $v) {
			$ans = "a";
			foreach(range('a','e') as $i) {
				if($this->find_answer($jawaban, $v->id_soal, $i) == true) {
					$check[$i] = "■";
					$ans = $i;
				}else{
					$check[$i] = "☐";
				}
			}
			$values[] = [
				'x' => "{$v->no}.",
				'soal' => "{$v->soal}",
				'jawaban_a' => "{$check['a']} {$v->jawaban_a}",
				'jawaban_b' => "{$check['b']} {$v->jawaban_b}",
				'jawaban_c' => "{$check['c']} {$v->jawaban_c}",
				'jawaban_d' => "{$check['d']} {$v->jawaban_d}",
				'jawaban_e' => "{$check['e']} {$v->jawaban_e}",
				'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}

		$zvalues = [];
		$check = [];
		foreach ($soalTeknis as $k => $v) {
			$ans = "a";
			foreach(range('a','e') as $i) {
				if($this->find_answer($jawabanTeknis, $v->id_soal, $i) == true) {
					$check[$i] = "■";
					$ans = $i;
				}else{
					$check[$i] = "☐";
				}
			}
			$zvalues[] = [
				'z' => "{$v->no}.",
				'z_soal' => "{$v->soal}",
				'z_jawaban_a' => "{$check['a']} {$v->jawaban_a}",
				'z_jawaban_b' => "{$check['b']} {$v->jawaban_b}",
				'z_jawaban_c' => "{$check['c']} {$v->jawaban_c}",
				'z_jawaban_d' => "{$check['d']} {$v->jawaban_d}",
				'z_jawaban_e' => "{$check['e']} {$v->jawaban_e}",
				'z_skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}
		$title = "$tipeVarName $namaDaerah {$badanName}";
		$templateProcessor = new TemplateProcessor('templates/variable.docx');
		$skorDataUmum = $this->mdb->getrowdatawhere("tb_skor", $whereJawaban);
		$skorDataTeknis = $this->mdb->getrowdatawhere("tb_skor", $whereJawabanTeknis);
		if($skorDataUmum && $skorDataTeknis){
			$perkalian = $this->mdb->getrowdatawhere("m_kategori_perkalian", ["id_kategori_perkalian" => $skorDataTeknis->id_kategori_perkalian]);
			$skorUmum = $skorDataUmum ? $skorDataUmum->skor : 0;
			$skorTeknis = $skorDataTeknis->skor;
			if($skorUmum == 0){
				$templateProcessor->setValue('skor_umum', "(Variable Umum belum diisi)");
				$templateProcessor->setValue('skor_teknis', $skorTeknis);
				$templateProcessor->setValue('pengkalian', $perkalian->kategori);
				$templateProcessor->setValue('total_skor', "(Variable Umum belum diisi)");
				$templateProcessor->setValue('kategori', "-");
			}else{
				$templateProcessor->setValue('skor_umum', $skorUmum);
				$templateProcessor->setValue('skor_teknis', $skorTeknis);
				$templateProcessor->setValue('pengkalian', $perkalian->kategori);
				$skorInt = (($skorTeknis * 0.8)+($skorUmum * 0.2)) * $perkalian->perkalian;
				$totalSkor = number_format($skorInt, 1);
				$kategori = "";
				if($skorInt <= 300){
					$kategori = "setingkat seksi/subbidang";
				}elseif($skorInt <= 400){
					$kategori = "setingkat bidang";
				}elseif($skorInt <= 600){
					$kategori = "tipe C";
				}elseif($skorInt <= 800){
					$kategori = "tipe B";
				}else{
					$kategori = "tipe A";
				}
				$templateProcessor->setValue('total_skor', $totalSkor);
				$templateProcessor->setValue('kategori', $kategori);
			}

		}
		$templateProcessor->setValue('title', $title);
		$templateProcessor->cloneRowAndSetValues('x', $values);
		$templateProcessor->cloneRowAndSetValues('z', $zvalues);
		$tempDocx = 'templates/temp/temp'.$kode_provinsi.$kode_kabupaten.rand(111111,999999).rand(111111,999999).'.docx';
		$templateProcessor->saveAs($tempDocx);
		$phpWord = IOFactory::load($tempDocx);
		$htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
		ob_start();
		$htmlWriter->save('php://output');
		$html = ob_get_clean();
		$html = '<style>
		@font-face {
			font-family: "DejaVu Sans";
			src: url("'.base_url("vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf").'") format("truetype");
		}
		body,td,p { font-family: "DejaVu Sans", sans-serif; font-size: 11pt; }
		td { padding: 10px}
		table {
			border-collapse: collapse;
			width: 100%;
		}
		table, th, td {
			border: 1px solid #000;
			padding: 5px;
			font-size: 11pt;
		}
		p { margin: 0; padding: 0px; line-height: 1; }
		</style>' . $html;
		$options = new Options();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream(str_replace(" ", "_", $title), ['Attachment' => false]);
		unlink($tempDocx);
		die;
	}
	public function download_variable()
	{
		$this->download_pdf($_GET["tipe_var"],$_GET["tipe_soal"],$_GET["id_badan"]);
	}
	private function find_answer($answer, $id_soal, $option)
	{
		foreach ($answer as $k => $v) {
			if($v->id_soal == $id_soal){
				if($v->jawaban == $option){
					return true;
				}
			}
		}
		return false;
	}
	private function find_answer_value($answer, $id_soal)
	{
		foreach ($answer as $k => $v) {
			if($v->id_soal == $id_soal){
				return $v->value;
			}
		}
		return false;
	}
	private function download_pdf($tipeVar, $tipeSoal, $idBadan)
	{
		$whereVarSoal = [
			"tipe_variable" => $tipeVar, 
			"tipe_soal" => $tipeSoal, 
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"tahun" => $this->session->userdata('whs_tahun')
		];
		if($tipeSoal == "umum"){
			unset($whereVarSoal["tipe_variable"]);
		}else{
			if($tipeVar == "dinas" || $tipeVar == "badan"){
				$whereVarSoal["id_badan"] = $idBadan;
			}elseif($tipeVar == "kecamatan"){
				$whereVarSoal["kode_kecamatan"] = $idBadan;
			}
		}


		$varSoal = $this->mdb->getrowdatawhere("tb_variable_soal", $whereVarSoal);
		$whereSoal = [
			"kode_soal" => $varSoal->kode_soal, 
			"tipe_soal" => $tipeSoal, 
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"tahun" => $this->session->userdata('whs_tahun')
		];
		$whereJawaban = [
			"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), 
			"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), 
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"tipe_soal" => $tipeSoal,
			"tipe_variable" => $tipeVar, 
			"tahun" => $this->session->userdata('whs_tahun')
		];
		$tipeVarName = "";
		$badanName = "";
		if($tipeSoal == "umum"){
			unset($whereJawaban["tipe_variable"]);
		}
		if($tipeVar == "dinas" || $tipeVar == "badan"){
			if($tipeSoal <> "umum"){
				$whereJawaban["id_badan"] = $idBadan;
			}

			$namaBadanTemp = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
			if($namaBadanTemp->parent == ""){
				$badanName = ucwords(strtolower($namaBadanTemp->nama_badan))." ";
			}else{
				$badanName = ucwords(strtolower("{$namaBadanTemp->parent} {$namaBadanTemp->nama_badan}"))." ";
			}

			$tipeVarName = ucwords($tipeVar);
		}elseif($tipeVar == "kecamatan"){
			if($tipeSoal <> "umum"){
				$whereJawaban["kode_kecamatan"] = $idBadan;
			}
			$badanName = ucwords(strtolower($this->mdb->getrowdatawhere("m_kecamatan", ["kode_kecamatan" => $idBadan])->nama_kecamatan))." ";
			$tipeVarName = ucwords($tipeVar);
		}else{
			if($tipeVar == "sekda"){
				$tipeVarName = "Sekretariat Daerah";
			}elseif($tipeVar == "sekda"){
				$tipeVarName = "Sekretariat DPRD";			
			}else{
				$tipeVarName = "Inspektorat";			
			}
		}
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? "Provinsi ". $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$namaDaerah = ucwords(strtolower($namaDaerah));
		$soal = $this->mdb->getdatawhere("m_soal", $whereSoal);
		$jawaban = $this->mdb->getdatawhere("tb_jawaban", $whereJawaban);
		$values = [];
		$check = [];
		$x = 0;
		foreach ($soal as $k => $v) {
			$ans = "a";
			foreach(range('a','e') as $i) {
				if($this->find_answer($jawaban, $v->id_soal, $i) == true) {
					$check[$i] = "■";
					$ans = $i;
				}else{
					$check[$i] = "☐";
				}
			}
			$x++;
			$values[] = [
				'x' => "{$v->no}.",
				'soal' => "{$v->soal}",
				'jawaban_a' => "{$check['a']} {$v->jawaban_a}",
				'jawaban_b' => "{$check['b']} {$v->jawaban_b}",
				'jawaban_c' => "{$check['c']} {$v->jawaban_c}",
				'jawaban_d' => "{$check['d']} {$v->jawaban_d}",
				'jawaban_e' => "{$check['e']} {$v->jawaban_e}",
				'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}
		if($tipeSoal == "umum"){
			$templateProcessor = new TemplateProcessor('templates/variable_umum.docx');
		}else{
			$templateProcessor = new TemplateProcessor('templates/variable_teknis.docx');
			$skorDataTeknis = $this->mdb->getrowdatawhere("tb_skor", $whereJawaban);
			if($skorDataTeknis){
				$perkalian = $this->mdb->getrowdatawhere("m_kategori_perkalian", ["id_kategori_perkalian" => $skorDataTeknis->id_kategori_perkalian]);

				$skorDataUmum = $this->mdb->getrowdatawhere("tb_skor", ["tipe_soal" => "umum", "tahun" => $this->session->userdata('whs_tahun'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "tipe_daerah" => $this->session->userdata('whs_role')]);
				$skorUmum = $skorDataUmum ? $skorDataUmum->skor : 0;
				$skorTeknis = $skorDataTeknis->skor;
				if($skorUmum == 0){
					$templateProcessor->setValue('skor_umum', "(Variable Umum belum diisi)");
					$templateProcessor->setValue('skor_teknis', $skorTeknis);
					$templateProcessor->setValue('pengkalian', $perkalian->kategori);
					$templateProcessor->setValue('total_skor', "(Variable Umum belum diisi)");
					$templateProcessor->setValue('kategori', "-");
				}else{
					$templateProcessor->setValue('skor_umum', $skorUmum);
					$templateProcessor->setValue('skor_teknis', $skorTeknis);
					$templateProcessor->setValue('pengkalian', $perkalian->kategori);
					$skorInt = (($skorTeknis * 0.8)+($skorUmum * 0.2)) * $perkalian->perkalian;
					$totalSkor = number_format($skorInt, 1);
					$kategori = "";
					if($skorInt <= 300){
						$kategori = "setingkat seksi/subbidang";
					}elseif($skorInt <= 400){
						$kategori = "setingkat bidang";
					}elseif($skorInt <= 600){
						$kategori = "tipe C";
					}elseif($skorInt <= 800){
						$kategori = "tipe B";
					}else{
						$kategori = "tipe A";
					}
					$templateProcessor->setValue('total_skor', $totalSkor);
					$templateProcessor->setValue('kategori', $kategori);
				}

			}

		}
		$title = "$tipeVarName $namaDaerah {$badanName}Variable ".ucwords($tipeSoal);
		$templateProcessor->setValue('title', $title);
		$templateProcessor->cloneRowAndSetValues('x', $values);

		$tempDocx = 'templates/temp/temp'.$this->session->userdata('whs_kode_provinsi').$this->session->userdata('whs_kode_kabupaten').rand(111111,999999).'.docx';
		$templateProcessor->saveAs($tempDocx);
		$phpWord = IOFactory::load($tempDocx);
		$htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
		ob_start();
		$htmlWriter->save('php://output');
		$html = ob_get_clean();
		$html = '<style>
		@font-face {
			font-family: "DejaVu Sans";
			src: url("'.base_url("vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf").'") format("truetype");
		}
		body,td,p { font-family: "DejaVu Sans", sans-serif; font-size: 11pt; }
		td { padding: 10px}
		table {
			border-collapse: collapse;
			width: 100%;
		}
		table, th, td {
			border: 1px solid #000;
			padding: 5px;
			font-size: 11pt;
		}
		p { margin: 0; padding: 0px; line-height: 1; }
		</style>' . $html;
		$options = new Options();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream(str_replace(" ", "_", $title), ['Attachment' => false]);
		unlink($tempDocx);
		die;
	}
}
