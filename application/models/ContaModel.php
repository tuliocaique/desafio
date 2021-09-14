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

    public function consultarPorId($conta){
        $resposta = $this->db->select(array('conta_id', 'conta_titular_nome AS titular_nome', 'conta_titular_cpf AS titular_cpf'))
                            ->get_where($this->tabela, $conta)
                            ->row_array();
        if(!empty($resposta)){
            return array('success' => true, 'data' => $resposta);
        } else {
            return array('success' => false, 'error' => 'Conta não encontrada.');
        }
    }

	public function verificaSeContaExiste($conta_id){
		$retorno = $this->db->select('conta_id')
							->where('conta_id', $conta_id)
							->get($this->tabela)
							->num_rows();
		return $retorno > 0;
	}

    public function get_default_rules()
    {
        return array(
            array(
                'field' => 'conta_titular_nome',
                'label' => 'nome',
                'rules' => 'required|max_length[100]'
            ),
            array(
                'field' => 'conta_titular_cpf',
                'label' => 'cpf',
                'rules' => 'required|exact_length[11]'
            )
        );
    }

    private function ultimoRegistroInserido(){
        return $this->db->select('*')
                        ->order_by('conta_cadastrado_em', 'DESC')
                        ->limit(1)
                        ->get($this->tabela)
                        ->row_array();
    }
}
