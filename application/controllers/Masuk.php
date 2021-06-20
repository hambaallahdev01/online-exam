<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masuk extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->input->post('recaptcha_response')) {
			$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = $this->config->item('recaptcha_secret');
			$recaptcha_response = $this->input->post('recaptcha_response');

			$recaptcha = file_get_contents_curl($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
			$recaptcha = json_decode($recaptcha);
			if (!isset($recaptcha->score)) {
				//captcha salah
			}elseif ($recaptcha->score >= 0.5) {
				//captcha betul -> check user pwd
			} else {
				//captcha salah
			}
		}elseif (isset($this->session->userdata['sess_logged_in']) && $this->session->userdata['sess_logged_in'] == 1) {
			//jika ada sesi login, redirect
		}
		$this->load->view('masuk');
	}
}