<?php
/**
 * @class   :ws_catalogo.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :
 * Classe Responsavel por disponibilizar funcoes relativas a entidade Catalogo e Catalogo_Produtos
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

Log::Msg(3, "INICIANDO WEB SERVICE [ ws_catalogo ]");

require_once('Main/Modulos/Nusoap/nusoap.php');

// criacao de uma instancia do servidor
$server = new soap_server;

$server->debug_flag = true;

// inicializa o suporte a WSDL

$server->configureWSDL('cronos_webservice.catalogo','urn:cronos_webservice.catalogo');
$server->wsdl->schemaTargetNamespace = 'urn:cronos_webservice.catalogo';


// Registra o método
$server->register('getCatalogoAtual', // Nome do método
                    array('pk_catalogo' => 'xsd:string'), // Parâmetros de entrada
                    array('return' => 'tns:Catalogo'), // Saída
                    'urn:cronos_webservice.catalogo', // Namespace
                    'urn:cronos_webservice.catalogo#getCatalogoAtual',
                    'rpc', // Style
                    'encoded', // use
                    'Retorna um registro do Catalogo mais atual, sem a lista de produtos.' // Serviço
                    );
// # TNS para saida


// Complex Type
$server->wsdl->addComplexType(
     'Catalogo',
     'complexType',
     'struct',
     'all',
     '',
     array(
         'pk_catalogo' => array('name' => 'pk_catalogo', 'type' => 'xsd:string'),
         'fk_id_usuario' => array('name' => 'fk_id_usuario', 'type' => 'xsd:string'),
         'quantidade_minima'  => array('name' => 'quantidade_minima', 'type' => 'xsd:string'),
         'quantidade_total_produtos' => array('name' => 'quantidade_total_produtos', 'type' => 'xsd:string'),
         'comentario' => array('name' => 'comentario', 'type' => 'xsd:string'),
         'dt_inclusao' => array('name' => 'dt_inclusao', 'type' => 'xsd:string'),
     )
);



function getCatalogoAtual($pk_catalogo) {
    Log::Msg(2, "WEB SERVICE [ ws_catalogo ] Method [ getCatalogoAtual ]");


    $objCatalogo = new Catalogos();
    $objCatalogo->setReturnJson(false);


    if ($pk_catalogo == 0) {
        $pk_catalogo = $objCatalogo->getCatalogoAtual();
    }

    if ($pk_catalogo) {
        Log::Msg(3, "Recuperando Informacaes Catalogo. pk_catalogo [ {$pk_catalogo} ]");

        // Recuperando Informacoes do Catalogo
        $objCatalogo->setPkCatalogo($pk_catalogo);
        $result = $objCatalogo->getCatalogoById();

        if ($result){
            Log::Msg(3, "Enviando Catalogo. pk_catalogo [ {$result->pk_catalogo} ]");
            return $result;
        }
        else {
            return FALSE;
        }
//         // Array de teste
//         $aResult['pk_catalogo']   = "1";
//         $aResult['fk_id_usuario'] = "1";
//         $aResult['quantidade_minima'] = "30";
//         $aResult['quantidade_total_produtos'] = "1000";
//         $aResult['comentario']  = "";
//         $aResult['dt_inclusao'] = "";


    }
    else {
        Log::Msg(3, "Nenhum Catalogo Encontrado");
        return FALSE;
    }
}


// Registra o método
$server->register('getCatalogoProdutos', // Nome do método
                    array('pk_catalogo' => 'xsd:string'), // Parâmetros de entrada
                    array('return' => 'tns:ArrayOfCatalogoProdutos'), // Saída
                    'urn:cronos_webservice.catalogo', // Namespace
                    'urn:cronos_webservice.catalogo#getCatalogoProdutos',
                    'rpc', // Style
                    'encoded', // use
                    'Retorna um registro do Catalogo mais atual, sem a lista de produtos.' // Serviço
                    );
// # TNS para saida


// Complex Type
$server->wsdl->addComplexType(
     'CatalogoProdutos',
     'complexType',
     'struct',
     'all',
     '',
     array(
         'pk_catalogo_produto' => array('name' => 'pk_catalogo_produto', 'type' => 'xsd:string'),
         'fk_catalogo' => array('name' => 'fk_catalogo', 'type' => 'xsd:string'),
         'fk_id_produto'  => array('name' => 'fk_id_produto', 'type' => 'xsd:string'),
         'excecao' => array('name' => 'excecao', 'type' => 'xsd:string'),
         'mtime' => array('name' => 'mtime', 'type' => 'xsd:string'),
     )
);

# Tipo do Array
$server->wsdl->addComplexType(
     'ArrayOfCatalogoProdutos',
     'complexType',
     'array',
     '',
     'SOAP-ENC:Array',
     array(),
     array(array('ref'=>'SOAP-ENC:arrayType',
                 'wsdl:arrayType'=>'tns:CatalogoProdutos[]')),
     'tns:CatalogoProdutos'
);


function getCatalogoProdutos($pk_catalogo) {
    Log::Msg(2, "WEB SERVICE [ ws_catalogo ] Method [ getCatalogoProdutos ]");


    $objCatalogo = new Catalogos();
    $objCatalogo->setReturnJson(false);

    if($pk_catalogo == 0) {
        $pk_catalogo = $objCatalogo->getCatalogoAtual();
    }

    $objCatalogo->setPkCatalogo($pk_catalogo);

    Log::Msg(3, "Recuperando Produtos do Catalogo. pk_catalogo [ {$pk_catalogo} ]");

    $aObjCatalogoProdutos = $objCatalogo->getProdutosCatalogoById();

    // Enviar a Data de Modificacao das Imagens
    // Para ser Usado Na Atualizacao
    $arrResult = array();
    $data = Common::Verifica_Diretorio_Data();
    $local_dir = $data . "Imagens_Produtos/";

    foreach ($aObjCatalogoProdutos as $aObjCatalogoProduto){
        $codigo = sprintf("%06d", $aObjCatalogoProduto->fk_id_produto);
        $local_file = $local_dir . $codigo . ".JPG";
        $aObjCatalogoProduto->mtime = filemtime($local_file);

        $arrResult[] = $aObjCatalogoProduto;
    }

    $aObjCatalogoProdutos = null;
    $aObjCatalogoProdutos = $arrResult;
    $count = count($aObjCatalogoProdutos);

    if ($count) {
        Log::Msg(3, "Enviando Produtos do Catalogo. total_produtos_catalogos [ $count ]");
        $arrCatalogoProdutos = Common::objectToArray($aObjCatalogoProdutos);
        return $arrCatalogoProdutos;
    }
    else {
        Log::Msg(3, "Nenhum Produto Encontrado no Catalogo");
        return FALSE;
    }

}



// requisicao para uso do servico
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>