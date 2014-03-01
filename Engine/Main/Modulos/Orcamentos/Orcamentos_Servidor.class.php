<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();
/**
 * @package  :Orcamentos
 * @name     :Manutencao de Orcamentos
 * @class    :Orcamentos_Servidor.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :16/01/2012
 * @Diretorio:Main/Modulos/Orcamentos/
 * Classe Responsavel pela Manutencao de Orcamentos

 * Regra para Criar um Id Unico Para o Orcamento
 * ano 2 digitos, mes 2 digitos, dia 2 digitos, cod_usuario com 4 digitos, total de orcamentos + 1 obs: minimo de 6 digitos no total podendo chegar a no maximo 20 digitos
 * data, usuario, total
 * [yymmdd][0000][000000]
 * Status:
    0 - Aberto     [Azul]
    1 - Fechado    [Verde]
    2 - Cancelado  [Vermelho]
    3 - À Separar  [Laranja]
    4 - Separando [ Azul Claro ]
    5 - Aguardando Pagamento [ Verde ]
    6 - Entregue [Azul]
    7 - Finalizado [Cinza]


 * Status_Servidor:
    0 - A Enviar
    1 - Enviado
    2 - Recebido

 * Finalizadoras
    1 - Dinheiro A Vista
    2 - Cartao de Credito
    3 - Cheque

 * NFE - Deseja Nota Fiscal Eletronica
    0 - Nao
    1 - Sim

 * Erros
    5006001 - Erro no Insert de Orcamento
    5006002 - Cliente Não Encontrado
    5006003 - Falha na hora de gravar o produto no orcamento
    5006004 - Falha ao Carregar o Orcamento pelo Id
    5006005 - Falha ao Finalizar o Orcamento
    5006006 - Falha ao Excluir Registro
    5006007 - Nao e possivel inserir produto por que nao tem orcamento aberto
    5006008 - Falha ao Cancelar o Orcamento
    5006008 - Falha ao Excluir Produto do Orcamento
    5006009 - Falha ao Alterar Observacoes dos produtos
    5006009 - Falha ao Alterar Observacoes do pedido
    5006010 - Falha ao Alterar Status do pedido
*/

class Orcamentos_Servidor {

    public    $return_json   = TRUE;

    protected $pk_orcamento  = 0;
    protected $fk_id_cliente = 0;
    protected $fk_id_usuario = 0;
    protected $qtd_itens     = 0.0;
    protected $valor_total   = 0.0;
    protected $finalizadora  = '';
    protected $parcelamento  = '';
    protected $nfe           = '';
    protected $valor_pagar   = 0.0;
    protected $status        = '';
    protected $status_servidor= '';
    protected $dt_inclusao   = '';
    protected $dt_envio      = '';
    protected $dt_entrega    = '';
    protected $observacao    = '';

    protected $session_pk_orcamento = 0;
    protected $fk_id_produto = 0;
    protected $quantidade    = 0.0;
    protected $preco_item    = 0.0;
    protected $valor_total_item = 0.0;
    protected $identificacao_cliente = '';
    protected $pk_orcamento_produto = '';
    protected $alteracao_qtd = '';
    protected $fk_orcamento = '';
    protected $observacao_produto = '';

    protected $tipo_pdf = 1;

    private $path_emporium = "/home/atacado/PosRcv/";

    // Acessores
    public function setReturnJson($value){
        $this->return_json = $value;
    }

    /**
    * Overloading.
    *
    * Esse método não é chamado diretamente. Ele irá interceptar chamadas
    * a métodos não definidos na classe. Se for um set* ou get* irá realizar
    * as ações necessárias sobre as propriedades da classe.
    * OBS: Os SETs foram comentados para que possa haver validacao independente
    *       Ficando assim so os GETs
    * @param string $metodo O nome do método quer será chamado
    * @param array $parametros Parâmetros que serão passados aos métodos
    * @return mixed
    */

    public function __call ($metodo, $parametros) {
        //se for set*, "seta" um valor para a propriedade
        if (substr($metodo, 0, 3) == 'set') {
          $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
          $this->$var = $parametros[0];
        }
        // se for get*, retorna o valor da propriedade
        if (substr($metodo, 0, 3) == 'get') {
        $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
        return $this->$var;
        }
    }

    public function __construct(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->setPkOrcamento("{$_REQUEST['pk_orcamento']}");

        $this->setFkIdCliente($_REQUEST['fk_id_cliente']);
        $this->setFkIdUsuario($_SESSION["id_Usuario"]);
        $this->setQtdItens($_REQUEST['qtd_itens']);
        $this->setValorTotal($_REQUEST['valor_total']);
        $this->setFinalizadora($_REQUEST['finalizadora']);
        $this->setParcelamento($_REQUEST['parcelamento']);
        $this->setNfe($_REQUEST['nfe']);
        $this->setValorPagar($_REQUEST['valor_pagar']);
        $this->setStatus($_REQUEST['status']);
        $this->setStatusServidor($_REQUEST['status_servidor']);
        $this->setDtInclusao($_REQUEST['dt_inclusao']);
        $this->setObservacao($_REQUEST['observacao']);

        $this->setSessionPkOrcamento($_SESSION["pk_orcamento"]);
        $this->setFkIdProduto($_REQUEST['pk_id_produto']);
        $this->setQuantidade($_REQUEST['quantidade_venda']);
        $this->setPrecoItem($_REQUEST['preco']);
        $this->setValorTotalItem($_REQUEST['valor_total_item']);
        $this->setIdentificacaoCliente($_REQUEST['identificacao_cliente']);

        $this->setPkOrcamentoProduto($_REQUEST['pk_orcamento_produto']);
        $this->setAlteracaoQtd($_REQUEST['alteracao_qtd']);
        $this->setFkOrcamento("{$_REQUEST['fk_orcamento']}");
        $this->setObservacaoProduto("{$_REQUEST['observacao_produto']}");

        $this->setTipoPdf("{$_REQUEST['tipo_pdf']}");
    }


    public function CriaOrcamento(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ CriaOrcamento ]");

        // Saber se O codigo do Cliente
        $this->fk_id_cliente = Clientes::Verifica_Cliente($this->fk_id_cliente, $this->identificacao_cliente);

        // 1 Passo - Verifica o Cliente
        if ($this->fk_id_cliente){

            // 2 Passo - Crio o Codigo do Orcamento
            $data = date('dmy');
            $cod_usuario = str_pad($this->fk_id_usuario, 3, "0", STR_PAD_LEFT);
            $total = ($this->getTotalRegistros() + 1);
            //$total = str_pad($total, 5, "0", STR_PAD_LEFT);

            $id = $data.$cod_usuario.$total;
            $id = $cod_usuario.$total;
            $this->setPkOrcamento($id);

            // 3 Passo - Crio o Orcamento
            $this->Insert_Orcamento();

        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "O Cliente Informado não Foi Encontrado, Verifique o se o Código está Correto...";
            $aResult['code'] = "5006002";
            die(json_encode($aResult));
        }

    }

    public function getTotalRegistros(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getTotalRegistros ]");

        $record = new Repository();

        $sql = "SELECT COUNT(*) as total_count FROM tb_orcamentos";
        $result = $record->total_count($sql);

        return $result->total_count;
    }

    public function Insert_Orcamento(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ Insert_Orcamento ]");

        // Status 0 - Aberto
        $record = new Repository();
        $sql_insert = "INSERT INTO tb_orcamentos (`pk_orcamento` ,`fk_id_cliente`, `fk_id_usuario`, `status`, `dt_inclusao`) VALUES ('{$this->pk_orcamento}', '{$this->fk_id_cliente}', '{$this->fk_id_usuario}', 0, NOW())";
        $result = $record->store($sql_insert);

        if ($result){
            // Salvo o Registro
            $record->commit();

            // Como eu crio um id, ignoro o last insert
            $_SESSION['pk_orcamento'] = $this->pk_orcamento;
            $aResult['success'] = "true";
            $aResult['pk_orcamento'] = $this->pk_orcamento;
            die(json_encode($aResult));

        }
        else {
            // nao salva o registro
            $record->rollback();

            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
            $aResult['code'] = "5006001";
            die(json_encode($aResult));
        }
    }

    public function adicionar_produto(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ adicionar_produto ]");

        $record = new Repository();


        if ($this->fk_orcamento){
                // Verifica se Foi Passado o codigo de Orcamento
                $orcamento = $this->fk_orcamento;
        }
        else {
                // Verificar se tem Orcamento Aberto
                $orcamento = $this->getOrcamentoAberto(FALSE);
        }
        if ($orcamento) {

            Log::Msg(3,"Verificando o Status do Orcamento");
            $objOrcamento = $this->getOrcamentoById($orcamento, FALSE);

            if (($objOrcamento->status == 7) || ($objOrcamento->status == 2)){
                Log::Msg(3,"Nao Altera o Produto por que o Status é [ {$objOrcamento->status} ]");

                $aResult['failure'] = "true";
                $aResult['msg']  = "Não Foi Possivel Alterar o Produto, Por que o Pedido está Fechado ou Cancelado...";
                $aResult['code'] = "5006003";
                die(json_encode($aResult));
            }

            Log::Msg(3,"Verificando se o produto ja existe neste orcamento");
            // Verificar se o Produto ja esta nesse orcamento
            // Se Estiver nao incluo de novo, altero a quantidade e o valor total
            $sql_verifica = "SELECT * FROM tb_orcamento_produtos_entregue WHERE fk_orcamento = '$orcamento' AND fk_id_produto = '{$this->fk_id_produto}'";
            $result = $record->load($sql_verifica);

            if($result->count != 0){
                Log::Msg(3,"Produto ja existe neste orcamento!");
                // Produto ja existe nesse orcamento
                // Altero o Produto

                $pk_orcamento_produto = $result->rows[0]->pk_orcamento_produto;
                $qtd_antiga = $result->rows[0]->quantidade;
                $preco = $result->rows[0]->preco;
                $observacao = $result->rows[0]->observacao_produto;

                if ($preco == 0){
                    $preco = Produtos::getPreco($this->fk_id_produto);
                }

                // Testo se e uma alteracao de quantidade
                if ($this->alteracao_qtd == true){
                    // Ignoro a quantidade antiga
                    $valor_total = ($preco * $this->quantidade);
                    Log::Msg(3,"Alteracao de Quantidade, Qtd_antida [ $qtd_antiga ], Qtd_atual [ {$this->quantidade} ], Preco [ $preco ], Valor_Total [ $valor_total ]");
                }
                else {
                    // Acrescento a quantidade ao que ja tinha
                    $qtd = ($qtd_antiga + $this->quantidade);
                    $valor_total = ($preco * $qtd);
                    Log::Msg(3,"Acrescimo de Quantidade, Qtd_antida [ $qtd_antiga ], Qtd_atual [ {$this->quantidade} ], Qtd_Final [ $qtd ] Preco [ $preco ], Valor_Total [ $valor_total ]");

                    $this->quantidade = $qtd;
                }

                if ($this->observacao_produto == ''){
                    $this->observacao_produto = $observacao;
                }

                $sql = "UPDATE tb_orcamento_produtos_entregue SET quantidade = '{$this->quantidade}', valor_total = '$valor_total', `observacao_produto` = '{$this->observacao_produto}' WHERE `pk_orcamento_produto` = '{$pk_orcamento_produto}'";

                $sql_pre_pedido = "UPDATE tb_orcamento_produtos SET quantidade = '{$this->quantidade}', valor_total = '$valor_total', `observacao_produto` = '{$this->observacao_produto}' WHERE `fk_orcamento` = '{$orcamento}' AND fk_id_produto = {$this->fk_id_produto}";
            }
            else {
                Log::Msg(3,"Produto nao existe neste orcamento!");
                // Produto nao existe
                // Adicionando Produto
                // Verificar o Preco
                if ($this->preco_item == 0){
                    $this->preco_item = Produtos::getPreco($this->fk_id_produto);
                    $this->valor_total_item = ($this->preco_item * $this->quantidade);
                }

                $sql = "INSERT INTO tb_orcamento_produtos_entregue (`pk_orcamento_produto` ,`fk_orcamento`, `fk_id_produto`, `quantidade`, `preco`, `valor_total`, `observacao_produto` ) VALUES ('', '$orcamento', '{$this->fk_id_produto}', '{$this->quantidade}', '{$this->preco_item}', '{$this->valor_total_item}', '{$this->observacao_produto}' )";

                $sql_pre_pedido = "INSERT INTO tb_orcamento_produtos (`pk_orcamento_produto` ,`fk_orcamento`, `fk_id_produto`, `quantidade`, `preco`, `valor_total`, `observacao_produto` ) VALUES ('', '$orcamento', '{$this->fk_id_produto}', '{$this->quantidade}', '{$this->preco_item}', '{$this->valor_total_item}', '{$this->observacao_produto}' )";
            }

            $result = $record->store($sql);

            Log::Msg(3,"Orcamento Status [ {$objOrcamento->status} ]");
            // Se o orcamento estiver aberto coloca o produto na tabela de pedido original
            if ($objOrcamento->status == 0){
                $result2 = $record->store($sql_pre_pedido);
            }


            if ($result){
                $record->commit();

                // A Cada Insercao de Produtos Atualizar os Valores Totais
                $informacoes = $this->atualizar_valores_totais();

                $aResult['success'] = "true";
                $aResult['msg'] = "Produto Colocado no Carrinho!";
                $aResult['qtd_itens'] = $informacoes->total_itens;
                $aResult['valor_total'] = $informacoes->valor_total;
                $aResult['nome'] = $informacoes->nome;

                die(json_encode($aResult));
            }
            else {
                $record->rollback();

                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                $aResult['code'] = "5006003";
                die(json_encode($aResult));
            }
        }
        else {
            // Nao tem Orcamento Aberto Retornar erro
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas não foi Possivel inserir o produto,<br> não existe orcamento aberto...";
            $aResult['code'] = "5006007";
            die(json_encode($aResult));
        }
    }


    public function atualizar_valores_totais(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ atualizar_valores ]");

        if ($this->fk_orcamento){
            // Verifica se Foi Passado o codigo de Orcamento
            $orcamento = $this->fk_orcamento;
        }

        if ($orcamento) {
            Log::Msg(3,"Buscando Orcamento [ $orcamento ]");
            $objOrcamento = $this->getOrcamentoById($orcamento, false);

            $record = new Repository();

            // Totalizando Pre Pedido
            Log::Msg(3,"Buscando Pre Pedido");
            $sql = "SELECT COUNT(pk_orcamento_produto) as total_itens, SUM(valor_total) as valor_total FROM `tb_orcamento_produtos` WHERE fk_orcamento = '$orcamento'";
            $result = $record->load($sql);

            // Pre Pedido
            $pre_qtd_itens   = $result->rows[0]->total_itens ? $result->rows[0]->total_itens : 0;
            $pre_valor_total = $result->rows[0]->valor_total ? $result->rows[0]->valor_total : 0;

            $this->finalizadora = $objOrcamento->finalizadora;
            $this->valor_total = $pre_valor_total;
            $pre_valor_a_paga  = $this->getValorAPagar();

            // Totalizando Valores Entregue
            Log::Msg(3,"Buscando Pedido Entregue");
            $sql = "SELECT COUNT(pk_orcamento_produto) as total_itens, SUM(valor_total) as valor_total FROM `tb_orcamento_produtos_entregue` WHERE fk_orcamento = '$orcamento'";
            $result2 = $record->load($sql);


            // Pedido entregue
            $valor_total = $result2->rows[0]->valor_total ? $result2->rows[0]->valor_total : 0;
            $qtd_itens   = $result2->rows[0]->total_itens;

            $this->finalizadora = $objOrcamento->finalizadora;
            $this->valor_total = $valor_total;
            $valor_pago  = $this->getValorAPagar();

            $desconto = $valor_total - $valor_pago;

            if ($result->count != 0 && $result2->count != 0){

                $sql = "UPDATE tb_orcamentos SET qtd_itens_entregue = {$qtd_itens}, valor_total_entrega = {$valor_total}, desconto_final = {$desconto}, valor_pago = {$valor_pago}, qtd_itens = $pre_qtd_itens, valor_total = $pre_valor_total, valor_pagar = $pre_valor_a_paga WHERE pk_orcamento = '$orcamento'";

                $result = $record->store($sql);

                if ($result){
                    $record->commit();

                    $obj = new StdClass();
                    $obj->total_itens = $qtd_itens;
                    $obj->valor_total = number_format($valor_total, 2, '.','.');
                    //$obj->nome   = $this->getNomeClienteOrcamento($orcamento);


                    return $obj;
                }
                else {
                    //Falha no Update nao alterou os valores
                    $record->rollback();

                    return FALSE;
                }
            }
            else {
                Log::Msg(3,"Nao tem Produto para Totalizar");
                return FALSE;
            }
        }
    }


    public function getNomeClienteOrcamento($orcamento){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getNomeClienteOrcamento ]");

        $record = new Repository();
        $sql = "SELECT a.pk_orcamento, b.nome FROM tb_orcamentos a INNER JOIN tb_clientes b ON a.fk_id_cliente = b.pk_id_cliente WHERE pk_orcamento = $orcamento";
        $result = $record->load($sql);

        if ($result->count != 0){
            return $result->rows[0]->nome;
        }
    }

    public function getInformacoesOrcamento(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getInformacoesOrcamentos ]");

		$orcamento = $this->getOrcamentoAberto(FALSE);
        if ($orcamento) {
			$record = new Repository();
			$sql = "SELECT a.pk_orcamento, a.qtd_itens, a.valor_total, b.nome FROM tb_orcamentos a INNER JOIN tb_clientes b ON a.fk_id_cliente = b.pk_id_cliente WHERE pk_orcamento = $orcamento";
			$result = $record->load($sql);

			if ($result->count != 0){
                $aResult['success'] = "true";
                $aResult['qtd_itens'] = $result->rows[0]->qtd_itens ? $result->rows[0]->qtd_itens : 0;
                $aResult['valor_total'] = $result->rows[0]->valor_total ? $result->rows[0]->valor_total : '0.00';
				$aResult['nome'] = $result->rows[0]->nome;
				die(json_encode($aResult));
			}
		}
		else {
			// Nao tem Orcamento Aberto Retornar vazio
			$aResult['failure'] = "true";
			die(json_encode($aResult));
		}

    }

    public function getOrcamentos(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getOrcamentos ]");

        $record = new Repository();

        $sql = "SELECT COUNT(pk_orcamento) as total_count FROM tb_orcamentos";
        $count = $record->total_count($sql);

        $sql = "SELECT a.*, b.nome as cliente, c.Nome as vendedor FROM tb_orcamentos a INNER JOIN  tb_clientes b ON a.fk_id_cliente = b.pk_id_cliente INNER JOIN usuarios c ON c.id_usuario = a.fk_id_usuario";
        $results = $record->load($sql);

       if ($results->count != 0) {
			$arr_orcamentos = $this->trata_retorno_orcamentos($results->rows);
            $rows = json_encode($arr_orcamentos);
            $result = "{rows:{$rows},totalCount:{$count->total_count}}";
            echo $result;
        }
    }


    public function getOrcamentoById($id = false, $json = true){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getOrcamentoById ]");

        $record = new Repository();

        if ($id){
            $pk_orcamento = $id;
        }
        else {
            $pk_orcamento = $this->pk_orcamento;
        }

        $sql = "SELECT a.*,DATE_FORMAT(a.dt_inclusao,'%d/%m/%Y %H:%i:%s') as dt_pedido, DATE_FORMAT(a.dt_envio,'%d/%m/%Y %H:%i:%s') as dt_envio, DATE_FORMAT(a.dt_entrega,'%d/%m/%Y %H:%i:%s') as dt_entrega, DATE_FORMAT(a.dt_finalizacao,'%d/%m/%Y %H:%i:%s') as dt_finalizacao, b.nome as nome_cliente, c.nome as nome_vendedor FROM tb_orcamentos a INNER JOIN tb_clientes b ON a.fk_id_cliente = b.pk_id_cliente INNER JOIN usuarios c ON a.fk_id_usuario = c.id_usuario WHERE pk_orcamento = {$pk_orcamento}";

        $results = $record->load($sql);

       if ($results->count != 0) {

            if ($json){
                if ($this->return_json) {
                    $arr_orcamentos = $this->trata_retorno_orcamentos($results->rows);
                    echo "{success: true,data:";
                    echo json_encode($arr_orcamentos[0]);
                    echo "}";
                }
                else {
                    return $results->rows[0];
                }
            }
            else {
                return $results->rows[0];
            }
        }
        else {
            if ($json){
                if ($this->return_json) {
                    $aResult['failure'] = "true";
                    $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Carregar o registro...";
                    $aResult['code'] = "5006004";
                    die(json_encode($aResult));
                }
                else {
                    return FALSE;
                }
            }
            else {
                return FALSE;
            }
        }
    }


    public function trata_retorno_orcamentos($arr_orcamentos){
        $array_result = array();
        foreach ($arr_orcamentos as $orcamento){
            // Status
            $orcamento->status_nome = $this->getListaStatus($orcamento->status);
            $orcamento->status_servidor_nome = $this->getListaStatusServidor($orcamento->status_servidor);
            $orcamento->nome_finalizadora = $this->getFormaPagamento($orcamento->finalizadora);
            $orcamento->nome_nfe = $this->getNFe($orcamento->nfe);
            $orcamento->nome_frete_por_conta = $this->getFretePorConta($orcamento->frete_por_conta);

            // Saber se um orcamento e NOVO
            // Marcando o Orcamento Como Novo
            if ($orcamento->status == 3){
                $orcamento->action_novo = 'silk-novo-gif';
            }

            $array_result[] = $orcamento;

        }
        return $array_result;
    }


    public function getOrcamentoProdutos(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getOrcamentoProdutos ]");

        $record = new Repository();

        $sql = "SELECT a.*, b.descricao_curta, b.descricao_longa FROM `tb_orcamento_produtos` a INNER JOIN tb_produtos b ON a.fk_id_produto = b.pk_id_produto WHERE fk_orcamento = {$this->pk_orcamento}";

        $results = $record->load($sql);

       if ($results->count != 0) {
            if ($this->return_json) {
                $arr_results = $this->trata_retorno_orcamento_produtos($results->rows);
                $rows =  json_encode($arr_results);
                echo "{rows:{$rows},totalCount:{$rows}}";
            }
            else {
                return $results->rows;
            }
        }
        else {
            // Nenhum Produto encontrado para o orcamento
            return FALSE;
        }

    }

    public function getOrcamentoProdutos_entregue(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getOrcamentoProdutos ]");

        $record = new Repository();

        $sql = "SELECT a.*, b.descricao_curta, b.descricao_longa FROM `tb_orcamento_produtos_entregue` a INNER JOIN tb_produtos b ON a.fk_id_produto = b.pk_id_produto WHERE fk_orcamento = {$this->pk_orcamento}";

        $results = $record->load($sql);

       if ($results->count != 0) {
            if ($this->return_json) {
                $arr_results = $this->trata_retorno_orcamento_produtos($results->rows);
                $rows =  json_encode($arr_results);
                echo "{rows:{$rows},totalCount:{$rows}}";
            }
            else {
                return $results->rows;
            }
        }
        else {
            // Nenhum Produto encontrado para o orcamento
            return FALSE;
        }

    }

    public function getOrcamentoProdutosObservacoes(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getOrcamentoProdutosObservacoes ]");

        $record = new Repository();

        $sql = "SELECT a.*, b.descricao_curta, b.descricao_longa FROM `tb_orcamento_produtos_entregue` a INNER JOIN tb_produtos b ON a.fk_id_produto = b.pk_id_produto WHERE fk_orcamento = {$this->pk_orcamento} AND quantidade > 0";

        $results = $record->load($sql);

       if ($results->count != 0) {
            if ($this->return_json) {
                $arr_results = $this->trata_retorno_orcamento_produtos($results->rows);
                $rows =  json_encode($arr_results);
                echo "{rows:{$rows},totalCount:{$rows}}";
            }
            else {
                return $results->rows;
            }
        }
        else {
            // Nenhum Produto encontrado para o orcamento
            return FALSE;
        }

    }


    public function trata_retorno_orcamento_produtos($arr){
        $array_result = array();
        foreach ($arr as $obj){

            if ($obj->observacao_produto){
                $obj->hide2   = false;
                $obj->action_observacao = 'silk-comments';
                $obj->qtip_observacao = $obj->observacao_produto;
            }
            else {
                $obj->hide2   = false;
                $obj->action_observacao = 'silk-comment-edit';
                $obj->qtip_observacao = 'Click Para Incluir uma Observa&ccedil;&atilde;o a este Registro';
            }
            $array_result[] = $obj;
        }
        return $array_result;
    }

    public function getFinalizadorasOrcamento(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getFinalizadorasOrcamento ]");

        $record = new Repository();

        // Saber o Total do Orcamento
        $sql = "SELECT valor_total FROM tb_orcamentos WHERE pk_orcamento = '{$this->pk_orcamento}'";
        $result = $record->load($sql);
        if ($result->count != 0){
            $this->valor_total = $result->rows[0]->valor_total;

            Log::Msg(2,"Valor_Total [ {$this->valor_total} ], Finalizadora [ {$this->finalizadora} ]");

            //$valor_a_pagar = Orcamentos::getValorAPagar($this->finalizadora, $valor_total);
            $this->valor_pagar = $this->getValorAPagar();

            // Retornar as Formas de Pagamento
            $aFinalizadoras = array();

            $aResult['success']        = "true";
            $aResult['finalizadora']   = $this->finalizadora;
            $aResult['valor_total']    = $this->valor_total;
            $aResult['valor_pagar']    = $this->valor_pagar;

            echo (json_encode($aResult));

        }
        else {
            // Nao Encontrou Valor Total
        }
    }

    public function setValorAPagar(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ setValorAPagar ]");

        $record = new Repository();
        $sql = "SELECT * FROM tb_orcamentos WHERE pk_orcamento = '{$this->pk_orcamento}'";
        $result = $record->load($sql);

        if ($result->count != 0){

            $total_entregue = $result->rows[0]->valor_total_entrega;

            Log::Msg(3,"Valor Total Entregue [ R$ $total_entregue ]");
        }

    }


    public function getValorAPagar(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getValorAPagar ]");

        $valor = $this->valor_total; // valor original

        switch ($this->finalizadora) {
            // Dinheiro
            case 1:
                // 10% Desconto
                $percentual = 10.0 / 100.0; // 10%
                $valor_final = $valor - ($percentual * $valor);

                return $valor_final;
            break;

            // Cartao de Credito
            case 2:
                return $valor;
            break;
            // Cheque
            case 3:
                return $valor;
            break;

            default:
                return $valor;
            break;
        }
    }


    public function cancelar_orcamento(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ cancelar_orcamento ]");

        $record = new Repository();
        $sql = "UPDATE tb_orcamentos SET status = 2, status_servidor = 0 WHERE pk_orcamento = '{$this->pk_orcamento}'";
        $result = $record->store($sql);

        if($result){
            $record->commit();

            // Limpando a Session
            unset($_SESSION["pk_orcamento"]);
            $aResult['success'] = "true";
            $aResult['pk_orcamento']  = $this->pk_orcamento;
            die(json_encode($aResult));
        }
        else {
            // Falha ao Cancelar o Orcamento
            $record->rollback();
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel CANCELAR o Pedido...";
            $aResult['code'] = "5006008";
            die(json_encode($aResult));
        }

    }

    public function delete_Orcametos(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ delete_Orcametos ]");

        $record = new Repository();
        // Desligando AutoCommit
        $record->setCommit(0);

        if (is_array($this->pk_orcamento)) {
            $id = implode(',', $this->pk_orcamento);
        }
        else {
            $id = $this->pk_orcamento;
        }

        $record = new Repository();
        // Desligando AutoCommit
        $record->setCommit(0);

        // Produtos do Orcamento
        $sql = "DELETE FROM tb_orcamento_produtos WHERE fk_orcamento IN ( $id )";
        $result_produtos = $record->delete($sql);

        // Produtos Entregues
        $sql = "DELETE FROM tb_orcamento_produtos_entregue WHERE fk_orcamento IN ( $id )";
        $result_produtos_entregues = $record->delete($sql);


        // Orcamento
        $sql = "DELETE FROM tb_orcamentos WHERE pk_orcamento IN ( $id )";
        $result_orcamento = $record->delete($sql);

        if ($result_orcamento) {
            $record->commit();
            echo "{success: true}";
        }
        else {
            $record->rollback();
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, <b>NÃO</b> foi possivel excluir o registro...";
            $aResult['code'] = "5006006";
            die(json_encode($aResult));
        }
    }

    public function  delete_orcamento_produtos(){

        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ delete_orcamento_produtos ]");

        if (is_array($this->pk_orcamento_produto)) {
            $id = implode(',', $this->pk_orcamento_produto);
        }
        else {
            $id = $this->pk_orcamento_produto;
        }

        $record = new Repository();
        // Desligando AutoCommit
        $record->setCommit(0);

        // Produtos do Orcamento
        $sql = "DELETE FROM tb_orcamento_produtos WHERE pk_orcamento_produto IN ( $id )";
        $result_produtos = $record->delete($sql);

        // Atualizo os Valores do Orcamento
        $this->atualizar_valores_totais();

        if ($result_produtos) {
            // Atualizo os Valores do Orcamento
            $record->commit();
            echo "{success: true}";
        }
        else {
            $record->rollback();
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, <b>NÃO</b> foi possivel excluir o registro...";
            $aResult['code'] = "5006006";
            die(json_encode($aResult));
        }
    }

    /**
     * @Metodo: delete_orcamento_produtos_entregue()
     * Exclui um produto na tabela de podutos entregues
     * e executa o metodo atualizar_valores_totais(),
     * que atualiza a tabela de orcamentos com os valores totais do pedido.
     */
    public function  delete_orcamento_produtos_entregue(){

        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ delete_orcamento_produtos_entregue ]");

        if (is_array($this->pk_orcamento_produto)) {
            $id = implode(',', $this->pk_orcamento_produto);
        }
        else {
            $id = $this->pk_orcamento_produto;
        }

        $record = new Repository();
        // Desligando AutoCommit
        $record->setCommit(0);

        // Produtos do Orcamento
        $sql = "DELETE FROM tb_orcamento_produtos_entregue WHERE pk_orcamento_produto IN ( $id )";
        $result_produtos = $record->delete($sql);

        // Atualizo os Valores do Orcamento
        $this->atualizar_valores_totais();

        if ($result_produtos) {
            // Atualizo os Valores do Orcamento
            $record->commit();
            echo "{success: true}";
        }
        else {
            $record->rollback();
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, <b>NÃO</b> foi possivel excluir o registro...";
            $aResult['code'] = "5006006";
            die(json_encode($aResult));
        }
    }



    public function getStoreStatus($id){

        $i = 0;
        $aStatus[$i] = new StdClass();
        $aStatus[$i]->id     = "0";
        $aStatus[$i]->status = 'Aberto';
        $aStatus[$i]->cor    = '0000FF';
        $i++;
        $aStatus[$i] = new StdClass();
        $aStatus[$i]->id     = "2";
        $aStatus[$i]->status = 'Cancelado';
        $aStatus[$i]->cor    = 'FF0000';
        $i++;
        $aStatus[$i] = new StdClass();
        $aStatus[$i]->id     = "3";
        $aStatus[$i]->status = 'À Separar';
        $aStatus[$i]->cor    = 'FF6600';
        $i++;
        $aStatus[$i] = new StdClass();
        $aStatus[$i]->id     = "4";
        $aStatus[$i]->status = 'Separando';
        $aStatus[$i]->cor    = '00FFFF';
        $i++;
        $aStatus[$i] = new StdClass();
        $aStatus[$i]->id     = "5";
        $aStatus[$i]->status = 'Aguardando Pagamento';
        $aStatus[$i]->cor    = '008000';
        $i++;
        $aStatus[$i] = new StdClass();
        $aStatus[$i]->id     = "6";
        $aStatus[$i]->status = 'Entregue';
        $aStatus[$i]->cor    = '333399';
        $i++;
        $aStatus[$i] = new StdClass();
        $aStatus[$i]->id     = "7";
        $aStatus[$i]->status = 'Finalizado';
        $aStatus[$i]->cor    = '969696';

        if ($id){
            $result = $aStatus[$id];
        }
        else {
            $result = $aStatus;
        }

        if ($this->return_json){
            $rows = json_encode($result);
            $result = "{rows:{$rows}}";
            echo ($result);
        }
        else{
            return $result;
        }

    }

    public function getListaStatusServidor($id){

        $status_servidor = array();
        $status_servidor[0] = "A Enviar";
        $status_servidor[1] = "Enviado";
        $status_servidor[2] = "Recebido";

        return $status_servidor[$id];
    }

    public function getFormaPagamento($id){

        $finalizadora= array();
        $finalizadora[1] = "Dinheiro";
        $finalizadora[2] = "Cartão de Credito";
        $finalizadora[3] = "Cheque";

        return $finalizadora[$id];
    }

	public function getNFe($id){

		$nfe = array();
		$nfe[0] = "NÃO";
		$nfe[1] = "SIM";

		return $nfe[$id];
	}

	public function getFretePorConta($id){

		$frete = array();
		$frete[0] = "Emitente";
		$frete[1] = "Destinatário";

		return $frete[$id];
	}



    /** Method: adicionar_observacao_produto()
     * Adiciona, Altera ou Exclui Observacao para um produto
     */
    public function adicionar_observacao_produto(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ adicionar_observacao_produto ]");

        $record = new Repository();
        $sql = "UPDATE tb_orcamento_produtos_entregue SET observacao_produto = '{$this->observacao_produto}' WHERE pk_orcamento_produto = '{$this->pk_orcamento_produto}'";
        $result = $record->store($sql);

        if($result){
            $record->commit();

            $aResult['success'] = "true";
            $aResult['pk_orcamento_produto'] = $this->pk_orcamento_produto;
            die(json_encode($aResult));
        }
        else {
            $record->rollback();

            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Alterar Observações do Produto...";
            $aResult['code'] = "5006009";
            die(json_encode($aResult));
        }


    }

    /** Method: adicionar_observacao_orcamento()
     * Adiciona, Altera ou Exclui Observacao para um orcamento
     */
    public function adicionar_observacao_orcamento(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ adicionar_observacao_orcamento ]");

        $record = new Repository();
        $sql = "UPDATE tb_orcamentos SET observacao = '{$this->observacao}' WHERE pk_orcamento = '{$this->pk_orcamento}'";
        $result = $record->store($sql);

        if($result){
            $record->commit();

            $aResult['success'] = "true";
            $aResult['pk_orcamento'] = $this->pk_orcamento;
            die(json_encode($aResult));
        }
        else {
            $record->rollback();

            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Alterar Observações do Pedido...";
            $aResult['code'] = "5006010";
            die(json_encode($aResult));
        }
    }

    /** Metodo:getIdsOrcamentosAEnviar()
     * @Param:
     * @Return: Array(pk_orcamento = value)
     * Este Metodo Faz uma Busca por todos os registros que estiverem
     * com o campo status_servidor marcado com 0 - A ENVIAR e
     * com o campo status marcado como 1 - FECHADO
     */

    public function getIdsOrcamentosAEnviar(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getIdsOrcamentosAEnviar ]");

        // Recuperar os Ids dos orcamentos fechados e que nao foram enviados ainda.
        $sql = "SELECT pk_orcamento FROM `tb_orcamentos` WHERE status_servidor = 0 AND status = 1 ORDER BY pk_orcamento DESC LIMIT 1";
        $record = new Repository();
        $results = $record->load($sql);
        if ($this->return_json){
            if ($results->count != 0) {
                $rows = json_encode($results->rows);
                $result = "{rows:{$rows},totalCount:{$count->total_count}}";
                echo $result;
            }
            else {
                $aResult['failure'] = "true";
                $aResult['msg']  = "Nenhum Registro Encontrado!";
                die(json_encode($aResult));
            }
        }
        else {
            if ($results->count != 0) {
                return $results->rows;
            }
            else {
                return false;
            }
        }
    }



    /** Metodo:getOrcamentoCompletoById()
     * @Param:
     * @Return: Objeto Orcamento + Objeto Orcamentos_Produtos
     * Este Metodo Faz uma Consulta e retorna um Objeto Orcamento, contendo todas as informacoes
     * do Orcamento incluindo os produtos
     */
    public function getOrcamentoCompletoById(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ getOrcamentoCompletoById ]");

        $orcamento = $this->getOrcamentoById();

        $orcamento_produtos = $this->getOrcamentoProdutos();

        $orcamento->produtos = $orcamento_produtos;

        if ($orcamento){
            return $orcamento;
        }
        else {
            return false;
        }
    }

   /** Metodo:importOrcamento()
     * @Param:
     * @Return: pk_orcamento ou false
     * Este Metodo Faz um insert usando todos os atributos,
     * usado quando ja existe um orcamento completo e finalizado,
     */
    public function importOrcamento(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ importOrcamento ]");

        $record = new Repository();


        // Marcado Como A Separar
        $this->status  = 3;

        // Marcar Como Recebido
        $this->status_servidor = 2;

        $sql_insert = "INSERT INTO `cronos`.`tb_orcamentos` (`pk_orcamento`, `fk_id_cliente`, `fk_id_usuario`, `qtd_itens`, `qtd_itens_entregue`, `valor_total`, `valor_total_entrega`, `valor_pagar`, `valor_pago`, `desconto`, `desconto_final`, `finalizadora`, `parcelamento`, `nfe`, `frete_por_conta`, `status`, `status_servidor`, `dt_inclusao`, `dt_envio`, `observacao`) VALUES ('{$this->pk_orcamento}', '{$this->fk_id_cliente}', {$this->fk_id_usuario}, '{$this->qtd_itens}', '{$this->qtd_itens}', '{$this->valor_total}', '{$this->valor_total}', '{$this->valor_pagar}', '{$this->valor_pagar}', '{$this->desconto}', '{$this->desconto}', '{$this->finalizadora}', '{$this->parcelamento}', '{$this->nfe}', '{$this->frete_por_conta}', '{$this->status}', '{$this->status_servidor}', '{$this->dt_inclusao}', NOW(), '{$this->observacao}');";

        $result = $record->store($sql_insert);

        if ($result){
            // Salvo o Registro
            $record->commit();

            if ($this->return_json) {
                $aResult['success'] = "true";
                $aResult['pk_orcamento'] = $this->pk_orcamento;
                die(json_encode($aResult));
            }
            else {
                return $this->pk_orcamento;
            }
        }
        else {
            // nao salva o registro
            $record->rollback();
            if ($this->return_json) {
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                $aResult['code'] = "5006001";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }


   /** Metodo:importOrcamentoProdutos()
     * @Param:
     * @Return: bool
     * Este Metodo Faz um insert usando todos os atributos referentes aos produtos,
     * usado quando ja existe um orcamento completo e finalizado, para importar os produtos,
     * um por vez
     * tambem isere uma copia do produto na tabela de produtos entregue para que essa possa ser
     * editada.
     */
    public function importOrcamentoProdutos(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ importOrcamentoProdutos ]");

        $record = new Repository();

        $sql_insert = "INSERT INTO `cronos`.`tb_orcamento_produtos` (`pk_orcamento_produto` ,`fk_orcamento`, `fk_id_produto`, `quantidade`, `preco`, `valor_total`, `observacao_produto`) VALUES ('', '{$this->pk_orcamento}', {$this->fk_id_produto}, {$this->quantidade}, {$this->preco_item}, {$this->valor_total_item}, '{$this->observacao_produto}');";

        $result = $record->store($sql_insert);

        $sql_insert2 = "INSERT INTO `cronos`.`tb_orcamento_produtos_entregue` (`pk_orcamento_produto` ,`fk_orcamento`, `fk_id_produto`, `quantidade`, `preco`, `valor_total`, `observacao_produto`) VALUES ('', '{$this->pk_orcamento}', {$this->fk_id_produto}, {$this->quantidade}, {$this->preco_item}, {$this->valor_total_item}, '{$this->observacao_produto}');";
        $result2 = $record->store($sql_insert2);


        if ($result AND $result2){
            // Salvo o Registro
            $record->commit();

            if ($this->return_json) {
                $aResult['success'] = "true";
                $aResult['pk_orcamento_produtos'] = $result;
                die(json_encode($aResult));
            }
            else {
                return TRUE;
            }
        }
        else {
            // nao salva o registro
            $record->rollback();
            if ($this->return_json) {
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                $aResult['code'] = "5006003";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }

    /**Metodo: AlterarStatus()
     * @param:pk_orcamento: chave do orcamento para trocar o status
     * @param:status_servidor: codigo do status do orcamento no servidor
     * Chamado toda vez que se altera o status do pedido, recebe o status,
     * faz update no orcamento, e dependendo do status faz algum tratamento
     */
    public function AlterarStatus(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ AlterarStatus() ]");

        //echo "alterar Status";
        $pk_orcamento = $this->pk_orcamento;
        $status = $this->status;

        $sql = "UPDATE tb_orcamentos set status = $status WHERE pk_orcamento = $pk_orcamento";

        // Tratamentos pelo status
        switch ($status){

            case 2:
            break;

            case 3:
            break;

            case 4:
            break;

            case 5:
            break;

            case 6:
                // Entregue
                $sql = "UPDATE tb_orcamentos set status = $status, dt_entrega = NOW() WHERE pk_orcamento = $pk_orcamento";
            break;

            case 7:
                // Finalizacao
                $sql = "UPDATE tb_orcamentos set status = $status, dt_finalizacao = NOW() WHERE pk_orcamento = $pk_orcamento";
            break;
        }

        $record = new Repository();

        $result = $record->store($sql);
/*        var_dump($result);
        var_dump($this->return_json);*/
        if ($result){
            if ($this->return_json) {
                $aResult['success'] = "true";
                die(json_encode($aResult));
            }
            else {
                return TRUE;
            }
        }
        else {
            if ($this->return_json) {
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Alterar o Status do Pedido...";
                $aResult['code'] = "5006010";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }


    public function Gera_Obj_Pdf_Orcamento(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ Gera_Pdf_Orcamento ]");

        $record = new Repository();

        $aObjs = array();
        $aObjs['emitente']     = '';
        $aObjs['orcamento']    = '';
        $aObjs['produtos']     = '';
        $aObjs['destinatario'] = '';

        $destinatario = '';

        // Passo 1 - Informacoes do Emitente
        $sql = "SELECT * FROM tb_lojas WHERE id = 0";
        $result = $record->load($sql);
        if ($result->count != 0){

            // CNPJ
            $cnpj = Common::format_string($result->rows[0]->cnpj, "##.###.###/####-##");
            $result->rows[0]->cnpj = $cnpj;
            // CEP
            $result->rows[0]->cep = Common::format_string($result->rows[0]->cep, "##.###-###");
            // TEL
            $tel = $result->rows[0]->telefone_fixo;
            if (strlen($tel) == 8){
                $result->rows[0]->telefone_fixo = Common::format_string($result->rows[0]->telefone_fixo, "####-####");
            }
            else {
                $result->rows[0]->telefone_fixo = Common::format_string($result->rows[0]->telefone_fixo, "(##) ####-####");
            }

            $aObjs['emitente'] = $result->rows[0];
        }


        // Passo 2 - Informacoes do Orcamento
        $sql = "SELECT a.*, DATE_FORMAT(a.dt_inclusao,'%d-%m-%Y') as data_emissao, b.Nome as vendedor FROM tb_orcamentos a INNER JOIN usuarios b ON a.fk_id_usuario = b.id_usuario WHERE pk_orcamento = {$this->pk_orcamento} LIMIT 1";
        $result = $record->load($sql);


        if ($result->count != 0){

            $destinatario = $result->rows[0]->fk_id_cliente;

            $result->rows[0]->forma_pagamento = $this->getFormaPagamento($result->rows[0]->finalizadora);
            $result->rows[0]->observacao = Common::Adiciona_Quebra_linha($result->rows[0]->observacao, 100);

            switch ($this->tipo_pdf) {
                case 1:
                    $result->rows[0]->valor_total = number_format($result->rows[0]->valor_total, 2, '.','.');
                    $result->rows[0]->valor_pagar = number_format($result->rows[0]->valor_pagar, 2, '.','.');
                    $result->rows[0]->desconto = number_format($result->rows[0]->desconto, 2, '.','.');
                break;

                case 2:
                    $result->rows[0]->valor_total = number_format($result->rows[0]->valor_total_entrega, 2, '.','.');
                    $result->rows[0]->valor_pagar = number_format($result->rows[0]->valor_pago, 2, '.','.');
                    $result->rows[0]->desconto = number_format($result->rows[0]->desconto_final, 2, '.','.');
                break;
            }



            $aObjs['orcamento'] = $result->rows[0];
        }

        // Passo 3 - Informacoes do Destinatario
        #$sql = "SELECT a.*, b.* from tb_clientes a INNER JOIN tb_endereco b ON a.fk_id_endereco = b.id_endereco WHERE pk_id_cliente = {$destinatario}";
        $sql = "SELECT a.*, b.* from tb_clientes a INNER JOIN tb_endereco b ON a.pk_id_cliente = b.id_referencia_pk WHERE pk_id_cliente = {$destinatario}";
        
        Log::Msg(3,"TESTE");
        $result = $record->load($sql);
        if ($result->count != 0){

            // CPF/CNPJ
            if ($result->rows[0]->tipo == 1){
                $cpf = Common::format_string($result->rows[0]->cpf, "###.###.###-##");
                $result->rows[0]->cpf_cnpj = $cpf;
            }
            else {
                $cnpj = Common::format_string($result->rows[0]->cnpj, "##.###.###/####-##");
                $result->rows[0]->cpf_cnpj = $cnpj;
            }

            // Inscricao Estadual / RG
            if ($result->rows[0]->tipo == 1){
                $rg = $result->rows[0]->rg;
                $result->rows[0]->rg_ie = $rg;
            }
            else {
                $ie = Common::format_string($result->rows[0]->inscricao_estadual, "##.###.##-##");
                $result->rows[0]->rg_ie = $ie;
            }


            // CEP
            $result->rows[0]->cep = Common::format_string($result->rows[0]->cep, "##.###-###");
            // TEL
            $tel = $result->rows[0]->telefone_fixo;
            if (strlen($tel) <= 8){
                $result->rows[0]->telefone_fixo = Common::format_string($result->rows[0]->telefone_fixo, "(21) ####-####");
            }
                        else if (strlen($tel) == 9){
                                $result->rows[0]->telefone_fixo = "(21) ". $result->rows[0]->telefone_fixo;
                        }
            else {
                $result->rows[0]->telefone_fixo = Common::format_string($result->rows[0]->telefone_fixo, "(##) ####-####");
            }

            $aObjs['destinatario'] = $result->rows[0];
        }

        // Passo 4 - Informacoes dos Produtos
        switch ($this->tipo_pdf) {
            case 1:
                // Pre Pedido
                $sql = "SELECT a.*, b.descricao_curta, b.descricao_longa, c.* FROM `tb_orcamento_produtos` a INNER JOIN tb_produtos b ON a.fk_id_produto = b.pk_id_produto LEFT JOIN tb_produtos_categoria c ON b.fk_id_categoria = c.pk_id_categoria WHERE fk_orcamento = {$this->pk_orcamento} AND a.quantidade > 0 ORDER BY c.pk_id_categoria;";
                $result = $record->load($sql);
            break;

            case 2:
                // Pedido

                $sql = "SELECT a.*, b.descricao_curta, b.descricao_longa, c.* FROM `tb_orcamento_produtos_entregue` a INNER JOIN tb_produtos b ON a.fk_id_produto = b.pk_id_produto LEFT JOIN tb_produtos_categoria c ON b.fk_id_categoria = c.pk_id_categoria WHERE fk_orcamento = {$this->pk_orcamento} AND a.quantidade > 0 ORDER BY c.pk_id_categoria;";
                $result = $record->load($sql);
            break;
        }

        if ($result->count != 0){

            foreach ($result->rows as $row) {
                $objresult = null;
                $objresult = $row;

                $objresult->desconto = number_format($row->desconto, 2, ',','.');
                $objresult->preco = number_format($row->preco, 2, ',','.');
                $objresult->valor_total = number_format($row->valor_total, 2, ',','.');

                // Tratamento de cor por categoria
                if ($row->codigo_cor){
                    $objresult->cod_rgb = Common::hex2rgb($row->codigo_cor);
                }
                else {
                    // Se nao Tiver Cor retorna cor preta
                    $objresult->cod_rgb = array( 'Red' => 0, 'Green' => 0, 'Blue' => 0 );
                }
                $aObjsResults[] = $objresult;
            }
            $aObjs['produtos'] = $aObjsResults;
        }

        return $aObjs;
    }



    public function Gera_Pedido_Pdv(){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ Gera_Pedido_Pdv ]");

        $record = new Repository();

        // Informacoes do Orcamento
        $sql = "SELECT a.*, DATE_FORMAT(a.dt_inclusao,'%d-%m-%Y') as data_emissao, b.Nome as vendedor FROM tb_orcamentos a INNER JOIN usuarios b ON a.fk_id_usuario = b.id_usuario WHERE pk_orcamento = {$this->pk_orcamento} LIMIT 1";

        $result = $record->load($sql);
        if ($result->count != 0){

            $result->rows[0]->forma_pagamento = $this->getFormaPagamento($result->rows[0]->finalizadora);
            $result->rows[0]->valor_total = number_format($result->rows[0]->valor_total_entrega, 2, '.','.');
            $result->rows[0]->valor_pagar = number_format($result->rows[0]->valor_pago, 2, '.','.');
            $result->rows[0]->desconto = number_format($result->rows[0]->desconto_final, 2, '.','.');

            $objOrcamento = $result->rows[0];
        }

        $sql = "SELECT a.*, b.descricao_curta, b.descricao_longa, b.fk_id_categoria, c.* FROM `tb_orcamento_produtos_entregue` a INNER JOIN tb_produtos b ON a.fk_id_produto = b.pk_id_produto INNER JOIN tb_produtos_categoria c ON b.fk_id_categoria = c.pk_id_categoria WHERE fk_orcamento = {$this->pk_orcamento} AND a.quantidade > 0 ORDER BY c.pk_id_categoria;";



        $result = $record->load($sql);

        if ($result->count != 0){
            $seq = 1;
            foreach ($result->rows as $row) {
                $objresult = null;
                $objresult->sequencia = $seq;
                $objresult->codigo = $row->fk_id_produto;
                $objresult->descricao = $row->descricao_curta;
                $objresult->valor_unitario = number_format($row->preco, 2, '.','.');
                $objresult->valor_total    = number_format($row->valor_total, 2, '.','.');
                $objresult->quantidade   = $row->quantidade;
                $objresult->subtotal     = number_format($row->valor_total, 2, '.','.');
                $objresult->departamento = $row->fk_id_categoria;

                $aObjsProdutos[] = $objresult;

                $seq++;
            }
        }

        // Criando Objeto Cliente
        $objCliente = new StdClass();
        $objCliente->id = '1:1';
        $objCliente->price = '1';

        // Criando Objeto Subtotal
        $objSubtotal = new StdClass();
        $objSubtotal->subtotal = number_format($objOrcamento->valor_total, 2, '.','.');
        $objSubtotal->total = number_format($objOrcamento->valor_pagar, 2, '.','.');

        // Criando Objeto Para o XML
        $objXml = new StdClass();
        $objXml->loja = Common::getParametro("emporium_pedido_store_key");
        $objXml->pdv  = Common::getParametro("emporium_pedido_pos_id");
        $objXml->ticket = 1068;
        $objXml->orcamento = $objOrcamento->pk_orcamento;
        $objXml->fiscal_store = Common::getParametro("emporium_pedido_store_key");
        $objXml->fiscal_pos = Common::getParametro("emporium_pedido_pos_id");
        $objXml->operador = 999;
        $objXml->operador_nome = 'Venda Externa';
        $objXml->data = date('Ymd');
        $objXml->data_hora = date('YmdHis');
        $objXml->session_sequence = 1;
        $objXml->cliente[] = $objCliente;
        $objXml->produtos = $aObjsProdutos;
        $objXml->subtotal[] = $objSubtotal;


        $this->Gerar_Xml_Pdv($objXml);
    }


    public function Gerar_Xml_Pdv($objXml){
        Log::Msg(2,"Class[ Orcamentos_Servidor ] Method[ Gera_Xml_Pdv ]");

        //var_dump($objXml);
        // Recuperando o Caminho da Pasta Compartilhada
        $this->path_emporium = Common::getParametro("emporium_path_rcv");



        # Instancia do objeto XMLWriter
        $xml = new XMLWriter;

        # Cria memoria para armazenar a saida
        $xml->openMemory();

        # Inicia o cabeçalho do documento XML
        $xml->startDocument( '1.0' , 'iso-8859-1' );

        # Adiciona/Inicia um Elemento / Nó Pai <item>
        $xml->startElement("REQUEST");
            $xml->writeElement("STORE", $objXml->loja);
            $xml->writeElement("POS", $objXml->pdv);
            $xml->writeElement("TICKET", $objXml->ticket);
            $xml->writeElement("TRN", $objXml->orcamento);
            $xml->writeElement("FISCAL_STORE", $objXml->fiscal_store);
            $xml->writeElement("FISCAL_POS", $objXml->fiscal_pos);
            $xml->writeElement("CASHIER_ID", $objXml->operador);
            $xml->writeElement("CASHIER_NAME", $objXml->operador_nome);
            $xml->writeElement("FISCAL_DAY", $objXml->data);
            $xml->writeElement("FISCAL_TIME", $objXml->data_hora);
            $xml->writeElement("SESSION_SEQUENCE", $objXml->session_sequence);


            $xml->startElement("CUSTOMER");
                $xml->writeElement("ID", $objXml->cliente[0]->id);
                $xml->writeElement("PRICE", $objXml->cliente[0]->price);
            $xml->endElement();

            // Criando os Itens
            foreach ($objXml->produtos as $item){

                $xml->startElement("ITEM");
                    $xml->writeElement("SEQ", $item->sequencia);
                    $xml->writeElement("ID", $item->codigo);
                    $xml->writeElement("DESCRIPTION", $item->descricao);
                    $xml->writeElement("UNIT_PRICE", $item->valor_unitario);
                    $xml->writeElement("AMOUNT", $item->valor_total);
                    $xml->writeElement("QTY", $item->quantidade);
                    $xml->writeElement("SUBTOTAL", $item->subtotal);
                    $xml->writeElement("DEPT_ID", $item->departamento);
                $xml->endElement();

            }

            // Subtotal
            $xml->writeElement("SUBTOTAL", $objXml->subtotal[0]->subtotal);
            $xml->writeElement("DUE", $objXml->subtotal[0]->total);


        $xml->endElement();

        #  Configura a saida do conteúdo para o formato XML
        #header( 'Content-type: text/xml' );
        # Imprime os dados armazenados
        #print $xml->outputMemory(true);

        $dir_work = Common::Verifica_Diretorio_Work();
        $file_name = "Pedido_{$objXml->orcamento}.xml";
        $file_work = $dir_work . $file_name;
        $file_emporium = $this->path_emporium . $file_name;

        # Salvando o arquivo em disco
        # retorna erro se o header foi definido
        # retorna erro se outputMemory já foi chamado
        $file = fopen($file_work,'w+');
        fwrite($file,$xml->outputMemory(true));
        fclose($file);

        Log::Msg(3,"Arquivo Local Criado [ $file_work ]");


        $comando = "chmod 777 $file_work";
        Log::Msg(3,"Comando [ $comando ]");
        $verbose = shell_exec($comando);


        $result = new StdClass();

        Log::Msg(3,"Tentando Copiar Para Pasta Compartilhada [ $file_work -> {$file_emporium} ]");
 

        $comando = "cp -rf $file_work {$this->path_emporium}";
        Log::Msg(3,"Comando [ $comando ]");
        $verbose = shell_exec($comando);

        if (file_exists($file_emporium)){
            Log::Msg(3,"Arquivo Enviado com Sucesso!");
            $result->success =  true;
        }
        else {

            $comando = "sudo /var/cronos/Engine/Main/Bin/move_file.sh $file_work $file_emporium";
            Log::Msg(3,"Comando [ $comando ]");
            $verbose = shell_exec($comando);

            if (file_exists($file_emporium)){
                $result->success =  true;
            }
            else {
                $result->failure =  true;
                $result->msg = "Falha ao Enviar o Pedido para o Servidor Emporium";
            }
        }

//         $result->success =  true;
        echo json_encode($result);
    }
}

?>
