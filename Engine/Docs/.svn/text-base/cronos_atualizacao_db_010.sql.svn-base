INSERT INTO `cronos`.`configuracao` (
`id_configuracao` ,
`parametro` ,
`valor` ,
`descricao`
)
VALUES (
NULL , 'cliente_verificar_cpf_cnpj', '1', 'Parametro para permitir cadastro de clientes sem cpf ou cnpj, desabilita a verificação de cpf cnpj. 0 - desabilitada (Permite branco) 1 - habilitado (cpf ou cnpj obrigatorio)'
);


-- Controle de Versao
UPDATE `cronos`.`configuracao` SET `valor` = '1.52' WHERE `configuracao`.`id_configuracao` = 3;
UPDATE `cronos`.`configuracao` SET `valor` = '010' WHERE `configuracao`.`id_configuracao`  = 6;