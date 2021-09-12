<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'API.php';

class Conta extends API {

	public $Conta;

	public  function  __construct()
	{
		parent::__construct();
		$this->load->model('ContaModel', 'Conta');

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}
}
