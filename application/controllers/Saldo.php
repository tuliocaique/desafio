<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'API.php';

class Saldo extends API {

	public $Saldo;
	public $Conta;

	public  function  __construct()
	{
		parent::__construct();
		$this->load->model('SaldoModel', 'Saldo');
		$this->load->model('ContaModel', 'Conta');

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		date_default_timezone_set( 'America/Sao_Paulo' );
	}

    /**
     * @api {GET} /saldo/{id_conta}
     * @apiSampleRequest /saldo/25
     * @apiName Saldo da Conta
     * @apiGroup Saldo
     * @apiDescription Retorna o saldo de uma conta informada contendo todas as moedas
     * @apiParam {numeric} [id_conta] obrigatório
     */
	public function getSaldoDaConta($saldo_id_conta){
		self::config(array(
			'methods' => array('GET')
		));

		if (is_numeric($saldo_id_conta)) {
			$contaExiste = $this->Conta->verificaSeContaExiste($saldo_id_conta);

			if($contaExiste){
				$response = array();
				$response['conta'] = intval($saldo_id_conta);
				$response['consulta_realizada_em'] = date("d-m-Y H:i:s");

				$saldo = $this->Saldo->getSaldoDaConta($saldo_id_conta);
				foreach ($saldo as $index => $moeda){
					$response['saldo'][$index]['valor'] = number_format((float)$moeda['saldo_valor'], 2, '.', '');
					$response['saldo'][$index]['moeda'] = $moeda['saldo_moeda'];
				}
				unset($saldo);
				self::response(array(
					"success" => true,
					"status" => self::HTTP_OK,
					"response" => $response
				), self::HTTP_OK);
			} else self::response(array(
				"success" => false,
				"status" => self::HTTP_BAD_REQUEST,
				"error" => "Não existe uma conta com a id informada."
			), self::HTTP_BAD_REQUEST);
		} else self::response(array(
				"success" => false,
				"status" => 400,
				"error" => "O paramêtro de conta informado não é um valor aceito."
			), self::HTTP_BAD_REQUEST);
	}

    /**
     * @api {GET} /saldo/{id_conta}/{moeda}
     * @apiSampleRequest /saldo/25/BRL
     * @apiName Saldo da Conta Por Moeda
     * @apiGroup Saldo
     * @apiDescription Retorna o saldo de uma moeda de uma conta informada
     * @apiParam {numeric} [id_conta] obrigatório
     * @apiParam {String} [moeda] obrigatório
     */
	public function getSaldoDaContaPorMoeda($saldo_id_conta, $saldo_moeda){
		self::config(array(
			'methods' => array('GET')
		));

		if (is_numeric($saldo_id_conta)) {
			//Verifica se a conta informada existe
			if($this->Conta->verificaSeContaExiste($saldo_id_conta)){
				$saldo_moeda = filter_var ($saldo_moeda, FILTER_SANITIZE_STRING);

				//Verifica se a moeda informada é válida
				if($this->verificaMoedaPermitida($saldo_moeda)){
					$response = array();
					$response['conta'] = intval($saldo_id_conta);
					$response['data'] = date("d-m-Y H:i:s");
					$saldo = $this->Saldo->getSaldoDaContaPorMoeda($saldo_id_conta, $saldo_moeda);
					$response['saldo']['valor'] = number_format((float)$saldo['saldo_valor'], 2, '.', '');
					$response['saldo']['moeda'] = $saldo_moeda;

					unset($saldo);
					self::response(array(
						"success" => true,
						"status" => self::HTTP_OK,
						"response" => $response
					), self::HTTP_OK);

				} else self::response(array(
					"success" => false,
					"status" => self::HTTP_BAD_REQUEST,
					"error" => "A moeda informada não é válida."
				), self::HTTP_BAD_REQUEST);

			} else self::response(array(
				"success" => false,
				"status" => self::HTTP_BAD_REQUEST,
				"error" => "Não existe uma conta com a id informada."
			), self::HTTP_BAD_REQUEST);

		} else self::response(array(
			"success" => false,
			"status" => self::HTTP_BAD_REQUEST,
			"error" => "O paramêtro de conta informado não é um valor aceito."
		), self::HTTP_BAD_REQUEST);
	}


	//Metodo para verificar se o saldo na conta é sulficiente para saque
	public function verificaSaldoSulficiente($cliente_id, $valor_saque, $moeda){
		$saldo = $this->Saldo->getSaldoDaContaPorMoeda($cliente_id, $moeda);
		if(!empty($saldo)){
			if($saldo['saldo_valor'] >= $valor_saque)
				return true;
		}
		return false;
	}

	//Metodo para verificar se uma determinada moeda está no formato da ISO-4217
	public function verificaMoedaPermitida($moeda){
		$moedas = array(
			'AFA' => array('Afghan Afghani', '971'),
			'AWG' => array('Aruban Florin', '533'),
			'AUD' => array('Australian Dollars', '036'),
			'ARS' => array('Argentine Pes', '032'),
			'AZN' => array('Azerbaijanian Manat', '944'),
			'BSD' => array('Bahamian Dollar', '044'),
			'BDT' => array('Bangladeshi Taka', '050'),
			'BBD' => array('Barbados Dollar', '052'),
			'BYR' => array('Belarussian Rouble', '974'),
			'BOB' => array('Bolivian Boliviano', '068'),
			'BRL' => array('Brazilian Real', '986'),
			'GBP' => array('British Pounds Sterling', '826'),
			'BGN' => array('Bulgarian Lev', '975'),
			'KHR' => array('Cambodia Riel', '116'),
			'CAD' => array('Canadian Dollars', '124'),
			'KYD' => array('Cayman Islands Dollar', '136'),
			'CLP' => array('Chilean Peso', '152'),
			'CNY' => array('Chinese Renminbi Yuan', '156'),
			'COP' => array('Colombian Peso', '170'),
			'CRC' => array('Costa Rican Colon', '188'),
			'HRK' => array('Croatia Kuna', '191'),
			'CPY' => array('Cypriot Pounds', '196'),
			'CZK' => array('Czech Koruna', '203'),
			'DKK' => array('Danish Krone', '208'),
			'DOP' => array('Dominican Republic Peso', '214'),
			'XCD' => array('East Caribbean Dollar', '951'),
			'EGP' => array('Egyptian Pound', '818'),
			'ERN' => array('Eritrean Nakfa', '232'),
			'EEK' => array('Estonia Kroon', '233'),
			'EUR' => array('Euro', '978'),
			'GEL' => array('Georgian Lari', '981'),
			'GHC' => array('Ghana Cedi', '288'),
			'GIP' => array('Gibraltar Pound', '292'),
			'GTQ' => array('Guatemala Quetzal', '320'),
			'HNL' => array('Honduras Lempira', '340'),
			'HKD' => array('Hong Kong Dollars', '344'),
			'HUF' => array('Hungary Forint', '348'),
			'ISK' => array('Icelandic Krona', '352'),
			'INR' => array('Indian Rupee', '356'),
			'IDR' => array('Indonesia Rupiah', '360'),
			'ILS' => array('Israel Shekel', '376'),
			'JMD' => array('Jamaican Dollar', '388'),
			'JPY' => array('Japanese yen', '392'),
			'KZT' => array('Kazakhstan Tenge', '368'),
			'KES' => array('Kenyan Shilling', '404'),
			'KWD' => array('Kuwaiti Dinar', '414'),
			'LVL' => array('Latvia Lat', '428'),
			'LBP' => array('Lebanese Pound', '422'),
			'LTL' => array('Lithuania Litas', '440'),
			'MOP' => array('Macau Pataca', '446'),
			'MKD' => array('Macedonian Denar', '807'),
			'MGA' => array('Malagascy Ariary', '969'),
			'MYR' => array('Malaysian Ringgit', '458'),
			'MTL' => array('Maltese Lira', '470'),
			'BAM' => array('Marka', '977'),
			'MUR' => array('Mauritius Rupee', '480'),
			'MXN' => array('Mexican Pesos', '484'),
			'MZM' => array('Mozambique Metical', '508'),
			'NPR' => array('Nepalese Rupee', '524'),
			'ANG' => array('Netherlands Antilles Guilder', '532'),
			'TWD' => array('New Taiwanese Dollars', '901'),
			'NZD' => array('New Zealand Dollars', '554'),
			'NIO' => array('Nicaragua Cordoba', '558'),
			'NGN' => array('Nigeria Naira', '566'),
			'KPW' => array('North Korean Won', '408'),
			'NOK' => array('Norwegian Krone', '578'),
			'OMR' => array('Omani Riyal', '512'),
			'PKR' => array('Pakistani Rupee', '586'),
			'PYG' => array('Paraguay Guarani', '600'),
			'PEN' => array('Peru New Sol', '604'),
			'PHP' => array('Philippine Pesos', '608'),
			'QAR' => array('Qatari Riyal', '634'),
			'RON' => array('Romanian New Leu', '946'),
			'RUB' => array('Russian Federation Ruble', '643'),
			'SAR' => array('Saudi Riyal', '682'),
			'CSD' => array('Serbian Dinar', '891'),
			'SCR' => array('Seychelles Rupee', '690'),
			'SGD' => array('Singapore Dollars', '702'),
			'SKK' => array('Slovak Koruna', '703'),
			'SIT' => array('Slovenia Tolar', '705'),
			'ZAR' => array('South African Rand', '710'),
			'KRW' => array('South Korean Won', '410'),
			'LKR' => array('Sri Lankan Rupee', '144'),
			'SRD' => array('Surinam Dollar', '968'),
			'SEK' => array('Swedish Krona', '752'),
			'CHF' => array('Swiss Francs', '756'),
			'TZS' => array('Tanzanian Shilling', '834'),
			'THB' => array('Thai Baht', '764'),
			'TTD' => array('Trinidad and Tobago Dollar', '780'),
			'TRY' => array('Turkish New Lira', '949'),
			'AED' => array('UAE Dirham', '784'),
			'USD' => array('US Dollars', '840'),
			'UGX' => array('Ugandian Shilling', '800'),
			'UAH' => array('Ukraine Hryvna', '980'),
			'UYU' => array('Uruguayan Peso', '858'),
			'UZS' => array('Uzbekistani Som', '860'),
			'VEB' => array('Venezuela Bolivar', '862'),
			'VND' => array('Vietnam Dong', '704'),
			'AMK' => array('Zambian Kwacha', '894'),
			'ZWD' => array('Zimbabwe Dollar', '716'),
		);
		if (array_key_exists($moeda, $moedas))
			return true;
		return false;
	}

	public function atualizaSaldo($tipoTransacao, $conta_id, $valor, $moeda)
	{
		$saldoTotalPorMoeda = $this->Saldo->getSaldoDaContaPorMoeda($conta_id, $moeda);
		$isSaldoExiste = (isset($saldoTotalPorMoeda) ? true : false);
		$saldoTotalPorMoeda = ($isSaldoExiste ? $saldoTotalPorMoeda['saldo_valor'] : 0);

		$saldo_condicao = array(
			'saldo_id_conta' => $conta_id,
			'saldo_moeda' => $moeda
		);

		switch ($tipoTransacao) {
			case 'deposito':
				if ($isSaldoExiste) {
					$saldo = array(
						'saldo_valor' => (double)($saldoTotalPorMoeda + $valor),
						'saldo_atualizado_em' => date('Y-m-d H:i:s')
					);
					$this->Saldo->atualizar($saldo, $saldo_condicao);
				} else {
					$saldo = array(
						'saldo_id_conta' => $conta_id,
						'saldo_moeda' => $moeda,
						'saldo_valor' => $valor
					);
					$this->Saldo->cadastrar($saldo);
				}
				break;

			case 'saque':
				if ($isSaldoExiste) {
					$saldo = array(
						'saldo_valor' => (double)($saldoTotalPorMoeda - $valor),
						'saldo_atualizado_em' => date('Y-m-d H:i:s')
					);
					$this->Saldo->atualizar($saldo, $saldo_condicao);
					break;
				}
		}
	}

	public function consultarTodosSaldosConta($conta_id){
		return $this->Saldo->getSaldoDaConta($conta_id);
	}

	public function conversao($saldo, $saldoMoeda, $saqueMoeda){
		$data = $this->diaUtilMaisRecente(date("d-m-Y"));

		if($saldoMoeda == 'BRL'){
			$cotacao = $this->getCotacao($data, $saqueMoeda);
			if($cotacao){

                //Conversão de BRL para moeda de saque
				return ($saldo/$cotacao['cotacaoVenda']);
			}
		}else{
			$cotacao = $this->getCotacao($data, $saldoMoeda);
			if($cotacao){

                //Conversão da moeda para BRL
				$valorEmReal = $saldo*$cotacao['cotacaoCompra'];

				$cotacaoMoeda = $this->getCotacao($data, $saqueMoeda);
				if($cotacaoMoeda){

                    //Conversão de BRL para moeda de saque
					return ($valorEmReal/$cotacaoMoeda['cotacaoVenda']);
				}
			}
		}
		return $this;
	}

	private function getCotacao($data, $moeda){
		$cotacao = self::get("https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?@moeda='".$moeda."'&@dataCotacao='".$data."'&%24top=100&%24format=json");
		if(!empty($cotacao['value'])){
			foreach ($cotacao['value'] as $boletim){
				if ($boletim['tipoBoletim'] == 'Fechamento PTAX')
					return array("cotacaoCompra" => $boletim['cotacaoCompra'], "cotacaoVenda" => $boletim['cotacaoVenda']);
			}
		}

		$this->getCotacao(date("d-m-Y", strtotime($data."-1 days")), $moeda);
	}

	private function diaUtilMaisRecente($data){
		$data = date($data);

        try {
            $data_inicio = new DateTime(date("Y-m-d", strtotime($data)));
        } catch (Exception $e) {
        }
        try {
            $data_fim = new DateTime(date("Y-m-d"));
        } catch (Exception $e) {
        }
        $dateInterval = $data_inicio->diff($data_fim);

		if($dateInterval->days > 0){
			$data = date("d-m-Y");
		}

		$diaDaSemanaAtual = date("w", strtotime($data));
		if($diaDaSemanaAtual == 0) //caso o dia atual for domingo
			return  date("m-d-Y", strtotime($data."-2 days"));
		elseif($diaDaSemanaAtual == 6) //caso o dia atual for sábado
			return date("m-d-Y", strtotime($data."-1 days"));

		return date("m-d-Y", strtotime($data));
	}
}
