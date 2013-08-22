<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();
/**
 * @package  :Produtos
 * @name     :Manutencao de Fabricantes
 * @class    :Fabricantes.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :23/11/2010
 * @version  :1.0
 * @Diretorio:Main/Modulos/Produtos/
 * Classe Responsavel pela Manutencao de Fabricantes de Produtos
 * @revision:
*/

class Fabricantes {

    private $_entidade   = 'tb_produtos_fabricantes';
    private $_fields     = array("pk_id_fabricantes", "fabricante");
    private $_pkey       = "pk_id_fabricantes";   // Chave Primaria

    private $_pk_id_fabricantes = "";
    private $_fabricante       = "";               // Nome do Fabricante


    function __construct(){
        Log::Msg(2,"Class[Fabricantes] Method[__construct]");
        Log::Msg(4, $_REQUEST);

        $this->_pk_id_fabricantes = $_REQUEST['pk_id_fabricantes'];
        $this->_fabricante       = $_REQUEST['fabricante'];
    }

    public function criaAtualiza(){
        Log::Msg(2,"Class[Fabricantes] Method[criaAtualiza]");

        $record = new Repository();
        Log::Msg(3,"id[{$this->_pk_id_fabricantes}]");
        if ($this->_pk_id_fabricantes != 0) {
        Log::Msg(3,"Update");
            $sql = "UPDATE {$this->_entidade} SET fabricante = '{$this->_fabricante}' WHERE {$this->_pkey} = {$this->_pk_id_fabricantes}";
            $record->store($sql);
        }
        else {
            $sql = "INSERT INTO {$this->_entidade} (".implode(',', $this->_fields).") VALUES ('','{$this->_fabricante}')";
            $this->_pk_id_fabricantes = $record->store($sql);
        }

        $this->getFabricante();
    }

    /**
     *
     */
    public function getFabricante(){
        Log::Msg(2,"Class[Fabricantes] Method[getFabricante]");

        $record = new Repository();

        $sql = "SELECT * FROM {$this->_entidade} WHERE {$this->_pkey} = {$this->_pk_id_fabricantes}";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            echo "{success: true,data:";
            echo json_encode($results->rows[0]);
            echo "}";
        }
    }


    public function getFabricantes(){
        Log::Msg(2,"Class[Fabricantes] Method[getFabricantes]");

        $record = new Repository();

        $sql = "SELECT * FROM {$this->_entidade}";

        $results = $record->load($sql);
        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }

    public function deleteFabricantes(){
        Log::Msg(2,"Class[Fabricantes] Method[deleteFabricantes]");

        if (is_array($this->_pk_id_fabricantes)) {
            $id = implode(',', $this->_pk_id_fabricantes);
        }
        else {
            $id = $this->_pk_id_fabricantes;
        }
        $record = new Repository();
        $sql = "DELETE FROM {$this->_entidade} WHERE {$this->_pkey} IN ({$id})";
        $record->delete($sql);

    }
}

?>