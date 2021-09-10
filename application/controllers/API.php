<?php defined('BASEPATH') OR exit('No direct script access allowed');

class API extends CI_Controller
{
    /**
     * Lista dos métodos HTTP permitidos
     *
     * @var array
     */
    protected $allowed_http_methods = ['get', 'delete', 'post', 'put', 'options', 'patch', 'head'];

    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_NOT_FOUND = 404;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_OK = 200;
    const HEADER_STATUS_STRINGS = [
        '405' => 'HTTP/1.1 405 Method Not Allowed',
        '400' => 'BAD REQUEST',
        '408' => 'Request Timeout',
        '404' => 'NOT FOUND',
        '401' => 'UNAUTHORIZED',
        '200' => 'OK',
    ];

    /**
     * RETURN DATA
     */
    protected $return_other_data = [];

	/**
	 * @var CI_Controller
	 */
	private $CI;

	/**
	 *
	 */
	public function __construct() {
        parent::__construct();
        $this->CI =& get_instance();

        date_default_timezone_set('America/Sao_Paulo');
    }

	/**
	 * @param array $config
	 * @return $this|array[]
	 */
	public function config(array $config = [])
    {
        // return other data
        if(isset($config['data']))
            $this->return_other_data = $config['data'];

        // by default method `GET`
        if ((isset($config) AND empty($config)) OR empty($config['methods'])) {
            $this->_allow_methods(['GET']);
        } else {
            $this->_allow_methods($config['methods']);
        }

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

        // check request method in `$allowed_http_methods` array()
        if (in_array(strtolower($REQUEST_METHOD), $this->allowed_http_methods))
        {
            // check request method in user define `$methods` array()
            if (in_array(strtolower($REQUEST_METHOD), $methods) OR in_array(strtoupper($REQUEST_METHOD), $methods))
              return true; // allow request method
            else
              $this->_response(['status' => FALSE, 'error' => 'Método não permitido.', 'request' => $REQUEST_METHOD], self::HTTP_METHOD_NOT_ALLOWED); // not allow request method
        }
		$this->_response(['status' => FALSE, 'error' => 'Método não conhecido', 'request' => $REQUEST_METHOD], self::HTTP_METHOD_NOT_ALLOWED);
	}

    /**
     * Check Request Header Exists
     * @return ['status' => true, 'value' => value ]
     */
    private function exists_header($header_name)
    {
        $headers = apache_request_headers();
        foreach ($headers as $header => $value) {
            if($header === $header_name) {
                return ['status' => true, 'value' => $value ];
            }
        }
		return ['status' => false, 'value' => NULL ];
    }

	/**
	 * Private Response Function
	 */
	private function _response($data = NULL, $http_code = NULL)
	{
		ob_start();
		header("Access-Control-Allow-Origin: *");
		header('content-type:application/json; charset=UTF-8');
		header(self::HEADER_STATUS_STRINGS[$http_code], true, $http_code);

		if (!is_array($this->return_other_data)) {
			print_r(json_encode(['status' => false, 'error' => 'Formato de dados inválido']));
		} else {
			print_r(json_encode(array_merge($data, $this->return_other_data)));
		}
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
        header(self::HEADER_STATUS_STRINGS[$http_code], true, $http_code);
        print_r(json_encode($data));
        ob_end_flush();
    }
}
