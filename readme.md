# Desafio

* ``PHP 7.3``
* ``CodeIgniter 3``
* ``MySQL``

## Banco de Dados
* ``SQL``: /documentação/bd.sql

* ``Diagrama ER``:  /documentação/Diagrama ER.jpeg

## Demonstração Online
        BASE URL: [http://65.21.123.125/desafio/](http://65.21.123.125/desafio/)

## Documentação API
`POST` **/conta/cadastrar**

    Realiza o cadastro de uma conta.
* ``nome``  :  _**string**_
* ``cpf`` : _**numeric**_

---

`GET` **/conta/listar/{id_conta}**

    Exibe as informações de uma conta.
* ``id_conta`` : _**numeric**_
---

`POST` **/deposito**

    Realiza a operação de deposito em uma conta informada.
* ``conta`` : _**numeric**_
* ``valor`` : _**decimal**_  `(format: 00000.00)`
* ``moeda`` : _**string**_ `(format: XXX)`

---

`POST` **/saque**

    Realiza a operação de saque em uma conta informada.
* ``conta`` : _**numeric**_
* ``valor`` : _**decimal**_  `(format: 00000.00)`
* ``moeda`` : _**string**_ `(format: XXX)`

---

`GET` **/saldo/{id_conta}**

    Exibe os saldos em uma conta informada.
* `id_conta` : _**numeric**_

---

`GET` **/saldo/{id_conta}/{moeda}**

    Exibe o saldo de uma moeda especificada de uma conta informada.
* `id_conta` : _**numeric**_
* ``moeda`` : _**string**_ `(format: XXX)`

---

`GET` **/extrato/{id_conta}**

    Exibe o extrato de uma conta informada.
* `id_conta` : _**numeric**_
---

`GET` **/extrato/{id_conta}/{data_inicial}/{data_final}**

    Exibe o extrato de uma conta informada em um determinado período.
* `id_conta` : _**numeric**_
* `data_incial` : _**date**_ `(format: dd-mm-yyy)`
* `data_final` :  _**date**_ `(format: dd-mm-yyy)`