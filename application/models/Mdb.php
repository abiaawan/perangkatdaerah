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
	function getdatawhereselect($table, $select, $where=null)
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
} 