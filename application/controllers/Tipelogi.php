<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tipelogi extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
	}
	public function index()
	{
		$data["title"] = "Data Tipelogi";
		$data["provinsi"] = $this->mdb->getdatawhere("m_provinsi", []);
		$data["content"] = $this->load->view('v_tipelogi', $data, true);
		$this->load->view('v_public_header', $data);
	}
	public function load_provinsi()
	{
		$data = $this->mdb->getdatawhere("vw_provinsi_informasi");
		echo json_encode($data);
	}
	public function load_kabupaten()
	{
		$data = $this->mdb->getdatawhere("vw_kabupaten_informasi", ["kode_provinsi" => $_GET['id']]);
		$dataProv = $this->mdb->getrowdatawhere("m_provinsi", ["kode_provinsi" => $_GET['id']]);
		echo json_encode(["data_kabupaten" => $data, "data_provinsi" => $dataProv]);
	}
	public function cari_skor()
	{
		$data = [];
		$badan = [];
		if($_GET["daerah"] == "provinsi"){
			if($_GET['perangkat'] == "sekda" || $_GET['perangkat'] == "sekdprd" || $_GET['perangkat'] == "inspektorat"){
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "tipe_variable" => $_GET['perangkat']]);
			}elseif($_GET['perangkat'] == "badan" || $_GET['perangkat'] == "dinas"){
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "tipe_variable" => $_GET['perangkat'], "id_badan" => $_GET['subperangkat']]);
			}
		}else{
			if($_GET['perangkat'] == "sekda" || $_GET['perangkat'] == "sekdprd" || $_GET['perangkat'] == "inspektorat"){
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "kode_kabupaten" => $_GET["id_kab"], "tipe_variable" => $_GET['perangkat']]);
			}elseif($_GET['perangkat'] == "badan" || $_GET['perangkat'] == "dinas"){
				$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $_GET['subperangkat']]);
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "kode_kabupaten" => $_GET["id_kab"], "tipe_variable" => $_GET['perangkat'], "id_badan" => $_GET['subperangkat']]);
			}else{
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "kode_kabupaten" => $_GET["id_kab"], "tipe_variable" => $_GET['perangkat'], "id_badan" => $_GET['subperangkat']]);
			}
		}
		$skor = "Belum Mengisi!";
		if($data){
			$skorVal = (($data->variable_teknis * 0.8)+($data->variable_umum * 0.2)) * $data->perkalian;
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
		echo json_encode($skor);
	}
	public function get_badan()
	{
		if($_GET["type"] == "kecamatan"){
			$data = $this->mdb->getdatawhere("m_kecamatan", ["kode_kabupaten" => $_GET['kab']]);
			$output = [];
			$x=0;
			$output[$x] = [];
			$output[$x]["value"] = "";
			$output[$x]["label"] = "(Pilih Kecamatan)";
			$output[$x]["selected"] = true;
			foreach ($data as $k => $v) {
				$x++;
				$output[$x] = [];
				$output[$x]["value"] = $v->kode_kecamatan;
				$output[$x]["label"] = $v->nama_kecamatan;
				$output[$x]["selected"] = false;
			}
		}else{
			$data = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => $_GET['type'], "tipe_daerah" => $_GET['daerah']]);
			$output = [];
			$x=0;
			$output[$x] = [];
			$output[$x]["value"] = "";
			$output[$x]["label"] = "(Pilih ".ucwords($_GET['type']).")";
			$output[$x]["selected"] = true;
			foreach ($data as $k => $v) {
				$x++;
				$output[$x] = [];
				$output[$x]["value"] = $v->id_badan;
				if($v->parent <> ""){
					$output[$x]["label"] = $v->parent ." " .$v->nama_badan;

				}else{
					$output[$x]["label"] = $v->nama_badan;
				}
				$output[$x]["selected"] = false;
			}
		}
		
		echo json_encode($output);
	}
}
