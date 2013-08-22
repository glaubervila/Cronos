<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @class   :GruposUsuarios.class.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :27/05/2010
 * Classe Responsavel pela Manutencao de Grupos Usuarios
 * @revision:
 */
class GruposUsuarios {

    public $return_json = true;

    private $_entidade = 'grupos_usuarios';
    private $_fields   = array("id","Grupo", "Descricao");
    private $_id       = "";  // Chave Primaria
    private $_grupo    = "";  // Nome do Grupo
    private $_descricao= "";  // Descricao

    // Acessores
    public function setReturnJson($value){
        $this->return_json = $value;
    }

    public function setId($id){ $this->_id = $id; }
    public function setGrupo($grupo){ $this->_grupo = $grupo; }
    public function setDescricao($descricao){ $this->_descricao = $descricao; }

    function __construct(){
        Log::Msg(2,"Class[GruposUsuarios] Method[__construct]");
        Log::Msg(4, $_REQUEST);
        $this->_id        = $_REQUEST['id'];
        $this->_grupo     = $_REQUEST['Grupo'];
        $this->_descricao = $_REQUEST['Descricao'];
    }

    public function criaAtualizaGrupo(){
        Log::Msg(2,"Class[GruposUsuarios] Method[criaAtualizaGrupo]");

        $record = new Repository();

        Log::Msg(3,"id[{$this->_id}]");

        // Verifica se Existe
        $id = $this->verifica_se_existe();

        if ($id) {
            Log::Msg(3,"Update");
            $sql = "UPDATE {$this->_entidade} SET Grupo = '{$this->_grupo}' , Descricao = '{$this->_descricao}' WHERE id = {$this->_id}";
            //$record->store($sql);
        }
        else {
            $sql = "INSERT INTO {$this->_entidade} (".implode(',', $this->_fields).") VALUES ('{$this->_id}','{$this->_grupo}', '{$this->_descricao}')";
            //$this->_id = $record->store($sql);
        }

        $this->_id = $record->store($sql);
        if ($this->_id){
            if ($this->return_json){
                $this->getGrupo();
            }
            else{
                return TRUE;
            }
        }
        else {
            return FALSE;
        }

    }

    /**
     *@return {success: true, data:{"id":"","Grupo":"","Descricao":""}}
     */
    public function getGrupo(){
        Log::Msg(2,"Class[GruposUsuarios] Method[getGrupo]");

        $record = new Repository();

        $sql = "SELECT ".implode(',', $this->_fields)." FROM {$this->_entidade} WHERE id = {$this->_id}";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            if ($this->return_json){
                echo "{success: true,data:";
                echo json_encode($results->rows[0]);
                echo "}";
            }
            else {
                return $results->rows[0];
            }
        }
    }


    public function getGrupos(){
        Log::Msg(2,"Class[GruposUsuarios] Method[getGrupos]");

        $record = new Repository();

        $sql = "SELECT ".implode(',', $this->_fields)." FROM {$this->_entidade}";

        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json) {
                $rows = json_encode($results->rows);
                $result = "{rows:{$rows},totalCount:{$results->count}}";
                echo $result;
            }
            else {
                return $results->rows;
            }
        }
    }

    public function deleteGrupos(){
        Log::Msg(2,"Class[ GruposUsuarios ] Method[deleteGrupos]");

        if (is_array($this->_id)) {
            $id = implode(',', $this->_id);
        }
        else {
            $id = $this->_id;
        }
        $record = new Repository();
        $sql = "DELETE FROM {$this->_entidade} WHERE id IN ({$id})";
        $record->delete($sql);

    }


    public function verifica_se_existe(){
        Log::Msg(2,"Class[ GruposUsuarios ] Method[ verifica_se_existe ]");

        if ($this->_id != 0) {

            $json = $this->return_json;
            $this->return_json = false;

            $result = $this->getGrupo();

            $this->return_json = $json;

            if($result){
                Log::Msg(3, "Registro Existe, Fazer Update");
                return TRUE;
            }
            else {
                Log::Msg(3, "Registro Nao Existe, Fazer Insert");
                return FALSE;
            }

        }
        else {
            return FALSE;
        }
    }

}

?>