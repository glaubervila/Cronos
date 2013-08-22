<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @class   :Permissoes.class.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :19/04/2010
 * @descricao: Classe Responsavel pelas Permissoes do usuario
   0 permissao negativa/1 permissao positiva
   cada usuario tem que ter um registro para cada tela no sistema e tb as respectivas categorias a qual ele vai ter acesso  implementacao futura ( cada usuario vai ter um registro para cada tela somente, e para o controle de acesso tera que  ter * uma flag marcada em cada pagina)
 * @revision:27/05/2010
   - inclusao da permissao de impressao
   - as permissoes passaram a ser tratadas por grupos de usuarios, as querys que usavam o id_usuario passaram a usar id_Grupo. Obs. O CAMPO NA TABELA PERMISSOES NAO MUDOU CONTINUA SENDO USUARIO mas refere-se ao id do grupo do usuario.
 */
class Permissoes {

    private $_entidade = 'Permissoes';
    private $_fields   = array ('id', 'Usuario', 'Tela', 'sel', 'ins', 'upd', 'del', 'imp', 'exc');
    private $_usuario      = null; // Id de do Usuario Logado ($_SESSION["id_Usuario"])
    private $_grupo        = null; // Id de do Grupo de Usuario Logado ($_SESSION["id_Grupo"])
    private $_id_Usuario   = null; // Id de Usuario ($_REQUEST['id_Usuario']) retorno da grid (refere ao campo Usuario na tabela)
    private $_tela         = null;
    private $_permissoes   = null;

    /**
     *
     */
    function __construct(){
        Log::Msg(2,"Class[Permissoes] Method[__construct]");
        Log::Msg(4,$_REQUEST);
        $this->_usuario    = $_SESSION["id_Usuario"];
        $this->_grupo      = $_SESSION["id_Grupo"];
        $this->_id_Usuario = $_REQUEST['id_Usuario'];
        $this->_tela       = $_REQUEST['tela'];
        $this->_permissoes = stripslashes($_REQUEST['dados']);
    }

    /**
     * Metodo get_todas_categorias()
     * utiliza o metodo get_categorias() e retorna um json contendo id, e titulo
     * @return $json = {rows:{"id":"","titulo":""}}
     */
    public function get_todas_categorias() {
        Log::Msg(2,"Class[Permissoes] Method[get_todas_categorias]");
        $aCategorias = $this->get_categorias();
        echo "{rows:";
        $k = 0;
        foreach ($aCategorias as $categoria){
            $rows[$k]->id = $categoria->id;
            $rows[$k]->titulo = $categoria->titulo;
            $k++;
        }
        echo json_encode($rows);
        echo "}";
    }

    /**
     *
     */
    public function get_todas_permissoes() {
        Log::Msg(2,"Class[Permissoes] Method[get_todas_permissoes]");

        // Recuperar todas as Telas que nao sejao Categorias
        Log::Msg(3,"Recuperando Todas as Telas Habilitadas Grupo_Usuario [ {$this->_id_Usuario} ]");
        $sql = "SELECT id as id_tela, Root, Titulo, Descricao FROM Telas  WHERE Root <> 0 ";
		// Se o Grupo de Usuario for Diferente Desenvolvimento
		if ($this->_grupo != 2){
			$sql .= " AND desenvolvimento <> 1";
		}
        $sql .= " ORDER BY Root, Ordem, Titulo;";
        $record = new Repository();
        $rTelas = $record->load($sql);
        Log::Msg(3,"Para Cada Tela Recuperar as Permissoes");
        if ($rTelas->count != 0) {
            foreach ($rTelas->rows as $tela) {
                $sql = "SELECT id as id_perm, sel, ins, upd, imp, del, exc FROM {$this->_entidade} WHERE Tela = {$tela->id_tela} AND Usuario = {$this->_id_Usuario};";
                $record = new Repository();
                $rPermissoes = $record->load($sql);
                $rPermissoes = $rPermissoes->rows[0];
                // Atribuindo Permissoes
                $tela->id  = $rPermissoes->id_perm != 0 ? $rPermissoes->id_perm  : "";
                $tela->Usuario = $this->_id_Usuario;
                $tela->Tela= $tela->id_tela;
                $tela->sel = $rPermissoes->sel ? $rPermissoes->sel : "0";
                $tela->ins = $rPermissoes->ins ? $rPermissoes->ins : "0";
                $tela->upd = $rPermissoes->upd ? $rPermissoes->upd : "0";
                $tela->del = $rPermissoes->del ? $rPermissoes->del : "0";
                $tela->imp = $rPermissoes->imp ? $rPermissoes->imp : "0";
                $tela->exc = $rPermissoes->exc ? $rPermissoes->exc : "0";
            }
        }
        $rows = json_encode($rTelas->rows);
        $result = "{rows:{$rows},totalCount:{$rTelas->count}}";
        echo $result;
    }

    /**
     *
     */
    public function CriaAtualizaPermissoes(){
        Log::Msg(2,"Class[Permissoes] Method[CriaAtualizaPermissoes]");
        $aPermissoes = json_decode($this->_permissoes);
        foreach ($aPermissoes as $permissao) {
            $sql = "SELECT id FROM {$this->_entidade} WHERE Tela = {$permissao->Tela} AND Usuario = {$permissao->Usuario};";
            $record = new Repository();
            $result = $record->load($sql);
            $id = $result->rows[0]->id;

            $sel = $permissao->sel ? 1 : 0;
            $ins = $permissao->ins ? 1 : 0;
            $upd = $permissao->upd ? 1 : 0;
            $del = $permissao->del ? 1 : 0;
            $imp = $permissao->imp ? 1 : 0;
            $exc = $permissao->exc ? 1 : 0;

            // Verificando se ja existe esta permissao (se tiver id fazer Update se nao fazer Insert)
            if ($id) {
                Log::Msg(3,"Alteracao de Permissao");
                $sql = "UPDATE {$this->_entidade} SET sel = $sel, ins = $ins, upd = $upd, del = $del, imp = $imp, exc = $exc WHERE id = {$permissao->id};";
                $record = new Repository();
                $record->store($sql);
            }
            else {
                Log::Msg(3,"Inclusao de Permissao");
                $sql = "INSERT INTO {$this->_entidade} (".implode(",",$this->_fields).") VALUES ('', {$permissao->Usuario}, {$permissao->Tela}, $sel, $ins, $upd, $del, $imp, $exc);";
                $record = new Repository();
                $record->store($sql);
            }
        }
        echo '{"success":true}';
    }
     /**
     *
     */
    public function retorna_permissoes() {
        Log::Msg(2,"Class[Permissoes] Method[retorna_permissoes]");

        $sql = "SELECT P.ins, P.upd, P.del, P.imp, P.exc FROM {$this->_entidade} P, Telas T WHERE P.Tela = T.id AND T.identificacao = '{$this->_tela}' AND P.Usuario = '{$this->_grupo}'";
        $record = new Repository('mysql');
        $results = $record->load($sql);
        foreach ($results->rows as $result) {
            echo json_encode($result);
        }
    }
    /**
     *
     */
    public function retorna_todas_permissoes() {
        Log::Msg(2,"Class[Permissoes] Method[retorna_permissoes]");

        $sql = "SELECT P.id, P.Usuario, P.Tela, T.identificacao, P.ins, P.upd, P.del, P.imp, P.exc FROM {$this->_entidade} P, Telas T WHERE P.Tela = T.id AND P.Usuario = '{$this->_grupo}'";
        $record = new Repository();
        $results = $record->load($sql);
        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }

    /**
     *
     */
    public function menu_dinamico(){
        Log::Msg(2,"Class[Permissoes] Method[menu_dinamico]");

        $aCategorias = $this->get_categorias();

        $k = 0;
        $i = 0;
        $json_itens = null;
        if ($aCategorias) {
            foreach ($aCategorias as $categoria) {
                $k = 0;
                unset($json_itens);

                $aItens = $this->get_itens($categoria->id);
                //var_dump($aItens);
                foreach ($aItens as $item) {
                    $json_itens[$k] = array(
                        titulo      => $item->titulo
                        , eXtype    => $item->eXtype
                        , iconCls   => $item->icone
                        , diretorio => $item->diretorio
                        , arquivo   => stripslashes($item->arquivo)
                    );
                    $k++;
                }
                $json_categorias[$i] = array(
                    titulo => $categoria->titulo
                    , icone  => $categoria->icone
                    , itens  => $json_itens
                );
                $i++;
            }
            echo json_encode($json_categorias);
        }
        else {
            echo '{success: false}';
        }
    }

    /**
     *
     */
    public function get_categorias(){
        Log::Msg(2,"Class[Permissoes] Method[get_categorias]");

        //$sql = "SELECT DISTINCT(T.root) from Telas T, {$this->_entidade} P WHERE T.id = P.Tela AND T.root <> 0 AND P.Usuario = {$this->_usuario} AND P.sel = 1;";
        // So retornar as categorias, se o usuario tiver permissao em pelo menos uma das telas filhas
        $sql = "SELECT DISTINCT(T.root) from Telas T, {$this->_entidade} P WHERE T.id = P.Tela AND P.Usuario = {$this->_grupo} AND P.sel = 1 AND T.root <> 0 ;";
        $record = new Repository('mysql');
        $results = $record->load($sql);
        if ($results->rows) {
            foreach ($results->rows as $categoria) {
                $in[] = $categoria->root;
            }
            $in = implode(',',$in);

            //$sql = "SELECT T.id, T.root, T.titulo, T.icone, T.eXtype  FROM Telas T, {$this->_entidade} P WHERE T.id = P.Tela AND T.root = 0 AND T.id IN ($in) AND P.Usuario = {$this->_usuario} ORDER BY T.ordem;";
            $sql = "SELECT T.id, T.root, T.titulo, T.icone, T.eXtype  FROM Telas T, {$this->_entidade} P WHERE T.id = P.Tela AND T.root = 0 AND T.id IN ($in) ORDER BY T.ordem;";
            $record = new Repository('mysql');
            $results = $record->load($sql);

            return $results->rows;
        }
        else {
            Log::Msg(0,"ERROR: Message[Nenhuma Permissao encontrada para este usuario] Query[$sql]");
            return false;
        }
    }

    /**
     *
     */
    public function get_itens($categoria){
        Log::Msg(2,"Class[Permissoes] Method[get_itens($categoria)]");

        $sql =" SELECT T.id, T.root, T.titulo, T.icone, T.eXtype, T.diretorio, T.arquivo, T.ordem FROM Telas T, {$this->_entidade} P WHERE T.id = P.Tela AND T.root = $categoria AND P.Usuario = {$this->_grupo} AND P.sel = 1 ORDER BY T.ordem, T.titulo";
        $record = new Repository('mysql');
        $results = $record->load($sql);

        return $results->rows;
    }

}

?>