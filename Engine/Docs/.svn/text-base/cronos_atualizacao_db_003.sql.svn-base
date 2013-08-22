ALTER TABLE `tb_clientes` CHANGE `pk_id_cliente` `pk_id_cliente` VARCHAR( 20 ) NOT NULL;

ALTER TABLE `tb_endereco` CHANGE `id_referencia_pk` `id_referencia_pk` VARCHAR( 20 ) NOT NULL COMMENT 'guarda a chave primaria a qual pertence o registro, juntando o campo id_referencia + id_referencia_pk sabemos de qual tabela e qual registro pertence esse endereco' ;

ALTER TABLE `tb_clientes` ADD `status_servidor` INT NULL COMMENT 'Flag que indica se o registro foi exportado ou nao para o servidor. ( 0 - A Enviar | 1 - Enviado )' AFTER `email`;


UPDATE `cronos`.`Configuracao` SET `valor` = '1.7' WHERE `configuracao`.`id_configuracao` =3;
UPDATE `cronos`.`Configuracao` SET `valor` = '003' WHERE `Configuracao`.`id_configuracao` =6;