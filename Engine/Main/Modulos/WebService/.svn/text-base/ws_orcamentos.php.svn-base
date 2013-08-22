<?php
/**
 * @class   : ws_orcamentos.php
 * @author  : Glauber Costa Vila-Verde
 * @date    : 11/11/2011
 * Classe Responsavel por disponibilizar funcoes relativas a entidade Orcamentos
 * a um servico de WebService
 * @revision:
 */

// Por este arquivo ser executado fora do escopo normal do programa
// troco o diretorio atual pelo diretorio principal da aplicacao (cronos/Engine) onde fica o index
chdir ("../../../");


/**
 * função __autoload()
 *  Carrega uma classe quando ela é instânciada pela primeira vez.
 */
function __autoload($classe) {
    $pastas = array('Main/PHP');
    $modulos = scandir("Main/Modulos");
    foreach ($modulos as $pasta) {
        if ($pasta != "." && $pasta != ".."){
            array_push($pastas, "Main/Modulos/".$pasta);
        }
    }
    foreach ($pastas as $pasta) {
        if (file_exists("{$pasta}/{$classe}.class.php")){
            include_once "{$pasta}/{$classe}.class.php";
            Log::Msg(7, "AUTOLOAD Class[ $classe ] File [ {$pasta}/{$classe}.class.php ]");
        }
    }
}

Log::Msg(3, "INICIANDO WEB SERVICE [ ws_orcamentos ]");

require_once('Main/Modulos/Nusoap/nusoap.php');

// criacao de uma instancia do servidor
$server = new soap_server;

$server->debug_flag = true;

// inicializa o suporte a WSDL

$server->configureWSDL('cronos_webservice.orcamentos','urn:cronos_webservice.orcamentos');
$server->wsdl->schemaTargetNamespace = 'urn:cronos_webservice.orcamentos';



/** @Method: CriaAtualizaCliente
 *  @Param:$tipo
 *  @Param:$tipo
 *  Este Recebe um Array com as Informaçoes do Cliente e realiza as operacoes de insercao ou update
 * @return:
 */
$server->register('ImportaOrcamento', // Nome do método
//                    array('jsonCliente' => 'xsd:string'), // Parâmetros de entrada
                    array('orcamento' => 'tns:Orcamento'),
                    array('return' => 'tns:ArrayOfResultOrcamento'), // Saída
                    'urn:cronos_webservice.orcamentos', // Namespace
                    'urn:cronos_webservice.orcamentos#ImportaOrcamento',
                    'rpc', // Style
                    'encoded', // use
                    'Recebe um Orcamento.' // Serviço
                    );

// # TNS para Entrada
// Complex Type
$server->wsdl->addComplexType(
     'Orcamento',
     'complexType',
     'struct',
     'all',
     '',
     array(
        // Cliente
        'pk_orcamento' => array('name' => 'pk_orcamento', 'type' => 'xsd:string'),
        'fk_id_cliente' => array('name' => 'fk_id_cliente', 'type' => 'xsd:string'),
        'fk_id_usuario' => array('name' => 'fk_id_usuario', 'type' => 'xsd:string'),
        'qtd_itens' => array('name' => 'qtd_itens', 'type' => 'xsd:string'),
        'valor_total' => array('name' => 'valor_total', 'type' => 'xsd:string'),
        'valor_pagar' => array('name' => 'valor_pagar', 'type' => 'xsd:string'),
        'desconto' => array('name' => 'desconto', 'type' => 'xsd:string'),
        'finalizadora' => array('name' => 'finalizadora', 'type' => 'xsd:string'),
        'parcelamento' => array('name' => 'parcelamento', 'type' => 'xsd:string'),
        'nfe' => array('name' => 'nfe', 'type' => 'xsd:string'),
        'frete_por_conta' => array('name' => 'frete_por_conta', 'type' => 'xsd:string'),
        'status' => array('name' => 'status', 'type' => 'xsd:string'),
        'status_servidor' => array('name' => 'status_servidor', 'type' => 'xsd:string'),
        'dt_inclusao' => array('name' => 'dt_inclusao', 'type' => 'xsd:string'),
        'dt_envio' => array('name' => 'dt_envio', 'type' => 'xsd:string'),
        'observacao' => array('name' => 'observacao', 'type' => 'xsd:string'),
        'nome_cliente' => array('name' => 'nome_cliente', 'type' => 'xsd:string'),

        'produtos' => array('name' => 'produtos', 'type' => 'xsd:string'),
    )
);

// # TNS Para Entrada
// Complex Type
$server->wsdl->addComplexType(
     'OrcamentoProdutos',
     'complexType',
     'struct',
     'all',
     '',
     array(
         'pk_orcamento_produto' => array('name' => 'pk_orcamento_produto', 'type' => 'xsd:string'),
         'fk_orcamento' => array('name' => 'fk_orcamento', 'type' => 'xsd:string'),
         'fk_id_produto' => array('name' => 'fk_id_produto', 'type' => 'xsd:string'),
         'quantidade' => array('name' => 'quantidade', 'type' => 'xsd:string'),
         'preco' => array('name' => 'preco', 'type' => 'xsd:string'),
         'valor_total' => array('name' => 'valor_total', 'type' => 'xsd:string'),
         'observacao_produto' => array('name' => 'observacao_produto', 'type' => 'xsd:string'),
         'descricao_curta' => array('name' => 'descricao_curta', 'type' => 'xsd:string'),
     )
);

# Tipo do Array
$server->wsdl->addComplexType(
     'ArrayOfOrcamentoProdutos',
     'complexType',
     'array',
     '',
     'SOAP-ENC:Array',
     array(),
     array(array('ref'=>'SOAP-ENC:arrayType',
                 'wsdl:arrayType'=>'tns:ArrayOfOrcamentoProdutos[]')),
     'tns:OrcamentoProdutos'
);


// # TNS para saida
// Complex Type
$server->wsdl->addComplexType(
     'ArrayOfResultOrcamento',
     'complexType',
     'struct',
     'all',
     '',
     array(
         'status' => array('name' => 'status', 'type' => 'xsd:string'),
         'pk_orcamento' => array('name' => 'pk_orcamento', 'type' => 'xsd:string'),
     )
);


function ImportaOrcamento ($orcamento = array()) {
    Log::Msg(2, "WEB SERVICE [ ws_orcamentos ] Method [ ImportaOrcamento ]");

    $objOrcamento = (object)$orcamento;

    // Obs.Fiz uma GAMBIARRA para passar os produtos,
    // Usei Json para transformar o array em string
    $objOrcamentoProdutos = json_decode($objOrcamento->produtos);


    Log::Msg(3, "Pk_Orcamento [ {$objOrcamento->pk_orcamento} ]");


    $oOrcamentos = new Orcamentos_Servidor();
    $oOrcamentos->setReturnJson(FALSE);

    // Setando Atributos
    $oOrcamentos->setPkOrcamento("{$objOrcamento->pk_orcamento}");

    $oOrcamentos->setFkIdCliente($objOrcamento->fk_id_cliente);
    $oOrcamentos->setFkIdUsuario($objOrcamento->fk_id_usuario);
    $oOrcamentos->setIdentificacaoCliente($objOrcamento->identificacao_cliente);
    $oOrcamentos->setQtdItens($objOrcamento->qtd_itens);
    $oOrcamentos->setValorTotal($objOrcamento->valor_total);
    $oOrcamentos->setFinalizadora($objOrcamento->finalizadora);
    $oOrcamentos->setParcelamento($objOrcamento->parcelamento);
    $oOrcamentos->setNfe($objOrcamento->nfe);
    $oOrcamentos->setFretePorConta($objOrcamento->frete_por_conta);
    $oOrcamentos->setValorPagar($objOrcamento->valor_pagar);
    $oOrcamentos->setDesconto($objOrcamento->desconto);
    $oOrcamentos->setStatus($objOrcamento->status);
    $oOrcamentos->setStatusServidor($objOrcamento->status_servidor);
    $oOrcamentos->setDtInclusao($objOrcamento->dt_inclusao);
    $oOrcamentos->setObservacao($objOrcamento->observacao);

    // Chamo o Metodo importOrcamento()
    $result_orcamento = $oOrcamentos->importOrcamento();


    $total_produtos_ok = 0;
    $total_produtos_erro = 0;
    // Setando os Atributos dos Produtos
    //$oOrcamentos->setSessionPkOrcamento($objOrcamento->pk_orcamento);
    foreach ($objOrcamentoProdutos as $produtos) {
        Log::Msg(3, "Pk_Orcamento_Produto. Produto [ {$produtos->fk_id_produto} ] Qtd [ {$produtos->quantidade} ] Preco [ $produtos->preco ] Valor_Total [ $produtos->valor_total ]");
        $oOrcamentos->setFkIdProduto($produtos->fk_id_produto);
        $oOrcamentos->setQuantidade($produtos->quantidade);
        $oOrcamentos->setPrecoItem($produtos->preco);
        $oOrcamentos->setValorTotalItem($produtos->valor_total);
        $oOrcamentos->setObservacaoProduto($produtos->observacao_produto);

        $result_orcamento_produtos = $oOrcamentos->importOrcamentoProdutos();

        if ($result_orcamento_produtos){
            $total_produtos_ok++;
        }
        else {
            $total_produtos_erro++;
        }
    }

    // Coloco status_servidor = 2  marcando como recebido pelo servidor
    if (!$result_orcamento === false) {
        $aResult['status'] = "true";
        $aResult['pk_orcamento'] = "$result_orcamento";
    }
    else {
        $aResult['status'] = "false";
        $aResult['pk_orcamento'] = "$result_orcamento";
    }
    return $aResult;

}

// requisicao para uso do servico
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>