<?php
class SaqueModel extends CI_Model {

	private $tabela;

	public  function  __construct()
	{
		parent::__construct();
		$this->tabela = 'saque';
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

	/*
	public function alterar($parametros, $condicao){
		$this->db->update($this->tabela, $parametros, $condicao);
	}

	public function deletar($condicao){
		$this->db->delete($this->tabela, $condicao);
	}

	public function listarTodos(){
		$resposta = $this->db->select('*')
			->get($this->tabela)
			->result_array();
		return array('success' => true, 'status' => 200, 'response' => $resposta);
	}

	public function listarPorId($cliente){
		$resposta = $this->db->select(array('cliente_id', 'cliente_nome', 'cliente_cpf', 'conta_id'))
			->join('conta', 'cliente_id = cliente_id', 'join')
			->get_where($this->tabela, $cliente)
			->row_array();
		if(!empty($resposta)){
			return array('success' => true, 'status' => 200, 'response' => $resposta);
		} else {
			return array('success' => false, 'status' => 400, 'error' => 'Cliente não encontrado.');
		}
	}
*/

	private function ultimoRegistroInserido(){
		return $this->db->select('*')
						->order_by('saque_realizado_em', 'DESC')
						->limit(1)
						->get($this->tabela)
						->row_array();
	}
/*
	function get_default_rules()
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
				'rules' => 'required|decimal'
			),
			array(
				'field' => 'deposito_moeda',
				'label' => 'moeda',
				'rules' => 'required|exact_length[3]'
			)
		);
	}
*/
}
