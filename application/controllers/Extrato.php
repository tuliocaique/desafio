<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'API.php';

class Extrato extends API {

	public $Extrato;
    public $Conta;

    public  function  __construct()
	{
		parent::__construct();
		$this->load->model('ExtratoModel', 'Extrato');
		$this->load->model('ContaModel', 'Conta');

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

    /**
     * @api {GET} /extrato/{id_conta}/{data_inicial}/{data_final}
     * @apiSampleRequest /extrato/25/01-01-2021/01-09/2021
     * @apiName Extrato da Conta
     * @apiGroup Extrato
     * @apiDescription Retorna o extrato de uma conta informada
     * @apiParam {numeric} [id_conta] obrigatório
     * @apiParam {date} [data_inicial] opcional
     * @apiParam {date} [data_final] opcional
     */
	public function getExtratoPorConta($id_conta, $data_incial = NULL, $data_final = NULL){
		self::config(array(
			'methods' => array('GET')
		));

		//Verifica se a conta informada existe
		if($this->Conta->verificaSeContaExiste($id_conta)){
			$extrato = array(
				"id_conta" => $id_conta
			);

			if($data_incial != NULL && $data_final != NULL){

				//Verifica se as datas estão no formato dd-mm-YYYY
				if($this->verificaSeDataFormatoValido($data_incial) && $this->verificaSeDataFormatoValido($data_final)){

					//Verifica se a data de fim não é anterior a data de inicio
					if($this->verificaOrdemDasDatas($data_incial, $data_final)){

						$extrato["data_inicio"] = date("Y-m-d", strtotime($data_incial));
						$extrato["data_fim"] = date("Y-m-d", strtotime($data_final));

						$this->imprimirExtrato($extrato);

					} else self::response(array(
							"success" => false,
							"status" => self::HTTP_METHOD_NOT_ACCEPTABLE,
							"error" => "A data final é anterior a data de inicio."
						), self::HTTP_METHOD_NOT_ACCEPTABLE);

				} else self::response(array(
					"success" => false,
					"status" => self::HTTP_METHOD_NOT_ACCEPTABLE,
					"error" => "A data está no formato errado. Por favor, utilize o seguinte formato:  dd-mm-yyyy"
				), self::HTTP_METHOD_NOT_ACCEPTABLE);
			} else {

				if($data_incial && $data_final == NULL){
					self::response(array(
						"success" => false,
						"status" => self::HTTP_METHOD_NOT_ACCEPTABLE,
						"error" => "A data final não foi informada."
					), self::HTTP_METHOD_NOT_ACCEPTABLE);
				} else {
					$this->imprimirExtrato($extrato);
				}
			}

		} else self::response(array(
				"success" => false,
				"status" => self::HTTP_METHOD_NOT_ACCEPTABLE,
				"error" => "A conta informada não existe."
			), self::HTTP_METHOD_NOT_ACCEPTABLE);

	}


	private function imprimirExtrato($extrato){
		$response = $this->Extrato->getExtrato($extrato);
		$totalSaques = 0;
		$totalDepositos = 0;

		foreach ($response['extrato'] as $index => $transacao){
			if($transacao['tipo'] == 'Depósito')
				$totalDepositos++;
			elseif ($transacao['tipo'] == 'Saque')
				$totalSaques++;

			$response['transacoes'][$index]['valor'] = number_format((float)$transacao['valor'], 2, '.', '');
			$response['transacoes'][$index]['moeda'] = $transacao['moeda'];
			$response['transacoes'][$index]['operacao'] = $transacao['tipo'];
			$response['transacoes'][$index]['realizado_em'] = $transacao['realizado_em'];
		}

		$response['total']['transacoes'] = ($totalDepositos+$totalSaques);
		$response['total']['saques'] = $totalSaques;
		$response['total']['depositos'] = $totalDepositos;
		unset($response['extrato']);

		self::response(array(
			"success" => true,
			"status" => self::HTTP_OK,
			"response" => $response,
		), self::HTTP_OK);
	}

	private function verificaSeDataFormatoValido($data, $formato = 'd-m-Y')
	{
		$d = DateTime::createFromFormat($formato, $data);
		return ($d && $d->format($formato) === $data);
	}

	private function verificaOrdemDasDatas($incial, $final)
	{
		return (strtotime($final) > strtotime($incial));
	}
}
