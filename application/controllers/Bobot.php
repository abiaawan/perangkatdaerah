<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bobot extends CI_Controller {
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
		$data["title"] = "Bobot Variable";
		$data["data_bobot"] = $this->mdb->getrowdatawhere("m_pembagian_skor", null, ["updated_date" => "desc"]);
		$data["content"] = $this->load->view('v_bobot_variable', $data, true);
		$this->load->view('v_header', $data);
	}
	public function send_bobot()
	{
		$data = array(
			"variable_umum" => number_format($this->input->post("umum") / 100, 2, '.', ''),
			"variable_teknis" => number_format($this->input->post("teknis") / 100, 2, '.', ''),
			"updated_date" => date("Y-m-d H:i:s")
		);
		$this->mdb->postdata("m_pembagian_skor", $data);
		$this->session->set_flashdata('success', "Berhasil mengubah bobot variable!");
		redirect(site_url('bobot-variable'));

	}
}
