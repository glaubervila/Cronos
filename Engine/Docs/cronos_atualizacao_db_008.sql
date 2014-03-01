
INSERT INTO `cronos`.`configuracao` (
`id_configuracao` ,
`parametro` ,
`valor` ,
`descricao`
)
VALUES (
NULL , 'emporium_exportacao_cliente_replica', '1', 'Parametro para habilitar a replica de arquivo cvs de cliente para servidor emporium'
);


-- Controle de Versao
UPDATE `cronos`.`configuracao` SET `valor` = '1.46' WHERE `configuracao`.`id_configuracao` = 3;
UPDATE `cronos`.`configuracao` SET `valor` = '008' WHERE `configuracao`.`id_configuracao`  = 6;