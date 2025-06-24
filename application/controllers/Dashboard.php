<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
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
		$data["title"] = "Dashboard Analytic";
		$data["provinsi"] = $this->mdb->getdatawhere("m_provinsi");
		$data["content"] = $this->load->view('v_dashboard', $data, true);
		$this->load->view('v_header', $data);
	}
	public function load_kabupaten()
	{
		if(!isset($_GET['id'])){
			echo json_encode([]);die;
		}
		if(is_array($_GET['id'])){
			$id = $_GET['id'];
		}else{
			die;
		}
		$data = $this->mdb->getdatawhere("vw_kabupaten", null, null, null, ["kode_provinsi", $id]);
		$x=-1;
		$z=0;
		$kdProv = 0;
		foreach ($data as $k => $v) {
			if($v->kode_provinsi <> $kdProv){
				$x++;
				$kdProv = $v->kode_provinsi;
				$output[$x] = [];
				$output[$x]["label"] = $v->nama_provinsi;
				$output[$x]["choices"] = [];
				$z=0;
			}
			$output[$x]["choices"][$z] = [];
			$output[$x]["choices"][$z]["value"] = $v->kode_kabupaten;
			$output[$x]["choices"][$z]["label"] = $v->nama_kabupaten;
			$output[$x]["choices"][$z]["selected"] = false;
			$z++;
		}
		echo json_encode($output);
	}
	public function load_perangkat()
	{
		$role = $_GET["role"];
		if($role == "kabupaten"){
			$kode_kabupaten = $_GET["kabupaten"];
		}
		

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
		$x=0;
		$baseCreated = false;
		$y=0;
		foreach ($arr as $k => $v) {
			if($k == "A" || $k == "B" || $k == "C"){
				if($baseCreated == false){
					$output[$x] = [];
					$output[$x]["label"] = "";
					$output[$x]["choices"] = [];
					$y=0;
					$baseCreated = true;
				}
				$output[$x]["choices"][$y] = [];
				$output[$x]["choices"][$y]["label"] = $v[1];
				$output[$x]["choices"][$y]["value"] = $v[0]."-0";
				$output[$x]["choices"][$y]["selected"] = false;
				$y++;
			}
			if($k == "D" || $k == "E"){
				$x++;
				$output[$x] = [];
				$output[$x]["label"] = $v[1];
				$output[$x]["choices"] = [];
				$z=0;
				$dataBadan = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => $v[0], "tipe_daerah" => $role, "tahun" => $_GET["tahun"]]);
				foreach ($dataBadan as $ka => $va) {
					$output[$x]["choices"][$z] = [];
					$output[$x]["choices"][$z]["value"] = $v[0]."-".$va->id_badan;
					$naming = $va->kode_badan .". ". $va->nama_badan;
					if($va->parent <> ""){
						$naming = $va->kode_badan .". ". $va->parent . " " . $va->nama_badan;
					}
					$output[$x]["choices"][$z]["label"] = $naming;
					$output[$x]["choices"][$z]["selected"] = false;
					$z++;
				}
			}
			if($k == "F"){
				$x++;
				$output[$x] = [];
				$output[$x]["label"] = $v[1];
				$output[$x]["choices"] = [];
				$z=0;
				$dataBadan = $this->mdb->getdatawhere("m_kecamatan", null, null, null, ["kode_kabupaten", $kode_kabupaten]);
				foreach ($dataBadan as $ka => $va) {
					$output[$x]["choices"][$z] = [];
					$output[$x]["choices"][$z]["value"] = $v[0]."-".$va->kode_kecamatan;
					$output[$x]["choices"][$z]["label"] = $va->nama_kecamatan;
					$output[$x]["choices"][$z]["selected"] = false;
					$z++;
				}
			}
		}
		echo json_encode($output);
	}
	public function load_chart()
	{
		$role = $_GET["role"];
		$tahun = $_GET["tahun"];
		$var = $_GET["var"];
		$kode_provinsi = $_GET["provinsi"];
		if($role == "kabupaten"){
			$kode_kabupaten = $_GET["kabupaten"];
			$kode_text = "kode_kabupaten";
			$nama_text = "nama_kabupaten";
			$kode_arr = $kode_kabupaten;
			$dataDaerah = $this->mdb->getdatawhere("m_kabupaten", null, null, null, ["kode_kabupaten", $kode_kabupaten]);
		}else{
			$kode_text = "kode_provinsi";
			$nama_text = "nama_provinsi";
			$kode_arr = $kode_provinsi;
			$dataDaerah = $this->mdb->getdatawhere("m_provinsi", null, null, null, ["kode_provinsi", $kode_provinsi]);
		}
		$arr = array(
			"sekda" => "Sekretariat Daerah",
			"sekdprd" => "Sekretariat DPRD",
			"inspektorat" => "Inspektorat"
		);
		$output = [];
		$output["series"] = [];
		$output["categories"] = [];
		$x = 0;
		foreach ($dataDaerah as $ka => $va) {
			$output["series"][$va->{$nama_text}] = [];
			foreach ($var as $k => $v) {
				$terisi = false;
				$kode = explode("-", $v);
				$perangkat = $kode[0];
				$id = $kode[1];
				$whereU = ["tipe_daerah" => $role, $kode_text => $va->{$kode_text}, "tipe_soal" => "umum"];
				$whereT = ["tipe_daerah" => $role, "tipe_variable" => $perangkat, $kode_text => $va->{$kode_text}, "tipe_soal" => "teknis"];
				if($perangkat == "dinas" || $perangkat == "badan"){
					$dataBadan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $id, "tahun" => $tahun]);
					if($dataBadan->tipe_penilaian == "terisi"){
						$terisi = true;
					}
					$whereT["id_badan"] = $id;
				}
				if($perangkat == "kecamatan"){
					$whereT["kode_kecamatan"] = $id;
				}
				$dataUmum = $this->mdb->getrowdatawhere("vw_skor_perkalian", $whereU);
				$dataTeknis = $this->mdb->getrowdatawhere("vw_skor_perkalian", $whereT);
				$skor = 0;
				if($terisi == false){
					if($dataUmum && $dataTeknis){
						$skor = (($dataTeknis->skor * 0.8)+($dataUmum->skor * 0.2)) * $dataUmum->perkalian;
					}
				}else{
					$skor = 1000;
				}
				$output["series"][$va->{$nama_text}][] = round($skor,1);
				$x++;
			}
		}
		foreach ($var as $k => $v) {
			$kode = explode("-", $v);
			$perangkat = $kode[0];
			$id = $kode[1];
			if($perangkat == "sekda" || $perangkat == "sekdprd" || $perangkat == "inspektorat"){
				$output["categories"][] = $arr[$perangkat];
			}
			if($perangkat == "dinas" || $perangkat == "badan"){
				$dataBadan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $id, "tahun" => $tahun]);
				$naming = $dataBadan->nama_badan;
				if($dataBadan->parent <> ""){
					$naming = $dataBadan->parent . " " . $dataBadan->nama_badan;
				}
				if($dataBadan->tipe_penilaian == "terisi"){
					$terisi = true;
				}
				$output["categories"][] = $naming;
			}
			if($perangkat == "kecamatan"){
				$dataBadan = $this->mdb->getrowdatawhere("m_kecamatan", ["kode_kecamatan" => $id]);
				$output["categories"][] = $dataBadan->nama_kecamatan;
			}
		}
		echo json_encode($output);die;
	}
	public function load_pie()
	{
		$role = $_GET["role"];
		$tahun = $_GET["tahun"];
		$var = $_GET["var"];
		$badan = $_GET["badan"];
		$kode_provinsi = $_GET["provinsi"];
		if($role == "kabupaten"){
			$nama_text = "nama_kabupaten";
			$tb_name = "m_kabupaten";
		}else{
			$nama_text = "nama_provinsi";
			$tb_name = "m_provinsi";
		}
		$baseWhere = array(
			"tipe_daerah" => $role,
			"tahun" => $tahun,
			"tipe_variable" => $var,
		);
		if($var == "dinas" || $var == "badan"){
			$baseWhere["id_badan"] = $badan;
		}
		if($var == "kecamatan"){
			$baseWhere["kode_kecamatan"] = $badan;
		}
		if($role == "kabupaten"){
			$baseWhere["kode_provinsi"] = $kode_provinsi;
		}
		$output = [];
		$all1000 = false;
		if($var == "badan"){
			$dataBadan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $badan, "tahun" => $tahun]);
			if($dataBadan){
				if($dataBadan->tipe_penilaian == "terisi"){
					$all1000 = true;
				}
			}
		}
		if($all1000 == false){
			$where = $baseWhere;
			$data = $this->mdb->getdatawhere("vw_total_skor", $where);
			$counta = 0;
			$countb = 0;
			$countc = 0;
			$countd = 0;
			$counte = 0;
			foreach ($data as $ka => $va) {
				if(round($va->total_skor,1) > 800){
					$counta++;
				}elseif(round($va->total_skor,1) > 600){
					$countb++;
				}elseif(round($va->total_skor,1) > 400){
					$countc++;
				}elseif(round($va->total_skor,1) > 300){
					$countd++;
				}else{
					$counte++;
				}
			}
			$output["value"][0] = $counta;
			$output["text"][0] = "Tipe A";
			$output["value"][1] = $countb;
			$output["text"][1] = "Tipe B";
			$output["value"][2] = $countc;
			$output["text"][2] = "Tipe C";
			$output["value"][3] = $countd;
			$output["text"][3] = "Setingkat Bidang";
			$output["value"][4] = $counte;
			$output["text"][4] = "Setingkat Seksi/Subbidang";
		}else{
			$where2 = [];
			if($role == "kabupaten"){
				if($kode_provinsi){
					$where2["kode_provinsi"] = $kode_provinsi;
				}
			}
			$data = $this->mdb->getdatawhere($tb_name, $where2);
			$output["value"][0] = count($data);
			$output["text"][0] = "Tipe A";
			$output["value"][1] = 0;
			$output["text"][1] = "Tipe B";
			$output["value"][2] = 0;
			$output["text"][2] = "Tipe C";
			$output["value"][3] = 0;
			$output["text"][3] = "Setingkat Bidang";
			$output["value"][4] = 0;
			$output["text"][4] = "Setingkat Seksi/Subbidang";
		}

		echo json_encode($output);
	}
	public function load_pie_table()
	{
		$role = $_GET["role"];
		$skor = $_GET["skor"];
		$tahun = $_GET["tahun"];
		$var = $_GET["var"];
		$badan = $_GET["badan"];
		$kode_provinsi = $_GET["provinsi"];
		if($role == "kabupaten"){
			$nama_text = "nama_kabupaten";
			$tb_name = "m_kabupaten";
		}else{
			$nama_text = "nama_provinsi";
			$tb_name = "m_provinsi";
		}
		$baseWhere = array(
			"tipe_daerah" => $role,
			"tahun" => $tahun,
			"tipe_variable" => $var,
		);
		if($var == "dinas" || $var == "badan"){
			$baseWhere["id_badan"] = $badan;
		}
		if($var == "kecamatan"){
			$baseWhere["kode_kecamatan"] = $badan;
		}
		if($role == "kabupaten"){
			$baseWhere["kode_provinsi"] = $kode_provinsi;
		}
		$output = [];
		$all1000 = false;
		if($var == "badan"){
			$dataBadan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $badan, "tahun" => $tahun]);
			if($dataBadan){
				if($dataBadan->tipe_penilaian == "terisi"){
					$all1000 = true;
				}
			}
		}
		if($all1000 == false){
			$where = $baseWhere;
			if($skor == "Tipe A"){
				$where["total_skor >"] = 800;
			}elseif($skor == "Tipe B"){
				$where["total_skor >"] = 600;
				$where["total_skor <"] = 801;
			}elseif($skor == "Tipe C"){
				$where["total_skor >"] = 400;
				$where["total_skor <"] = 601;
			}elseif($skor == "Setingkat Bidang"){
				$where["total_skor >"] = 300;
				$where["total_skor <"] = 401;
			}elseif($skor == "Setingkat Seksi/Subbidang"){
				$where["total_skor <"] = 301;
			}else{
				die;
			}
			$data = $this->mdb->getdatawhere("vw_total_skor", $where);
			foreach ($data as $ka => $va) {
				$output[$va->{$nama_text}] = round($va->total_skor,1);
			}
		}else{
			if($skor == "Tipe A"){
				$where2 = [];
				if($role == "kabupaten"){
					if($kode_provinsi){
						$where2["kode_provinsi"] = $kode_provinsi;
					}
				}
				$data = $this->mdb->getdatawhere($tb_name, $where2);
				foreach ($data as $ka => $va) {
					$output[$va->{$nama_text}] = 1000;
				}
			}else{
				$output = [];
			}
		}
		ksort($output);
		echo json_encode($output);
	}

}
