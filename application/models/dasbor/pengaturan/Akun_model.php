<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akun_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	public function get_detail_sekolah($id_sekolah)
	{
		$this->dbselect = $this->load->database('select', true);
		$this->dbselect->where('id_sekolah', $id_sekolah);
		return $this->dbselect->get('sekolah')->row();
	}

	public function update_detail_sekolah($id_sekolah, $data)
	{
		$this->load->database();
		$this->db->where('id_sekolah', $id_sekolah);
		$this->db->update('sekolah', $data);
		return $this->db->affected_rows();
	}
}