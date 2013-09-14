<?php
header('Content-Type: text/javascript; charset=UTF-8');
session_start();
/**
 * @package  :Emporium
 * @name     :Manutencao de Integracao Emporium
 * @class    :Integracao_Emporium.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :09/12/2010
 * @version  :1.0
 * @revision :
 * @Diretorio:Main/Modulos/Emporium/
 * Classe Responsavel pela Manutencao das Integracoes com Sistema de Retaguarda Emporium da Conecto Sistemas
 *
 * Tabelas envolvidas
 * tb_integracao_emporium

 * Tipo
 * 1 - Importacao
 * 2 - Exportacao
*/

class Integracao_Emporium {

    private $pk_id_integracao = "";
    private $fk_id_usuario    = "";
    private $tipo             = "";
    private $entidade         = "";
    private $dt_inicio        = "";
    private $dt_termino       = "";
    private $total            = "";
    private $total_erros      = "";
    private $total_exportados = "";
    private $status           = "";
    private $arquivo          = "";


    function __construct(){
        Log::Msg(2,"Class[ Integracao_Emporium ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->pk_id_integracao  = $_REQUEST['pk_id_integracao'];
        $this->fk_id_usuario     = $_REQUEST['fk_id_usuario'];
        $this->tipo              = $_REQUEST['tipo'];
        $this->entidade          = $_REQUEST['entidade'];
        $this->dt_inicio         = $_REQUEST['dt_inicio'];
        $this->dt_termino        = $_REQUEST['dt_termino'];
        $this->total             = $_REQUEST['total'];
        $this->total_erros       = $_REQUEST['total_erros'];
        $this->status            = $_REQUEST['status'];
        $this->arquivo           = $_REQUEST['arquivo'];

    }

    // Acessores
    public function SetTotal($total){$this->total = $total;}
    public function SetTotalExportados($total){$this->total_exportados = $total;}
    public function SetArquivo($arquivo){$this->arquivo = $arquivo;}

    function load_integracao(){
        Log::Msg(2,"Class[ Integracao_Emporium ] Method[ load_integracao ]");

        $record = new Repository();

        $sql = "SELECT integracao.*, DATE_FORMAT(integracao.dt_inicio,'%d-%m-%Y %H:%i:%s') as dt_inicio, DATE_FORMAT(integracao.dt_termino,'%d-%m-%Y %H:%i:%s') as dt_termino, Usuarios.Nome as usuario  FROM tb_integracao_emporium integracao LEFT JOIN Usuarios on integracao.fk_id_usuario = Usuarios.id_usuario WHERE pk_id_integracao = {$this->pk_id_integracao}";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            echo "{success: true,data:";
            echo json_encode($results->rows[0]);
            echo "}";
        }
    }


    function load_integracoes(){
        Log::Msg(2,"Class[ Integracao_Emporium ] Method[ load_integracoes ]");

        $record = new Repository();

        $sql = "SELECT *, DATE_FORMAT(dt_inicio,'%d-%m-%Y %H:%i:%s') as dt_inicio, DATE_FORMAT(dt_termino,'%d-%m-%Y %H:%i:%s') as dt_termino FROM tb_integracao_emporium ";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }

    public function delete_integracoes(){
        Log::Msg(2,"Class[ Integracao_Emporium ] Method[ delete_integracoes ]");

        if (is_array($this->pk_id_integracao)) {
            $id = implode(',', $this->pk_id_integracao);
        }
        else {
            $id = $this->pk_id_integracao;
        }
        $record = new Repository();
        $sql = "DELETE FROM tb_integracao_emporium WHERE pk_id_integracao IN ({$id})";
        $record->delete($sql);

    }

    public function criar_registro($tipo, $entidade){
        Log::Msg(2,"Class[ Integracao_Emporium ] Method[ criar_registro ]");

        $record = new Repository();

        $sql = "INSERT INTO tb_integracao_emporium (pk_id_integracao, fk_id_usuario, tipo, entidade, status , dt_inicio) VALUES ('', '{$_SESSION["id_Usuario"]}', '$tipo', '$entidade', 0, NOW() ) ";

        $this->pk_id_integracao = $record->store($sql);
        $this->tipo             = $tipo;
        $this->entidade         = $entidade;

        if($this->pk_id_integracao){
            return $this->pk_id_integracao;
        }
    }

    public function atualiza_status($status){
        Log::Msg(2,"Class[ Integracao_Emporium ] Method[ atualiza_status ]");

        $record = new Repository();

        $sql = "UPDATE tb_integracao_emporium set status = $status";

        if($this->total != 0){
            $sql .= ", total = {$this->total}";
        }

        $sql .= " WHERE pk_id_integracao = {$this->pk_id_integracao}";

        $result = $record->store($sql);
        if ($result) {
            return TRUE;
        }
    }

    public function finaliza_integracao(){
        Log::Msg(2,"Class[ Integracao_Emporium ] Method[ finaliza_integracao ]");

        $record = new Repository();
        $sql = "UPDATE tb_integracao_emporium SET dt_termino = NOW() ";

        if ($this->total_exportados != 0) {
            $this->total_erros = ($this->total - $this->total_exportados);
            if ($this->total_erros != 0) {
                $status = 5; // Concluido Com Erros
                $sql .= ", total_erros = {$this->total_erros}";
            }
            else {
                // Se Nao Teve Erro
                $status = 3;
            }
        }
        else {
            $status = 4; // Nao Concluido
        }
        // Se Teve Arquivo
        if($this->arquivo){
            $sql .= ", arquivo = '{$this->arquivo}'";
        }

        $sql .= ", status = $status";
        $sql .= ", total = {$this->total}";
        $sql .= " WHERE pk_id_integracao = {$this->pk_id_integracao}";

        $result = $record->store($sql);
        if ($result) {
            return TRUE;
        }

    }

    /**
     * Verifica se o Servidor Esta Fazendo Alguma Operacao de Importacao ou Exportacao
     */
    public function verifica_disponibilidade(){
        Log::Msg(2,"Class[ Integracao_Emporium ] Method[ verifica_disponibilidade ]");

        $record = new Repository();

        // Se tiver com algum com Status em Andamento retorna servidor ocupado
        $sql = "SELECT pk_id_integracao, dt_inicio FROM `tb_integracao_emporium` WHERE STATUS IN ( 2 )";
        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            return FALSE;
        }

    }
}
?>