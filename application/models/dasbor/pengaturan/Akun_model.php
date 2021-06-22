<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akun_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	public function get_detail_sekolah($id_sekolah)
	{
		$this->load->database();
		$this->db->where('id_sekolah', $id_sekolah);
		return $this->db->get('sekolah')->row();
	}
}