<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
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
		$data["title"] = "User";
		$data["users"] = $this->mdb->getdatawhere("vw_user_nama_daerah", ["role !=" => "superadmin"]);
		$data["provinsi"] = $this->mdb->getdatawhere("m_provinsi");
		$data["content"] = $this->load->view('v_user', $data, true);
		$this->load->view('v_header', $data);
	}
	public function load_kabupaten()
	{
		$data = $this->mdb->getdatawhere("m_kabupaten", ["kode_provinsi" => $_GET['id']]);
		$x=0;
		$output[$x] = [];
		$output[$x]["value"] = "";
		$output[$x]["label"] = "(Pilih Kabupaten)";
		$output[$x]["selected"] = true;
		foreach ($data as $k => $v) {
			$x++;
			$output[$x] = [];
			$output[$x]["value"] = $v->kode_kabupaten;
			$output[$x]["label"] = $v->nama_kabupaten;
			$output[$x]["selected"] = false;
		}
		echo json_encode($output);
	}
	public function load_user()
	{
		$data = $this->mdb->getrowdatawhereselect("m_user", "id, name, username, email, role, kode_provinsi, kode_kabupaten, nip, jabatan", ["id" => $_GET['id']]);
		echo json_encode($data);
	}
	public function delete($id)
	{
		$this->mdb->deletedata("m_user", ["id" => $id]);
		$this->session->set_flashdata('success', "Berhasil menghapus user!");
		redirect(site_url('user'));
	}
	public function send()
	{
		$data = array(
			"name" => $this->input->post("name"),
			"email" => $this->input->post("email"),
			"nip" => $this->input->post("nip"),
			"jabatan" => $this->input->post("jabatan"),
			"updated_by" => $this->session->userdata('whs_id_user'),
			"updated_date" => date("Y-m-d H:i:s")
		);
		$data["role"] = $this->input->post("role");
		$data["kode_kabupaten"] = $this->input->post("kabupaten");
		$data["kode_provinsi"] = $this->input->post("provinsi");
		$data["username"] = $this->input->post("username");

		if($this->input->post("mode") == "add"){
			$this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[m_user.username]');
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', "Username telah terdaftar!");
				redirect(site_url('user'));
			}
			$options = [
				'cost' => 13,
			];
			$data["password"] = password_hash($this->input->post("password"), PASSWORD_BCRYPT, $options);

			$this->mdb->postdata("m_user", $data);
			$this->session->set_flashdata('success', "Berhasil menambah user!");
			redirect(site_url('user'));
		}else{
			if($this->input->post("password") <> ""){
				$data["password"] = password_hash($this->input->post("password"), PASSWORD_BCRYPT, $options);
			}
			$this->mdb->putdatawhere("m_user", ["id" => $this->input->post("id")], $data);
			$this->session->set_flashdata('success', "Berhasil mengedit user!");
			redirect(site_url('user'));
		}
	}
}
