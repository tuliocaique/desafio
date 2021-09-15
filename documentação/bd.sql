CREATE SCHEMA `api`;
USE `api`;

CREATE TABLE IF NOT EXISTS `conta` (
	`conta_id` INT(11) AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`conta_titular_nome` VARCHAR(100) NOT NULL,
	`conta_titular_cpf` VARCHAR(11) UNIQUE NOT NULL,
	`conta_cadastrado_em` TIMESTAMP DEFAULT NOW()
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `saque` (
	`saque_id` INT(11) AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`saque_id_conta` INT(11) NOT NULL,
	`saque_valor` DECIMAL(13,4) NOT NULL DEFAULT 0.0,
	`saque_moeda` VARCHAR(3) NOT NULL,
	`saque_realizado_em` TIMESTAMP DEFAULT NOW(),
	FOREIGN KEY (`saque_id_conta`) REFERENCES `conta`(`conta_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `deposito` (
	`deposito_id` INT(11) AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`deposito_id_conta` INT(11) NOT NULL,
	`deposito_valor` DECIMAL(13,4) NOT NULL DEFAULT 0.0,
	`deposito_moeda` VARCHAR(3) NOT NULL,
	`deposito_realizado_em` TIMESTAMP DEFAULT NOW(),
	FOREIGN KEY (`deposito_id_conta`) REFERENCES `conta`(`conta_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `saldo` (
	`saldo_id` INT(11) AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`saldo_id_conta` INT(11) NOT NULL,
	`saldo_valor` DECIMAL(13,4) NOT NULL DEFAULT 0.0,
	`saldo_moeda` VARCHAR(3) NOT NULL,
	`saldo_atualizado_em` TIMESTAMP DEFAULT NOW(),
	FOREIGN KEY (`saldo_id_conta`) REFERENCES `conta`(`conta_id`)
) ENGINE=InnoDB;

CREATE VIEW `extrato` AS
    SELECT 	deposito_id_conta AS id_conta,
           	deposito_valor AS valor,
           	deposito_moeda AS moeda,
           	deposito_realizado_em AS realizado_em,
           	(SELECT "Depósito") as tipo
    FROM	deposito
	UNION
	SELECT 	saque_id_conta AS id_conta,
	    	saque_valor AS valor,
			saque_moeda AS moeda,
			saque_realizado_em AS realizado_em,
			(SELECT "Saque") as tipo
	FROM	saque
    ORDER BY realizado_em DESC;

