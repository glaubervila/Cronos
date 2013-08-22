<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @class   :GruposLojas.class.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :09/06/2010
 * Classe Responsavel pela Manutencao de Grupos de Lojas
 * Utilizado quando a mais de uma empresa ou quando a necessidade de separar lojas por grupos
 * @revision:
 */
class GruposLojas {

    private $_entidade = 'Grupos_Lojas';
    private $_fields   = array("id","Grupo", "Descricao");
    private $_id       = "";  // Chave Primaria
    private $_grupo    = "";  // Nome do Grupo
    private $_descricao= "";  // Descricao

    function __construct(){
        Log::Msg(2,"Class[GruposLojas] Method[__construct]");
        Log::Msg(4, $_REQUEST);
        $this->_id        = $_REQUEST['id'];
        $this->_grupo     = $_REQUEST['Grupo'];
        $this->_descricao = $_REQUEST['Descricao'];
    }

    public function criaAtualizaGrupo(){
        Log::Msg(2,"Class[GruposLojas] Method[criaAtualizaGrupo]");

        $record = new Repository();
        Log::Msg(3,"id[{$this->_id}]");
        if ($this->_id != 0) {
        Log::Msg(3,"Update");
            $sql = "UPDATE {$this->_entidade} SET Grupo = '{$this->_grupo}' , Descricao = '{$this->_descricao}' WHERE id = {$this->_id}";
            $record->store($sql);
        }
        else {
            $sql = "INSERT INTO {$this->_entidade} (".implode(',', $this->_fields).") VALUES ('','{$this->_grupo}', '{$this->_descricao}')";
            $this->_id = $record->store($sql);
        }

        $this->getGrupo();
    }

    /**
     *@return {success: true, data:{"id":"","Grupo":"","Descricao":""}}
     */
    public function getGrupo(){
        Log::Msg(2,"Class[GruposLojas] Method[getGrupo]");

        $record = new Repository();

        $sql = "SELECT ".implode(',', $this->_fields)." FROM {$this->_entidade} WHERE id = {$this->_id}";

        $results = $record->load($sql);
        Log::Msg(5,$results);

        if ($results->count != 0) {
            echo "{success: true,data:";
            echo json_encode($results->rows[0]);
            echo "}";
        }
    }


    public function getGrupos(){
        Log::Msg(2,"Class[GruposLojas] Method[getGrupos]");

        $record = new Repository();

        //Total de registros usado na paginacao
        $sql = "SELECT COUNT(id) as total_count FROM {$this->_entidade}";
        $count = $record->total_count($sql);


        $sql = "SELECT ".implode(',', $this->_fields)." FROM {$this->_entidade}";
        $results = $record->load($sql);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$count->total_count}}";
            echo $result;
        }
    }

    public function deleteGrupos(){
        Log::Msg(2,"Class[GruposLojas] Method[deleteGrupos]");

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
}

?>