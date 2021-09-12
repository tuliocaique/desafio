<?php
class ContaModel extends CI_Model {

	private $tabela;

	public  function  __construct()
	{
		parent::__construct();
		$this->tabela = 'conta';
		$this->load->database();
	}

	public function cadastrar($parametros){
		$status = $this->db->insert($this->tabela, $parametros);
		if($status)
			return array('success' => true, 'data' => $this->ultimoRegistroInserido());

		return array('success' => false, 'error' => $this->db->error());
	}

	public function verificaSeContaExiste($conta_id){
		$retorno = $this->db->select('conta_id')
							->where('conta_id', $conta_id)
							->get($this->tabela)
							->num_rows();
		return $retorno > 0;
	}

	private function ultimoRegistroInserido(){
		return $this->db->select('*')
						->order_by('conta_cadastrado_em', 'DESC')
						->limit(1)
						->get($this->tabela)
						->row_array();
	}
}
