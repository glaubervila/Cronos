<?php
// WebServices - iMasters.com.br
// Autor: Mauricio Reckziegel
// http://www.mauricioreckziegel.com
// Arquivo : server2.php
// Observacoes:
// Comentar a extensao php_soap
// Baixar o arquivo nusoap.php



//ini_set('error_reporting','E_ALL & ~E_NOTICE');
/**
 * função __autoload()
 *  Carrega uma classe quando ela é instânciada pela primeira vez.
 */
// function __autoload($classe) {
//     echo exec('pwd');
//     echo "</br>";
//     chdir ("../../../");
//     echo exec('pwd');
//     echo "</br>";
//
//     $pastas = array('Main/PHP');
//     $modulos = scandir("Main/Modulos");
//     foreach ($modulos as $pasta) {
//         if ($pasta != "." && $pasta != ".."){
//             array_push($pastas, "Main/Modulos/".$pasta);
//         }
//     }
//     foreach ($pastas as $pasta) {
//         if (file_exists("{$pasta}/{$classe}.class.php")){
//             include_once "{$pasta}/{$classe}.class.php";
//             echo "FILE EXISTS";
//             Log::Msg(7, "AUTOLOAD Class[ $classe ] File [ {$pasta}/{$classe}.class.php ]");
//
//         }
//     }
// }
//Log::Msg(3, "TENTANDO INSTANCIAR CLASSES\n");


//$usuarios = new Usuarios();

// // inclusao do arquivo de classes NuSOAP
// require_once('../Nusoap/nusoap.php');
// //require_once('../../PHP/nusoap.php');
// // criacao de uma instancia do servidor
// $server = new soap_server;
// // inicializa o suporte a WSDL
//
// $server->configureWSDL('server.hello','urn:server.hello');
// $server->wsdl->schemaTargetNamespace = 'urn:server.hello';
//
//
// // registra o metodo a ser oferecido
// $server->register('hello', //nome do metodo
// array('name' => 'xsd:string'), //parametros de entrada
// array('return' => 'xsd:string'), //parametros de saida
// 'urn:server.hello', //namespace
// 'urn:server.hello#hello', //soapaction
// 'rpc', //style
// 'encoded', //use
// 'Retorna o nome' //documentacao do servico
// );
//
// $server->register('getUsuarios', //nome do metodo
// array('tipe' => 'xsd:string'), //parametros de entrada
// array('return' => 'xsd:string'), //parametros de saida
// 'urn:server.getUsuarios', //namespace
// 'urn:server.getUsuarios#getUsuarios', //soapaction
// 'rpc', //style
// 'encoded', //use
// 'Retorna o nome' //documentacao do servico
// );
//
// 	function hello($name) {
// 		return 'Hello '.$name;
// 	}
//
//     function getUsuarios($tipo) {
//
//         $usuarios = new Usuarios();
//
//         return 'Tipo '.$tipo;
//     }
//
//
// //---------------------------------//----------------------------------------
// // Registra o método
// $server->register('listarFuncionarios', // Nome do método
//                     array(), // Parâmetros de entrada
//                     array('return' => 'tns:ArrayOfFuncionario'), // Saída
//                     'urn:server.wsFuncionario', // Namespace
//                     'urn:server.wsFuncionario#listarFuncionarios',
//                     'rpc', // Style
//                     'encoded', // use
//                     'Retorna todos os funcionarios' // Serviço
//                     );
//
// # TNS para saida
//
// // Complex Type
// $server->wsdl->addComplexType(
//     'Funcionario',
//     'complexType',
//     'struct',
//     'all',
//     '',
//     array(
//         'matricula' => array('name' => 'matricula', 'type' => 'xsd:string'),
//         'nome' => array('name' => 'nome', 'type' => 'xsd:string'),
//         'salario' => array('name' => 'salario', 'type' => 'xsd:float'),
//     )
// );
//
//
// # Tipo do Array
// $server->wsdl->addComplexType(
//     'ArrayOfFuncionario',
//     'complexType',
//     'array',
//     '',
//     'SOAP-ENC:Array',
//     array(),
//     array(array('ref'=>'SOAP-ENC:arrayType',
//                 'wsdl:arrayType'=>'tns:Funcionario[]')),
//     'tns:Funcionario'
// );
//
// // Função para listar os Funcionarios
// function listarFuncionarios()
// {
//     #------------------------------------------#
//     #  ARRAY MULTIDIMENSIONAL DE FUNCIONARIOS  #
//     #------------------------------------------#
//
//     // Funcionario Rômulo Paiva
//     $arrFunc[0]['matricula'] = '000001';
//     $arrFunc[0]['nome'] = 'Rômulo Paiva';
//     $arrFunc[0]['salario'] = '300,00';
//
//     // Funcionario Bruno Vicente
//     $arrFunc[1]['matricula'] = '000002';
//     $arrFunc[1]['nome'] = 'Bruno Vicente';
//     $arrFunc[1]['salario'] = '2000,00';
//
//     // Funcionario Raul Sales
//     $arrFunc[2]['matricula'] = '000003';
//     $arrFunc[2]['nome'] = 'Raul Sales';
//     $arrFunc[2]['salario'] = '1000,00';
//
//     // Funcionario Eric Cavalcanti
//     $arrFunc[3]['matricula'] = '000004';
//     $arrFunc[3]['nome'] = 'Eric Cavalcanti';
//     $arrFunc[3]['salario'] = '4500,00';
//
//     // Retorna o array de funcionarios
//     return $arrFunc;
//
// }
//
// // requisicao para uso do servico
// $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
// $server->service($HTTP_RAW_POST_DATA);
//

// $server->register('getUsuarios', // Nome do método
//                     array('tipo' => 'xsd:string'), // Parâmetros de entrada
//                     array('return' => 'tns:ArrayOfUsuarios'), // Saída
//                     'urn:cronos_webservice.usuarios', // Namespace
//                     'urn:cronos_webservice.usuarios#getUsuarios',
//                     'rpc', // Style
//                     'encoded', // use
//                     'Retorna Todos os Usuarios. Parametros: * tipo: 0 - Lista Completa ou 1 - Retorna Lista So com os Alterados Necessita da Data do Registro mais recente no Cliente. * dt_atualizacao - Parametro com o DateTime do Registro Mais Recente no Cliente Utilizado com a opçao tipo 1' // Serviço
//                     );


?>
