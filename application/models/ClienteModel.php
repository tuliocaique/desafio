<?php
class ClienteModel extends CI_Model {

	private $tabela;

	public  function  __construct()
	{
		parent::__construct();
		$this->tabela = 'cliente';
		$this->load->database();
	}

	public function cadastrar($parametros){
		$status = $this->db->insert($this->tabela, $parametros);
		if($status){
			return array('success' => true, 'status' => 200, 'response' => array('menssage' => 'Cliente foi cadastrado.', 'data' => $this->ultimoRegistroInserido()));
		} else {
			return array('success' => false, 'status' => 400, 'error' => $this->db->error());
		}
	}

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
		$resposta = $this->db->select('*')
							->get_where($this->tabela, $cliente)
							->row_array();
		if(!empty($resposta)){
			return array('success' => true, 'status' => 200, 'response' => $resposta);
		} else {
			return array('success' => false, 'status' => 400, 'error' => 'Cliente não encontrado.');
		}
	}

	private function ultimoRegistroInserido(){
		return $this->db->select('*')
						->order_by('cadastrado_em', 'DESC')
						->get($this->tabela)
						->row();
	}

	function get_default_rules()
	{
		return array(
			array(
				'field' => 'nome',
				'label' => 'nome',
				'rules' => 'required|max_length[100]'
			),
			array(
				'field' => 'cpf',
				'label' => 'cpf',
				'rules' => 'required|exact_length[11]'
			)
		);
	}
}
