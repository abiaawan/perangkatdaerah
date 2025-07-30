<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		if($this->session->userdata('whs_logged')==true){
		}else{
			$this->session->set_flashdata('error', "Your session has expired!");
			redirect(site_url(''));
		}
		session_write_close();
	}
	public function index()
	{
		
		$data["title"] = "Semua Notifikasi";
		$userId = $this->session->userdata("whs_id_user");
		$notifData = $this->mdb->getdatawhere(
			"tb_notification",
			["id_receiver" => $userId],
			null,
			["id_notification", "desc"],
			null,
			[10, 0]
		);
		$output = [];
		foreach ($notifData as $k => $v) {
			$output[$k]["id"] = $v->id_notification;
			$output[$k]["body"] = $this->get_notif($v,2);
			$output[$k]["title"] = $v->type;
			$output[$k]["icon"] = $this->get_notif($v,3);
			$output[$k]["color"] = $this->get_notif($v,4);
			$output[$k]["date"] = $v->updated_date;
		}
		$data["notif"] = json_decode(json_encode($output));
		$this->mdb->putdatawhere("tb_notification", ["id_receiver" => $this->session->userdata("whs_id_user"), "read" => 0], ["read" => 1]);
		$data["content"] = $this->load->view('v_notification', $data, true);
		$this->load->view('v_header', $data);
	}
	public function get_more($len)
	{
		$userId = $this->session->userdata("whs_id_user");
		session_write_close();
		$notifData = $this->mdb->getdatawhere(
			"tb_notification",
			["id_receiver" => $userId],
			null,
			["id_notification", "desc"],
			null,
			[10, $len*10]
		);
		$output = [];
		foreach ($notifData as $k => $v) {
			$output[$k]["id"] = $v->id_notification;
			$output[$k]["body"] = $this->get_notif($v,2);
			$output[$k]["title"] = $v->type;
			$output[$k]["icon"] = $this->get_notif($v,3);
			$output[$k]["color"] = $this->get_notif($v,4);
			$output[$k]["date"] = $v->updated_date;
		}
		header('Content-Type: application/json');
		echo json_encode($output);
	}
	// public function get()
	// {
	// 	$lastId = (int) $this->input->get('since_id', TRUE) ?: 0;
	// 	$userId = $this->session->userdata("whs_id_user");
	// 	session_write_close();
	// 	$output = [];
	// 	if ($lastId == 0) {
	// 		$notifData = $this->mdb->getdatawhere(
	// 			"tb_notification",
	// 			["id_receiver" => $userId, "read" => 0],
	// 			null,
	// 			["id_notification", "asc"]
	// 		);
	// 	} else {
	// 		set_time_limit(30);
	// 		$startTime = time();
	// 		$timeout = 28;
	// 		$notifData = [];

	// 		while (time() - $startTime < $timeout) {
	// 			$newNotifs = $this->mdb->getdatawhere(
	// 				"tb_notification",
	// 				[
	// 					"id_receiver" => $userId,
	// 					"read" => 0,
	// 					"id_notification >" => $lastId
	// 				],
	// 				null,
	// 				["id_notification", "asc"]
	// 			);

	// 			if (!empty($newNotifs)) {
	// 				$notifData = $newNotifs;
	// 				break;
	// 			}
	// 			sleep(2);
	// 		}
	// 	}
	// 	foreach ($notifData as $k => $v) {
	// 		$output[$k]["id"] = $v->id_notification;
	// 		$output[$k]["body"] = $this->get_notif($v,2);
	// 		$output[$k]["title"] = $v->type;
	// 		$output[$k]["icon"] = $this->get_notif($v,3);
	// 		$output[$k]["color"] = $this->get_notif($v,4);
	// 		$output[$k]["date"] = $v->updated_date;
	// 	}
	// 	header('Content-Type: application/json');
	// 	echo json_encode($output);
	// }
	public function get()
	{
		$lastId = (int) $this->input->get('since_id', TRUE) ?: 0;
		$userId = $this->session->userdata("whs_id_user");
		session_write_close();
		$output = [];
		$notifData = [];
		if ($lastId == 0) {
			$notifData = $this->mdb->getdatawhere(
				"tb_notification",
				["id_receiver" => $userId, "read" => 0],
				null,
				["id_notification", "asc"]
			);
		} else {
			$newNotifs = $this->mdb->getdatawhere(
				"tb_notification",
				[
					"id_receiver" => $userId,
					"read" => 0,
					"id_notification >" => $lastId
				],
				null,
				["id_notification", "asc"]
			);

			if (!empty($newNotifs)) {
				$notifData = $newNotifs;
			}
		}
		foreach ($notifData as $k => $v) {
			$output[$k]["id"] = $v->id_notification;
			$output[$k]["body"] = $this->get_notif($v,2);
			$output[$k]["title"] = $v->type;
			$output[$k]["icon"] = $this->get_notif($v,3);
			$output[$k]["color"] = $this->get_notif($v,4);
			$output[$k]["date"] = $v->updated_date;
		}
		header('Content-Type: application/json');
		echo json_encode($output);
	}
	private function get_notif($data, $type){
		$url = "#";
		$body = "";
		$icon = "bi-info-circle";
		$color = "bg-secondary";
		switch ($data->type) {
			case 'Permintaan Approval':
			$url = "approval/?&d={$data->var3}&p={$data->var4}&k={$data->var5}";
			$body = "{$data->var1} melakukan submit data perangkat daerah {$data->var2} dan membutuhkan approval.";
			$icon = "bi-file-earmark-text";
			$color = "bg-warning";
			break;
			case 'Approved Permintaan Approval':
			$url = "variable/{$data->var3}";
			$body = "{$data->var1} meng-approve data perangkat daerah {$data->var2}.";
			$icon = "bi-file-earmark-check";
			$color = "bg-success";
			break;
			case 'Rejected Permintaan Approval':
			$url = "variable/{$data->var3}";
			$body = "{$data->var1} meng-reject data perangkat daerah {$data->var2}.";
			$icon = "bi-file-earmark-x";
			$color = "bg-danger";
			break;
			default:
			break;
		}
		switch ($type) {
			case 1:
			return $url;
			case 2:
			return $body;
			case 3:
			return $icon;
			case 4:
			return $color;
			break;
			default:
			break;
		}
	}
	public function read($id)
	{
		$notifData = $this->mdb->getrowdatawhere("tb_notification", ["id_receiver" => $this->session->userdata("whs_id_user"), "id_notification" => $id]);	
		if($notifData){
			$this->mdb->putdatawhere("tb_notification", ["id_receiver" => $this->session->userdata("whs_id_user"), "read" => 0, "id_notification" => $id], ["read" => 1]);
			$url = $this->get_notif($notifData,1);
			redirect(site_url($url));
		}else{
			redirect(site_url(''));
		}
	}
}
