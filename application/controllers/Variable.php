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
			$last = $this->uri->total_segments();
			$sec_last_uri = $this->uri->segment($last-1);

			if($sec_last_uri == "variable"){
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
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
		}else{
			redirect(site_url('informasi-data-umum'));
		}
		die;
	}
	public function add($url)
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
		}

		$data_tahun = $this->mdb->getrowdatawhere("m_tahun_pengisian", null, ["updated_date" => "desc"]);
		if($data_tahun){
			if($data_tahun->tahun <> $this->session->userdata('whs_tahun_pengisian')){
				$this->session->set_userdata('whs_tahun_pengisian', $data_tahun->tahun);
			}
		}
		if($this->session->userdata('whs_tahun') <> $this->session->userdata('whs_tahun_pengisian')){
			$data['info'] = "Pengisian data untuk tahun {$this->session->userdata('whs_tahun')} sudah ditutup!";
		}

		$data["check_tahun_pengisian"] = $this->session->userdata('whs_tahun') == $this->session->userdata('whs_tahun_pengisian');
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
				$dataBadan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan, "tahun" => $this->session->userdata('whs_tahun')]);
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
			$this->session->set_flashdata('error', "Soal belum dibuat!");
			redirect(site_url("/variable/$url"));
		}

		$data["soal"] = $this->mdb->getdatawhere("m_soal", [
			"kode_soal" => $varSoal->kode_soal,
			"tipe_soal" => $jenisSoal,
			"tipe_daerah" => $this->session->userdata('whs_role'),
			"tahun" => $this->session->userdata('whs_tahun')
		], null, ["no" => "asc"]);

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
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
		}
		$data_tahun = $this->mdb->getrowdatawhere("m_tahun_pengisian", null, ["updated_date" => "desc"]);
		if($data_tahun){
			if($data_tahun->tahun <> $this->session->userdata('whs_tahun_pengisian')){
				$this->session->set_userdata('whs_tahun_pengisian', $data_tahun->tahun);
			}
		}
		if($this->session->userdata('whs_tahun') <> $this->session->userdata('whs_tahun_pengisian')){
			$this->output->set_status_header(403);
			echo "Pengisian data untuk tahun {$this->session->userdata('whs_tahun')} sudah ditutup!";
			die;
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
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
		}
		$data_tahun = $this->mdb->getrowdatawhere("m_tahun_pengisian", null, ["updated_date" => "desc"]);
		if($data_tahun){
			if($data_tahun->tahun <> $this->session->userdata('whs_tahun_pengisian')){
				$this->session->set_userdata('whs_tahun_pengisian', $data_tahun->tahun);
			}
		}
		if($this->session->userdata('whs_tahun') <> $this->session->userdata('whs_tahun_pengisian')){
			$this->output->set_status_header(403);
			echo "Pengisian data untuk tahun {$this->session->userdata('whs_tahun')} sudah ditutup!";
			die;
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
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
		}
		$data_tahun = $this->mdb->getrowdatawhere("m_tahun_pengisian", null, ["updated_date" => "desc"]);
		if($data_tahun){
			if($data_tahun->tahun <> $this->session->userdata('whs_tahun_pengisian')){
				$this->session->set_userdata('whs_tahun_pengisian', $data_tahun->tahun);
			}
		}
		if($this->session->userdata('whs_tahun') <> $this->session->userdata('whs_tahun_pengisian')){
			$this->output->set_status_header(403);
			echo "Pengisian data untuk tahun {$this->session->userdata('whs_tahun')} sudah ditutup!";
			die;
		}
		$tipeVar = $this->input->post("tipe_var");
		$soalJawab = [];
		$soalJawabValue = [];
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
			$soalJawabValue[] = $v;
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
				$data["comment_kementerian"] = "";
				$data["comment_kl"] = "";
				$data["comment_provinsi"] = "";
				$data["approval_kementerian"] = 3;
				if($this->session->userdata('whs_role') == "provinsi"){
					if($this->input->post("tipe_soal") == "teknis"){
						if($tipeVar == "sekda" || $tipeVar == "sekdprd" || $tipeVar == "inspektorat"){
							$data["approval_kl"] = 3;
							$data["approval_provinsi"] = 4;
						}elseif($tipeVar == "dinas" || $tipeVar == "badan"){
							$data["approval_kl"] = 3;
							$data["approval_provinsi"] = 4;
						}
					}else{
						$data["approval_kl"] = 4;
						$data["approval_provinsi"] = 4;
					}
				}elseif($this->session->userdata('whs_role') == "kabupaten"){
					if($this->input->post("tipe_soal") == "teknis"){
						if($tipeVar == "sekda" || $tipeVar == "sekdprd" || $tipeVar == "inspektorat"){
							$data["approval_kl"] = 3;
							$data["approval_provinsi"] = 3;
						}elseif($tipeVar == "dinas" || $tipeVar == "badan"){
							$data["approval_kl"] = 3;
							$data["approval_provinsi"] = 3;
						}elseif($tipeVar == "kecamatan"){
							$data["approval_kl"] = 3;
							$data["approval_provinsi"] = 3;
						}
					}else{
						$data["approval_kl"] = 4;
						$data["approval_provinsi"] = 3;
					}
				}
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

				$sendTo = [];
				$admins = $this->mdb->getdatawhere("m_user", ["role" => "admin"]);
				foreach ($admins as $k => $v) {
					if($v->email){
						$sendTo[$v->email] = $v->name;
					}
				}
				if($data["approval_kl"] == 3){
					if(isset($where["kode_kecamatan"])){
						unset($where["kode_kecamatan"]);
					}
					unset($where["kode_kabupaten"]);
					unset($where["kode_provinsi"]);
					$klBadan = $this->mdb->getdatawhere("tb_kl_badan", $where);
					$klBadanArr = [];
					foreach ($klBadan as $k => $v) {
						$klBadanArr[] = $v->id_kl;
					}
					$kls = $this->mdb->getdatawhere("m_user", null, null, null, ["id_kl", $klBadanArr]);
					foreach ($kls as $k => $v) {
						if($v->email){
							$sendTo[$v->email] = $v->name;
						}
					}
				}
				if($data["approval_provinsi"] == 3){
					$provs = $this->mdb->getdatawhere("m_user", ["kode_provinsi" => $this->session->userdata('whs_kode_provinsi')]);
					foreach ($provs as $k => $v) {
						if($v->email){
							$sendTo[$v->email] = $v->name;
						}
					}
				}
				$secName = "";
				if($this->session->userdata('whs_role') == "provinsi"){
					$secName = ucwords(strtolower("Provinsi ". $this->session->userdata('whs_nama_provinsi')));
				}elseif($this->session->userdata('whs_role') == "kabupaten"){
					$secName = ucwords(strtolower($this->session->userdata('whs_nama_kabupaten')));
				}else{
					$secName = ucwords($this->session->userdata('whs_role'));
				}
				if($this->input->post("tipe_soal") == "teknis"){
					$title = preg_replace('/[^a-zA-Z :]/', '', $this->input->post("title"))." Variable Teknis";	
				}else{
					$title = $secName . " Variable Umum";
				}
				$secName = $this->session->userdata("whs_name") . " ($secName)";
				$url = base_url("approval/")."?&d={$this->session->userdata('whs_role')}&p={$this->session->userdata('whs_kode_provinsi')}&k={$this->session->userdata('whs_kode_kabupaten')}";
				$this->mdb->send_approval_request([$sendTo, $secName, $title, $url]);
				echo json_encode($outputNilai);
			}

		}

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
	public function sekda()
	{
		if($this->session->userdata('whs_role')=="admin" || $this->session->userdata('whs_role')=="superadmin"){
			redirect(site_url('dashboard'));
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
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
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
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
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
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
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
		}
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "dinas";
		$data["data_variable"] = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => "dinas", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun')]);

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
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
		}
		$data["title"] = "Add Variable";
		$data["tipe_variable"] = "badan";
		$data["data_variable"] = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => "badan", "tipe_daerah" => $this->session->userdata('whs_role'), "tahun" => $this->session->userdata('whs_tahun')]);
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
		}elseif($this->session->userdata('whs_role')=="kl"){
			redirect(site_url('approval'));
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
		if(!$jawaban || !$jawabanTeknis){
			echo "204";
			die;
		}
		$values = [];
		$check = [];
		$x = 0;

		foreach ($soal as $k => $v) {
			$ans = "a";
			$val = 0;
			foreach(range('a','e') as $i) {
				$ansRow = $this->find_answer($jawaban, $v->id_soal, $i);
				if($ansRow[0] == true) {
					$ans = $i;
					$val = $ansRow[1];
					break;
				}
			}
			$x++;
			$values[] = [
				'x' => "{$v->no}.",
				'soal' => "{$v->soal}",
				'jawaban' => number_format($val,0,",","."),
				'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}
		$html = '<div class="text-center h5">Faktor Umum</div><div class="table-responsive mb-2"><table class="table table-bordered mb-0 border"><thead class="text-center"><tr><th width="5%">No</th><th width="80%">Indikator & Kelas Interval</th><th width="15%">Skor</th></tr></thead><tbody>';
		foreach ($values as $k => $v) {
			$html .= <<<SMF
			<tr><td class="text-center">{$v['x']}</td><td><p class="m-0">{$v['soal']}</p>
			<p class="m-0">{$v['jawaban']}</p>
			</td><td class="text-center">{$v['skor']}</td></tr>
			SMF;
		}
		$html .= "</tbody></table></div>";

		$values = [];
		$check = [];
		foreach ($soalTeknis as $k => $v) {
			$ans = "a";
			$val = 0;
			foreach(range('a','e') as $i) {
				$ansRow = $this->find_answer($jawabanTeknis, $v->id_soal, $i);
				if($ansRow[0] == true) {
					$ans = $i;
					$val = $ansRow[1];
					break;
				}
			}
			$x++;
			$values[] = [
				'x' => "{$v->no}.",
				'soal' => "{$v->soal}",
				'jawaban' => number_format($val,0,",","."),
				'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}
		$html .= '<div class="text-center h5">Faktor Teknis</div><div class="table-responsive mb-2"><table class="table table-bordered mb-0 border"><thead class="text-center"><tr><th width="5%">No</th><th width="80%">Indikator & Kelas Interval</th><th width="15%">Skor</th></tr></thead><tbody>';
		foreach ($values as $k => $v) {
			$html .= <<<SMF
			<tr><td class="text-center">{$v['x']}</td><td><p class="m-0">{$v['soal']}</p>
			<p class="m-0">{$v['jawaban']}</p>
			</td><td class="text-center">{$v['skor']}</td></tr>
			SMF;
		}
		$html .= "</tbody></table></div>";

		$skor = '<div class="">';
		$title = "$tipeVarName $namaDaerah {$badanName}";
		$skorDataUmum = $this->mdb->getrowdatawhere("tb_skor", $whereJawaban);
		$skorDataTeknis = $this->mdb->getrowdatawhere("tb_skor", $whereJawabanTeknis);
		if($skorDataUmum && $skorDataTeknis){
			$perkalian = $this->mdb->getrowdatawhere("m_kategori_perkalian", ["id_kategori_perkalian" => $skorDataTeknis->id_kategori_perkalian]);
			$perkalianText = ($perkalian->kategori ? "Pengkalian Wilayah:  {$perkalian->perkalian} ({$perkalian->kategori})" : "");
			$skorUmum = $skorDataUmum ? $skorDataUmum->skor : 0;
			$skorTeknis = $skorDataTeknis->skor;
			if($skorUmum == 0){
				$skor .= <<<SMF
				<p>$title mempunyai Nilai Tipelogi -. dengan Nilai Skor : </p>
				<p class="m-0">1. Variable Umum: (Variable Umum belum diisi)</p>
				<p class="m-0">2. Variable Teknis: {$skorTeknis}</p>
				<p class="m-0">{$perkalianText}</p>
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
				<p>{$perkalianText}</p>
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
		$values = [];
		$check = [];

		foreach ($soal as $k => $v) {
			$ans = "a";
			$val = 0;
			foreach(range('a','e') as $i) {
				$ansRow = $this->find_answer($jawaban, $v->id_soal, $i);
				if($ansRow[0] == true) {
					$ans = $i;
					$val = $ansRow[1];
					break;
				}
			}
			$values[] = [
				'x' => "{$v->no}.",
				'soal' => "{$v->soal}",
				'jawaban' => number_format($val,0,",","."),
				'skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}

		$zvalues = [];
		$check = [];
		foreach ($soalTeknis as $k => $v) {
			$ans = "a";
			$val = 0;
			foreach(range('a','e') as $i) {
				$ansRow = $this->find_answer($jawabanTeknis, $v->id_soal, $i);
				if($ansRow[0] == true) {
					$ans = $i;
					$val = $ansRow[1];
					break;
				}
			}
			$zvalues[] = [
				'z' => "{$v->no}.",
				'z_soal' => "{$v->soal}",
				'z_jawaban' => number_format($val,0,",","."),
				'z_skor' => (($v->bobot / 100) * $v->{"skala_{$ans}"}),
			];
		}
		$title = "$tipeVarName $namaDaerah {$badanName}";
		$templateProcessor = new TemplateProcessor('templates/variable.docx');
		$skorDataUmum = $this->mdb->getrowdatawhere("tb_skor", $whereJawaban);
		$skorDataTeknis = $this->mdb->getrowdatawhere("tb_skor", $whereJawabanTeknis);
		if($skorDataUmum && $skorDataTeknis){
			$perkalian = $this->mdb->getrowdatawhere("m_kategori_perkalian", ["id_kategori_perkalian" => $skorDataTeknis->id_kategori_perkalian]);
			$perkalianText = $perkalian->perkalian . ($perkalian->kategori ? " ({$perkalian->kategori})" : "");
			$skorUmum = $skorDataUmum ? $skorDataUmum->skor : 0;
			$skorTeknis = $skorDataTeknis->skor;
			if($skorUmum == 0){
				$templateProcessor->setValue('skor_umum', "(Variable Umum belum diisi)");
				$templateProcessor->setValue('skor_teknis', $skorTeknis);
				$templateProcessor->setValue('pengkalian', $perkalianText);
				$templateProcessor->setValue('total_skor', "(Variable Umum belum diisi)");
				$templateProcessor->setValue('kategori', "-");
			}else{
				$templateProcessor->setValue('skor_umum', $skorUmum);
				$templateProcessor->setValue('skor_teknis', $skorTeknis);
				$templateProcessor->setValue('pengkalian', $perkalianText);
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

		thead tr th:nth-child(1),
		tbody tr td:nth-child(1) {
			width: 5%;
		}
		thead tr th:nth-child(2),
		tbody tr td:nth-child(2) {
			width: 80%;
		}
		thead tr th:nth-child(3),
		tbody tr td:nth-child(3) {
			width: 15%;
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
		$dompdf->stream(str_replace(" ", "_", $title.date("Ymdhis")), ['Attachment' => false]);
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
					return [true, $v->value];
				}
			}
		}
		return [false, 0];
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

			$namaBadanTemp = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $idBadan, "tahun" => $this->session->userdata('whs_tahun')]);
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
			}elseif($tipeVar == "sekdprd"){
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
			$val = 0;
			foreach(range('a','e') as $i) {
				$ansRow = $this->find_answer($jawaban, $v->id_soal, $i);
				if($ansRow[0] == true) {
					$ans = $i;
					$val = $ansRow[1];
					break;
				}
			}
			$x++;
			$values[] = [
				'x' => "{$v->no}.",
				'soal' => "{$v->soal}",
				'jawaban' => number_format($val,0,",","."),
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
				$perkalianText = $perkalian->perkalian . ($perkalian->kategori ? " ({$perkalian->kategori})" : "");

				$skorDataUmum = $this->mdb->getrowdatawhere("tb_skor", ["tipe_soal" => "umum", "tahun" => $this->session->userdata('whs_tahun'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'), "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "tipe_daerah" => $this->session->userdata('whs_role')]);
				$skorUmum = $skorDataUmum ? $skorDataUmum->skor : 0;
				$skorTeknis = $skorDataTeknis->skor;
				if($skorUmum == 0){
					$templateProcessor->setValue('skor_umum', "(Variable Umum belum diisi)");
					$templateProcessor->setValue('skor_teknis', $skorTeknis);
					$templateProcessor->setValue('pengkalian', $perkalianText);
					$templateProcessor->setValue('total_skor', "(Variable Umum belum diisi)");
					$templateProcessor->setValue('kategori', "-");
				}else{
					$templateProcessor->setValue('skor_umum', $skorUmum);
					$templateProcessor->setValue('skor_teknis', $skorTeknis);
					$templateProcessor->setValue('pengkalian', $perkalianText);
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

		thead tr th:nth-child(1),
		tbody tr td:nth-child(1) {
			width: 5%;
		}
		thead tr th:nth-child(2),
		tbody tr td:nth-child(2) {
			width: 80%;
		}
		thead tr th:nth-child(3),
		tbody tr td:nth-child(3) {
			width: 15%;
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
		$dompdf->stream(str_replace(" ", "_", $title.date("Ymdhis")), ['Attachment' => false]);
		unlink($tempDocx);
		die;
	}
}
