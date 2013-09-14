<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();
/**
 * @package  :Produtos
 * @name     :Manutencao de Categorias
 * @class    :Categoria.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :23/11/2010
 * @version  :1.0
 * @Diretorio:Main/Modulos/Produtos/
 * Classe Responsavel pela Manutencao das Categorias de Produtos
 * @revision:
 * 5003001 - Não Foi Possivel Gravar o Registro
 * 5003002 - Não Foi Possivel Carregar os Registros
 */

class Categoria {

    public $return_json = true;
    public $importacao  = false;

    private $_entidade   = 'tb_produtos_categoria';
    private $_fields     = array("pk_id_categoria", "categoria", "codigo_cor");
    private $_pkey       = "pk_id_categoria";   // Chave Primaria

    private $_pk_id_categoria = "";
    private $_categoria       = "";
    private $_codigo_cor      = "";


    // Acessores
    public function setReturnJson($value){
        $this->return_json = $value;
    }
    public function SetImportacao($importacao){$this->importacao = $importacao;}

    public function Set_id_categoria($id_categoria){$this->_pk_id_categoria = $id_categoria;}
    public function Set_categoria($categoria){$this->_categoria = $categoria;}
    public function Set_codigo_cor($cor){$this->_codigo_cor = $cor;}

    function __construct(){
        Log::Msg(2,"Class[Categoria] Method[__construct]");
        Log::Msg(4, $_REQUEST);

        $this->_pk_id_categoria = $_REQUEST['pk_id_categoria'];
        $this->_categoria       = $_REQUEST['categoria'];
        $this->_codigo_cor      = $_REQUEST['codigo_cor'];
    }


    public function criaAtualiza(){
        Log::Msg(2,"Class[Categoria] Method[criaAtualiza]");

        $record = new Repository();
        Log::Msg(3,"id[{$this->_pk_id_categoria}]");
        if ($this->_pk_id_categoria != 0) {

            $verifica_se_existe = $this->Verifica_se_Existe();

            if ($verifica_se_existe){
                $sql = $this->query_update();
            }
            else {
                $sql = $this->query_insert();
            }
            $result = $record->store($sql);
        }
        else {
            $sql = $this->query_insert();
            $result = $record->store($sql);
            $this->_pk_id_categoria = $result;
        }

        if ($result){
            $this->getCategoria();
        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
            $aResult['code'] = "5003001";
            die(json_encode($aResult));
        }

    }

    public function Verifica_se_Existe(){
        Log::Msg(2,"Class[Categoria] Method[Verifica_se_Existe]");

        $record = new Repository();
        $sql = "SELECT * FROM {$this->_entidade} WHERE {$this->_pkey} = {$this->_pk_id_categoria}";
        $result = $record->load($sql);

        if($result->count != 0){
            return TRUE;
        }
        else {
            return FALSE;
        }

    }

    public function query_update(){
        Log::Msg(2,"Class[Categoria] Method[ query_update ]");
        // Tratamento para Importacao de Categorias do Stock
        // Se for uma importacao nao faz update do codigo de cor
        $sql = "UPDATE {$this->_entidade} SET categoria = '{$this->_categoria}' ";
        if (!$this->importacao){
            $sql .= ", codigo_cor = '{$this->_codigo_cor}' ";
        }
        $sql .= " WHERE {$this->_pkey} = {$this->_pk_id_categoria}";

        return $sql;
    }

    public function query_insert(){
        Log::Msg(2,"Class[Categoria] Method[ query_insert ]");

        if ($this->_pk_id_categoria == 0){ $this->_pk_id_categoria = '';}
        $sql = "INSERT INTO {$this->_entidade} (".implode(',', $this->_fields).") VALUES ('{$this->_pk_id_categoria}','{$this->_categoria}', '{$this->_codigo_cor}')";
        return $sql;
    }

    /**
     *
     */
    public function getCategoria(){
        Log::Msg(2,"Class[Categoria] Method[getCategoria]");

        $record = new Repository();

        $sql = "SELECT * FROM {$this->_entidade} WHERE {$this->_pkey} = {$this->_pk_id_categoria}";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            echo "{success: 'true',data:";
            echo json_encode($results->rows[0]);
            echo "}";
        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
            $aResult['code'] = "5003002";
            die(json_encode($aResult));
        }
    }


    public function getCategorias($json){
        Log::Msg(2,"Class[Categoria] Method[getCategorias]");

        if ($json){
            $this->return_json = $json;
        }

        $record = new Repository();

        $sql = "SELECT * FROM tb_produtos_categoria";

        $results = $record->load($sql);
        if ($results->count != 0) {

            if ($this->return_json){
                $aResult['rows'] = $results->rows;
                $aResult['totalCount']  = $results->count;
                die(json_encode($aResult));
            }
            else {
                return $results->rows;
            }
        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Carregar os registros...";
                $aResult['code'] = "5003002";
                die(json_encode($aResult));
            }
            else {
                return false;
            }
        }

    }

    public function getCategoriasCmb(){
        Log::Msg(2,"Class[Categoria] Method[ getCategoriasCmb ]");

        $record = new Repository();

        $sql = "SELECT pk_id_categoria, categoria FROM {$this->_entidade}";

        $results = $record->load($sql);
        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }

    public function deleteCategoria(){
        Log::Msg(2,"Class[Categoria] Method[deleteCategoria]");

        if (is_array($this->_pk_id_categoria)) {
            $id = implode(',', $this->_pk_id_categoria);
        }
        else {
            $id = $this->_pk_id_categoria;
        }
        $record = new Repository();
        $sql = "DELETE FROM {$this->_entidade} WHERE {$this->_pkey} IN ({$id})";
        $record->delete($sql);

    }
}

?>