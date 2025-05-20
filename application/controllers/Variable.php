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
			if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
				redirect(site_url('dashboard'));
			}
		}else{
			$this->session->set_flashdata('error', "Your session has expired!");
			redirect(site_url(''));
		}
	}
	public function index()
	{
		die;
	}
	public function add($url)
	{

		// echo json_encode($_POST);die;
		$data["title"] = "Add Variable";
		$kodeSoal = [];
		$tipeVar = $this->input->post("tipe_variable");
		if(!$tipeVar){
			redirect(site_url("/variable/$url"));
		}
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = ucwords($this->session->userdata('whs_role'));
		

		$data["tipe_var"] = $tipeVar;
		$data["url"] = $url;
		if($tipeVar == "sekda" || $tipeVar == "sekdprd" || $tipeVar == "inspektorat"){
			if($tipeVar == "sekda"){
				$data["subtitle"] = ucwords(strtolower("Sekretariat Daerah $tipeDaerah $namaDaerah"));
			}elseif($tipeVar == "sekdprd"){
				$data["subtitle"] = "Sekretariat DPRD " . ucwords(strtolower("$tipeDaerah $namaDaerah"));
			}elseif($tipeVar == "inspektorat"){
				$data["subtitle"] = ucwords(strtolower("Inspektorat $tipeDaerah $namaDaerah"));
			}
			$jenisSoal = $this->input->post("tipe_soal");
			$varSoal = $this->mdb->getrowdatawhere("tb_variable_soal", [
				"tipe_variable" => $tipeVar, 
				"tipe_soal" => $jenisSoal, 
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"tahun" => $this->session->userdata('whs_tahun')
			]);
			if(!$varSoal){
				echo "Soal belum dibuat";
				die;
			}
			$data["soal"] = $this->mdb->getdatawhere("m_soal", [
				"kode_soal" => $varSoal->kode_soal, 
				"tipe_soal" => $jenisSoal, 
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"tahun" => $this->session->userdata('whs_tahun')
			]);
			$data["jawaban"] = $this->mdb->getdatawhere("tb_jawaban", [
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), 
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), 
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"tipe_soal" => $jenisSoal,
				"tipe_variable" => $tipeVar, 
				"tahun" => $this->session->userdata('whs_tahun')
			]);
			$where = array(
				"tahun" => $this->session->userdata('whs_tahun'),
				"tipe_daerah" => $this->session->userdata('whs_role'),
				"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), 
				"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), 
				"tipe_soal" => $jenisSoal, 
				"tipe_variable" => $tipeVar,
			);
			$data["id_badan"] = 0;
		}elseif($tipeVar == "dinas" || $tipeVar == "badan" || $tipeVar == "kecamatan"){
			$jenisSoalPlus = explode("_", $this->input->post("tipe_soal_plus"));
			$jenisSoal = $jenisSoalPlus[0];
			$idBadan = $jenisSoalPlus[1];

			if($tipeVar == "dinas" || $tipeVar == "badan"){
				$varSoal = $this->mdb->getrowdatawhere("tb_variable_soal", [
					"tipe_variable" => $tipeVar, 
					"tipe_soal" => $jenisSoal, 
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"tahun" => $this->session->userdata('whs_tahun'),
					"id_badan" => $idBadan
				]);
				if(!$varSoal){
					echo "Soal belum dibuat";
					die;
				}
				$data["soal"] = $this->mdb->getdatawhere("m_soal", [
					"kode_soal" => $varSoal->kode_soal, 
					"tipe_soal" => $jenisSoal, 
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"tahun" => $this->session->userdata('whs_tahun')
				]);
				$data["jawaban"] = $this->mdb->getdatawhere("tb_jawaban", [
					"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), 
					"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), 
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"tipe_soal" => $jenisSoal,
					"tipe_variable" => $tipeVar,
					"id_badan" => $idBadan,
					"tahun" => $this->session->userdata('whs_tahun')
				]);
				$where = array(
					"tahun" => $this->session->userdata('whs_tahun'),
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), 
					"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), 
					"tipe_soal" => $jenisSoal, 
					"tipe_variable" => $tipeVar,
					"id_badan" => $idBadan,
				);
				$dataBadan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
			}else{
				$varSoal = $this->mdb->getrowdatawhere("tb_variable_soal", [
					"tipe_variable" => $tipeVar, 
					"tipe_soal" => $jenisSoal, 
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"tahun" => $this->session->userdata('whs_tahun')
				]);
				if(!$varSoal){
					echo "Soal belum dibuat";
					die;
				}
				$data["soal"] = $this->mdb->getdatawhere("m_soal", [
					"kode_soal" => $varSoal->kode_soal, 
					"tipe_soal" => $jenisSoal, 
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"tahun" => $this->session->userdata('whs_tahun')
				]);
				$data["jawaban"] = $this->mdb->getdatawhere("tb_jawaban", [
					"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), 
					"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), 
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"tipe_soal" => $jenisSoal,
					"tipe_variable" => $tipeVar,
					"kode_kecamatan" => $idBadan,
					"tahun" => $this->session->userdata('whs_tahun')
				]);
				$where = array(
					"tahun" => $this->session->userdata('whs_tahun'),
					"tipe_daerah" => $this->session->userdata('whs_role'),
					"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), 
					"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), 
					"tipe_soal" => $jenisSoal, 
					"tipe_variable" => $tipeVar,
					"kode_kecamatan" => $idBadan,
				);
				$dataBadan = $this->mdb->getrowdatawhere("m_kecamatan", ["kode_kecamatan" => $idBadan]);
			}

			$data["id_badan"] = $idBadan;
			if($tipeVar == "dinas"){
				$data["subtitle"] = ucwords(strtolower("Dinas $tipeDaerah $namaDaerah"));
				$data["subsubtitle"] = ucwords(strtolower($dataBadan->nama_badan));
			}elseif($tipeVar == "badan"){
				$data["subtitle"] = "Badan " . ucwords(strtolower("$tipeDaerah $namaDaerah"));
				$data["subsubtitle"] = ucwords(strtolower($dataBadan->nama_badan));
			}elseif($tipeVar == "kecamatan"){
				$data["subtitle"] = ucwords(strtolower("Kecamatan $tipeDaerah $namaDaerah"));
				$data["subsubtitle"] = ucwords(strtolower($dataBadan->nama_kecamatan));
			}
		}

		
		if($jenisSoal == "umum"){
			$data["header"] = "Faktor Umum";
		}else{
			$data["header"] = "Faktor Teknis";
		}
		
		$status_jawaban = $this->mdb->getrowdatawhere("tb_status_jawaban", $where);
		if(!$status_jawaban){
			$status_jawaban = new stdClass();
			$status_jawaban->{"status"} = "pure";
		}
		$data["status_jawaban"] = $status_jawaban;
		$data["tipe_soal"] = $jenisSoal;
		$data["content"] = $this->load->view('v_lembar_soal', $data, true);
		$this->load->view('v_header', $data);

	}
	public function delete_file()
	{
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
				"id_badan" => $this->input->post("kode_kecamatan")
			];
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
			unset($data["jawaban"]);
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
				"id_badan" => $this->input->post("kode_kecamatan")
			];
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
			$this->mdb->putdatawhere("tb_jawaban", $where, $data, $upload, [$path]);
		}
		if($data && $where){
			$data["status"] = "draft";
			$where["tipe_soal"] = $this->input->post("tipe_soal");
			unset($where["id_soal"]);
			unset($data["upload"]);
			unset($data["id_soal"]);
			unset($data["jawaban"]);
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
		$tipeVar = $this->input->post("tipe_var");
		$soalJawab = [];
		$searchSoal = [];
		if(!$this->input->post("ans")){
			die;
		}
		foreach ($this->input->post("ans") as $k => $v) {
			$data = array(
				"id_soal" => $k,
				"jawaban" => $v,
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
					"id_badan" => $this->input->post("kode_kecamatan")
				];
			}
			$soalJawab[$k] = $v;
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
			$search = $this->mdb->getrowdatawhere("tb_status_jawaban", $where);
			if(!$search){
				$this->mdb->postdata("tb_status_jawaban", $data);
			}else{
				$this->mdb->putdatawhere("tb_status_jawaban", $where, $data);
			}
			if($this->input->post("submit_type") == "submit"){
				unset($data["status"]);
				unset($data["tipe_soal"]);
				unset($where["tipe_soal"]);
				$perkalian = $this->pengkalian_wilayah($this->session->userdata('whs_kode_provinsi'), $this->session->userdata('whs_kode_kabupaten'));
				$nilai = 0;
				$searchNilai = $this->mdb->getdatawhere("m_soal", null, null, null, ["id_soal", $searchSoal]);
				$outputNilai = [];
				foreach ($searchNilai as $k => $v) {
					$skor = (($v->bobot / 100) * $v->{"skala_{$soalJawab[$v->id_soal]}"});
					$nilai += $skor;
					$outputNilai[$v->id_soal] = [];
					$outputNilai[$v->id_soal]["indikator"] = $v->soal;
					$outputNilai[$v->id_soal]["skor"] = $skor;
				}
				if($this->input->post("tipe_soal") == "umum"){
					$data["variable_umum"] = $nilai;
				}else{
					$data["variable_teknis"] = $nilai; 
				}
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
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "sekda";
		$data["data_status"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_variable" => "sekda", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		// echo json_encode($data["data_status"]);die;
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Sekretariat Daerah $tipeDaerah $namaDaerah"));
		$data["url"] = "sekretariat-daerah";
		$data["content"] = $this->load->view('v_pilih_variable', $data, true);
		$this->load->view('v_header', $data);
	}
	public function sekdprd()
	{
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "sekdprd";
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$data["data_status"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_variable" => "sekdprd", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = "Sekretariat DPRD " . ucwords(strtolower("$tipeDaerah $namaDaerah"));
		$data["url"] = "sekretariat-dprd";
		$data["content"] = $this->load->view('v_pilih_variable', $data, true);
		$this->load->view('v_header', $data);
	}
	public function inspektorat()
	{
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "inspektorat";
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$data["data_status"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_variable" => "inspektorat", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Inspektorat $tipeDaerah $namaDaerah"));
		$data["url"] = "inspektorat";
		$data["content"] = $this->load->view('v_pilih_variable', $data, true);
		$this->load->view('v_header', $data);
	}
	public function dinas()
	{
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "dinas";
		$data["data_variable"] = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => "dinas", "tipe_daerah" => $this->session->userdata('whs_role')]);
		$data["data_status"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_variable" => "dinas", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Dinas $tipeDaerah $namaDaerah"));
		$data["url"] = "dinas";
		$data["content"] = $this->load->view('v_pilih_variable_detail', $data, true);
		$this->load->view('v_header', $data);
	}
	public function badan()
	{
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "badan";
		$data["data_variable"] = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => "badan", "tipe_daerah" => $this->session->userdata('whs_role')]);
		$data["data_status"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_variable" => "badan", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$namaDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_nama_provinsi') : $this->session->userdata('whs_nama_kabupaten');
		$tipeDaerah = $this->session->userdata('whs_role') == "provinsi" ? ucwords($this->session->userdata('whs_role')) : "";
		$data["subtitle"] = ucwords(strtolower("Badan $tipeDaerah $namaDaerah"));
		$data["url"] = "badan";
		$data["content"] = $this->load->view('v_pilih_variable_detail', $data, true);
		$this->load->view('v_header', $data);
	}
	public function kecamatan()
	{
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "kecamatan";
		$data["data_variable"] = $this->mdb->getdatawhere("m_kecamatan", ["kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		$data["data_status"] = $this->mdb->getdatawhere("tb_status_jawaban", ["tipe_variable" => "kecamatan", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
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
		$tipeSoal = $this->input->post("tipe_soal");
		$idBadan = $this->input->post("id_badan");
		
		$whereVarSoal = [
			"tipe_variable" => $tipeVar, 
			"tipe_soal" => $tipeSoal,
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"tahun" => $this->session->userdata('whs_tahun')
		];
		if($tipeVar == "dinas" || $tipeVar == "badan"){
			$whereVarSoal["id_badan"] = $idBadan;
		}elseif($tipeVar == "kecamatan"){
			$whereVarSoal["kode_kecamatan"] = $idBadan;
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
		if($tipeVar == "dinas" || $tipeVar == "badan"){
			$whereJawaban["id_badan"] = $idBadan;
			$namaBadanTemp = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
			if($namaBadanTemp->parent == ""){
				$badanName = ucwords(strtolower($namaBadanTemp->nama_badan))." ";
			}else{
				$badanName = ucwords(strtolower("{$namaBadanTemp->parent} {$namaBadanTemp->nama_badan}"))." ";
			}
			
			$tipeVarName = ucwords($tipeVar);
		}elseif($tipeVar == "kecamatan"){
			$whereJawaban["kode_kecamatan"] = $idBadan;
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
		$html = '<div class="table-responsive"><table class="table table-bordered mb-0 border"><thead class="text-center"><tr><th>No</th><th>Indikator & Kelas Interval</th><th>Skor</th></tr></thead><tbody>';
		foreach ($values as $k => $v) {
			$html .= <<<SMF
			<tr><td class="text-center">{$v['x']}</td><td><p class="m-0">{$v['soal']}</p>
			<p class="m-0">{$v['jawaban_a']}</p>
			<p class="m-0">{$v['jawaban_b']}</p>
			<p class="m-0">{$v['jawaban_c']}</p>
			<p class="m-0">{$v['jawaban_d']}</p>
			<p class="m-0">{$v['jawaban_e']}</p>
			</td><td class="text-center">{$v['skor']}</td></tr>
			SMF;
		}
		$html .= "</tbody></table></div>";
		$skor = '<div class="mt-2">';
		$title = "$tipeVarName $namaDaerah {$badanName}Variable ".ucwords($tipeSoal);
		if($tipeVar == "umum"){
		}else{
			unset($whereJawaban["tipe_soal"]);
			$skorData = $this->mdb->getrowdatawhere("tb_skor", $whereJawaban);
			if($skorData){
				$perkalian = $this->mdb->getrowdatawhere("m_kategori_perkalian", ["id_kategori_perkalian" => $skorData->id_kategori_perkalian]);
				if($skorData->variable_umum == 0){
					$skor .= <<<SMF
					<p>$title mempunyai Nilai Tipelogi -. dengan Nilai Skor : </p>
					<p class="m-0">1. Variable Umum: (Variable Umum belum diisi)</p>
					<p class="m-0">2. Variable Teknis: {$skorData->variable_teknis}</p>
					<p class="m-0">Pengkalian Wilayah: {$perkalian->kategori}</p>
					<p>Total Skor: (Variable Umum belum diisi) (-)</p>
					SMF;
				}else{
					$skorInt = (($skorData->variable_teknis * 0.8)+($skorData->variable_umum * 0.2)) * $perkalian->perkalian;
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
					<p class="m-0">1. Variable Umum: {$skorData->variable_umum}</p>
					<p class="m-0">2. Variable Teknis: {$skorData->variable_teknis}</p>
					<p>Pengkalian Wilayah: {$perkalian->kategori}</p>
					<p class="m-0">Total Skor: $totalSkor ($kategori)</p>
					SMF;
				}
				
			}
			
		}
		$skor .= "</div>";
		echo $html.$skor;
	}
	public function download_variable_umum()
	{
		$this->variable_umum($_GET["tipe_var"],$_GET["tipe_soal"],$_GET["id_badan"]);
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
	private function variable_umum($tipeVar, $tipeSoal, $idBadan)
	{
		$whereVarSoal = [
			"tipe_variable" => $tipeVar, 
			"tipe_soal" => $tipeSoal, 
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"tahun" => $this->session->userdata('whs_tahun')
		];
		if($tipeVar == "dinas" || $tipeVar == "badan"){
			$whereVarSoal["id_badan"] = $idBadan;
		}elseif($tipeVar == "kecamatan"){
			$whereVarSoal["kode_kecamatan"] = $idBadan;
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
		if($tipeVar == "dinas" || $tipeVar == "badan"){
			$whereJawaban["id_badan"] = $idBadan;
			$namaBadanTemp = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan]);
			if($namaBadanTemp->parent == ""){
				$badanName = ucwords(strtolower($namaBadanTemp->nama_badan))." ";
			}else{
				$badanName = ucwords(strtolower("{$namaBadanTemp->parent} {$namaBadanTemp->nama_badan}"))." ";
			}
			
			$tipeVarName = ucwords($tipeVar);
		}elseif($tipeVar == "kecamatan"){
			$whereJawaban["kode_kecamatan"] = $idBadan;
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
		if($tipeVar == "umum"){
			$templateProcessor = new TemplateProcessor('templates/variable_umum.docx');
		}else{
			$templateProcessor = new TemplateProcessor('templates/variable_teknis.docx');
			unset($whereJawaban["tipe_soal"]);
			$skorData = $this->mdb->getrowdatawhere("tb_skor", $whereJawaban);
			if($skorData){
				$perkalian = $this->mdb->getrowdatawhere("m_kategori_perkalian", ["id_kategori_perkalian" => $skorData->id_kategori_perkalian]);
				if($skorData->variable_umum == 0){
					$templateProcessor->setValue('skor_umum', "(Variable Umum belum diisi)");
					$templateProcessor->setValue('skor_teknis', $skorData->variable_teknis);
					$templateProcessor->setValue('pengkalian', $perkalian->kategori);
					$templateProcessor->setValue('total_skor', "(Variable Umum belum diisi)");
					$templateProcessor->setValue('kategori', "-");
				}else{
					$templateProcessor->setValue('skor_umum', $skorData->variable_umum);
					$templateProcessor->setValue('skor_teknis', $skorData->variable_teknis);
					$templateProcessor->setValue('pengkalian', $perkalian->kategori);
					$skorInt = (($skorData->variable_teknis * 0.8)+($skorData->variable_umum * 0.2)) * $perkalian->perkalian;
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
