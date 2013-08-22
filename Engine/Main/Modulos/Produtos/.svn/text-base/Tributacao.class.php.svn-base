<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();
/**
 * @package  :Produtos
 * @name     :Manutencao de Tributacoes
 * @class    :Tributacao.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :24/11/2010
 * @version  :1.0
 * @revision :
 * @Diretorio:Main/Modulos/Produtos/
 * Classe Responsavel pela Manutencao das Tributacoes de Produtos
 *
 * Tabelas envolvidas
 * tb_tributacao
*/

class Tributacao {

    private $pk_id_tributacao = "";
    private $porcentagem      = "";
    private $tributacao       = "";
    private $descricao        = "";

    function __construct(){
        Log::Msg(2,"Class[ Tributacao ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->pk_id_tributacao = $_REQUEST['pk_id_tributacao'];
        $this->porcentagem      = $_REQUEST['porcentagem'];
        $this->tributacao       = $_REQUEST['tributacao'];
        $this->descricao        = $_REQUEST['descricao'];

    }


    public function criaAtualiza(){
        Log::Msg(2,"Class[ Tributacao ] Method[ criaAtualiza ]");
        Log::Msg(3,"id[ {$this->pk_id_categoria} ]");

        $record = new Repository();

        if ($this->pk_id_tributacao != 0) {
            $sql = "UPDATE tb_tributacao SET tributacao = '{$this->tributacao}', porcentagem = {$this->porcentagem}, descricao = '{$this->descricao}' WHERE pk_id_tributacao = {$this->pk_id_tributacao}";
            $record->store($sql);
        }
        else {
            $sql = "INSERT INTO tb_tributacao (pk_id_tributacao, tributacao, porcentagem, descricao) VALUES ('', '{$this->tributacao}', {$this->porcentagem}, '{$this->descricao}')";
            $this->pk_id_tributacao = $record->store($sql);
        }

        $this->load_tributacao();
    }


    function load_tributacao(){
        Log::Msg(2,"Class[ Tributacao ] Method[ load_tributacao ]");

        $record = new Repository();

        $sql = "SELECT * FROM tb_tributacao WHERE pk_id_tributacao = {$this->pk_id_tributacao}";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            echo "{success: true,data:";
            echo json_encode($results->rows[0]);
            echo "}";
        }
    }


    function load_tributacoes(){
        Log::Msg(2,"Class[ Tributacao ] Method[ load_tributacoes ]");

        $record = new Repository();

        $sql = "SELECT * FROM tb_tributacao ";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }


    function load_tributacao_cmb(){
        Log::Msg(2,"Class[ Tributacao ] Method[ load_tributacao_cmb ]");

        $record = new Repository();

        $sql = "SELECT pk_id_tributacao, tributacao FROM tb_tributacao ";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }

    public function delete_tributacoes(){
        Log::Msg(2,"Class[ Tributacao ] Method[ delete_tributacoes ]");

        if (is_array($this->pk_id_tributacao)) {
            $id = implode(',', $this->pk_id_tributacao);
        }
        else {
            $id = $this->pk_id_tributacao;
        }
        $record = new Repository();
        $sql = "DELETE FROM tb_tributacao WHERE pk_id_tributacao IN ({$id})";
        $record->delete($sql);

    }
}
?>