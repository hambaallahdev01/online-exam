<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alamat_model extends CI_Model{

	function __construct()
	{
		parent::__construct();
	}

	public function get_list_provinsi()
	{
		$this->dbselect = $this->load->database('select', true);
		return $this->db->get('tb_provinsi')->result();
	}

	public function get_list_kabupaten($id_provinsi)
	{
		$this->dbselect = $this->load->database('select', true);
		$this->dbselect->where('id_provinsi', $id_provinsi);
		return $this->db->get('tb_kabupaten')->result();
	}

	public function get_list_kecamatan($id_kabupaten)
	{
		$this->dbselect = $this->load->database('select', true);
		$this->dbselect->where('id_kabupaten', $id_kabupaten);
		return $this->db->get('tb_kecamatan')->result();
	}
}