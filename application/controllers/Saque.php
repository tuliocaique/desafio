<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'Saldo.php';

class Saque extends Saldo {

	public $Saque;

	public  function  __construct()
	{
		parent::__construct();
		$this->load->model('SaqueModel', 'Saque');
		$this->load->model('ContaModel', 'Conta');

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

    /**
     * @api {POST} /saque
     * @apiSampleRequest
     * @apiName Cadastrar
     * @apiGroup Saque
     * @apiDescription Realiza o saque de um valor em uma conta
     * @apiParam {numeric} [conta] obrigatório
     * @apiParam {decimal} [valor] obrigatório
     * @apiParam {String} [moeda] obrigatório
     */
	public function cadastrar()
	{
		self::config(array(
			'methods' => array('POST')
		));

		$saque = array(
			"saque_id_conta" => $this->input->post('conta',TRUE),
			"saque_valor" => $this->input->post('valor',TRUE),
			"saque_moeda" => $this->input->post('moeda',TRUE)
		);

		$this->form_validation->set_rules($this->Saque->get_default_rules());
		$this->form_validation->set_data($saque);

		if ($this->form_validation->run()) {

			//Verifica se a conta informada existe
			if($this->Conta->verificaSeContaExiste($saque['saque_id_conta'])){

				//Verifica se a moeda informada é válida
				if(self::verificaMoedaPermitida($saque['saque_moeda'])){

					//Verifica se a conta possui o saldo sulficiente na moeda informada para realizar o saque
					if(self::verificaSaldoSulficiente($saque['saque_id_conta'], $saque['saque_valor'], $saque['saque_moeda'])){
						//Realiza o saque no valor e moeda informados
						$response_saque = $this->Saque->cadastrar($saque);

						//Verifica se o deposito foi realizado com sucesso
						if($response_saque['success']){
							//Atualiza a tabela saldo na moeda e com o saldo informado da conta em questão
							self::atualizaSaldo('saque', $saque['saque_id_conta'], $saque['saque_valor'], $saque['saque_moeda']);
						} else self::response(array(
								"success" => false,
								"status" => self::HTTP_BAD_REQUEST,
								"error" => $response_saque['error']
							), self::HTTP_BAD_REQUEST);

					}else{
						//Quando o saldo na moeda informada não é sulfiente para realizar o saque
						$saldosParaSacar = array();
						$somatorio = 0;

						//Retorna todos os saldos de todas as moedas da conta informada
						$todosSaldos = self::consultarTodosSaldosConta($saque['saque_id_conta']);

						foreach ($todosSaldos as $index => $saldo){

							//Verifica se a moeda do saldo é diferente da moeda que deseja sacar
							if($saldo['saldo_moeda'] !== $saque['saque_moeda']) {

								//Como as moedas são diferentes, realiza-se a conversão da moeda atual para a moeda de saque
								$saqueConversao = $this->conversao($saldo['saldo_valor'], $saldo['saldo_moeda'], $saque['saque_moeda']);

								//Armazena o saldo convertido e a sigla moeda
								$todosSaldos[$index]['saque_conversao'] = $saqueConversao;
								$todosSaldos[$index]['saque_conversao_moeda'] = $saque['saque_moeda'];

								//Armazena o somatório do valor de saldo convertido para a moeda de saque
								$somatorio += $saqueConversao;
							} else {
								//Como as moedas são iguais, não é feita a conversão
								$todosSaldos[$index]['saque_conversao'] = '-';
								$todosSaldos[$index]['saque_conversao_moeda'] = $saque['saque_moeda'];

								//Subtrai o valor a ser sacado pelo saldo disponivel, sendo 'quantidade_restante' o valor restante a sacar das demais moedas para completar o valor do saque
								$quantidade_restante = $saque['saque_valor'] - $saldo['saldo_valor'];

								//Armazena o somatório do valor de saldo para a moeda de saque
								$somatorio += $saldo['saldo_valor'];

								//Adiciona no array o saldo e moeda que serão sacados
								array_push($saldosParaSacar, array(
										"saldo_valor" => $saldo['saldo_valor'],
										"saldo_moeda" => $saldo['saldo_moeda'])
								);
							}
						}

						//Percorre novamente os saldos para obter a quantidade de saldo necessaria das outras moedas para realizar o saque
						foreach ($todosSaldos as $index => $saldo){

							//Verifica se a moeda do saldo é diferente da moeda que deseja sacar
							if($saldo['saldo_moeda'] !== $saque['saque_moeda']) {

								$todosSaldos[$index]['saque_conversao'] = $this->conversao($saldo['saldo_valor'], $saldo['saldo_moeda'], $saque['saque_moeda']);
								$todosSaldos[$index]['saque_conversao_moeda'] = $saque['saque_moeda'];

								if($quantidade_restante > 0){
									if($todosSaldos[$index]['saque_conversao'] >= $quantidade_restante ){
										//$valorConverter = $todosSaldos[$index]['saque_conversao'] - $quantidade_restante;
										$conversaoFinal = $this->conversao($quantidade_restante, $saque['saque_moeda'], $saldo['saldo_moeda']);
										array_push($saldosParaSacar, array(
												"saldo_valor" => $conversaoFinal,
												"saldo_moeda" => $saldo['saldo_moeda'])
										);
										$quantidade_restante = 0;
									}else{
										$conversaoFinal = $this->conversao($quantidade_restante, $saldo['saldo_moeda'], $saque['saque_moeda']);

										array_push($saldosParaSacar, array(
												"saldo_valor" => $conversaoFinal,
												"saldo_moeda" => $saldo['saldo_moeda'])
										);
										$quantidade_restante = ($quantidade_restante - $todosSaldos[$index]['saque_conversao']);
									}
								}
							}
						}


						if($quantidade_restante > 0){
							$response_saque = array(
								'menssagem' => 'O Saque não pode ser realizado devido ao saldo insulficiente! Seu saldo atual é de '.$saque['saque_moeda'] .' '.number_format((float)$somatorio, 2, '.', ''),
							);

							self::response(array(
								"success" => false,
								"status" => self::HTTP_BAD_REQUEST,
								"error" => $response_saque
							), self::HTTP_BAD_REQUEST);
						} else {
							foreach ($saldosParaSacar as $item_saldo){
								print_r($item_saldo['saldo_valor']." | ". $item_saldo['saldo_moeda'].PHP_EOL);
								//self::atualizaSaldo('saque', $saque['saque_id_conta'], $item_saldo['saldo_valor'], $item_saldo['saldo_moeda']);
							}
							/*$response_saque = $this->Saque->cadastrar($saque);
							self::response(array(
								"success" => true,
								"status" => self::HTTP_OK,
								"response" => array("Saque realizado!")
							), self::HTTP_OK);*/
						}
					}

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
