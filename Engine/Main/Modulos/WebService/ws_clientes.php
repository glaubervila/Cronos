<?php
/**
 * @class   : ws_clientes.php
 * @author  : Glauber Costa Vila-Verde
 * @date    : 03/11/2011
 * Classe Responsavel por disponibilizar funcoes relativas a entidade Clientes
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

Log::Msg(3, "INICIANDO WEB SERVICE [ ws_clientes ]");

require_once('Main/Modulos/Nusoap/nusoap.php');

// criacao de uma instancia do servidor
$server = new soap_server;

$server->debug_flag = true;

// inicializa o suporte a WSDL

$server->configureWSDL('cronos_webservice.clientes','urn:cronos_webservice.clientes');
$server->wsdl->schemaTargetNamespace = 'urn:cronos_webservice.clientes';



/** @Method: CriaAtualizaCliente
 *  @Param:$tipo
 *  @Param:$tipo
 *  Este Recebe um Array com as Informaçoes do Cliente e realiza as operacoes de insercao ou update
 * @return:
 */
$server->register('CriaAtualizaCliente', // Nome do método
//                    array('jsonCliente' => 'xsd:string'), // Parâmetros de entrada
                    array('cliente' => 'tns:ArrayOfCliente'),
                    array('return' => 'tns:ArrayOfResultCliente'), // Saída
                    'urn:cronos_webservice.clientes', // Namespace
                    'urn:cronos_webservice.clientes#CriaAtualizaCliente',
                    'rpc', // Style
                    'encoded', // use
                    'Este Serviço, Recebe um Json com as informações do cliente, seta os atributos da classe Clientes, e Executa o Metodo CriaAtualizaCliente' // Serviço
                    );

// # TNS para Entrada
// Complex Type
$server->wsdl->addComplexType(
     'ArrayOfCliente',
     'complexType',
     'struct',
     'all',
     '',
     array(
        // Cliente
        'pk_id_cliente' => array('name' => 'total_a_importar', 'type' => 'xsd:string'),
        'fk_id_loja' => array('name' => 'fk_id_loja', 'type' => 'xsd:string'),
        'fk_id_endereco' => array('name' => 'fk_id_endereco', 'type' => 'xsd:string'),
        'fk_id_usuario' => array('name' => 'fk_id_usuario', 'type' => 'xsd:string'),
        'tipo' => array('name' => 'tipo', 'type' => 'xsd:string'),
        'status' => array('name' => 'status', 'type' => 'xsd:string'),
        'tipo_cliente' => array('name' => 'tipo_cliente', 'type' => 'xsd:string'),
        'nome' => array('name' => 'nome', 'type' => 'xsd:string'),
        'cpf' => array('name' => 'cpf', 'type' => 'xsd:string'),
        'cnpj' => array('name' => 'cnpj', 'type' => 'xsd:string'),
        'rg' => array('name' => 'rg', 'type' => 'xsd:string'),
        'inscricao_estadual' => array('name' => 'inscricao_estadual', 'type' => 'xsd:string'),
        'dt_nascimento' => array('name' => 'dt_nascimento', 'type' => 'xsd:string'),
        'sexo' => array('name' => 'sexo', 'type' => 'xsd:string'),
        'profissao' => array('name' => 'profissao', 'type' => 'xsd:string'),
        'estado_civil' => array('name' => 'estado_civil', 'type' => 'xsd:string'),
        'telefone_fixo' => array('name' => 'telefone_fixo', 'type' => 'xsd:string'),
        'telefone_movel' => array('name' => 'telefone_movel', 'type' => 'xsd:string'),
        'email' => array('name' => 'email', 'type' => 'xsd:string'),
        'status_servidor' => array('name' => 'status_servidor', 'type' => 'xsd:string'),
        'dt_inclusao' => array('name' => 'dt_inclusao', 'type' => 'xsd:string'),
        'dt_alteracao' => array('name' => 'dt_alteracao', 'type' => 'xsd:string'),
        'observacoes' => array('name' => 'dt_alteracao', 'type' => 'xsd:string'),

        // Endereco
        'id_endereco' => array('name' => 'id_endereco', 'type' => 'xsd:string'),
        'tipo_endereco' => array('name' => 'tipo_endereco', 'type' => 'xsd:string'),
        'rua' => array('name' => 'rua', 'type' => 'xsd:string'),
        'numero' => array('name' => 'numero', 'type' => 'xsd:string'),
        'bairro' => array('name' => 'bairro', 'type' => 'xsd:string'),
        'cidade' => array('name' => 'cidade', 'type' => 'xsd:string'),
        'uf' => array('name' => 'uf', 'type' => 'xsd:string'),
        'cep' => array('name' => 'cep', 'type' => 'xsd:string'),
        'complemento' => array('name' => 'complemento', 'type' => 'xsd:string'),
        'dt_inclusao' => array('name' => 'dt_inclusao', 'type' => 'xsd:string'),
        'dt_alteracao' => array('name' => 'dt_alteracao', 'type' => 'xsd:string'),
        'id_referencia' => array('name' => 'id_referencia', 'type' => 'xsd:string'),
        'id_referencia_pk' => array('name' => 'id_referencia_pk', 'type' => 'xsd:string'),
     )
);


// # TNS para saida
// Complex Type
$server->wsdl->addComplexType(
     'ArrayOfResultCliente',
     'complexType',
     'struct',
     'all',
     '',
     array(
         'status' => array('name' => 'status', 'type' => 'xsd:string'),
         'pk_id_cliente' => array('name' => 'pk_id_cliente', 'type' => 'xsd:string'),
     )
);


function CriaAtualizaCliente ($cliente = array()) {
    Log::Msg(2, "WEB SERVICE [ ws_clientes ] Method [ CriaAtualizaCliente ]");

    $objCliente = (object)$cliente;

    // Criar um Objeto Endereco
    $oEndereco = new Enderecos();
    // Criar um Objeto Cliente Setar os Parametros
    $oCliente = new Clientes();

    Log::Msg(3, "Setando Atributos de Endereco");

    $oEndereco->setTipo($objCliente->tipo_endereco);
    $oEndereco->setRua($objCliente->rua);
    $oEndereco->setNumero($objCliente->numero);
    $oEndereco->setBairro($objCliente->bairro);
    $oEndereco->setCidade($objCliente->cidade);
    $oEndereco->setUf($objCliente->uf);
    $oEndereco->setCep($objCliente->cep);
    $oEndereco->setComplemento($objCliente->complemento);
    $oEndereco->setDtInclusao($objCliente->dt_inclusao);
    $oEndereco->setDtAlteracao($objCliente->dt_alteracao);

    $oEndereco->set_referencia($objCliente->id_referencia, $objCliente->pk_id_cliente);

    $id_endereco = $oEndereco->import_endereco();

    Log::Msg(3, "Importando Endereco. result [ $id_endereco ]");


    $oCliente->setPkIdCliente($objCliente->pk_id_cliente);
    $oCliente->setFkIdLoja($objCliente->fk_id_loja);
    $oCliente->setFkIdEndereco($id_endereco);
    $oCliente->setFkIdUsuario($objCliente->fk_id_usuario);
    $oCliente->setTipo($objCliente->tipo);
    $oCliente->setTipoCliente($objCliente->tipo_cliente);
    $oCliente->setStatus($objCliente->status);
    $oCliente->setNome($objCliente->nome);
    $oCliente->setCpf($objCliente->cpf);
    $oCliente->setCnpj($objCliente->cnpj);
    $oCliente->setRg($objCliente->rg);
    $oCliente->setInscricaoEstadual($objCliente->inscricao_estadual);
    $oCliente->setDtNascimento($objCliente->dt_nascimento);
    $oCliente->setSexo($objCliente->sexo);
    $oCliente->setProfissao($objCliente->profissao);
    $oCliente->setEstadoCivil($objCliente->estado_civil);
    $oCliente->setTelefoneFixo($objCliente->telefone_fixo);
    $oCliente->setTelefoneMovel($objCliente->telefone_movel);
    $oCliente->setEmail($objCliente->email);
    $oCliente->setDtInclusao($objCliente->dt_inclusao);
    $oCliente->setDtAlteracao($objCliente->dt_alteracao);
    $oCliente->setObservacoes($objCliente->observacoes);

    $result = $oCliente->import_cliente();
    Log::Msg(3, "Importando Endereco. result [ $result ]");

    if ($result){
        $aResult['status'] = "true";
        $aResult['pk_id_cliente'] = "{$objCliente->pk_id_cliente}";
    }
    else {
        $aResult['status'] = "false";
        $aResult['pk_id_cliente'] = "{$objCliente->pk_id_cliente}";
    }
    return $aResult;

}

// requisicao para uso do servico
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>