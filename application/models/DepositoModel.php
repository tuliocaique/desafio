<?php
class DepositoModel extends CI_Model {

	private $tabela;

	public  function  __construct()
	{
		parent::__construct();
		$this->tabela = 'deposito';
		$this->load->database();
	}

	public function cadastrar($parametros){
		$status = $this->db->insert($this->tabela, $parametros);
		if($status){
			return array('success' => true, 'data' => $this->ultimoRegistroInserido());
		} else {
			return array('success' => false, 'error' => $this->db->error());
		}
	}

	public function getDefaultRules()
	{
		return array(
			array(
				'field' => 'deposito_id_conta',
				'label' => 'conta',
				'rules' => 'required|numeric'
			),
			array(
				'field' => 'deposito_valor',
				'label' => 'valor',
				'rules' => 'required|decimal|greater_than[0]'
			),
			array(
				'field' => 'deposito_moeda',
				'label' => 'moeda',
				'rules' => 'required|exact_length[3]'
			)
		);
	}

    private function ultimoRegistroInserido(){
        return $this->db->select('*')
            ->order_by('deposito_realizado_em', 'DESC')
            ->limit(1)
            ->get($this->tabela)
            ->row_array();
    }
}
