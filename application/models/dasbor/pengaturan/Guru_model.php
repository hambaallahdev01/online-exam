<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	public function insert_guru($data)
	{
		$this->load->database();
		$data['password'] = md5($data['password']);
		$this->db->insert('guru', $data);
		return $this->db->insert_id();
	}
}