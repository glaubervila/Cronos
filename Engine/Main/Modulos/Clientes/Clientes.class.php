<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Clientes
 * @name     :Manutencao de Clientes
 * @class    :Clientes.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :12/07/2010
 * @version  :1.0
 * @Diretorio:Main/Modulos/Clientes/
 * Classe Responsavel pela Manutencao do Cadastro de Clientes
 * @revision:
 * Obs:
 * Tipo de Pessoa
    ['1', 'Pessoa Física']
    ['2', 'Pessoa Jurídica']

 * Status
    [ 0, 'Ativo']
    [ 1, 'Inativo']
    [ 2, 'Lista Negra']

 * Status_Servidor
    [ 0, A Enviar ]
    [ 1, Enviado ]

 * Erros Previstos
    4001001 - CPF/CNPJ ja cadastrado
    4001002 - Erro no Insert ou Update
 */

class Clientes {

    private $return_json   = TRUE;

    private $_entidade      = 'tb_clientes';
    private $_record        = null;

    private $_pkey          = "pk_id_cliente";
    private $_fields        = array("fk_id_loja", "fk_id_endereco", "fk_id_usuario", "tipo", "tipo_cliente", "status", "nome", "cpf", "cnpj", "rg", "inscricao_estadual", "dt_nascimento", "sexo", "profissao", "estado_civil", "telefone_fixo", "telefone_movel", "email", "status_servidor", "dt_inclusao", "dt_alteracao", "observacoes, vendedor");

    private $_objEndereco        = null;

    private $_pk_id_cliente      = "";  // Chave Primaria
    private $_fk_id_loja         = "";  // Chave Estrangeira com a tabela lojas
    private $_fk_id_endereco     = "";  // Chave Estrangeira com a tabela enderecos
    private $_fk_id_usuario      = "";  // Chave Estrangeira com a tabela usuarios
    private $_tipo               = "";
    private $_tipo_cliente       = "";
    private $_status             = "";
    private $_nome               = "";
    private $_cpf                = "";
    private $_cnpj               = "";
    private $_rg                 = "";
    private $_inscricao_estadual = "";
    private $_dt_nascimento      = "";
    private $_sexo               = "";
    private $_profissao          = "";
    private $_estado_civil       = "";
    private $_telefone_fixo      = "";
    private $_telefone_movel     = "";
    private $_email              = "";
    private $_status_servidor    = ""; // Flag se o Registro ja foi enviado ao servidor
    private $_dt_inclusao        = "";
    private $_dt_alteracao       = "";
    private $_observacoes        = "";
    private $_vendedor           = "";


    // Acessores
    public function setObjEndereco($obj){
        $this->_objEndereco = $obj;
    }

    public function setReturnJson($value){
        $this->return_json = $value;
    }


   /**
    * Overloading.
    *
    * Esse método não é chamado diretamente. Ele irá interceptar chamadas
    * a métodos não definidos na classe. Se for um set* ou get* irá realizar
    * as ações necessárias sobre as propriedades da classe.
    * @param string $metodo O nome do método quer será chamado
    * @param array $parametros Parâmetros que serão passados aos métodos
    * @return mixed
    */
    public function __call ($metodo, $parametros) {
        // se for set*, "seta" um valor para a propriedade
        if (substr($metodo, 0, 3) == 'set') {
            $var = '_' . substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
            $this->$var = $parametros[0];
        }
        // se for get*, retorna o valor da propriedade
        if (substr($metodo, 0, 3) == 'get') {
            $var = '_' . substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
            return $this->$var;
        }
    }


    function __construct(){
        Log::Msg(2,"Class[ Clientes ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->_record = new Repository();

        $this->_pk_id_cliente        = $_REQUEST['pk_id_cliente'];
        $this->_fk_id_loja           = $_REQUEST['fk_id_loja'];
        $this->_fk_id_endereco       = $_REQUEST['id_endereco'];

        $this->_tipo                 = $_REQUEST['tipo'];
        $this->_tipo_cliente         = $_REQUEST['tipo_cliente'];
        $this->_status               = $_REQUEST['status'];

        $this->_nome                 = ucwords($_REQUEST['nome']);
        $this->_dt_nascimento        = Common::converte_data($_REQUEST['dt_nascimento']);
        $this->_sexo                 = $_REQUEST['sexo'];

        $this->_profissao            = ucwords($_REQUEST['profissao']);
        $this->_estado_civil         = $_REQUEST['estado_civil'];
        $this->_telefone_fixo        = $_REQUEST['telefone_fixo'];

        $this->_telefone_movel       = $_REQUEST['telefone_movel'];
        $this->_email                = strtolower($_REQUEST['email']);
        $this->_status_servidor      = $_REQUEST['status_servidor'];
        $this->_dt_inclusao          = $_REQUEST['dt_inclusao'];

        $this->_dt_alteracao         = $_REQUEST['dt_alteracao'];
        $this->_observacoes          = $_REQUEST['observacoes'];
        $this->_vendedor             = $_REQUEST['vendedor'];

        // Tratamento Pessoa Fisica ou Juridica

        // Fisica
        if ($this->_tipo == 1) {
            $this->_cpf = $_REQUEST['cpf_cnpj'];
            $this->_rg  = $_REQUEST['rg_ie'];
        }
        // Juridica
        else {
            $this->_cnpj               = $_REQUEST['cpf_cnpj'];
            $this->_inscricao_estadual = $_REQUEST['rg_ie'];
        }


        $this->_fk_id_usuario = $_SESSION["id_usuario"];


        // Tratamento de Endereco
        $this->_objEndereco = new Enderecos();

        // Tratamento Status Servidor
        if (!$this->_status_servidor){
            $this->_status_servidor = 0;
        }


    }


    public function Get_Next_Key(){
        Log::Msg(2,"Class[ Clientes ] Method[ Get_Next_Key ]");

        $record = new Repository();

        $chave_cliente = Common::getParametro("chave_cliente");

        $sql = "SHOW TABLE STATUS LIKE 'tb_clientes'";
        $result = $record->load($sql);
        $total = $result->rows[0]->Auto_increment;

        return $total;
    }


    public function Verifica_CPF_CNPJ(){
        Log::Msg(2,"Class[ Clientes ] Method[ Verifica_CPF_CNPJ ]");

        // Recuperar o Parametro cliente_verificar_cpf_cnpj
        $verifica_cpfcnpj = Common::getParametro("cliente_verificar_cpf_cnpj");

        if ($verifica_cpfcnpj == 0){
            // Desabilitado nao Valida Cpf e nem CNPJ aceita valores em branco
            return 0;
        }
        else {
            // Habilitado Valida Cpf e  CNPJ NAO ACEITA  valores em branco ou duplicados
            // Tratamento Pessoa Fisica ou Juridica
            if ($this->_tipo == 1) {
                // Fisica
                $query = "SELECT {$this->_pkey} FROM {$this->_entidade} WHERE cpf = '{$this->_cpf}'";
            }
            else {
                // Juridica
                $query = "SELECT {$this->_pkey} FROM {$this->_entidade} WHERE cnpj = '{$this->_cnpj}'";
            }

            $results = $this->_record->load($query);

            if ($results->count != 0) {
                return $results->rows[0]->pk_id_cliente;
            }
            else {
                return 0;
            }
        }
   }

    public function gera_stmt($query){
        Log::Msg(2,"Class[ Clientes ] Method[ gera_stmt ]");

        $stmt = $this->_record->prepare($query);

        $stmt->bindValue(":pk_id_cliente", $this->_pk_id_cliente);
        $stmt->bindValue(":fk_id_loja", $this->_fk_id_loja);
        $stmt->bindValue(":fk_id_endereco", $this->_fk_id_endereco);

        $stmt->bindValue(":fk_id_usuario", $this->_fk_id_usuario);
        $stmt->bindValue(":tipo", $this->_tipo);
        $stmt->bindValue(":tipo_cliente", $this->_tipo_cliente);
        $stmt->bindValue(":status", $this->_status);

        $stmt->bindValue(":nome", $this->_nome);
        $stmt->bindValue(":cpf", $this->_cpf);
        $stmt->bindValue(":cnpj", $this->_cnpj);

        $stmt->bindValue(":rg", $this->_rg);
        $stmt->bindValue(":inscricao_estadual", $this->_inscricao_estadual);
        $stmt->bindValue(":dt_nascimento", $this->_dt_nascimento);

        $stmt->bindValue(":sexo", $this->_sexo);
        $stmt->bindValue(":profissao", $this->_profissao);
        $stmt->bindValue(":estado_civil", $this->_estado_civil);

        $stmt->bindValue(":telefone_fixo", $this->_telefone_fixo);
        $stmt->bindValue(":telefone_movel", $this->_telefone_movel);
        $stmt->bindValue(":estado_civil", $this->_estado_civil);

        $stmt->bindValue(":email", $this->_email);
        $stmt->bindValue(":status_servidor", $this->_status_servidor);
        //$stmt->bindValue(":dt_inclusao", $this->_dt_inclusao);
        //$stmt->bindValue(":dt_alteracao", $this->_t_alteracao);

        $stmt->bindValue(":observacoes", $this->_observacoes);

        return $stmt;

    }

    public function update_cliente(){
        Log::Msg(2,"Class[ Clientes ] Method[ update_cliente ]");

        // Marco o Status Servidor para 0 - a enviar
        $this->_status_servidor = 0;

        // UPDATE
        $query_update = "UPDATE {$this->_entidade} SET  pk_id_cliente = '{$this->_pk_id_cliente}', fk_id_loja = '{$this->_fk_id_loja}', fk_id_usuario = '{$this->_fk_id_usuario}', fk_id_endereco = '{$this->_fk_id_endereco}', tipo = '{$this->_tipo}', tipo_cliente = '{$this->_tipo_cliente}', status = '{$this->_status}', nome = '{$this->_nome}', cpf = '{$this->_cpf}', cnpj = '{$this->_cnpj}', rg = '{$this->_rg}', inscricao_estadual = '{$this->_inscricao_estadual}', dt_nascimento = '{$this->_dt_nascimento}', sexo = '{$this->_sexo}', profissao = '{$this->_profissao}', estado_civil = '{$this->_estado_civil}', telefone_fixo = '{$this->_telefone_fixo}', telefone_movel = '{$this->_telefone_movel}', email = '{$this->_email}', status_servidor = '{$this->_status_servidor}',  dt_alteracao = NOW(), observacoes = '{$this->_observacoes}', vendedor = '{$this->_vendedor}' WHERE pk_id_cliente = '{$this->_pk_id_cliente}'";


        $result = $this->_record->store($query_update);

        if ($result){
            // Encerrando operação e salvando as alteracoes
            $this->_record->commit();
            return $this->_pk_id_cliente;
        }
        else {
            return FALSE;
        }
    }

    public function insert_cliente(){
        Log::Msg(2,"Class[ Clientes ] Method[ insert_cliente ]");

        $prox = $this->Get_Next_Key();

        $query_insert = "INSERT INTO {$this->_entidade} (pk_id_cliente , fk_id_loja, fk_id_endereco, fk_id_usuario, tipo, tipo_cliente, status, nome, cpf, cnpj, rg, inscricao_estadual, dt_nascimento, sexo, profissao, estado_civil, telefone_fixo, telefone_movel, email, status_servidor, dt_inclusao, dt_alteracao, observacoes, vendedor ) VALUES ('{$this->_pk_id_cliente}', '{$this->_fk_id_loja}', '{$this->_fk_id_endereco}', '{$this->_fk_id_usuario}', '{$this->_tipo}', '{$this->_tipo_cliente}', '{$this->_status}', '{$this->_nome}', '{$this->_cpf}', '{$this->_cnpj}', '{$this->_rg}', '{$this->_inscricao_estadual}', '{$this->_dt_nascimento}', '{$this->_sexo}', '{$this->_profissao}', '{$this->_estado_civil}', '{$this->_telefone_fixo}', '{$this->_telefone_movel}', '{$this->_email}', '{$this->status_servidor}', NOW(), NOW(), '{$this->_observacoes}', '{$this->_vendedor}')";

        $result = $this->_record->store($query_insert);
        $this->_pk_id_cliente = $result;


        if ($result){
            // Encerrando operação e salvando as alteracoes
            $this->_record->commit();

            return $this->_pk_id_cliente;
        }
        else {
            return FALSE;
        }
    }

    public function CriaAtualiza(){
        Log::Msg(2,"Class[ Clientes ] Method[ CriaAtualiza ]");
        // Desligando o auto commit
        $this->_record->setCommit(0);

        // INSERT ou UPDATE ?
        Log::Msg(3,"RegistroId[{$this->_pk_id_cliente}]");

        if ($this->_pk_id_cliente != 0) {
            Log::Msg(3,"Update [ Entidade: {$this->_entidade}] ");

            // Gravando Endereco
            // Para Garantir a integridade guardo tb a tabela e o id do registro principal
            // futuramente pode ser usado como indices para agilizar o processo
            $this->_objEndereco->set_referencia($this->_entidade, "{$this->_pk_id_cliente}");

            $this->_objEndereco->CriaAtualiza();

            $result = $this->update_cliente();

        }
        else {
            // INSERT
            Log::Msg(3,"Insert [ Entidade: {$this->_entidade}]");

            $chave_cliente = Common::getParametro("chave_cliente");

            if ($chave_cliente == 1) {
                // Auto Increment
                $this->_pk_id_cliente = $this->Get_Next_Key();
            }
            else{
                // Cria uma regra para o codigo do cliente
                $data = date('dmy');
                $cod_usuario = str_pad($this->_fk_id_usuario, 3, "0", STR_PAD_LEFT);
                $seconds = microtime(true);
                list($u, $s) = explode('.',microtime(true));
                //$total = round( ($seconds * 1000));
                //$total = str_pad($total, 4, "0", STR_PAD_LEFT);
                $total = $s;
                $this->_pk_id_cliente = $data.$cod_usuario.$total;

                Log::Msg(3,"Montando ID DATA [ $data ] USUARIO [ $cod_usuario ] Total [ $total ]");

            }

            Log::Msg(3,"Verificando ID [{$this->_pk_id_cliente}]");

            // Verificando se Existe CPF/CNPJ
            $verifica_cpf_cnpj = $this->Verifica_CPF_CNPJ();
            if ($verifica_cpf_cnpj != 0){
                //Usuario Existe
                // Retornar mensagem de usuario ja cadastrado
                Log::Msg(0,"Insert[ ERROR ] Message[ CPF/CNPJ ja Cadastrado ]");
                $aResult['failure'] = "true";
                $aResult['msg']  = "Não foi possivel Inserir o(s) registro(s).<br>CPF/CNPJ já Cadastrado.";
                $aResult['code'] = "4001001";
                die(json_encode($aResult));
            }
            else {
                // Gravando Endereco
                // Para Garantir a integridade guardo tb a tabela e o id do registro principal
                // futuramente pode ser usado como indices para agilizar o processo
                $this->_objEndereco->set_referencia($this->_entidade, "{$this->_pk_id_cliente}");
                $this->_fk_id_endereco = $this->_objEndereco->CriaAtualiza();

                $result = $this->insert_cliente();

            }
        }

        if ($result){
            // Carrego o Registro Salvo
            $this->getCliente();
        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro..";
            $aResult['code'] = "4001002";
            die(json_encode($aResult));
        }
    }


    public function Verifica_Cliente($valor, $campo = 'pk_id_cliente'){
        Log::Msg(2,"Class[ Clientes ] Method[ Verifica_Cliente ]");

        $record = new Repository();

        $sql = "SELECT pk_id_cliente FROM tb_clientes WHERE `$campo` = '$valor'";
        $results = $record->load($sql);
        if($results->rows[0]->pk_id_cliente != 0){
            return $results->rows[0]->pk_id_cliente;
        }
        else {
            return FALSE;
        }

    }

    public function getCliente($pkey){
        Log::Msg(2,"Class[ Clientes ] Method[ getCliente ]");

        if ($pkey){
            $this->_pk_id_cliente = $pkey;
        }

        $sql = "SELECT " . $this->_pkey . ',' . implode(', ', $this->_fields)." FROM {$this->_entidade} WHERE {$this->_pkey} = '{$this->_pk_id_cliente}'";
        $results = $this->_record->load($sql);
        Log::Msg(5,$results);
        
        //Recuperando endereco
        //$this->_objEndereco->_id = $results->rows[0]->fk_id_endereco;
        //$endereco = $this->_objEndereco->getEndereco();
        $this->_objEndereco->set_referencia('tb_clientes', "{$this->_pk_id_cliente}");
        $endereco = $this->_objEndereco->getEnderecoByReferencia();

        // Juntando os 2 Objetos (Loja,Endereco)
        $results = Common::mergeObject(array($results->rows[0],$endereco));

        // Tratamento Pessoa Fisica ou Juridica
        // Fisica
        if ($results->tipo == 1) {
            $results->cpf_cnpj = $results->cpf;
            $results->rg_ie    = $results->rg;
        }
        // Juridica
        else {
            $results->cpf_cnpj = $results->cnpj;
            $results->rg_ie    = $results->inscricao_estadual;
        }

        // Tratamento para as Datas
        $results->dt_inclusao = strtotime($results->dt_inclusao);
        $results->dt_inclusao = date('d-m-Y H:i:s',$results->dt_inclusao);

        $results->dt_alteracao = strtotime($results->dt_alteracao);
        $results->dt_alteracao = date('d-m-Y H:i:s',$results->dt_alteracao);

        if ($this->return_json) {
            if ($results) {
                echo "{success: true,data:";
                echo json_encode($results);
                echo "}";
            }
        }
        else {
            if ($results){
                return $results;
            }
            else {
                return false;
            }
        }

    }



    public function getClientes(){
        Log::Msg(2,"Class[ Clientes ] Method[ getClientes ]");

        //Total de registros usado na paginacao
        $sql = "SELECT COUNT(" . $this->_pkey . ") as total_count FROM {$this->_entidade}";
        $count = $this->_record->total_count($sql);

        $sql = "SELECT " . $this->_pkey . ',' . implode(', ', $this->_fields) . " FROM {$this->_entidade}";
        $results = $this->_record->load($sql);
        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$count->total_count}}";
            echo $result;
        }
    }


    public function deleteClientes(){
        Log::Msg(2,"Class[ Clientes ] Method[ deleteClientes ]");

        if (is_array($this->_pk_id_cliente)) {
            $id = implode(',', $this->_pk_id_cliente);
        }
        else {
            $id = "'{$this->_pk_id_cliente}'";
        }

        // Tratamento para Excluir Endereco
        $sql = "SELECT fk_id_endereco FROM {$this->_entidade} WHERE {$this->_pkey} IN ({$id})";
        $results = $this->_record->load($sql);

        // Desligando AutoCommit
        $this->_record->setCommit(0);

        if ($results->count != 0) {
            foreach ($results->rows as $result) {
                //var_dump($result);
                $arr_ids[] = $result->fk_id_endereco;
            }
            $this->_objEndereco->_id = $arr_ids;
            //var_dump($arr_ids);
            $this->_objEndereco->deleteEnderecos();
        }
        $sql = "DELETE FROM {$this->_entidade} WHERE {$this->_pkey} IN ({$id})";
        $result  = $this->_record->delete($sql);
        $this->_record->commit();
        if ($result) {
            echo "{success: true}";
        }
    }


    public function import_cliente(){
        Log::Msg(2,"Class[ Clientes ] Method[ import_cliente ]");

        $cliente = $this->Verifica_Cliente($this->_pk_id_cliente, 'pk_id_cliente');

        if ($cliente){
            $result = $this->update_cliente();
            if ($result){
                return $result;
            }
            else {
                return FALSE;
            }
        }
        else {
            $result = $this->insert_cliente();
            if ($result) {
                return $result;
            }
            else {
                return FALSE;
            }
        }
    }

    public function alterar_status_servidor(){
        Log::Msg(2,"Class[ Clientes ] Method[ alterar_status_servidor ]");

        $record = new Repository();

        $sql = "UPDATE tb_clientes SET status_servidor = 1 WHERE pk_id_cliente = '{$this->_pk_id_cliente}';";

        $results = $record->store($sql);

    }

    /** Metodo:getIdsClientesAEnviar()
     * @Param:
     * @Return: Array(pk_id_cliente = value)
     * Este Metodo Faz uma Busca por todos os registros que estiverem
     * com o campo status_servidor marcado com 0 - a enviar.
     */

    public function getIdsClientesAEnviar(){
        Log::Msg(2,"Class[ Clientes ] Method[ getIdsClientesAEnviar ]");

        $sql = "SELECT pk_id_cliente FROM `tb_clientes` WHERE status_servidor = 0";

        $results = $this->_record->load($sql);
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

    public function RelatorioClientes(){
        Log::Msg(2,"Class[ Clientes ] Method[ RelatorioClientes ]");

        // Recuperar os Clientes
        $sql = "SELECT pk_id_cliente FROM `tb_clientes` WHERE status_servidor = 1";
        $results = $this->_record->load($sql);


        if ($results->count != 0) {
            $this->return_json = false;
            $i = 0;
            foreach ($results->rows as $cliente){
                Log::Msg(3,"[$i] Cliente [ {$cliente->pk_id_cliente} ]");
                $aClientes[] = $this->getCliente($cliente->pk_id_cliente);
                $i++;
            }
            Log::Msg(3,"SAIU DO FOR");
            
        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "Nenhum Registro Encontrado!";
            die(json_encode($aResult));
        }
        Log::Msg(3,"Return");

        return $aClientes;
    }

    public function RelatorioClientesVendedor(){
        Log::Msg(2,"Class[ Clientes ] Method[ RelatorioClientesVendedor ]");

        // Recuperar os Clientes
        $sql = "SELECT pk_id_cliente FROM `tb_clientes` WHERE status_servidor = 1";
        $results = $this->_record->load($sql);


        $objusuario = new Usuarios();
        $usuarios = $objusuario->getArrayUsuarios();

        if ($results->count != 0) {
            $this->return_json = false;
            foreach ($results->rows as $cliente){

                $record = $this->getCliente($cliente->pk_id_cliente);

                $record->usuario_nome = $usuarios[$record->fk_id_usuario];

                $id_vendedor = $record->vendedor;
                $nome_vendedor = Usuarios::getNomeById($id_vendedor);
                $record->vendedor = "$id_vendedor - {$nome_vendedor}";

                $ultimo_pedido = $this->getUltimoPedidoCliente($cliente->pk_id_cliente);

                $record->dt_ultimo_pedido = $ultimo_pedido->dt_ultimo_pedido;
                $record->qtd_pedidos = $ultimo_pedido->qtd_pedidos;
                $record->valor_ultimo_pedido = $ultimo_pedido->valor_ultimo_pedido;

                $aClientes[] = $record;
            }
        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "Nenhum Registro Encontrado!";
            die(json_encode($aResult));
        }

        return $aClientes;
    }


    public function getUltimoPedidoCliente($id){
        Log::Msg(2,"Class[ Clientes ] Method[ getUltimoPedidoCliente($id) ]");


        $sql = "SELECT qtd_itens_entregue as qtd_itens, valor_pagar as valor, DATE_FORMAT(dt_inclusao , '%d/%m/%Y') AS data FROM tb_orcamentos WHERE fk_id_cliente = '$id' ORDER BY data DESC;";
        $results = $this->_record->load($sql);

        $record = new StdClass();
        $record->qtd_pedidos = $results->count;
        $record->dt_ultimo_pedido = $results->rows[0]->data;
        $record->valor_ultimo_pedido = $results->rows[0]->valor;

        return $record;
    }

    function localizar_clientes(){
        Log::Msg(2,"Class[ Clientes ] Method[ localizar_clientes ]");

        $record = new Repository();

        $sql = "SELECT COUNT(*) as total_count FROM tb_clientes ";
        $total = $record->total_count($sql);

        $sql = "SELECT * FROM tb_clientes ";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$total->total_count}}";
            echo $result;
        }
        else {
            echo "{failure:true}";
        }

    }



}

?>