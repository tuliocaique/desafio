<?php
class ExtratoModel extends CI_Model {

	private $view;

	public  function  __construct()
	{
		parent::__construct();
		$this->view = 'extrato';
		$this->load->database();
	}

	public function getExtrato($extrato){
		$this->db->select('*');

		if(isset($extrato['data_inicio']) && isset($extrato['data_fim']))
			$this->db->where("DATE_FORMAT(realizado_em, '%Y-%m-%d') BETWEEN '".$extrato['data_inicio']."' AND '".$extrato['data_fim']."'");

		$this->db->where("id_conta", $extrato['id_conta']);
		$response = $this->db->get($this->view)
							->result_array();
		return array("extrato" => $response);
	}

	public function getDefaultRules()
	{
		return array(
			array(
				'field' => 'id_conta',
				'label' => 'conta',
				'rules' => 'required|numeric'
			),
			/*array(
				'field' => 'data_inicio',
				'label' => 'data de inicio',
				'rules' => 'regex_match[(0[1-9]|1[0-9]|2[0-9]|3(0|1))-(0[1-9]|1[0-2])-\d{4}]'
			),
			array(
				'field' => 'data_fim',
				'label' => 'data de fim',
				'rules' => 'regex_match[(0[1-9]|1[0-9]|2[0-9]|3(0|1))-(0[1-9]|1[0-2])-\d{4}]'
			)*/
		);
	}
}
