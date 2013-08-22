
ALTER TABLE `usuarios` ADD `status` INT NULL COMMENT 'Status do Usuario 0 - inativo , 1 - ativo';
update  usuarios set status = 1;
-- Controle de Versao
UPDATE `cronos`.`configuracao` SET `valor` = '1.51' WHERE `configuracao`.`id_configuracao` = 3;
UPDATE `cronos`.`configuracao` SET `valor` = '009' WHERE `configuracao`.`id_configuracao`  = 6;