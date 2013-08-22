<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @class   :Enderecos.class.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :23/06/2010
 * Classe Responsavel pela Manutencao do Cadastro de  Enderecos
 * @revision:
 */
class Enderecos {

    private $return_json = TRUE;

    private $_entidade = 'tb_endereco';
    private $_record   = null;
    private $_fields   = array("id_endereco", "tipo_endereco","rua", "numero", "bairro", "cidade", "uf", "cep", "complemento", "dt_inclusao", "dt_alteracao", "id_referencia", "id_referencia_pk");

    public $_id            = "";  // Chave Primaria
    public $_tipo          = "";  // Tipo de Endereco(1 - Residecial/2 - Comercial)
    public $_rua           = "";  // Nome da Rua
    public $_numero        = "";  // Numero
    public $_bairro        = "";  // Bairro
    public $_cidade        = "";  // Cidade
    public $_uf            = "";  // Uf
    public $_cep           = "";  // Cep
    public $_complemento   = "";  // Complemento
    public $_id_referencia = "";  // Id que identifica a tela de origem do registro
    public $_id_referencia_pk = "";  // chave primaria do registro a qual pertence esse endereco
    public $_dt_inclusao   = "";  // Data da Inclusao
    public $_dt_alteracao  = "";  // Data da Alteracao

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
        Log::Msg(2,"Class[ Enderecos ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->_record = new Repository();
        // Desligando o auto commit
        $this->_record->setCommit(0);


        $this->_id            = $_REQUEST['id_endereco'];
        $this->_tipo          = $_REQUEST['tipo_endereco'];
        $this->_rua           = $_REQUEST['rua'];
        $this->_numero        = $_REQUEST['numero'];
        $this->_bairro        = $_REQUEST['bairro'];
        $this->_cidade        = $_REQUEST['cidade'];
        $this->_uf            = $_REQUEST['uf'];
        $this->_cep           = $_REQUEST['cep'];
        $this->_complemento   = $_REQUEST['complemento'];
        $this->_dt_inclusao   = $_REQUEST['dt_inclusao'];
        $this->_dt_alteracao  = $_REQUEST['dt_alteracao'];
    }

    /**
     * Uma seguranca extra para nao perder a integridade do registro
     * guardamos o id da tela + a chave primaria a qual pertence o endereço
     */
    public function set_referencia($refencia, $referencia_pk){

        $this->_id_referencia    = $refencia;
        $this->_id_referencia_pk = $referencia_pk;
    }

    public function CriaAtualiza(){
        Log::Msg(2,"Class[ Enderecos ] Method[ CriaAtualiza ]");

        Log::Msg(3,"id[{$this->_id}]");
        if ($this->_id != 0) {
            if ($this->update_endereco()){
                return $this->_id;
            }
        }
        else {
            if ($this->insert_endereco()){
                return $this->_id;
            }
        }
    }


    public function update_endereco(){
        Log::Msg(2,"Class[ Enderecos ] Method[ update_endereco ]");

            Log::Msg(3,"Update [ Entidade: {$this->_entidade} ]");

            $sql = "UPDATE {$this->_entidade} SET tipo_endereco = '{$this->_tipo}', rua = '{$this->_rua}', numero = '{$this->_numero}', bairro = '{$this->_bairro}', cidade = '{$this->_cidade}', uf = '{$this->_uf}', cep = '{$this->_cep}', complemento = '{$this->_complemento}', dt_alteracao = NOW(), id_referencia = '{$this->_id_referencia}', id_referencia_pk = '{$this->_id_referencia_pk}' WHERE {$this->_fields[0]} = {$this->_id}";

            $this->_id = $this->_record->store($sql);

            return TRUE;
    }

    public function insert_endereco(){
        Log::Msg(2,"Class[ Enderecos ] Method[ insert_endereco ]");

        $sql = "INSERT INTO {$this->_entidade} (".implode(', ', $this->_fields).") VALUES ('', '{$this->_tipo}', '{$this->_rua}',  '{$this->_numero}', '{$this->_bairro}', '{$this->_cidade}', '{$this->_uf}', '{$this->_cep}', '{$this->_complemento}', NOW(), '', '{$this->_id_referencia}', '{$this->_id_referencia_pk}' )";

        $this->_id = $this->_record->store($sql);

        return TRUE;

    }

    /**
     *
     */
    public function getEndereco(){
        Log::Msg(2,"Class[ Enderecos ] Method[ getEndereco ]");

        $sql = "SELECT ".implode(', ', $this->_fields)." FROM {$this->_entidade} WHERE {$this->_fields[0]} = {$this->_id}";

        $results = $this->_record->load($sql);
        Log::Msg(5,$results);

        return $results->rows[0];
    }

    public function deleteEnderecos(){
        Log::Msg(2,"Class[ Enderecos ] Method[ deleteEnderecos ]");

        // Desligando o auto commit
        $this->_record->setCommit(0);

        if (is_array($this->_id)) {
            $id = implode(', ', $this->_id);
        }
        else {
            $id = $this->_id;
        }
        $sql = "DELETE FROM {$this->_entidade} WHERE {$this->_fields[0]} IN ({$id})";
        $this->_record->delete($sql);

    }

    public function getEnderecoByReferencia(){
        Log::Msg(2,"Class[ Enderecos ] Method[ getEnderecoByReferencia ]");
        // Verifica se ja existe um endereco para o registro
        // Usando a referencia e o tipo de endereco

        $sql = "SELECT ".implode(', ', $this->_fields)." FROM {$this->_entidade} WHERE {$this->_fields[11]} = '{$this->_id_referencia}' AND {$this->_fields[12]} = '{$this->_id_referencia_pk}'";
        if ($this->_tipo){
            $sql .= " AND {$this->_fields[1]} = '{$this->_tipo}'";
        }

        $results = $this->_record->load($sql);
        Log::Msg(5,$results);

        if ($results->count != 0){
            Log::Msg(3,"Endereco Encontrado,  id_endereco [ {$results->rows[0]->id_endereco} ]");
            return $results->rows[0];
        }
        else {
            return FALSE;
        }
    }



    public function import_endereco(){
        Log::Msg(2,"Class[ Enderecos ] Method[ import_endereco ]");

        $endereco = $this->getEnderecoByReferencia();
        if ($endereco) {
            // Se houver um Endereco com as mesmas referencias e mesmo tipo_endereco
            // Usar o Id do endereco para fazer um update
            $this->setId($endereco->id_endereco);
            if ($this->update_endereco()){
                return $endereco->id_endereco;
            }
            else {
                return FALSE;
            }
        }
        else {
            if($this->insert_endereco()){
                return $this->_id;
            }
            else {
                return FALSE;
            }
        }
    }
}

?>