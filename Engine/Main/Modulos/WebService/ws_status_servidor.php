<?php
/**
 * @class   :ws_status_servidor.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :
 * Classe Responsavel por disponibilizar funcoes relativas ao Status do Servidor
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

Log::Msg(3, "INICIANDO WEB SERVICE [ ws_status_servidor ]");

require_once('Main/Modulos/Nusoap/nusoap.php');

// criacao de uma instancia do servidor
$server = new soap_server;

$server->debug_flag = true;

// inicializa o suporte a WSDL

$server->configureWSDL('cronos_webservice.status_servidor','urn:cronos_webservice.status_servidor');
$server->wsdl->schemaTargetNamespace = 'urn:cronos_webservice.status_servidor';


// Registra o método
/** @Method: getStatusServidor
 *
 */
$server->register('getStatusServidor', // Nome do método
                    array(), // Parâmetros de entrada
                    array('return' => 'tns:StatusServidor'), // Saída
                    'urn:cronos_webservice.status_servidor', // Namespace
                    'urn:cronos_webservice.status_servidor#getStatusServidor',
                    'rpc', // Style
                    'encoded', // use
                    'Retorna O Status do Servidor, TRUE se estiver online' // Serviço
                    );

// # TNS para saida
// Complex Type
$server->wsdl->addComplexType(
    'StatusServidor',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'status' => array('name' => 'status', 'type' => 'xsd:string'),
    )
);



function getStatusServidor(){
    Log::Msg(3, "GetStatusServidor");

    $result['status'] = TRUE;

    if ($result) {
        Log::Msg(3, "Retornando Status do Servidor. Status [ TRUE ]");

        return $result;
    }
    else {
        return FALSE;
    }
}

// requisicao para uso do servico
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>