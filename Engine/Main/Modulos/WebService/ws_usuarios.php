<?php
/**
 * @class   :ws_usuarios.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :
 * Classe Responsavel por disponibilizar funcoes relativas a entidade Usuarios e Grupos Usuarios
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

Log::Msg(3, "INICIANDO WEB SERVICE [ ws_usuarios ]");

require_once('Main/Modulos/Nusoap/nusoap.php');

// criacao de uma instancia do servidor
$server = new soap_server;

$server->debug_flag = true;

// inicializa o suporte a WSDL

$server->configureWSDL('cronos_webservice.usuarios','urn:cronos_webservice.usuarios');
$server->wsdl->schemaTargetNamespace = 'urn:cronos_webservice.usuarios';


// Registra o método
$server->register('getUsuarios', // Nome do método
                    array(), // Parâmetros de entrada
                    array('return' => 'tns:ArrayOfUsuarios'), // Saída
                    'urn:cronos_webservice.usuarios', // Namespace
                    'urn:cronos_webservice.usuarios#getUsuarios',
                    'rpc', // Style
                    'encoded', // use
                    'Retorna Todos os Usuarios. OBS. Nome dos campos com PRIMEIRA LETRA MAIUSCULA, ex. Nome.' // Serviço
                    );
// # TNS para saida

// Complex Type
$server->wsdl->addComplexType(
     'Usuario',
     'complexType',
     'struct',
     'all',
     '',
     array(
         'id_usuario' => array('name' => 'id_usuario', 'type' => 'xsd:string'),
         'Grupo' => array('name' => 'Grupo', 'type' => 'xsd:string'),
         'Nome'  => array('name' => 'Nome', 'type' => 'xsd:string'),
         'Login' => array('name' => 'Login', 'type' => 'xsd:string'),
         'Senha' => array('name' => 'Senha', 'type' => 'xsd:string'),
     )
);

# Tipo do Array
$server->wsdl->addComplexType(
     'ArrayOfUsuarios',
     'complexType',
     'array',
     '',
     'SOAP-ENC:Array',
     array(),
     array(array('ref'=>'SOAP-ENC:arrayType',
                 'wsdl:arrayType'=>'tns:Usuario[]')),
     'tns:Usuario'
);

function getUsuarios() {
    Log::Msg(2, "WEB SERVICE [ ws_usuarios ] Method [ getUsuarios ]");


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


// ------------------------------------------------------- Grupos Usuarios -------------------------------------------
// Registra o método
$server->register('getGruposUsuarios', // Nome do método
                    array(), // Parâmetros de entrada
                    array('return' => 'tns:ArrayOfGruposUsuarios'), // Saída
                    'urn:cronos_webservice.usuarios', // Namespace
                    'urn:cronos_webservice.usuarios#getGruposUsuarios',
                    'rpc', // Style
                    'encoded', // use
                    'Retorna Todos os Grupos Usuarios. OBS. Nome dos campos com PRIMEIRA LETRA MAIUSCULA Exceto o Campo id, ex. id, Grupo.' // Serviço
                    );
// # TNS para saida

// Complex Type
$server->wsdl->addComplexType(
     'GruposUsuarios',
     'complexType',
     'struct',
     'all',
     '',
     array(
         'id' => array('name' => 'id', 'type' => 'xsd:string'),
         'Grupo' => array('name' => 'Grupo', 'type' => 'xsd:string'),
         'Descricao'  => array('name' => 'Descricao', 'type' => 'xsd:string'),
     )
);

# Tipo do Array
$server->wsdl->addComplexType(
     'ArrayOfGruposUsuarios',
     'complexType',
     'array',
     '',
     'SOAP-ENC:Array',
     array(),
     array(array('ref'=>'SOAP-ENC:arrayType',
                 'wsdl:arrayType'=>'tns:GruposUsuarios[]')),
     'tns:GruposUsuarios'
);

function getGruposUsuarios() {
    Log::Msg(2, "WEB SERVICE [ ws_usuarios ] Method [ getGruposUsuarios ]");


    $objGrupos = new GruposUsuarios();
    $objGrupos->setReturnJson(false);
    $aObjGruposUsuarios = $objGrupos->getGrupos();

    $count = count($aObjGruposUsuarios);

    if($count != 0){
        $arrGruposUsuario = Common::objectToArray($aObjGruposUsuarios);

        Log::Msg(2, "WEB SERVICE [ ws_usuarios ] Status [  OK  ] Total_Resultados [ $count ]");
        return $arrGruposUsuario;
    }
    else {
        Log::Msg(2, "WEB SERVICE [ ws_usuarios ] Status [ ERRO ] ");
    }

}


// requisicao para uso do servico
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);


//getUsuarios();

?>