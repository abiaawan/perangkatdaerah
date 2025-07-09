<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;
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
		$data = $this->mdb->getdatawhere("vw_provinsi_informasi", ["tahun" => $_GET['tahun']], [["tahun" => null]]);
		echo json_encode($data);
	}
	public function load_kabupaten()
	{
		$data = $this->mdb->getdatawhere("vw_kabupaten_informasi", ["kode_provinsi" => $_GET['id'], "tahun" => $_GET['tahun']], [["kode_provinsi" => $_GET['id'],"tahun" => null]]);
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
				$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $_GET['subperangkat'], "tahun" => $_GET['tahun']]);
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "tipe_variable" => $_GET['perangkat'], "id_badan" => $_GET['subperangkat']]);
			}
			$dataUmum = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov']]);
		}else{
			if($_GET['perangkat'] == "sekda" || $_GET['perangkat'] == "sekdprd" || $_GET['perangkat'] == "inspektorat"){
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "kode_kabupaten" => $_GET["id_kab"], "tipe_variable" => $_GET['perangkat']]);
			}elseif($_GET['perangkat'] == "badan" || $_GET['perangkat'] == "dinas"){
				$badan = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $_GET['subperangkat'], "tahun" => $_GET['tahun']]);
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "kode_kabupaten" => $_GET["id_kab"], "tipe_variable" => $_GET['perangkat'], "id_badan" => $_GET['subperangkat']]);
			}else{
				$data = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "kode_kabupaten" => $_GET["id_kab"], "tipe_variable" => $_GET['perangkat'], "id_badan" => $_GET['subperangkat']]);
			}
			$dataUmum = $this->mdb->getrowdatawhere("vw_skor_perkalian", ["tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun'], "kode_provinsi" => $_GET['id_prov'], "kode_kabupaten" => $_GET["id_kab"]]);
		}
		$skor = "Skor Belum Tersedia";
		$detail = "";
		if($data && $dataUmum){
			$skorVal = (($data->skor * 0.8)+($dataUmum->skor * 0.2)) * $dataUmum->perkalian;
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

			$detail = <<<SMF
			<button type="button" data-role="{$_GET["daerah"]}" data-var="{$_GET['perangkat']}" data-tahun="{$_GET['tahun']}" data-prov="{$_GET['id_prov']}" data-kab="{$_GET["id_kab"]}" data-badan="{$_GET['subperangkat']}" class="btn btn-primary me-1 mb-1 view-skor-btn"><i class="bi bi-eye"></i> Lihat Detail</button>
			<button type="button" data-role="{$_GET["daerah"]}" data-var="{$_GET['perangkat']}" data-tahun="{$_GET['tahun']}" data-prov="{$_GET['id_prov']}" data-kab="{$_GET["id_kab"]}" data-badan="{$_GET['subperangkat']}" class="btn btn-primary me-1 mb-1 download-skor-btn"><i class="bi bi-download"></i> Unduh</button>
			SMF;
		}
		if($badan){
			if($badan->tipe_penilaian == "terisi"){
				$skor = ">800 (tipe A)";
			}
		}
		$output = [0,1];
		$output[0] = $skor;

		$output[1] = $detail;
		echo json_encode($output);
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
			$data = $this->mdb->getdatawhere("m_badan", ["tipe_badan" => $_GET['type'], "tipe_daerah" => $_GET['daerah'], "tahun" => $_GET['tahun']]);
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

		$values = [];
		$check = [];
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
		$html .= '<div class="text-center h5">Faktor Teknis</div><div class="table-responsive mb-2"><table class="table table-bordered mb-0 border"><thead class="text-center"><tr><th width="5%">No</th><th width="70%">Indikator & Kelas Interval</th><th width="10%">Skor</th><th>Lampiran</th></tr></thead><tbody>';
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

		$skor = '<div class="">';
		$title = "$tipeVarName $namaDaerah {$badanName}";
		$skorDataUmum = $this->mdb->getrowdatawhere("tb_skor", $whereJawaban);
		$skorDataTeknis = $this->mdb->getrowdatawhere("tb_skor", $whereJawabanTeknis);
		if($skorDataUmum && $skorDataTeknis){
			$perkalian = $this->mdb->getrowdatawhere("m_kategori_perkalian", ["id_kategori_perkalian" => $skorDataTeknis->id_kategori_perkalian]);
			$perkalianText = ($perkalian->kategori ? "Pengkalian Wilayah: {$perkalian->perkalian} ({$perkalian->kategori})" : "");
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
	public function download_pdf()
	{
		$tipeVar = $_GET["tipe_var"];
		$idBadan = $_GET["id_badan"];
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
		$dompdf->stream(str_replace(" ", "_", $title.date("Ymdhis")), ['Attachment' => false]);
		unlink($tempDocx);
		die;
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
}
