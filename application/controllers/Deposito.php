<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'Saldo.php';

class Deposito extends Saldo {

	public $Deposito;

	public  function  __construct()
	{
		parent::__construct();
		$this->load->model('DepositoModel', 'Deposito');
		$this->load->model('ContaModel', 'Conta');

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

    /**
     * @api {POST} /deposito
     * @apiSampleRequest
     * @apiName Cadastrar
     * @apiGroup Deposito
     * @apiDescription Realiza o deposito de um valor em uma conta
     * @apiParam {numeric} [conta] obrigatório
     * @apiParam {decimal} [valor] obrigatório
     * @apiParam {String} [moeda] obrigatório
     */
	public function cadastrar()
	{
		self::config(array(
			'methods' => array('POST')
		));

		$deposito = array(
			"deposito_id_conta" => $this->input->post('conta',TRUE),
			"deposito_valor" => $this->input->post('valor',TRUE),
			"deposito_moeda" => $this->input->post('moeda',TRUE)
		);

		$this->form_validation->set_rules($this->Deposito->get_default_rules());
		$this->form_validation->set_data($deposito);

		if ($this->form_validation->run()) {

			//Verifica se a conta informada existe
			if($this->Conta->verificaSeContaExiste($deposito['deposito_id_conta'])){

				//Verifica se a moeda informada é válida
				if(self::verificaMoedaPermitida($deposito['deposito_moeda'])){
					//Realiza o deposito no valor e moeda informados
					$response_deposito = $this->Deposito->cadastrar($deposito);

					//Verifica se o deposito foi realizado com sucesso
					if($response_deposito['success']){
						//Atualiza a tabela saldo na moeda e com o saldo informado da conta em questão
						self::atualizaSaldo('deposito', $deposito['deposito_id_conta'], $deposito['deposito_valor'], $deposito['deposito_moeda']);

						$response = array(
							'menssagem' => 'O Deposito foi realizado com sucesso!',
							'deposito' => array(
								'codigo' => md5($response_deposito['data']['deposito_id']),
								'conta' => $response_deposito['data']['deposito_id_conta'],
								'valor' => $response_deposito['data']['deposito_valor'],
								'moeda' => $response_deposito['data']['deposito_moeda'],
								'realizado' => $response_deposito['data']['deposito_reealizado_em']
							)
						);
						self::response(array(
							"success" => true,
							"status" => self::HTTP_OK,
							"response" => $response
						), self::HTTP_OK);

					} else self::response(array(
						"success" => false,
						"status" => self::HTTP_BAD_REQUEST,
						"error" => $response_deposito['error']
					), self::HTTP_BAD_REQUEST);

				} else self::response(array(
					"success" => false,
					"status" => self::HTTP_METHOD_NOT_ACCEPTABLE,
					"error" => "A moeda informada não é válida."
				), self::HTTP_METHOD_NOT_ACCEPTABLE);

			} else self::response(array(
					"success" => false,
					"status" => self::HTTP_METHOD_NOT_ACCEPTABLE,
					"error" => "A conta informada não existe."
				), self::HTTP_METHOD_NOT_ACCEPTABLE);

		} else {
			$errors = $this->form_validation->error_array();
			$response = array();
			foreach ($errors as $error){
				array_push($response, $error);
			}
			self::response(array(
				"success" => false,
				"status" => self::HTTP_BAD_REQUEST,
				"errors" => $response
			), self::HTTP_BAD_REQUEST);
		}
	}
}
