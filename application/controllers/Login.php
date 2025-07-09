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
		$data["redirect"] = "";
		if(isset($_GET["r"])){
			if(str_contains(urldecode($_GET["r"]), base_url(''))){
				$data["redirect"] = preg_replace('/[^a-zA-Z0-9\/\:%-=?]/', '', urlencode($_GET["r"]));
			}else{
				redirect(site_url(''));
			}
		}
		$this->load->view('v_stylish_login', $data);
		// $this->load->view('v_login', $data);
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
	private function verify_recaptcha($token) {
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array(
			'secret' => $this->config->item("recaptcha_secret_key"),
			'response' => $token
		);

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		if ($result === FALSE) {
			// $this->session->set_flashdata('error', 'Failed to connect to reCAPTCHA API.');
			return ['success' => false, 'error-codes' => ['connection-error']];
		}

		return json_decode($result, true);
	}
	public function send_auth()
	{
		if($this->input->post("hushbot") <> "67b279476cef18ce0be52bb1f7945d46bdd9e0ba1698a893caf470e7409ad62f"){
			header("HTTP/1.0 404 Not Found");die;
		}
		$recaptcha_token = $this->input->post('g-recaptcha-response');

		$recaptcha_response = $this->verify_recaptcha($recaptcha_token);
		$recaptcha_verification = $recaptcha_response['success'] == true && $recaptcha_response['score'] >= $this->config->item("recaptcha_score_threshold") && $recaptcha_response['action'] == 'login';
		// if($recaptcha_verification == false){
		// 	log_message('error', 'reCAPTCHA verification failed: ' . json_encode($recaptcha_response));
		// 	$error_message = 'reCAPTCHA verification failed. Please try again.';
		// 	if (isset($recaptcha_response['error-codes'])) {
		// 		$error_message .= ' Error codes: ' . implode(', ', $recaptcha_response['error-codes']);
		// 	}
		// 	$this->session->set_flashdata('error', $error_message);
		// 	redirect(site_url(''));
		// }
		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_rules('tahun', 'Tahun', 'trim|required');
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
						'whs_tahun' => $this->input->post("tahun"),
						'whs_id_user' => $data->id,
						'whs_username' => $data->username,
						'whs_role' => $data->role,
						'whs_name' => $data->name,
						'whs_email' => $data->email,
						'whs_nip' => $data->nip,
						'whs_jabatan' => $data->jabatan,
						'whs_id_kl' => $data->id_kl,
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
					}elseif($data->role=="kl"){
						$datasess["whs_nama_provinsi"] = "";
						$datasess["whs_nama_kabupaten"] = "";
						$dataKl = $this->mdb->getrowdatawhere("m_kl", ["id_kl" => $data->id_kl]);
						$datasess["whs_nama_kl"] = $dataKl->nama_kl;
					}else{
						$datasess["whs_nama_provinsi"] = "";
						$datasess["whs_nama_kabupaten"] = "";
					}
					$data_tahun = $this->mdb->getrowdatawhere("m_tahun_pengisian", null, ["updated_date" => "desc"]);
					if($data_tahun){
						$datasess["whs_tahun_pengisian"] = $data_tahun->tahun;
					}else{
						$datasess["whs_tahun_pengisian"] = "";
					}
					$this->session->set_userdata($datasess);
					if($this->input->post("redirect")){
						if(str_contains(urldecode($this->input->post("redirect")), base_url(''))){
							redirect(preg_replace('/[^a-zA-Z0-9\/\:%-=?]/', '', urldecode($this->input->post("redirect"))));
						}else{
							redirect(site_url(''));
						}
					}else{
						if($data->role=="provinsi"||$data->role=="kabupaten"){
							redirect(site_url('informasi-data-umum'));
						}elseif($data->role=="kl"){
							redirect(site_url('approval'));
						}else{
							redirect(site_url('dashboard-analytic'));
						}
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
