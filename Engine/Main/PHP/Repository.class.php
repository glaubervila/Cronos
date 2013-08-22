<?php
/**
 * @Class Repository
 * @author  :Glauber Costa Vila-Verde
 * @e-mail  :glauber.vila.verde@gmail.com
 * @date    :14/04/2010
 * @revision:28/04/2010
 * @description: Esta classe prove os metodos
 * necessarios para manipular cole�oes de objetos.
 * classe baseada em exemplos do livro PhpOO de Pablo d'Oglio
 * @dependencias:
 *    Transaction.class.php
 */

final class Repository {

    public $db_name     = 'mysql'; // Nome do banco de dados
    public $auto_commit = 1;  // 1 Ligado 0 Desligado
    public $log         = 1;  // 1 Ligado 0 Desligado
    public $paginacao   = 1;  // 1 Ligado 0 Desligado
    public $charset     = "utf8";

    private $_fields   = ""; // uma string separada por virgula com os campos
    private $_query    = "";
    private $_start    = "";
    private $_limit    = "";
    private $_sort     = "";
    private $_asc      = "";
    private $_dir      = "";


    /**
     * Metodo __construct()
     *  instacia um Repositorio de objetos
     */
    function __construct($db_name){

        $this->db_name = $db_name ? $db_name : $this->db_name;
        $this->_fields= $_REQUEST['fields'];
        $this->_query = $_REQUEST['query'];
        $this->_start = $_REQUEST['start'] ? intval($_REQUEST['start']) : 0;
        $this->_limit = $_REQUEST['limit'] ? intval($_REQUEST['limit']) : 0;
        $this->_sort  = $_REQUEST['sort'];

        // Tratamento Para Sentido da Ordenacao que pode vir de  asc ou dir
        if ($_REQUEST['asc']){
            $this->_asc   = $_REQUEST['asc'] ? $_REQUEST['asc'] : ' ASC';
        }
        elseif ($_REQUEST['dir']){
            $this->_asc   = $_REQUEST['dir'] ? $_REQUEST['dir'] : ' ASC';
        }
    }

    function setLog($log){
        $this->log = $log;
        Log::Msg(6,"Repository [ Log: {$this->log} ]");
    }

    function setCommit($commit){
        $this->auto_commit = $commit;
        Log::Msg(6,"Repository [ Auto Commit: {$this->auto_commit} ]");
    }

    function setPaginacao($paginacao){
        $this->paginacao = $paginacao;
        Log::Msg(6,"Repository [ Paginacao: {$this->paginacao} ]");
    }

    function setCharset($charset){
        $this->charset = $charset;
        Log::Msg(6,"Repository [ Charset: {$this->charset} ]");
    }


    /**
     * Metodo load()
     *  Recuperar um conjunto de objetos (collection) da base de dados
     *  atraves de um criterio  de selecao, e instancia-los em memoria
     *  @param $query = String SQL
     *  @return = Objeto com atributo rows = array de objetos,
     *  atributo count = inteiro com total de objetos
     */
    function load($query){
        try {
            // Clausulas Where
            $query .= $this->setWhere($query);
            // Clausulas Order By e Limit
            $query = $this->setOrder($query);

            // Abrindo conexao
            Transaction::open($this->db_name);
            // Inicia transacao
            if ($conn = Transaction::get()){
                // Definindo Charset UTF-8
                $conn->exec("SET NAMES {$this->charset}");
                // Excuta a consulta no banco de dados
                $result = $conn->Query($query);
                if ($result){
                    // Percorre os resultados da consulta, retornando um array de objetos
                    while ($row = $result->fetchObject()){
                        // Armazena no array $results
                        $results->rows[] = $row;
                        $results->count++;
                    }
                }
                if ($this->log == 1) {Log::Msg(1,"Status[ OK ] Results[ {$results->count} ] Query[ $query ]");}

                // Fechando conexao
                if ($this->auto_commit == 1) {Transaction::close();}

                return $results;
            }
            else {
                // Se nao houver transacao, retorna excecao
                Log::Msg(0,"Status[ EXCEPTION ] Message[Nao ha transacao ativa!]");
                throw new Exception('Nao ha! transacao ativa!');
            }
        }
        catch (Exception $e) {
            // recebe a mensagem de erro
            $error = $e->getMessage();
            $error_code = $e->getCode();
            Log::Msg(0,"Status[ ERROR ] Code [ $error_code ] Message[ $error ] Query[ $query ]");
            // desfaz operacoes realizadas durante a transacao
            Transaction::rollback();

            //echo "{sucess:false, msg:'Desculpe! Mas não foi possivel Carregar o(s) registro(s).', code: [$error_code]}";
        }
    }

    public function setOrder($query){
        // Ordenacao
        if ($this->_sort)  {$query .= " ORDER BY {$this->_sort} {$this->_asc}";}

        // Paginacao
        if ($this->paginacao == 1) {
            if ($this->_limit) {$query .= " LIMIT {$this->_limit} OFFSET {$this->_start}";}
        }

        return $query;
    }

    /**
     * Metodo: setWhere
     * @param : $query  = uma string para ser usada como condicao
     * @return: string com clausula where
     * @description: Gera uma clausula where para ser usada como filtro
     */
    public function setWhere($query) {

        if ($this->_fields and $this->_query) {
            $fields = $this->_fields;
            $query  = $this->_query;

            // Retirando os []
            $fields = substr($fields,0,-1);
            $fields = substr($fields,1);
            // Retirando ""
            $fields = str_replace("\"","",$fields);
            $fields = str_replace("\\","",$fields);
            // Convertendo para array
            $afields = explode(',',$fields);

            $where = " WHERE ";
            $qtd_fields = sizeof($afields);
            $k = 0;
            // montando where
            foreach ($afields as $field) {
                $where .= " $field LIKE \"%$query%\" ";
                if ($qtd_fields > 1 and $k < ($qtd_fields -1 )) {$where .= " OR ";}
                $k++;
            }
            return $where;
        }
    }

    public function delete($query){
        try {
            // Abrindo conexao
            Transaction::open($this->db_name);
            // Inicia transacao
            if ($conn = Transaction::get()){
                // Definindo Charset UTF-8
                $conn->exec("SET NAMES {$this->charset}");
                // Excuta a consulta no banco de dados
                $result = $conn->exec($query);
                Log::Msg(1,"Status[ OK ] Query[ $query ]");

                // Fechando conexao
                if ($this->auto_commit == 1) {Transaction::close();}

                return $result;
            }
            else {
                // Se nao houver transacao, retorna excecao
                Log::Msg(0,"Status[ EXCEPTION ] Message[ Nao ha transacao ativa! ]");
                throw new Exception('Nao ha! transacao ativa!');
            }
        }
        catch (Exception $e) {
            // recebe a mensagem de erro
            $error = $e->getMessage();
            $error_code = $e->getCode();
            Log::Msg(0,"Status[ ERROR ] Code [ $error_code ] Message[ $error ] Query[ $query ]");
            // desfaz operacoes realizadas durante a transacao
            Transaction::rollback();
            return FALSE;
            //echo "{sucess:false, msg:'Desculpe! Mas não foi possivel Excluir o(s) registro(s).', code: [$error_code]}";
        }
    }

    public function store($query){
        try {
            // Abrindo conexao
            Transaction::open($this->db_name);
            // Inicia transacao
            if ($conn = Transaction::get()){
                // Definindo Charset UTF-8
                $conn->exec("SET NAMES {$this->charset}");
                // Excuta a consulta no banco de dados
                $conn->exec($query);
                $result = $conn->lastInsertId($query);

                if ($this->log == 1) { Log::Msg(1,"Status[ OK ] LastId[ $result ] Query[ $query ]");}

                // Fechando conexao
                if ($this->auto_commit == 1) {Transaction::close();}

                if ($result){
                    return $result;
                }
                else {
                    return TRUE;
                }
            }
            else {
                // Se nao houver transacao, retorna excecao
                Log::Msg(0,"Status[ EXCEPTION ] Message[ Nao ha transacao ativa! ]");
                throw new Exception('Nao ha! transacao ativa!');
            }
        }
        catch (Exception $e) {
            // recebe a mensagem de erro
            $error = $e->getMessage();
            $error_code = $e->getCode();
            Log::Msg(0,"Status[ ERROR ] Code [ $error_code ] Message[ $error ] Query[ $query ]");
            // desfaz operacoes realizadas durante a transacao
            Transaction::rollback();
            return FALSE;
            //echo "{sucess:false, msg:'Desculpe! Mas não foi possivel Inserir o(s) registro(s).', code: [$error_code]}";
        }
    }


    public function prepare($query){
        try {
            Transaction::open($this->db_name);
            // Inicia transacao
            if ($conn = Transaction::get()){
                // Definindo Charset UTF-8
                $conn->exec("SET NAMES {$this->charset}");
                // Preparando a Query
                $stmt = $conn->prepare($query);

                Log::Msg(1,"Prepare[ OK ] Query[ $query ]");
                return $stmt;
            }
            else {
                // Se nao houver transacao, retorna excecao
                Log::Msg(0,"Status[ EXCEPTION ] Message[ Nao ha transacao ativa! ]");
                throw new Exception('Nao ha! transacao ativa!');
            }
        }
        catch (Exception $e) {
            // recebe a mensagem de erro
            $error = $e->getMessage();
            $error_code = $e->getCode();
            Log::Msg(0,"Status[ ERROR ] Code[ $error_code ] Message[ $error ] Query[ $query ]");
        }
    }

    public function store_stmt($stmt){
        try {
            // Abrindo conexao
            Transaction::open($this->db_name);
            // Inicia transacao
            if ($conn = Transaction::get()){
                // Definindo Charset UTF-8
                $conn->exec("SET NAMES {$this->charset}");
                // Excuta a consulta no banco de dados
                $result = $stmt->execute();
                if ($result == TRUE) {
                    $result = $conn->lastInsertId();
                }
                Log::Msg(1,"Status[ OK ] LastId[ $result ]");

                // Fechando conexao
                if ($this->auto_commit == 1) {Transaction::close();}

                $result = $result != 0 ? $result : TRUE;
                return $result;
            }
            else {
                // Se nao houver transacao, retorna excecao
                Log::Msg(0,"Status[ EXCEPTION ] Message[ Nao ha transacao ativa! ]");
                throw new Exception('Nao ha! transacao ativa!');
            }
        }
        catch (Exception $e) {
            // recebe a mensagem de erro
            $error = $e->getMessage();
            $error_code = $e->getCode();
            Log::Msg(0,"Status[ ERROR ] Code [ $error_code ] Message[ $error ]");
            // desfaz operacoes realizadas durante a transacao
            Transaction::rollback();

            echo "{sucess:false, msg:'Desculpe! Mas não foi possivel Inserir o(s) registro(s).', code: [$error_code]}";
        }
    }

    public function load_stmt($stmt){

        try {
            // Abrindo conexao
            Transaction::open($this->db_name);
            // Inicia transacao
            if ($conn = Transaction::get()){
                // Definindo Charset UTF-8
                $conn->exec("SET NAMES {$this->charset}");
                // Excuta a consulta no banco de dados
                $result = $stmt->execute();

                if ($result){
                    // Percorre os resultados da consulta, retornando um array de objetos
                    while ($row = $stmt->fetchObject()){
                        // Armazena no array $results
                        $results->rows[] = $row;
                        $results->count++;
                    }
                }
                Log::Msg(1,"Select[ OK ] Results[ {$results->count} ]");

                // Fechando conexao
                if ($this->auto_commit == 1) {Transaction::close();}

                return $results;
            }
            else {
                // Se nao houver transacao, retorna excecao
                Log::Msg(0,"Select[ EXCEPTION ] Message[ Nao ha transacao ativa! ]");
                throw new Exception('Nao ha! transacao ativa!');
            }
        }
        catch (Exception $e) {
            // recebe a mensagem de erro
            $error = $e->getMessage();
            $error_code = $e->getCode();
            Log::Msg(0,"Select[ ERROR ] Code [ $error_code ] Message[ $error ]");
            // desfaz operacoes realizadas durante a transacao
            Transaction::rollback();

            echo "{sucess:false, msg:'Desculpe! Mas não foi possivel Carregar o(s) registro(s).', code: [$error_code]}";
        }

    }

    /**
     * Metodo total_count()
     *  @param $query = String SQL
     *  @return = inteiro com total de objetos
     */
    function total_count($query){
        try {
            // Clausulas Where
            $query .= $this->setWhere($query);

            // Abrindo conexao
            Transaction::open($this->db_name);
            // Inicia transacao
            if ($conn = Transaction::get()){
                // Definindo Charset UTF-8
                $conn->exec("SET NAMES {$this->charset}");
                // Excuta a consulta no banco de dados
                $result = $conn->Query($query);

                if ($result){
                    // Percorre os resultados da consulta, retornando um array de objetos
                    while ($row = $result->fetchObject()){
                        // Armazena no array $results
                        $results = $row;
                    }
                }
                if ($this->log == 1) {Log::Msg(1,"Status[ OK ] Results[ {$results->total_count} ] Query[ $query ]");}

                // Fechando conexao
                if ($this->auto_commit == 1) {Transaction::close();}

                return $results;
            }
            else {
                // Se nao houver transacao, retorna excecao
                Log::Msg(0,"Status[ EXCEPTION ] Message[Nao ha transacao ativa!]");
                throw new Exception('Nao ha! transacao ativa!');
            }
        }
        catch (Exception $e) {
            // recebe a mensagem de erro
            $error = $e->getMessage();
            $error_code = $e->getCode();
            Log::Msg(0,"Status[ ERROR ] Code [ $error_code ] Message[ $error ] Query[ $query ]");
            // desfaz operacoes realizadas durante a transacao
            Transaction::rollback();
            return FALSE;
        }
    }



    public function commit(){
        Transaction::close();
    }
}
?>