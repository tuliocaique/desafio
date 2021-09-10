<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'API.php';

class Cliente extends API {

	public $Cliente;

	public  function  __construct()
	{
		parent::__construct();
		$this->load->model('ClienteModel', 'Cliente');

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

	public function cadastrar()
	{
		self::config([
			'methods' => ['POST'],
			'requireAuthorization' => false,
		]);

		$cliente = array(
			"nome" => $this->input->post('nome',TRUE),
			"cpf" => $this->input->post('cpf',TRUE)
		);

		$this->form_validation->set_rules($this->Cliente->get_default_rules());
		$this->form_validation->set_data($cliente);

		if ($this->form_validation->run()) {
			$response = $this->Cliente->cadastrar($cliente);
			self::response($response, $response['status']);
		} else self::response(
			[   "success" => false,
				"status" => 400,
				"error" => $this->form_validation->error_array()
			], self::HTTP_BAD_REQUEST);
	}

	public function listarPorId($id_cliente)
	{
		self::config([
			'methods' => ['GET'],
			'requireAuthorization' => false,
		]);

		$cliente = array(
			"id_cliente" => $id_cliente
		);

		if(isset($cliente['id_cliente'])){
			$response = $this->Cliente->listarPorId($cliente);
			self::response($response, $response['status']);
		} else self::response(
			[   "success" => false,
				"status" => self::HTTP_BAD_REQUEST,
				"error" => 'Parametro de id não foi informado.'
			], self::HTTP_BAD_REQUEST);
	}
}
