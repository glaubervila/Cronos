<?php
/**
 * @class   : ws_produtos.php
 * @author  : Glauber Costa Vila-Verde
 * @date    : 24/06/2011
 * Classe Responsavel por disponibilizar funcoes relativas a entidade Produtos
 * a um servico de WebService
 * @revision: 27/06/2011
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

Log::Msg(3, "INICIANDO WEB SERVICE [ ws_produtos ]");

require_once('Main/Modulos/Nusoap/nusoap.php');

// criacao de uma instancia do servidor
$server = new soap_server;

$server->debug_flag = true;

// inicializa o suporte a WSDL

$server->configureWSDL('cronos_webservice.produtos','urn:cronos_webservice.produtos');
$server->wsdl->schemaTargetNamespace = 'urn:cronos_webservice.produtos';


// Registro de Metodos

/** @Method: getTotalProdutos
 *  @Param:$tipo string = 0 para Completo - Mostra todos os registro ou 1 para alterados
 *  @Param:$tipo string = data do registro mais recente no client
 *  Este Metodo retorna o Total de Produtos a serem importados
 * @return:$total_geral string    = total de registros no servidor.
 *        :$total_importar string = total de registros a serem importados.
 */
$server->register('getTotalProdutos', // Nome do método
                    array('tipo' => 'xsd:string', 'data' => 'xsd:string'), // Parâmetros de entrada
                    array('return' => 'tns:ArrayOfProdutos'), // Saída
                    'urn:cronos_webservice.produtos', // Namespace
                    'urn:cronos_webservice.produtos#getProdutos',
                    'rpc', // Style
                    'encoded', // use
                    'Retorna Todos os Produtos. O Parametro tipo diferencia se a resposta vai conter todos os registros ou apenas o mais atuais, no caso de mais atuais o parametro data se torna obrigatorio. Param: tipo 0 - Completa 1 - Alterados, Param: data - Data do registro mais recente no client, o servidor usara essa data como parametro para saber os mais recentes' // Serviço
                    );

// # TNS para saida
// Complex Type
$server->wsdl->addComplexType(
     'total_produtos',
     'complexType',
     'struct',
     'all',
     '',
     array(
         'total_geral' => array('name' => 'total_geral', 'type' => 'xsd:string'),
         'total_importar' => array('name' => 'total_importar', 'type' => 'xsd:string'),
     )
);

function getTotalProdutos () {
    Log::Msg(2, "WEB SERVICE [ ws_produtos ] Method [ getTotalProdutos ]");

    $objUsuario = new Usuarios();
    $objUsuario->setReturnJson(false);
    $aObjUsuarios = $objUsuario->getUsuariosFromWebService();

    $count = count($aObjUsuarios);

    if($count != 0){
        $arrUsuario = Common::objectToArray($aObjUsuarios);

        Log::Msg(2, "WEB SERVICE [ ws_usuarios ] Status [  OK  ] Total_Resultados [ $count ]");
        return $arrUsuario;
    }
    else {
        Log::Msg(2, "WEB SERVICE [ ws_usuarios ] Status [ ERRO ] ");
    }

}



/** @Method: getProdutoById
 *  @Param:$pk_id_produto string = chave primaria do produto
 *  Este Metodo retorna todas as informacoes do produto
 *  @return: array com as informacoes do produto
 */
$server->register('getProdutoById', // Nome do método
                    array('pk_id_produto' => 'xsd:string'), // Parâmetros de entrada
                    array('return' => 'tns:Produto'), // Saída
                    'urn:cronos_webservice.produtos', // Namespace
                    'urn:cronos_webservice.produtos#getProdutoById',
                    'rpc', // Style
                    'encoded', // use
                    'Recebe um id e retorna um array com os dados do produto' // Serviço
                    );

// # TNS para saida
// Complex Type
$server->wsdl->addComplexType(
    'Produto',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'pk_id_produto' => array('name' => 'pk_id_produto', 'type' => 'xsd:string'),
        'fk_id_categoria' => array('name' => 'fk_id_categoria', 'type' => 'xsd:string'),
        'fk_id_fabricante' => array('name' => 'fk_id_fabricante', 'type' => 'xsd:string'),
        'descricao_curta' => array('name' => 'descricao_curta', 'type' => 'xsd:string'),
        'descricao_longa' => array('name' => 'descricao_longa', 'type' => 'xsd:string'),
        'unidade' => array('name' => 'unidade', 'type' => 'xsd:string'),
        'tributacao' => array('name' => 'tributacao', 'type' => 'xsd:string'),
        'garantia' => array('name' => 'garantia', 'type' => 'xsd:string'),
        'url_image' => array('name' => 'url_image', 'type' => 'xsd:string'),
        'name_image' => array('name' => 'name_image', 'type' => 'xsd:string'),
        'dt_inclusao' => array('name' => 'dt_inclusao', 'type' => 'xsd:string'),
        'dt_alteracao' => array('name' => 'dt_alteracao', 'type' => 'xsd:string'),
        'pk_estoque' => array('name' => 'pk_estoque', 'type' => 'xsd:string'),
        'quantidade' => array('name' => 'quantidade', 'type' => 'xsd:string'),
        'dt_inclusao_estoque' => array('name' => 'dt_inclusao_estoque', 'type' => 'xsd:string'),
        'pk_preco' => array('name' => 'pk_preco', 'type' => 'xsd:string'),
        'preco' => array('name' => 'preco', 'type' => 'xsd:string'),
        'dt_inclusao_preco' => array('name' => 'dt_inclusao_preco', 'type' => 'xsd:string'),

    )
);

/** @Method: getProdutoById
 *  @Param:$pk_catalogo string = chave primaria do catalogo a ser atualizado
 *  Este Metodo retorna todas as informacoes dos produtos no catalogo
 *  @return: array com as informacoes do produto
 */
$server->register('getProdutoInCatalogo', // Nome do método
                    array('pk_catalogo' => 'xsd:string'), // Parâmetros de entrada
                    array('return' => 'tns:ArrayOfProdutos'), // Saída
                    'urn:cronos_webservice.produtos', // Namespace
                    'urn:cronos_webservice.produtos#getProdutoInCatalogo',
                    'rpc', // Style
                    'encoded', // use
                    'Recebe o Id do Catalogo e retorna um array com os dados de todos os produtos no catalogo' // Serviço
                    );

// # TNS para saida
// Complex Type
$server->wsdl->addComplexType(
    'Produto',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'pk_id_produto' => array('name' => 'pk_id_produto', 'type' => 'xsd:string'),
        'fk_id_categoria' => array('name' => 'fk_id_categoria', 'type' => 'xsd:string'),
        'fk_id_fabricante' => array('name' => 'fk_id_fabricante', 'type' => 'xsd:string'),
        'descricao_curta' => array('name' => 'descricao_curta', 'type' => 'xsd:string'),
        'descricao_longa' => array('name' => 'descricao_longa', 'type' => 'xsd:string'),
        'unidade' => array('name' => 'unidade', 'type' => 'xsd:string'),
        'tributacao' => array('name' => 'tributacao', 'type' => 'xsd:string'),
        'garantia' => array('name' => 'garantia', 'type' => 'xsd:string'),
        'url_image' => array('name' => 'url_image', 'type' => 'xsd:string'),
        'name_image' => array('name' => 'name_image', 'type' => 'xsd:string'),
        'dt_inclusao' => array('name' => 'dt_inclusao', 'type' => 'xsd:string'),
        'dt_alteracao' => array('name' => 'dt_alteracao', 'type' => 'xsd:string'),
        'pk_estoque' => array('name' => 'pk_estoque', 'type' => 'xsd:string'),
        'quantidade' => array('name' => 'quantidade', 'type' => 'xsd:string'),
        'dt_inclusao_estoque' => array('name' => 'dt_inclusao_estoque', 'type' => 'xsd:string'),
        'pk_preco' => array('name' => 'pk_preco', 'type' => 'xsd:string'),
        'preco' => array('name' => 'preco', 'type' => 'xsd:string'),
        'dt_inclusao_preco' => array('name' => 'dt_inclusao_preco', 'type' => 'xsd:string'),

    )
);

# Tipo do Array
$server->wsdl->addComplexType(
     'ArrayOfProdutos',
     'complexType',
     'array',
     '',
     'SOAP-ENC:Array',
     array(),
     array(array('ref'=>'SOAP-ENC:arrayType',
                 'wsdl:arrayType'=>'tns:ArrayOfProdutos[]')),
     'tns:Produto'
);

function getProdutoInCatalogo($pk_catalogo){


    // Recuperar os Ids dos Produtos no Catalogo
    $objCatalogo = new Catalogos();
    $objCatalogo->setReturnJson(false);
    $objCatalogo->setPkCatalogo($pk_catalogo);

    Log::Msg(3, "Recuperando Produtos do Catalogo. pk_catalogo [ {$pk_catalogo} ]");

    $arrResult = $objCatalogo->getAllProdutosInCatalogo();

    $count = count($arrResult);
    if ($count) {
        Log::Msg(3, "Enviando Array de Produtos do Catalogo. total_produtos_catalogos [ $count ]");
        $arrProdutosCatalogo = Common::objectToArray($arrResult);
        return $arrProdutosCatalogo;
    }
    else {
        Log::Msg(3, "Nenhum Produto Encontrado no Catalogo");
        return FALSE;
    }

}



// ==================< CATEGORIAS/DEPARTAMENTOS >================== //


/** @Method: getCategorias
 *  @Param:
 *  Este Metodo retorna todas as Categorias/Departamentos dos produtos
 *  @return: array com as informacoes das Categorias
 */
$server->register('getCategorias', // Nome do método
                    array(), // Parâmetros de entrada
                    array('return' => 'tns:ArrayOfCategorias'), // Saída
                    'urn:cronos_webservice.produtos', // Namespace
                    'urn:cronos_webservice.produtos#getCategorias',
                    'rpc', // Style
                    'encoded', // use
                    'Retorna um array com todas as Categorias/Departamentos' // Serviço
                    );

// # TNS para saida
// Complex Type
$server->wsdl->addComplexType(
    'Categoria',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'pk_id_categoria' => array('name' => 'pk_id_categoria', 'type' => 'xsd:string'),
        'categoria' => array('name' => 'categoria', 'type' => 'xsd:string'),
        'codigo_cor' => array('name' => 'codigo_cor', 'type' => 'xsd:string'),
    )
);

# Tipo do Array
$server->wsdl->addComplexType(
     'ArrayOfCategorias',
     'complexType',
     'array',
     '',
     'SOAP-ENC:Array',
     array(),
     array(array('ref'=>'SOAP-ENC:arrayType',
                 'wsdl:arrayType'=>'tns:ArrayOfCategorias[]')),
     'tns:Categoria'
);


function getCategorias() {
    Log::Msg(2, "WEB SERVICE [ ws_produtos ] Method [ getCategorias ]");


    $objCategoria = new Categoria();
    $objCategoria->setReturnJson(false);

    Log::Msg(3, "Recuperando Categorias/Departamentos.");

    $aObjCategorias = $objCategoria->getCategorias();

    $count = count($aObjCategorias);

    if($count != 0){
        $arrCategorias = Common::objectToArray($aObjCategorias);

        Log::Msg(3, "Enviando Categorias. total [ $count ]");
        return $arrCategorias;
    }
    else {
        Log::Msg(3, "Nenhuma Categoria Encontrada");
        return FALSE;
    }
}

// requisicao para uso do servico
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>