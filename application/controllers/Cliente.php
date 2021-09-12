<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'API.php';

class Cliente extends API {

	public $Cliente;

	public  function  __construct()
	{
		parent::__construct();
		$this->load->model('ClienteModel', 'Cliente');
		$this->load->model('ContaModel', 'Conta');

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

	public function cadastrar()
	{
		self::config(array(
			'methods' => array('POST')
		));

		$cliente = array(
			"cliente_nome" => $this->input->post('nome',TRUE),
			"cliente_cpf" => $this->input->post('cpf',TRUE)
		);

		$this->form_validation->set_rules($this->Cliente->get_default_rules());
		$this->form_validation->set_data($cliente);

		if ($this->form_validation->run()) {
			$response_cliente = $this->Cliente->cadastrar($cliente);
			if($response_cliente['success']){
				$conta = array('conta_id_cliente' => $response_cliente['data']['cliente_id']);
				$response_conta = $this->Conta->cadastrar($conta);
				if($response_conta['success']){
					$response = $this->Cliente->listarPorId(array("cliente_id" => $response_cliente['data']['cliente_id']));
					self::response(array(
							"success" => true,
							"status" => self::HTTP_OK,
							"response" => $response
					), self::HTTP_OK);

				} else self::response(array(
						"success" => false,
						"status" => self::HTTP_BAD_REQUEST,
						"error" => $response_conta['error']
					), self::HTTP_BAD_REQUEST);

			} else self::response(array(
					"success" => false,
					"status" => self::HTTP_BAD_REQUEST,
					"error" => $response_cliente['error']
				), self::HTTP_BAD_REQUEST);

		} else self::response(array(
					"success" => false,
					"status" => 400,
					"error" => $this->form_validation->error_array()
				), self::HTTP_BAD_REQUEST);
	}

	public function listarPorId($cliente_id)
	{
		self::config(array(
			'methods' => array('GET')
		));

		$cliente = array(
			"cliente_id" => $cliente_id
		);

		if(isset($cliente['cliente_id'])){
			$response = $this->Cliente->listarPorId($cliente);
			self::response($response, $response['status']);
		} else self::response(array(
					"success" => false,
					"status" => self::HTTP_BAD_REQUEST,
					"error" => 'Parametro de cliente_id não foi informado.'
				), self::HTTP_BAD_REQUEST);
	}
}
