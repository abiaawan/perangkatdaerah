<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$last = $this->uri->total_segments();
		$uri = $this->uri->segment($last);
		if(strtolower($uri ?? "") == "login" || strtolower($uri ?? "") == "send_auth" || strtolower($uri ?? "") == ""){
			if ($this->session->userdata('whs_logged')==true){
				redirect(site_url('dashboard'));
			}
		}
		date_default_timezone_set('Asia/Jakarta');
	}
	public function index()
	{
		$data["title"] = "Login";
		$this->load->view('v_login', $data);
	}
	// public function scrap_kecamatan()
	// {
	// 	$kab = $this->mdb->getdatawhere("m_kabupaten_bps", ["kode_kabupaten" => 1473]);
	// 	echo json_encode($kab);die;
	// 	foreach ($kab as $k => $v) {
	// 		$content = file_get_contents("https://sig.bps.go.id/rest-bridging/getwilayah?level=kecamatan&parent={$v->kode_kabupaten}&periode_merge=2024_1.2022");
	// 		$kec = json_decode($content);
	// 		foreach ($kec as $k2 => $v2) {
	// 			if((int)$v2->kode_bps <= 9408051){
	// 				continue;
	// 			}
	// 			$dataKec = array(
	// 				"kode_kecamatan" => $v2->kode_bps,
	// 				"nama_kecamatan" => $v2->nama_bps,
	// 				"kode_kabupaten" => $v->kode_kabupaten,
	// 				"removed" => 0
	// 			);
	// 			$this->mdb->postdata("m_kecamatan", $dataKec);
	// 		}	
	// 	}
	
	// }
	public function send_auth()
	{
		if($this->input->post("hushbot") <> "67b279476cef18ce0be52bb1f7945d46bdd9e0ba1698a893caf470e7409ad62f"){
			header("HTTP/1.0 404 Not Found");die;
		}
		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', "Invalid combination of username and password!");
			redirect(site_url(''));
		}
		$searchData = $this->mdb->getcountwhere("m_user", ["username" => $this->input->post("username")]);
		if($searchData[0] == 1){
			if($searchData[1][0]){
				$data = $searchData[1][0];
				if (password_verify($this->input->post("password"), $data->password)) {
					$datasess = array(
						'whs_logged' => TRUE,
						'whs_tahun' => 2024,
						'whs_id_user' => $data->id,
						'whs_username' => $data->username,
						'whs_role' => $data->role,
						'whs_name' => $data->name,
						'whs_email' => $data->email,
						'whs_nip' => $data->nip,
						'whs_jabatan' => $data->jabatan,
						'whs_kode_kabupaten' => $data->kode_kabupaten,
						'whs_kode_provinsi' => $data->kode_provinsi
					);
					if($data->role=="provinsi"){
						$dataGeo = $this->mdb->getrowdatawhere("m_provinsi", ["kode_provinsi" => $data->kode_provinsi]);
						$datasess["whs_nama_provinsi"] = $dataGeo->nama_provinsi;
						$datasess["whs_nama_kabupaten"] = "";
					}elseif($data->role=="kabupaten"){
						$dataGeo = $this->mdb->getrowdatawhere("m_provinsi", ["kode_provinsi" => $data->kode_provinsi]);
						$dataGeo2 = $this->mdb->getrowdatawhere("m_kabupaten", ["kode_kabupaten" => $data->kode_kabupaten]);
						$datasess["whs_nama_provinsi"] = $dataGeo->nama_provinsi;
						$datasess["whs_nama_kabupaten"] = $dataGeo2->nama_kabupaten;
					}else{
						$datasess["whs_nama_provinsi"] = "";
						$datasess["whs_nama_kabupaten"] = "";
					}
					$this->session->set_userdata($datasess);
					if($data->role=="provinsi"||$data->role=="kabupaten"){
						redirect(site_url('informasi-data-umum'));
					}else{
						redirect(site_url('dashboard-analytic'));
					}
					
				}else{
					$this->session->set_flashdata('error', "Invalid combination of username and password!");
					redirect(site_url(''));
				}
			}else{
				$this->session->set_flashdata('error', "Invalid combination of username and password!");
				redirect(site_url(''));
			}
		}else{
			$this->session->set_flashdata('error', "Invalid combination of username and password!");
			redirect(site_url(''));
		}
	}
	public function logout()
	{
		$this->session->sess_destroy();
		redirect(site_url(''));
	}
}
