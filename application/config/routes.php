<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'API';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['conta/cadastrar'] = 'Conta/cadastrar';
$route['conta/listar'] = 'Conta/consultarPorId';
$route['conta/listar/(:num)'] = 'Conta/consultarPorId/$1';

$route['deposito'] = 'Deposito/cadastrar';

$route['saque'] = 'Saque/cadastrar';

$route['saldo/(:num)'] = 'Saldo/getSaldoDaConta/$1';
$route['saldo/(:num)/(:any)'] = 'Saldo/getSaldoDaContaPorMoeda/$1/$2';

$route['extrato/(:num)'] = 'Extrato/getExtratoPorConta/$1';
$route['extrato/(:num)/(:any)'] = 'Extrato/getExtratoPorConta/$1/$2';
$route['extrato/(:num)/(:any)/(:any)'] = 'Extrato/getExtratoPorConta/$1/$2/$3';
