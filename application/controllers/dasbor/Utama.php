<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utama extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		echo 'dasbor';
	}
}