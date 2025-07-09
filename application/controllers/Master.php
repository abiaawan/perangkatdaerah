<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		if($this->session->userdata('whs_logged')==true){
			if($this->session->userdata('whs_role')=="superadmin"){
				
			}else{
				redirect(site_url(''));
			}
		}else{
			$this->session->set_flashdata('error', "Your session has expired!");
			redirect(site_url(''));
		}
	}
	public function index()
	{
		redirect(site_url('dashboard-analytic'));
	}
	public function dinas($year)
	{
		$data["title"] = "Master Dinas";
		$data["year"] = $year;
		$data["data"] = $this->mdb->getdatawhere("m_badan", ["tahun" => $year, "tipe_badan" => "dinas"]);
		$data["tipe_badan"] = "dinas";
		$data["content"] = $this->load->view('v_master_dinas', $data, true);
		$this->load->view('v_header', $data);
	}
	public function badan($year)
	{
		$data["title"] = "Master Badan";
		$data["year"] = $year;
		$data["data"] = $this->mdb->getdatawhere("m_badan", ["tahun" => $year, "tipe_badan" => "badan"]);
		$data["tipe_badan"] = "badan";
		$data["content"] = $this->load->view('v_master_dinas', $data, true);
		$this->load->view('v_header', $data);
	}
	public function load_badan()
	{
		$data = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $_GET['id']]);
		echo json_encode($data);
	}
	public function send_dinas()
	{
		$data = array(
			"tipe_badan" => $this->input->post("tipe_badan"),
			"kode_parent" => $this->input->post("kode_parent"),
			"parent" => $this->input->post("parent"),
			"kode_badan" => $this->input->post("kode_badan"),
			"nama_badan" => $this->input->post("nama_badan"),
			"tipe_penilaian" => $this->input->post("tipe_penilaian"),
			"tipe_daerah" => $this->input->post("tipe_daerah"),
			"tahun" => $this->input->post("tahun"),
			"updated_date" => date("Y-m-d H:i:s")
		);
		if($this->input->post("mode") == "add"){
			$this->mdb->postdata("m_badan", $data);
			$this->session->set_flashdata('success', "Berhasil menambah Dinas!");
			redirect(site_url('master/dinas/').$this->input->post("tahun"));
		}else{
			$this->mdb->putdatawhere("m_badan", ["id_badan" => $this->input->post("id_badan")], $data);
			$this->session->set_flashdata('success', "Berhasil mengedit Dinas!");
			redirect(site_url('master/dinas/').$this->input->post("tahun"));
		}
	}
	public function send_badan()
	{
		$data = array(
			"tipe_badan" => $this->input->post("tipe_badan"),
			"kode_parent" => $this->input->post("kode_parent"),
			"parent" => $this->input->post("parent"),
			"kode_badan" => $this->input->post("kode_badan"),
			"nama_badan" => $this->input->post("nama_badan"),
			"tipe_penilaian" => $this->input->post("tipe_penilaian"),
			"tipe_daerah" => $this->input->post("tipe_daerah"),
			"tahun" => $this->input->post("tahun"),
			"updated_date" => date("Y-m-d H:i:s")
		);
		if($this->input->post("mode") == "add"){
			$this->mdb->postdata("m_badan", $data);
			$this->session->set_flashdata('success', "Berhasil menambah Badan!");
			redirect(site_url('master/badan/').$this->input->post("tahun"));
		}else{
			$this->mdb->putdatawhere("m_badan", ["id_badan" => $this->input->post("id_badan")], $data);
			$this->session->set_flashdata('success', "Berhasil mengedit Badan!");
			redirect(site_url('master/badan/').$this->input->post("tahun"));
		}
	}
	public function delete_dinas($id)
	{
		$data = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $id]);
		if($data){
			$this->mdb->deletedata("m_badan", ["id_badan" => $id]);
			$this->session->set_flashdata('success', "Berhasil menghapus Dinas!");
			redirect(site_url('master/dinas/').$data->tahun);
		}
	}
	public function delete_badan($id)
	{
		$data = $this->mdb->getrowdatawhere("m_badan", ["id_badan" => $id]);
		if($data){
			$this->mdb->deletedata("m_badan", ["id_badan" => $id]);
			$this->session->set_flashdata('success', "Berhasil menghapus Badan!");
			redirect(site_url('master/badan/').$data->tahun);
		}
	}
	public function salin_dinas($year)
	{
		$data = $this->mdb->getdatawhere("m_badan", ["tahun" => ($year-1), "tipe_badan" => "dinas"]);
		$this->mdb->deletedata("m_badan", ["tahun" => $year, "tipe_badan" => "dinas"]);
		$batch = [];
		$x = 0;
		foreach ($data as $k => $v) {
			$batch[$x] = array(
				"tipe_badan" => $v->tipe_badan,
				"kode_parent" => $v->kode_parent,
				"parent" => $v->parent,
				"kode_badan" => $v->kode_badan,
				"nama_badan" => $v->nama_badan,
				"tipe_penilaian" => $v->tipe_penilaian,
				"tipe_daerah" => $v->tipe_daerah,
				"tahun" => $year,
				"updated_date" => date("Y-m-d H:i:s")
			);
			$x++;
		}
		$this->mdb->postdatabatch("m_badan", $batch);
		redirect(site_url('master/dinas/').$year);
	}
	public function salin_badan($year)
	{
		$data = $this->mdb->getdatawhere("m_badan", ["tahun" => ($year-1), "tipe_badan" => "badan"]);
		$this->mdb->deletedata("m_badan", ["tahun" => $year, "tipe_badan" => "badan"]);
		$batch = [];
		$x = 0;
		foreach ($data as $k => $v) {
			$batch[$x] = array(
				"tipe_badan" => $v->tipe_badan,
				"kode_parent" => $v->kode_parent,
				"parent" => $v->parent,
				"kode_badan" => $v->kode_badan,
				"nama_badan" => $v->nama_badan,
				"tipe_penilaian" => $v->tipe_penilaian,
				"tipe_daerah" => $v->tipe_daerah,
				"tahun" => $year,
				"updated_date" => date("Y-m-d H:i:s")
			);
			$x++;
		}
		$this->mdb->postdatabatch("m_badan", $batch);
		redirect(site_url('master/badan/').$year);
	}
	public function pembagian_soal($year)
	{
		$data["title"] = "Master Pembagian Soal";
		$data["year"] = $year;
		$data["data"] = $this->mdb->getdatawhere("vw_variable_soal_badan", ["tahun" => $year]);
		$data["content"] = $this->load->view('v_master_pembagian_soal', $data, true);
		$this->load->view('v_header', $data);
	}
	public function load_pembagian_soal()
	{
		$data = $this->mdb->getrowdatawhere("tb_variable_soal", ["id" => $_GET['id']]);
		echo json_encode($data);
	}
	public function send_pembagian_soal()
	{
		$data = array(
			"kode_soal" => $this->input->post("kode_soal"),
			"tipe_variable" => $this->input->post("tipe_variable"),
			"tipe_soal" => $this->input->post("tipe_soal"),
			"tipe_daerah" => $this->input->post("tipe_daerah"),
			"id_badan" => $this->input->post("id_badan"),
			"tahun" => $this->input->post("tahun"),
			"updated_date" => date("Y-m-d H:i:s")
		);
		if($this->input->post("mode") == "add"){
			$this->mdb->postdata("tb_variable_soal", $data);
			$this->session->set_flashdata('success', "Berhasil menambah pembagian soal!");
			redirect(site_url('master/pembagian_soal/').$this->input->post("tahun"));
		}else{
			$this->mdb->putdatawhere("tb_variable_soal", ["id" => $this->input->post("id")], $data);
			$this->session->set_flashdata('success', "Berhasil mengedit pembagian soal!");
			redirect(site_url('master/pembagian_soal/').$this->input->post("tahun"));
		}
	}
	private function search_data_dinas_lama($dataBaru, $dataLama, $id)
	{
		$namaLama = "";
		$daerahLama = "";
		$kodeLama = "";
		foreach ($dataLama as $k => $v){
			if($v->id_badan == $id){
				$namaLama = $v->nama_badan;
				$daerahLama = $v->tipe_daerah;
				$kodeLama = $v->kode_badan;
				break;
			}
		}
		foreach ($dataBaru as $k => $v){
			if($v->nama_badan == $namaLama && $v->tipe_daerah == $daerahLama && $v->kode_badan == $kodeLama){
				return $v->id_badan;
			}
		}
		return 0;
	}

	public function salin_pembagian_soal($year)
	{
		$data = $this->mdb->getdatawhere("tb_variable_soal", ["tahun" => ($year-1)]);
		$dataDinasBadan = $this->mdb->getdatawhere("m_badan", ["tahun" => $year]);
		if(!$dataDinasBadan){
			$this->session->set_flashdata('error', "Belum ada data Dinas dan Badan tahun $year!");
			redirect(site_url('master/pembagian_soal/').$year);
		}else{
			$dataDinasBadanLast = $this->mdb->getdatawhere("m_badan", ["tahun" => ($year-1)]);
			$this->mdb->deletedata("tb_variable_soal", ["tahun" => $year]);
			$batch = [];
			$x = 0;
			foreach ($data as $k => $v) {
				$batch[$x] = array(
					"kode_soal" => $v->kode_soal,
					"tipe_variable" => $v->tipe_variable,
					"tipe_soal" => $v->tipe_soal,
					"tipe_daerah" => $v->tipe_daerah,
					"id_badan" => $this->search_data_dinas_lama($dataDinasBadan,$dataDinasBadanLast,$v->id_badan),
					"tahun" => $year,
					"updated_date" => date("Y-m-d H:i:s")
				);
				$x++;
			}
			// echo json_encode($batch);die;
			$this->mdb->postdatabatch("tb_variable_soal", $batch);
			redirect(site_url('master/pembagian_soal/').$year);
		}
		
	}
	public function delete_pembagian_soal($id)
	{
		$data = $this->mdb->getrowdatawhere("tb_variable_soal", ["id" => $id]);
		if($data){
			$this->mdb->deletedata("tb_variable_soal", ["id" => $id]);
			$this->session->set_flashdata('success', "Berhasil menghapus soal!");
			redirect(site_url('master/pembagian_soal/').$data->tahun);
		}
	}
	public function soal($year)
	{
		$data["title"] = "Master Soal";
		$data["year"] = $year;
		$data["data"] = $this->mdb->getdatawhere("m_soal", ["tahun" => $year]);
		$data["content"] = $this->load->view('v_master_soal', $data, true);
		$this->load->view('v_header', $data);
	}
	public function delete_soal($id)
	{
		$data = $this->mdb->getrowdatawhere("m_soal", ["id_soal" => $id]);
		if($data){
			$this->mdb->deletedata("m_soal", ["id_soal" => $id]);
			$this->session->set_flashdata('success', "Berhasil menghapus soal!");
			redirect(site_url('master/soal/').$data->tahun);
		}
	}
	public function load_soal()
	{
		$data = $this->mdb->getrowdatawhere("m_soal", ["id_soal" => $_GET['id']]);
		echo json_encode($data);
	}
	public function send_soal()
	{
		$data = array(
			"kode_soal" => $this->input->post("kode_soal"),
			"tipe_soal" => $this->input->post("tipe_soal"),
			"tipe_daerah" => $this->input->post("tipe_daerah"),
			"no" => $this->input->post("no"),
			"soal" => $this->input->post("soal"),
			"jawaban_a" => $this->input->post("jawaban_a"),
			"jawaban_b" => $this->input->post("jawaban_b")."-".$this->input->post("jawaban_b2"),
			"jawaban_c" => $this->input->post("jawaban_c")."-".$this->input->post("jawaban_c2"),
			"jawaban_d" => $this->input->post("jawaban_d")."-".$this->input->post("jawaban_d2"),
			"jawaban_e" => $this->input->post("jawaban_e"),
			"bobot" => $this->input->post("bobot"),
			"skala_a" => $this->input->post("skala_a"),
			"skala_b" => $this->input->post("skala_b"),
			"skala_c" => $this->input->post("skala_c"),
			"skala_d" => $this->input->post("skala_d"),
			"skala_e" => $this->input->post("skala_e"),
			"tahun" => $this->input->post("tahun"),
			"updated_date" => date("Y-m-d H:i:s")
		);
		if($this->input->post("mode") == "add"){
			$this->mdb->postdata("m_soal", $data);
			$this->session->set_flashdata('success', "Berhasil menambah soal!");
			redirect(site_url('master/soal/').$this->input->post("tahun"));
		}else{
			$this->mdb->putdatawhere("m_soal", ["id_soal" => $this->input->post("id_soal")], $data);
			$this->session->set_flashdata('success', "Berhasil mengedit soal!");
			redirect(site_url('master/soal/').$this->input->post("tahun"));
		}
	}
	public function salin_soal($year)
	{
		$data = $this->mdb->getdatawhere("m_soal", ["tahun" => ($year-1)]);
		$this->mdb->deletedata("m_soal", ["tahun" => $year]);
		$batch = [];
		$x = 0;
		foreach ($data as $k => $v) {
			$batch[$x] = array(
				"kode_soal" => $v->kode_soal,
				"tipe_soal" => $v->tipe_soal,
				"tipe_daerah" => $v->tipe_daerah,
				"no" => $v->no,
				"soal" => $v->soal,
				"jawaban_a" => $v->jawaban_a,
				"jawaban_b" => $v->jawaban_b,
				"jawaban_c" => $v->jawaban_c,
				"jawaban_d" => $v->jawaban_d,
				"jawaban_e" => $v->jawaban_e,
				"bobot" => $v->bobot,
				"skala_a" => $v->skala_a,
				"skala_b" => $v->skala_b,
				"skala_c" => $v->skala_c,
				"skala_d" => $v->skala_d,
				"skala_e" => $v->skala_e,
				"tahun" => $year,
				"updated_date" => date("Y-m-d H:i:s")
			);
			$x++;
		}
		$this->mdb->postdatabatch("m_soal", $batch);
		redirect(site_url('master/soal/').$year);
	}
}
