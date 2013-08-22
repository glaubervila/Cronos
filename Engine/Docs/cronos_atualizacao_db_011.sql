INSERT INTO `cronos`.`configuracao` (
`id_configuracao` ,
`parametro` ,
`valor` ,
`descricao`
)
VALUES (
NULL , 'chave_vendedores', '3', 'chave primaria do grupo de ususarios (Vendedores)'
);



-- Controle de Versao
UPDATE `cronos`.`configuracao` SET `valor` = '1.52' WHERE `configuracao`.`id_configuracao` = 3;
UPDATE `cronos`.`configuracao` SET `valor` = '011' WHERE `configuracao`.`id_configuracao`  = 6;