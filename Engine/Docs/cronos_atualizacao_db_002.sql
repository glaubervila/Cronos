ALTER TABLE `tb_produtos_categoria` ADD `codigo_cor` VARCHAR( 7 ) NOT NULL COMMENT 'Codigo de Cor Atribuido a Categoria (Ex: #FFCCEE)';
UPDATE `cronos`.`Configuracao` SET `valor` = '002' WHERE `configuracao`.`id_configuracao` =6;
UPDATE `cronos-client`.`configuracao` SET `valor` = '1.10' WHERE `configuracao`.`id_configuracao` =3;
