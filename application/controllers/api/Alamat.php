<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Alamat extends RestController
{
	function __construct()
	{
		parent::__construct();
	}

	public function kabupaten_get()
	{
		$id_provinsi = $this->get('id_provinsi');
		if (!$id_provinsi) {
			$this->response( [
				'status' => false,
				'message' => 'Parameter tidak lengkap!'
			], 400 );
		}

		$this->dbselect = $this->load->database('select', true);
		$this->dbselect->select('id_kabupaten, nama');
		$this->dbselect->where('id_provinsi', $id_provinsi);
		$list_kabupaten = $this->dbselect->get('tb_kabupaten')->result();
		$this->response( $list_kabupaten, 200 );
	}

	public function kecamatan_get()
	{
		$id_kabupaten = $this->get('id_kabupaten');
		if (!$id_kabupaten) {
			$this->response( [
				'status' => false,
				'message' => 'Parameter tidak lengkap!'
			], 400 );
		}

		$this->dbselect = $this->load->database('select', true);
		$this->dbselect->select('id_kecamatan, nama');
		$this->dbselect->where('id_kabupaten', $id_kabupaten);
		$list_kecamatan = $this->dbselect->get('tb_kecamatan')->result();
		$this->response( $list_kecamatan, 200 );
	}
}