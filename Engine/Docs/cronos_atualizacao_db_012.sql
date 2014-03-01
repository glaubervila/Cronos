ALTER TABLE `tb_orcamentos` CHANGE `pk_orcamento` `pk_orcamento` INT NULL AUTO_INCREMENT;


-- Controle de Versao
-- UPDATE `cronos`.`configuracao` SET `valor` = '1.52' WHERE `configuracao`.`id_configuracao` = 3;
UPDATE `cronos`.`configuracao` SET `valor` = '012' WHERE `configuracao`.`id_configuracao`  = 6;