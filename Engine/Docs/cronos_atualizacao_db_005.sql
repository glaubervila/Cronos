-- Alteracao de Indice na tabela de estoque para que so tenha registro para um produto
ALTER TABLE `cronos`.`tb_produtos_estoque` ADD UNIQUE `fk_id_produto_unique` ( `fk_id_produto` );


--
-- Estrutura da tabela `tb_orcamento_produtos`
--

CREATE TABLE IF NOT EXISTS `tb_orcamento_produtos_entregue` (
  `pk_orcamento_produto` int(11) NOT NULL AUTO_INCREMENT,
  `fk_orcamento` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fk_id_produto` int(11) NOT NULL,
  `quantidade` float NOT NULL,
  `preco` float DEFAULT NULL,
  `valor_total` float DEFAULT NULL,
  `observacao_produto` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Observacoes referentes ao produto',
  PRIMARY KEY (`pk_orcamento_produto`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

-- Criacao dos Campos novos na tb_orcamentos
ALTER TABLE `tb_orcamentos` ADD `valor_total_entrega` DECIMAL( 10, 2 ) NULL AFTER `desconto`;
ALTER TABLE `tb_orcamentos` ADD `valor_pago` DECIMAL( 10, 2 ) NULL AFTER `valor_total_entrega`;
ALTER TABLE `tb_orcamentos` ADD `desconto_final` DECIMAL( 10, 2 ) NULL AFTER `valor_pago`;
ALTER TABLE `tb_orcamentos` ADD `qtd_itens_entregue` FLOAT NULL AFTER `qtd_itens`;

-- Controle de Versao
UPDATE `cronos`.`configuracao` SET `valor` = '1.29' WHERE `configuracao`.`id_configuracao` =3;
UPDATE `cronos`.`configuracao` SET `valor` = '005' WHERE `configuracao`.`id_configuracao`  =6;