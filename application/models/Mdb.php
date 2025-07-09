<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class Mdb extends CI_Model {
	function getrowdatawhere($table, $where=null, $order=null)
	{
		$this->db->from($table);
		if($where){
			$where = array_merge($where, ["removed" => 0]);
			$this->db->where($where);
		}else{
			$this->db->where(["removed" => 0]);
		}
		if($order != null){
			foreach ($order as $k => $v) {
				$this->db->order_by($k, $v);
			}
		}
		$q = $this->db->get();
		// echo $this->db->last_query();die;
		return $q->row();
	}
	function getrowdatawhereselect($table, $select, $where=null)
	{
		$this->db->select($select);
		$this->db->from($table);
		if($where){
			$where = array_merge($where, ["removed" => 0]);
			$this->db->where($where);
		}else{
			$this->db->where(["removed" => 0]);
		}
		$q = $this->db->get();
		return $q->row();
	}
	function getdatawhereselect($table, $select, $where=null, $orwhere=null, $order=null, $wherein=null)
	{
		$this->db->select($select);
		$this->db->from($table);
		if($where){
			$this->db->group_start();
			$where = array_merge($where, ["removed" => 0]);
			$this->db->where($where);
			$this->db->group_end();
		}else{
			$this->db->group_start();
			$this->db->where(["removed" => 0]);
			$this->db->group_end();
		}
		if($wherein){
			$this->db->group_start();
			$this->db->where_in($wherein[0],$wherein[1]);
			$this->db->group_end();
		}
		if($orwhere != null){
			foreach ($orwhere as $k => $v){
				$this->db->or_group_start();
				$this->db->where($v);
				$this->db->group_end();
			}
		}
		if($order != null){
			foreach ($order as $k => $v) {
				$this->db->order_by($k, $v);
			}
		}
		$q = $this->db->get();
		return $q->result();
	}
	function getdatawhere($table, $where=null, $orwhere=null, $order=null, $wherein=null)
	{
		$this->db->from($table);
		if($where){
			$this->db->group_start();
			$where = array_merge($where, ["removed" => 0]);
			$this->db->where($where);
			$this->db->group_end();
		}else{
			$this->db->group_start();
			$this->db->where(["removed" => 0]);
			$this->db->group_end();
		}
		if($wherein){
			$this->db->group_start();
			$this->db->where_in($wherein[0],$wherein[1]);
			$this->db->group_end();
		}
		if($orwhere != null){
			foreach ($orwhere as $k => $v){
				$this->db->or_group_start();
				$this->db->where($v);
				$this->db->group_end();
			}
		}
		if($order != null){
			foreach ($order as $k => $v) {
				$this->db->order_by($k, $v);
			}
		}
		$q = $this->db->get();

		// echo $this->db->last_query();die;
		return $q->result();
	}
	function getdatagroupwhere($table, $where=null, $groupby=null, $orwhere=null, $order=null)
	{
		$this->db->from($table);
		if($groupby != null){
			$this->db->group_by($groupby);
		}
		if($where){
			$this->db->group_start();
			$where = array_merge($where, ["removed" => 0]);
			$this->db->where($where);
			$this->db->group_end();
		}else{
			$this->db->group_start();
			$this->db->where(["removed" => 0]);
			$this->db->group_end();
		}
		if($orwhere != null){
			foreach ($orwhere as $k => $v){
				$this->db->or_group_start();
				$this->db->where($v);
				$this->db->group_end();
			}
		}
		if($order != null){
			foreach ($order as $k => $v) {
				$this->db->order_by($k, $v);
			}
		}
		$q = $this->db->get();
		// echo $this->db->last_query();die;
		return $q->result();
	}
	function getcountwhere($table, $where=null, $orwhere=null)
	{
		$this->db->from($table);
		if($where){
			$this->db->group_start();
			$where = array_merge($where, ["removed" => 0]);
			$this->db->where($where);
			$this->db->group_end();
		}else{
			$this->db->group_start();
			$this->db->where(["removed" => 0]);
			$this->db->group_end();
		}
		if($orwhere != null){
			foreach ($orwhere as $k => $v){
				$this->db->or_group_start();
				$this->db->where($v);
				$this->db->group_end();
			}
		}
		$q = $this->db->get();
		return [$q->num_rows(),$q->result()];
		// return [$q->num_rows(),true];
	}
	public function getcountfiltereddt($table, $where, $like = NULL, $likevalue = NULL)
	{
		$this->db->from($table);
		if($where){
			$where = array_merge($where, ["removed" => 0]);
			$this->db->where($where);
		}else{
			$this->db->where(["removed" => 0]);
		}
		if ($like <> NULL && $likevalue <> NULL) {
			$x = false;
			$this->db->group_start();
			foreach ($like as $l) {
				if ($x == false) {
					$this->db->like($l, $likevalue);
					$x = true;
				} else {
					$this->db->or_like($l, $likevalue);
				}
			}
			$this->db->group_end();
		}
		$q = $this->db->get();
		return $q->num_rows();
	}
	function postdata($table, $data, $upload=null)
	{
		if($upload != null){
			$this->upload->initialize($upload[1]);
			if ($this->upload->do_upload($upload[0])){
				$gbr = $this->upload->data();
				$nama = $gbr['file_name'];
			}
		}
		$q = $this->db->insert($table, $data);
		return $this->db->insert_id();;
	}
	function postdatabatch($table, $data, $upload=null)
	{
		if($upload != null){
			$this->upload->initialize($upload[1]);
			if ($this->upload->do_upload($upload[0])){
				$gbr = $this->upload->data();
				$nama = $gbr['file_name'];
				list($width, $height) = getimagesize($upload[1]['upload_path'] . "/" . $gbr['file_name']);
				$configer =  array(
					'image_library'   => 'gd2',
					'source_image'    =>  $upload[1]['upload_path'] . "/" . $gbr['file_name'],
					// 'maintain_ratio'  =>  TRUE,
					'width'           =>  $width - 1,
					'height'          =>  $height - 1,
					'quality'         => "40%",
					'new_image'       =>  $upload[1]['upload_path'] . "/" . $gbr['file_name'],
				);
				$this->load->library('image_lib', $configer);
				if (!$this->image_lib->resize()) {
					echo $this->image_lib->display_errors();
				}
			}
		}
		$q = $this->db->insert_batch($table, $data);
		return $q;
	}
	function putdatawhere($table, $where, $set, $upload=null, $remove=null)
	{
		if($remove != null){
			foreach($remove as $v){
				@unlink($v);
			}
		}
		if($upload != null){
			$this->upload->initialize($upload[1]);
			if ($this->upload->do_upload($upload[0])){
				$gbr = $this->upload->data();
				$nama = $gbr['file_name'];
			}
		}
		$this->db->where($where);
		$q = $this->db->update($table, $set);
		return $q;
	}
	function putdatabatch($table, $set, $key)
	{
		$this->db->where($where);
		$q = $this->db->update_batch($table, $set, $key);
		return $q;
	}
	function putdatalimitwhere($table, $where, $set, $limit, $upload=null, $remove=null)
	{
		if($remove != null){
			foreach($remove as $v){
				@unlink($v);
			}
		}
		if($upload != null){
			$this->upload->initialize($upload[1]);
			if ($this->upload->do_upload($upload[0])){
				$gbr = $this->upload->data();
				$nama = $gbr['file_name'];
			}
		}
		$this->db->where($where);
		$this->db->limit($limit);
		$q = $this->db->update($table, $set);
		return $q;
	}
	function deletedata($table, $where, $remove=null)
	{
		if($remove != null){
			foreach($remove as $v){
				@unlink($v);
			}
		}
		$this->db->where($where);
		$q = $this->db->delete($table);
		return $q;
	}
	function removefile($remove=null)
	{
		if($remove != null){
			foreach($remove as $v){
				@unlink($v);
			}
		}
	}
	function getdatatables($table, $where, $order, $dir, $limit, $offset, $like = NULL, $likevalue = NULL)
	{
		$this->db->from($table);
		if(!$where){
			$this->db->where(["removed" => 0]);
		}else{
			$where = array_merge($where, ["removed" => 0]);
			$this->db->where($where);
		}
		if ($like <> NULL && $likevalue <> NULL) {
			$x = false;
			$this->db->group_start();
			foreach ($like as $l) {
				if ($x == false) {
					$this->db->like($l, $likevalue);
					$x = true;
				} else {
					$this->db->or_like($l, $likevalue);
				}
			}
			$this->db->group_end();
		}
		if(is_array($order)){
			foreach ($order as $k => $value) {
				if(preg_match("/^[0-9]+$/", $value)){
					$this->db->order_by($value, $dir[$k]);
				}else{
					$this->db->order_by($value, $dir[$k]);
				}
			}
		}else{
			if(preg_match("/^[0-9]+$/", $order)){
				$this->db->order_by($order, $dir);
			}else{
				$this->db->order_by($order, $dir);
			}
		}
		

		$this->db->limit($limit, $offset);
		$Q = $this->db->get();
		// echo $this->db->last_query();die;
		return $Q->result();
	}
	function send_approval_request($data)
	{
		if(!empty($data[0])){
			try {
				$mail = new PHPMailer(true);
				$mail->isSMTP();
				$mail->Host       = 'mail.perangkat-daerah.com';
				$mail->SMTPAuth   = true;   
				$mail->Username   = 'admin@perangkat-daerah.com';
				$mail->Password   = 'Qk5DGPTUUE79cV3';
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				$mail->Port       = 465; 
				$mail->setFrom('admin@perangkat-daerah.com', 'Perangkat Daerah');

				foreach ($data[0] as $k => $v) {
					$mail->addAddress($k, $v);
				}

				$mail->isHTML(true);
				$mail->Subject = 'Permintaan Approval '.$data[2].' #'.date("YmdHis");
				$imgUrl = base_url("assets/static/images/logo/logo.png");
			// $url = base_url("approval");
				$url = $data[3];
				$mail->Body = <<<SMF
				<!doctype html>
				<html>
				<body>
				<div style='background-color:#132b51;color:#132b51;font-family:Avenir, "Avenir Next LT Pro", Montserrat, Corbel, "URW Gothic", source-sans-pro, sans-serif;font-size:16px;font-weight:400;letter-spacing:0.15008px;line-height:1.5;margin:0;padding:32px 0;min-height:100%;width:100%'>
				<table align="center" width="100%" style="border-radius: 20px;margin:0 auto;max-width:600px;background-color:#FFFFFF" role="presentation" cellspacing="0" cellpadding="0" border="0">
				<tbody>
				<tr style="width:100%">
				<td>
				<div style="padding:24px 24px 24px 24px;text-align:center"><img alt="Logo Kementerian Dalam Negeri" src="{$imgUrl}" height="32" style="height:72px;outline:none;border:none;text-decoration:none;vertical-align:middle;display:inline-block;max-width:100%" /></div>
				<h2 style="font-weight:bold;text-align:left;margin:0;font-size:24px;padding:16px 24px 0px 24px"> Permintaan Approval $data[2]</h2>
				<div style="font-size:16px;font-weight:normal;text-align:left;padding:16px 24px 16px 24px"> User $data[1] baru saja melakukan submit data perangkat daerah $data[2] dan membutuhkan approval sebagai syarat keabsahan data</div>

				<div style="padding:8px 24px 8px 24px">

				<div style="text-align:center;padding:16px 24px 16px 24px"><a href="{$url}" style="color:#FFFFFF;font-size:16px;font-weight:bold;background-color:#132b51;border-radius:4px;display:inline-block;padding:16px 32px;text-decoration:none" target="_blank"><span></span><span>Detail</span><span></span></a></div>
				<div style="font-size:14px;font-weight:normal;text-align:right;padding:46px 24px 16px 24px;color:#888"> Jangan membalas surel otomatis ini. </div>
				</td>
				</tr>
				</tbody>
				</table>
				</div>
				</body>
				</html>
				SMF;
				if (!$mail->send()) {
				} else {
				}
				$mail->smtpClose();
			} catch (Exception $e) {

			}
		}

	}
	function approve_approval_request($data)
	{
		if(!empty($data[0])){
			try {
				$mail = new PHPMailer(true);
				$mail->isSMTP();
				$mail->Host       = 'mail.perangkat-daerah.com';
				$mail->SMTPAuth   = true;   
				$mail->Username   = 'admin@perangkat-daerah.com';
				$mail->Password   = 'Qk5DGPTUUE79cV3';
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				$mail->Port       = 465; 
				$mail->setFrom('admin@perangkat-daerah.com', 'Perangkat Daerah');

				foreach ($data[0] as $k => $v) {
					$mail->addAddress($k, $v);
				}

				$mail->isHTML(true);
				$mail->Subject = 'Approved - Permintaan Approval '.$data[2].' #'.date("YmdHis");
				$imgUrl = base_url("assets/static/images/logo/logo.png");
				$url = $data[3];
				$mail->Body = <<<SMF
				<!doctype html>
				<html>
				<body>
				<div style='background-color:#132b51;color:#132b51;font-family:Avenir, "Avenir Next LT Pro", Montserrat, Corbel, "URW Gothic", source-sans-pro, sans-serif;font-size:16px;font-weight:400;letter-spacing:0.15008px;line-height:1.5;margin:0;padding:32px 0;min-height:100%;width:100%'>
				<table align="center" width="100%" style="border-radius: 20px;margin:0 auto;max-width:600px;background-color:#FFFFFF" role="presentation" cellspacing="0" cellpadding="0" border="0">
				<tbody>
				<tr style="width:100%">
				<td>
				<div style="padding:24px 24px 24px 24px;text-align:center"><img alt="Logo Kementerian Dalam Negeri" src="{$imgUrl}" height="32" style="height:72px;outline:none;border:none;text-decoration:none;vertical-align:middle;display:inline-block;max-width:100%" /></div>
				<h2 style="font-weight:bold;text-align:left;margin:0;font-size:24px;padding:16px 24px 0px 24px"> Approved - Permintaan Approval $data[2]</h2>
				<div style="font-size:16px;font-weight:normal;text-align:left;padding:16px 24px 16px 24px"> User $data[1] baru saja meng-approve data perangkat daerah $data[2]</div>

				<div style="padding:8px 24px 8px 24px">

				<div style="text-align:center;padding:16px 24px 16px 24px"><a href="{$url}" style="color:#FFFFFF;font-size:16px;font-weight:bold;background-color:#132b51;border-radius:4px;display:inline-block;padding:16px 32px;text-decoration:none" target="_blank"><span></span><span>Detail</span><span></span></a></div>
				<div style="font-size:14px;font-weight:normal;text-align:right;padding:46px 24px 16px 24px;color:#888"> Jangan membalas surel otomatis ini. </div>
				</td>
				</tr>
				</tbody>
				</table>
				</div>
				</body>
				</html>
				SMF;
				if (!$mail->send()) {
				} else {
				}
				$mail->smtpClose();
			} catch (Exception $e) {

			}
		}
	}
	function reject_approval_request($data)
	{
		if(!empty($data[0])){
			try {
				$mail = new PHPMailer(true);
				$mail->isSMTP();
				$mail->Host       = 'mail.perangkat-daerah.com';
				$mail->SMTPAuth   = true;   
				$mail->Username   = 'admin@perangkat-daerah.com';
				$mail->Password   = 'Qk5DGPTUUE79cV3';
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				$mail->Port       = 465; 
				$mail->setFrom('admin@perangkat-daerah.com', 'Perangkat Daerah');

				foreach ($data[0] as $k => $v) {
					$mail->addAddress($k, $v);
				}

				$mail->isHTML(true);
				$mail->Subject = 'Rejected - Permintaan Approval '.$data[2].' #'.date("YmdHis");
				$imgUrl = base_url("assets/static/images/logo/logo.png");
				$url = $data[4];
				$mail->Body = <<<SMF
				<!doctype html>
				<html>
				<body>
				<div style='background-color:#132b51;color:#132b51;font-family:Avenir, "Avenir Next LT Pro", Montserrat, Corbel, "URW Gothic", source-sans-pro, sans-serif;font-size:16px;font-weight:400;letter-spacing:0.15008px;line-height:1.5;margin:0;padding:32px 0;min-height:100%;width:100%'>
				<table align="center" width="100%" style="border-radius: 20px;margin:0 auto;max-width:600px;background-color:#FFFFFF" role="presentation" cellspacing="0" cellpadding="0" border="0">
				<tbody>
				<tr style="width:100%">
				<td>
				<div style="padding:24px 24px 24px 24px;text-align:center"><img alt="Logo Kementerian Dalam Negeri" src="{$imgUrl}" height="32" style="height:72px;outline:none;border:none;text-decoration:none;vertical-align:middle;display:inline-block;max-width:100%" /></div>
				<h2 style="font-weight:bold;text-align:left;margin:0;font-size:24px;padding:16px 24px 0px 24px"> Rejected - Permintaan Approval $data[2]</h2>
				<div style="font-size:16px;font-weight:normal;text-align:left;padding:16px 24px 16px 24px"> User $data[1] baru saja me-reject data perangkat daerah $data[2] dengan alasan: $data[3]</div>

				<div style="padding:8px 24px 8px 24px">

				<div style="text-align:center;padding:16px 24px 16px 24px"><a href="{$url}" style="color:#FFFFFF;font-size:16px;font-weight:bold;background-color:#132b51;border-radius:4px;display:inline-block;padding:16px 32px;text-decoration:none" target="_blank"><span></span><span>Detail</span><span></span></a></div>
				<div style="font-size:14px;font-weight:normal;text-align:right;padding:46px 24px 16px 24px;color:#888"> Jangan membalas surel otomatis ini. </div>
				</td>
				</tr>
				</tbody>
				</table>
				</div>
				</body>
				</html>
				SMF;
				if (!$mail->send()) {
				} else {
				}
				$mail->smtpClose();
			} catch (Exception $e) {

			}
		}


	}
} 