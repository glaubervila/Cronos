<?php
/**
 * @class   :Usuarios.class.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :17/04/2010
 * Classe Responsavel pela Manutencao de Usuarios
 * @revision:17/04/2010
 * @revision:27/05/2010
   - inclusao do campo Grupo na tabela, para tratamento de permissoes por grupo de usuarios
   - Json nao retorna senha
   - criptografia de senha utilizando hash 160 bits
 * @revision:09/06/2010
   - inclusão da variavel nome_Usuario no retorno do metodo verifica_sessao
 * @revision:24/06/2011
   - inclusao dos metodos acessores
 */
class Usuarios {

    public $return_json = true;

    private $_entidade = 'usuarios';
    private $_fields   = array("id_usuario","Grupo", "Nome", "Login", "Senha", "status");
    private $_id       = "";  // Chave Primaria
    private $_Grupo    = "";  // Chave Estrangeira Tabela Grupos_Usuarios campo id
    private $_nome     = "";  // Nome do Usuario
    private $_login    = "";  // Login de Acesso ao Sistema
    private $_senha    = "";  // Senha de Acesso
    private $_senha_form  = "";  // Senha de Acesso Vinda do Formulario de Cadastro
    private $_status   = "1";


    // Acessores
    public function setReturnJson($value){
        $this->return_json = $value;
    }

    public function setId($_id){ $this->_id = $_id; }
    public function setGrupo($_grupo){ $this->_grupo = $_grupo; }
    public function setNome($_nome){ $this->_nome = $_nome; }
    public function setLogin($_login){ $this->_login = $_login; }
    public function setSenha($_senha){ $this->_senha = $_senha; }
    public function setSenhaForm($_senha){ $this->_senha_form = $_senha; }
    public function setStatus($_status){ $this->_status = $_status; }


    function __construct(){
        Log::Msg(2,"Class[Usuarios] Method[__construct]");
        Log::Msg(4, $_REQUEST);
        $this->_id    = $_REQUEST['id'];
        $this->_grupo = $_REQUEST['Grupo'];
        $this->_nome  = $_REQUEST['Nome'];
        $this->_login = $_REQUEST['Login'];
        $this->_senha_form = sha1($_REQUEST['Senha']); // Valor vindo do Formulario de Cadastro (sem Criptografia)
        $this->_senha = $_REQUEST['Senha_Crip'];  // Valor vindo do Formulario de Login (Ja Criptografado)
        $this->_status = $_REQUEST['status'];
    }

    public function criaAtualizaUsuario(){
        Log::Msg(2,"Class[Usuarios] Method[criaAtualizaUsuario]");

        $record = new Repository();
        Log::Msg(3,"id[{$this->_id}]");

        // Verifica se Existe
        $id = $this->verifica_se_existe();

        if ($id) {
            Log::Msg(3,"Update");
            $sql = "UPDATE {$this->_entidade} SET Grupo = '{$this->_grupo}' , Nome = '{$this->_nome}', Login = '{$this->_login}', Senha = '{$this->_senha_form}', status = '{$this->_status}' WHERE id_usuario = {$this->_id}";
            //$record->store($sql);
        }
        else {
            Log::Msg(3,"Insert");
            $sql = "INSERT INTO {$this->_entidade} (".implode(',', $this->_fields).") VALUES ('{$this->_id}','{$this->_grupo}','{$this->_nome}', '{$this->_login}', '{$this->_senha_form}', '{$this->_status}')";
            //$this->_id = $record->store($sql);
        }

        $this->_id = $record->store($sql);
        if ($this->_id){
            if ($this->return_json){
                $this->getUsuario();
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
     *@return {success: true, data:{"id_usuario":"","Grupo":"","Login":"","Nome":""}}
     */
    public function getUsuario($id){
        Log::Msg(2,"Class[Usuarios] Method[getUsuario]");

        $record = new Repository();

        //$sql = "SELECT ".implode(',', $this->_fields)." FROM {$this->_entidade} WHERE id_usuario = {$this->_id}";
        $sql = "SELECT id_usuario, Grupo, Nome, Login, status FROM {$this->_entidade} WHERE id_usuario = {$this->_id}";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            if ($this->return_json){
                echo "{success: true, data:";
                echo json_encode($results->rows[0]);
                echo "}";
            }
            else {
                return $results->rows[0];
            }
        }
    }


    public function getUsuarios(){
        Log::Msg(2,"Class[Usuarios] Method[getUsuarios]");

        $record = new Repository();

        //$sql = "SELECT ".implode(',', $this->_fields)." FROM {$this->_entidade}";
        $sql = "SELECT id_usuario, Grupo, Nome, Login, status FROM {$this->_entidade}";

        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json){
                $rows = json_encode($results->rows);
                $result = "{rows:{$rows},totalCount:{$results->count}}";
                echo $result;
            }
            else {
                return $results->rows;
            }
        }
    }

    public function deleteUsuarios(){
        Log::Msg(2,"Class[Usuarios] Method[deleteUsuarios]");

        if (is_array($this->_id)) {
            $id = implode(',', $this->_id);
        }
        else {
            $id = $this->_id;
        }
        $record = new Repository();
        $sql = "DELETE FROM {$this->_entidade} WHERE id_usuario IN ({$id})";
        $record->delete($sql);

    }


    public function verifica_login(){

        Log::Msg(2,"Class[Usuarios] Method[verifica_login]");

        $_SESSION["isLogado"]     = NULL;
        $_SESSION["id_Usuario"]   = NULL;
        $_SESSION["id_Grupo"]     = NULL;
        $_SESSION["nome_Usuario"] = NULL;

        // Recuperando Usuarios na base
        $sql = "SELECT ".implode(',', $this->_fields)." FROM {$this->_entidade} WHERE Login = '{$this->_login}' AND Senha = '{$this->_senha}' AND status = 1;";
        $record = new Repository('mysql');
        $results = $record->load($sql);

        if ($results->count != 0) {

            $_SESSION["isLogado"]     = TRUE;
            $_SESSION["id_Usuario"]   = $results->rows[0]->id_usuario;
            $_SESSION["id_usuario"]   = $results->rows[0]->id_usuario;
            $_SESSION["id_Grupo"]     = $results->rows[0]->Grupo;
            $_SESSION["nome_Usuario"] = $results->rows[0]->Nome;

            Log::Msg(3,"Usuario encontrado, Sessao iniciada para usuario[{$_SESSION["id_Usuario"]}].");

            echo json_encode(array(
                success   => TRUE
                //, redirect  => 'principal.html'
            ));
        }
        else {

            $_SESSION["isLogado"]     = NULL;
            $_SESSION["id_Usuario"]   = NULL;
            $_SESSION["id_usuario"]   = NULL;
            $_SESSION["id_Grupo"]     = NULL;
            $_SESSION["nome_Usuario"] = NULL;

            Log::Msg(3,"Usuario Nao encontrado, Sessao encerrada.");

            echo json_encode(array(
                success=> FALSE
                , message=> utf8_encode("Verifique usu&aacute;rio e senha.")
            ));
        }
    }

    public function verifica_sessao(){
        Log::Msg(2,"Class[Usuarios] Method[verifica_sessao]");
        Log::Msg(3,"Result[{$_SESSION["isLogado"]}]");

        $nome_usuario = $_SESSION["nome_Usuario"];
        if ($_SESSION["isLogado"] == TRUE) {
            echo json_encode(array(
                success   => TRUE
                , nome_usuario => $nome_usuario
            ));
        }
        else {
            echo json_encode(array(
                success   => FALSE
            ));
        }
    }

    public function getUsuariosFromWebService(){
        Log::Msg(2,"Class[Usuarios] Method[getUsuariosFromWebService]");

        $record = new Repository();

        $sql = "SELECT id_usuario, Grupo, Nome, Login, Senha, status FROM {$this->_entidade}";

        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json){
                $rows = json_encode($results->rows);
                $result = "{rows:{$rows},totalCount:{$results->count}}";
                echo $result;
            }
            else {
                return $results->rows;
            }
        }
    }

    public function verifica_se_existe(){
        Log::Msg(2,"Class[Usuarios] Method[ verifica_se_existe ]");

        if ($this->_id != 0) {

            $json = $this->return_json;
            $this->return_json = false;

            $result = $this->getUsuario();

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

    public function getArrayUsuarios(){

        $this->return_json = false;

        $usuarios = $this->getUsuarios();

        foreach ($usuarios as $usuario){

            $array[$usuario->id_usuario] = $usuario->Nome;

        }
        return $array;
    }

    public function getUsuarioByGrupo($grupo, $json = true){
        Log::Msg(2,"Class[Usuarios] Method[ getUsuarioByGrupo ]");

        if ($grupo){
            $this->_grupo = $grupo;
        }
        if ($json == false){
            $this->return_json = false;
        }

        $record = new Repository();
        $sql = "SELECT id_usuario, Grupo, Nome FROM usuarios WHERE Grupo = '{$this->_grupo}' AND status = 1";

        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json){
                $rows = json_encode($results->rows);
                $result = "{rows:{$rows},totalCount:{$results->count}}";
                echo $result;
            }
            else {
                return $results->rows;
            }
        }
    }


    public function getNomeById($id){
        Log::Msg(2,"Class[Usuarios] Method[ getNomeById ]");

        $record = new Repository();
        $sql = "SELECT Nome FROM usuarios WHERE  id_usuario = '{$id}' ";

        $results = $record->load($sql);
        if ($results->count != 0) {
            return $results->rows[0]->Nome;
        }
    }
}

?>