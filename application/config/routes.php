<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'API';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['cliente/cadastrar'] = 'Cliente/cadastrar';
$route['cliente/listar'] = 'Cliente/listarTodos';
$route['cliente/listar/(:num)'] = 'Cliente/listarPorId/$1';

$route['deposito/novo'] = 'Deposito/cadastrar';

$route['saque/novo'] = 'Saque/cadastrar';

$route['saldo/(:num)'] = 'Saldo/getSaldoDaConta/$1';
$route['saldo/(:num)/(:any)'] = 'Saldo/getSaldoDaContaPorMoeda/$1/$2';

$route['extrato/(:num)'] = 'Extrato/getExtratoPorConta/$1';
$route['extrato/(:num)/(:any)'] = 'Extrato/getExtratoPorConta/$1/$2';
$route['extrato/(:num)/(:any)/(:any)'] = 'Extrato/getExtratoPorConta/$1/$2/$3';
