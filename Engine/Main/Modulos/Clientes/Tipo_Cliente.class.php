<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Clientes
 * @name     :Tipo_Cliente
 * @class    :Tipo_Cliente.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :22/02/2011
 * @Diretorio:Main/Modulos/Clientes/
 * Classe Responsavel pela Manutencao das Tipo de Clientes
 * @revision:
 * @Obs:
 *
 */

class Tipo_Cliente {


    private $pk_tipo_cliente = 0;
    private $tipo_cliente     = '';

    function __construct(){
        Log::Msg(2,"Class[ Tipo_Cliente ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->pk_tipo_cliente = $_REQUEST['pk_tipo_cliente'];
        $this->tipo_cliente    = $_REQUEST['tipo_cliente'];
    }


    public function criaAtualiza(){
        Log::Msg(2,"Class[ Tipo_Cliente ] Method[ criaAtualiza ]");
        Log::Msg(3,"id[ {$this->pk_tipo_cliente} ]");

        $record = new Repository();

        if ($this->pk_tipo_cliente != 0) {
            $sql = "UPDATE tb_tipo_cliente SET tipo_cliente = '{$this->tipo_cliente}' WHERE pk_tipo_cliente = {$this->pk_tipo_cliente}";
            $record->store($sql);
        }
        else {
            $sql = "INSERT INTO tb_tipo_cliente (pk_tipo_cliente, tipo_cliente) VALUES ('', '{$this->tipo_cliente}')";
            $this->pk_tipo_cliente = $record->store($sql);
        }

        $this->get_tipo_cliente();
    }


    function get_tipo_cliente(){
        Log::Msg(2,"Class[ Tributacao ] Method[ get_tipo_cliente ]");

        $record = new Repository();

        $sql = "SELECT * FROM tb_tipo_cliente WHERE pk_tipo_cliente = {$this->pk_tipo_cliente}";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            echo "{success: true,data:";
            echo json_encode($results->rows[0]);
            echo "}";
        }
    }

    function load_tipos_clientes(){
        Log::Msg(2,"Class[ Tipo_Cliente ] Method[ load_tipos_clientes ]");

        $record = new Repository();

        $sql = "SELECT * FROM tb_tipo_cliente ";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }


    public function getTipo_ClienteById($id) {
        Log::Msg(2,"Class[ Tipo_Cliente ] Method[ getTipo_ClienteById ] Param[ $id ]");

        $sql = "SELECT * FROM tb_tipo_cliente WHERE pk_tipo_cliente = $id";

        $record = new Repository();
        $results = $record->load($sql);
        Log::Msg(5,$results);

        if ($results->count != 0) {
            return $results->rows[0];
        }
    }

    public function getTipo_Clientecmb(){
        Log::Msg(2,"Class[ Tipo_Cliente ] Method[ getTipo_Cliente ]");

        $sql = "SELECT pk_tipo_cliente, tipo_cliente FROM tb_tipo_cliente";

        $record = new Repository();
        $results = $record->load($sql);
        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }

    }


}
?>