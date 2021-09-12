<?php
class SaldoModel extends CI_Model {

	private $tabela;

	public  function  __construct()
	{
		parent::__construct();
		$this->tabela = 'saldo';
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

	public function atualizar($parametros, $condicao){
		$this->db->update($this->tabela, $parametros, $condicao);
	}

	public function getSaldoDaContaPorMoeda($conta_id, $moeda){
		return $this->db->select('saldo_valor')
						->where('saldo_id_conta', $conta_id)
						->where('saldo_moeda', $moeda)
						->get($this->tabela)
						->row_array();
	}

	public function getSaldoDaConta($conta_id){
		return $this->db->select('*')
						->where('saldo_id_conta', $conta_id)
						->get($this->tabela)
						->result_array();
	}

	private function ultimoRegistroInserido(){
		return $this->db->select('*')
						->order_by('saldo_atualizado_em', 'DESC')
						->limit(1)
						->get($this->tabela)
						->row_array();
	}
}
