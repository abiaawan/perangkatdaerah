<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Informasi extends CI_Controller {
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
		$data["title"] = "Informasi Data Umum";
		$data["form"] = [];
		$data["form"]["penduduk"] = "";
		$data["form"]["kepadatan"] = "";
		$data["form"]["luas"] = "";
		$data["form"]["apbd"] = "";
		if($this->session->userdata('whs_role') == "provinsi"){
			$data["tipe_daerah"] = "provinsi";
		}elseif($this->session->userdata('whs_role') == "kabupaten"){
			$data["tipe_daerah"] = "kabupaten";
		}else{
			die;
		}
		$search = $this->mdb->getrowdatawhere("tb_informasi_tematik", ["tipe_daerah" => $data["tipe_daerah"], "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		if($search){
			$data["form"]["penduduk"] = $search->penduduk;
			$data["form"]["kepadatan"] = $search->kepadatan;
			$data["form"]["luas"] = $search->luas;
			$data["form"]["apbd"] = $search->apbd;
		}
		$data["content"] = $this->load->view('v_informasi', $data, true);
		$this->load->view('v_header', $data);
	}
	public function send()
	{
		$data = array(
			"penduduk" => $this->input->post("penduduk"),
			"kode_provinsi" => $this->session->userdata('whs_kode_provinsi'),
			"kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten'),
			"kepadatan" => round($this->input->post("penduduk")/$this->input->post("luas"), 1),
			"luas" => $this->input->post("luas"),
			"apbd" => $this->input->post("apbd"),
			"updated_date" => date("Y-m-d H:i:s")
		);
		if($this->session->userdata('whs_role') == "provinsi"){
			$data["tipe_daerah"] = "provinsi";
		}elseif($this->session->userdata('whs_role') == "kabupaten"){
			$data["tipe_daerah"] = "kabupaten";
		}else{
			die;
		}
		$search = $this->mdb->getrowdatawhere("tb_informasi_tematik", ["tipe_daerah" => $data["tipe_daerah"], "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')]);
		if($search){
			$this->mdb->putdatawhere("tb_informasi_tematik", ["tipe_daerah" => $data["tipe_daerah"], "kode_provinsi" => $this->session->userdata('whs_kode_provinsi'), "kode_kabupaten" => $this->session->userdata('whs_kode_kabupaten')], $data);
		}else{
			$this->mdb->postdata("tb_informasi_tematik", $data);
		}
		
		$this->session->set_flashdata('success', "Informasi data tematik berhasil disimpan!");
		redirect(site_url('informasi-data-umum'));
	}
}
