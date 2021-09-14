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

    /**
     * @api {POST} /conta/cadastrar
     * @apiSampleRequest
     * @apiName Cadastrar
     * @apiGroup Conta
     * @apiDescription Realiza o cadastro de uma conta
     * @apiParam {String} [nome] obrigatório
     * @apiParam {numeric} [cpf] obrigatório
     */
    public function cadastrar()
	{
		self::config(array(
			'methods' => array('POST')
		));

		$conta = array(
			"conta_titular_nome" => $this->input->post('nome',TRUE),
			"conta_titular_cpf" => $this->input->post('cpf',TRUE)
		);

		$this->form_validation->set_rules($this->Conta->get_default_rules());
		$this->form_validation->set_data($conta);

		if ($this->form_validation->run()) {

            //Realiza o cadastro de uma conta
            $response_conta = $this->Conta->cadastrar($conta);

            //Verifica se o cadastro da conta ocorreu com sucesso
            if($response_conta['success']){

                //Retorna os dados da conta cadastrada
                $response = $this->Conta->consultarPorId(array("conta_id" => $response_conta['data']['conta_id']));
                self::response(array(
                    "success" => true,
                    "status" => self::HTTP_OK,
                    "response" => $response['data']
                ), self::HTTP_OK);

            } else self::response(array(
                "success" => false,
                "status" => self::HTTP_BAD_REQUEST,
                "error" => $response_conta['error']
            ), self::HTTP_BAD_REQUEST);

		} else self::response(array(
                "success" => false,
                "status" => self::HTTP_BAD_REQUEST,
                "error" => $this->form_validation->error_array()
            ), self::HTTP_BAD_REQUEST);
	}

    /**
     * @api {GET} /conta/listar/{id_conta}
     * @apiSampleRequest /conta/listar/23
     * @apiName Listar Por ID
     * @apiGroup Conta
     * @apiDescription Exibe as informações de uma conta
     * @apiParam {numeric} [id_conta] obrigatório
     */
    public function consultarPorId($id_conta = NULL)
	{
		self::config(array(
			'methods' => array('GET')
		));

        //Verifica se a id do cliente foi informada
		if(isset($id_conta)){
            $cliente = array(
                "conta_id" => $id_conta
            );
			$response = $this->Conta->consultarPorId($cliente);

            //Verifica se a busca pelo cliente teve sucesso
            if ($response['success']){
                self::response(array(
                    "success" => true,
                    "status" => self::HTTP_OK,
                    "response" => $response['data']
                ), self::HTTP_OK);

            } else self::response(array(
                    "success" => false,
                    "status" => self::HTTP_BAD_REQUEST,
                    "error" => $response['error']
                ), self::HTTP_BAD_REQUEST);

		} else self::response(array(
					"success" => false,
					"status" => self::HTTP_BAD_REQUEST,
					"error" => 'O parâmetro id_conta não foi informado.'
				), self::HTTP_BAD_REQUEST);
	}
}
