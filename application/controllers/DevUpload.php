<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DevUpload extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->helper('s3_helper');
		$ret = upload_image('path');
		print_r($ret);
	}
}