ALTER DATABASE `cronos` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_orcamentos`
--

CREATE TABLE IF NOT EXISTS `tb_orcamentos` (
  `pk_orcamento` varchar(20) NOT NULL,
  `fk_id_cliente` varchar(20) NOT NULL,
  `fk_id_usuario` int(11) NOT NULL,
  `qtd_itens` float DEFAULT NULL,
  `valor_total` float DEFAULT NULL,
  `valor_pagar` float DEFAULT NULL,
  `desconto` float DEFAULT NULL,
  `finalizadora` varchar(1) DEFAULT NULL COMMENT '1 - Dinheiro, 2- Cartao Credito, 3 - Cheque',
  `parcelamento` varchar(1) DEFAULT NULL,
  `nfe` varchar(1) NOT NULL COMMENT '0 - Nao, 1 - Sim',
  `frete_por_conta` varchar(1) NOT NULL COMMENT '0 - Emitente, 1 - Destinatario',
  `status` int(2) DEFAULT NULL,
  `status_servidor` int(2) DEFAULT NULL,
  `dt_inclusao` datetime NOT NULL,
  `dt_envio` datetime DEFAULT NULL,
  `observacao` varchar(500) DEFAULT NULL,
  UNIQUE KEY `pk_orcamento` (`pk_orcamento`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_orcamento_produtos`
--

CREATE TABLE IF NOT EXISTS `tb_orcamento_produtos` (
  `pk_orcamento_produto` int(11) NOT NULL AUTO_INCREMENT,
  `fk_orcamento` varchar(20) NOT NULL,
  `fk_id_produto` int(11) NOT NULL,
  `quantidade` float NOT NULL,
  `preco` float DEFAULT NULL,
  `valor_total` float DEFAULT NULL,
  `observacao_produto` varchar(255) NOT NULL COMMENT 'Observacoes referentes ao produto',
  PRIMARY KEY (`pk_orcamento_produto`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;


INSERT INTO `cronos`.`Telas` (
`id` ,
`identificacao` ,
`root` ,
`titulo` ,
`icone` ,
`eXtype` ,
`diretorio` ,
`arquivo` ,
`Descricao` ,
`ordem` ,
`desenvolvimento`
)
VALUES (
'29', '7001', '28', 'Pedidos', '', 'e-Orcamentos_Servidor_Grid', 'Main/Modulos/Orcamentos/', 'Orcamentos_Servidor_Grid', 'Tela de Gerencia de Pedidos/Orcamentos', '', ''
);

INSERT INTO `cronos`.`Permissoes` (
`id` ,
`Usuario` ,
`Tela` ,
`sel` ,
`ins` ,
`upd` ,
`del` ,
`imp` ,
`exc`
)
VALUES (
NULL , '1', '28', '1', '1', '1', '1', '1', '1'
);

INSERT INTO `cronos`.`Permissoes` (
`id` ,
`Usuario` ,
`Tela` ,
`sel` ,
`ins` ,
`upd` ,
`del` ,
`imp` ,
`exc`
)
VALUES (
NULL , '1', '29', '1', '1', '1', '1', '1', '1'
);

-- Altera o Nome da Tabela Configuracao para configuracao`
ALTER TABLE Configuracao RENAME configuracao;

UPDATE `cronos`.`configuracao` SET `valor` = '1.20' WHERE `configuracao`.`id_configuracao` =3;
UPDATE `cronos`.`configuracao` SET `valor` = '004' WHERE `configuracao`.`id_configuracao` =6;