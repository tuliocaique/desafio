<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends CI_Controller
{
	const HTTP_OK = 200;
	const HTTP_BAD_REQUEST = 400;
	const HTTP_UNAUTHORIZED = 401;
	const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_METHOD_NOT_ACCEPTABLE = 406;
	const HTTP_REQUEST_TIMEOUT = 408;

	protected $hedarStatus;
	protected $allowed_http_methods;
    protected $return_other_data;
	protected $CI;


	public function __construct() {
        parent::__construct();
        $this->CI =& get_instance();
		$this->hedarStatus = array(
			'200' => 'OK',
			'401' => 'UNAUTHORIZED',
			'400' => 'BAD REQUEST',
			'404' => 'NOT FOUND',
			'405' => 'METHOD NOT ALLOWED',
			'406' => 'METHOD NOT ACCEPTABLE',
			'408' => 'REQUEST TIMEOUT',
		);
		$this->allowed_http_methods = array('get', 'delete', 'post', 'put', 'options', 'patch', 'head');
		$this->return_other_data = array();

        date_default_timezone_set('America/Sao_Paulo');
    }

	/**
	 * @param array $config
	 * @return $this
	 */
	public function config(array $config = array())
    {
        if(isset($config['data']))
            $this->return_other_data = $config['data'];

        if ((isset($config) AND empty($config)) OR empty($config['methods']))
            $this->_allow_methods(array('GET'));
        else
            $this->_allow_methods($config['methods']);

		return $this;
    }

	/**
	 * Allow Methods
	 * -------------------------------------
	 * @param array $methods
	 * @return bool|void
	 */
    public function _allow_methods(array $methods)
    {
        $REQUEST_METHOD = $this->CI->input->server('REQUEST_METHOD', TRUE);

        if (in_array(strtolower($REQUEST_METHOD), $this->allowed_http_methods))
        {
            if (in_array(strtolower($REQUEST_METHOD), $methods) OR in_array(strtoupper($REQUEST_METHOD), $methods))
              return true; // allow request method
            else
              $this->_response(array('status' => FALSE, 'error' => 'Método não permitido.', 'request' => $REQUEST_METHOD), self::HTTP_METHOD_NOT_ALLOWED); // not allow request method
        }
		$this->_response(array('status' => FALSE, 'error' => 'Método não conhecido', 'request' => $REQUEST_METHOD), self::HTTP_METHOD_NOT_ALLOWED);
	}

    /**
	 * Private Response Function
	 */
	private function _response($data = NULL, $http_code = NULL)
	{
		ob_start();
		header("Access-Control-Allow-Origin: *");
		header('content-type:application/json; charset=UTF-8');
		header($this->hedarStatus[$http_code], true, $http_code);

		if (!is_array($this->return_other_data))
			print_r(json_encode(array('status' => false, 'error' => 'Formato de dados inválido')));
		else
			print_r(json_encode(array_merge($data, $this->return_other_data)));
		ob_end_flush();
		die();
	}

    /**
     * Public Response Function
     */
    public function response($data = NULL, $http_code = NULL)
    {
        ob_start();
		header("Access-Control-Allow-Origin: *");
        header('content-type:application/json; charset=UTF-8');
        header($this->hedarStatus[$http_code], true, $http_code);
        print_r(json_encode($data));
        ob_end_flush();
    }

	public function get($endpoint){
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $endpoint,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response, true);
	}

    public function index(){
        $this->load->view('Home/index');
    }

}
