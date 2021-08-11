<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tahun_ajaran_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	public function insert_tahun_ajaran($data)
	{
		$this->load->database();
		$this->db->insert('tahun_ajaran', $data);
		return $this->db->insert_id();
	}
}