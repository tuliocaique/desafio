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

	public function getDefaultRules()
	{
		return array(
			array(
				'field' => 'saque_id_conta',
				'label' => 'conta',
				'rules' => 'required|numeric'
			),
			array(
				'field' => 'saque_valor',
				'label' => 'valor',
				'rules' => 'required|decimal|greater_than[0]'
			),
			array(
				'field' => 'saque_moeda',
				'label' => 'moeda',
				'rules' => 'required|exact_length[3]'
			)
		);
	}

    private function ultimoRegistroInserido(){
        return $this->db->select('*')
            ->order_by('saque_realizado_em', 'DESC')
            ->limit(1)
            ->get($this->tabela)
            ->row_array();
    }
}
