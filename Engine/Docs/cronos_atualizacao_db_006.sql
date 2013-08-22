

ALTER TABLE `cronos`.`tb_orcamentos` ADD COLUMN `dt_entrega` DATETIME NULL  AFTER `observacao` ;
ALTER TABLE `cronos`.`tb_orcamentos` ADD COLUMN `dt_finalizacao` DATETIME NULL  AFTER `dt_entrega` ;

-- Controle de Versao
UPDATE `cronos`.`configuracao` SET `valor` = '1.33' WHERE `configuracao`.`id_configuracao` = 3;
UPDATE `cronos`.`configuracao` SET `valor` = '006' WHERE `configuracao`.`id_configuracao`  = 6;