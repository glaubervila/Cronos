

INSERT INTO `cronos`.`configuracao` (`id_configuracao` , `parametro` , `valor` , `descricao` ) VALUES  (NULL , 'chave_cliente', '1', 'parametro para chave primaria de cliente. 1 - para incremental, 0 para regra' );

ALTER TABLE `tb_clientes` ADD `vendedor` INT NULL COMMENT 'Chave do Vendedor Responsavel pelo cliente';

INSERT INTO `cronos`.`configuracao` (`id_configuracao` ,`parametro` ,`valor` ,`descricao`) VALUES ( NULL , 'emporium_path_rcv', '/home/atacado/PosRcv/', 'Caminho para Pasta compartilhada com emporium, pasta de destino é a RCV emporium' );

INSERT INTO `cronos`.`configuracao` (`id_configuracao` ,`parametro` ,`valor` ,`descricao`) VALUES ( NULL , 'emporium_pedido_store_key', '1', 'Parametro para numero de loja no emporium para pedidos');

INSERT INTO `cronos`.`configuracao` ( `id_configuracao` , `parametro` , `valor` , `descricao` ) VALUES ( NULL , 'emporium_pedido_pos_id', '1', 'Parametro para numero do pdv no emporium para emissao de  pedidos' );

-- Controle de Versao
UPDATE `cronos`.`configuracao` SET `valor` = '1.46' WHERE `configuracao`.`id_configuracao` = 3;
UPDATE `cronos`.`configuracao` SET `valor` = '007' WHERE `configuracao`.`id_configuracao`  = 6;