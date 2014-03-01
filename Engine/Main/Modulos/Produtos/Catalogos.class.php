<?php
session_start();
/**
 * @package  :Produtos
 * @name     :Manutencao de Catalogos de Produtos
 * @class    :Catalogos.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :19/04/2011
 * @Diretorio:Main/Modulos/Produtos/
 * Classe Responsavel pela Manutencao de Catalogos de Produtos
 * Tem como objetivo criar uma lista de produtos que serão usados como catalogo
 * usa como parametro a quantidade de um item em estoque,
 * tem que permitir tambem a inclusao de excessoes ou seja itens com estoque abaixo do parametro mas que entrarao no catalogo
 * podem ter varios catalogos e um catalogo pode ter varios produtos
 *
 * Tabelas envolvidas
 * tb_catalogos
 * tb_catalogo_produtos
 * 5005001 -
 * 5005002 -
 * 5005003 -
 * 5005004 -
 * 5005005 -
 */


Class Catalogos {

    public $return_json = true;

    protected $pk_catalogo       = 0;
    protected $fk_id_usuario     = 0;
    protected $quantidade_minima = 0.0;
    protected $quantidade_total_produtos = 0.0;
    protected $comentario        = '';
    protected $dt_inclusao       = '';
    protected $pk_id_produto     = 0;

    protected $obj_catalogo_produtos = null;



    // Metodos Acessores
    //SETs
    public function setReturnJson($value){
        $this->return_json = $value;
    }

    public function setPkCatalogo($var){
        $this->pk_catalogo = $var ? $var : '';
    }

    public function setFkIdUsuario($var){
        $this->fk_id_usuario = $var ? $var : $_SESSION["id_Usuario"];
    }

    public function setQuantidadeMinima($var){
        $this->quantidade_minima = $var ? (float)$var : 0;
    }

    public function setQuantidadeTotalProdutos($var){
        $this->quantidade_total_produtos = $var ? (float)$var : 0;
    }

    public function setComentario($var){
        $this->comentario = $var ? (string)$var : '';
    }

    public function setDtInclusao($dt_inclusao){
        $this->dt_inclusao = $dt_inclusao ? (string)$dt_inclusao : '';
    }

    public function setObjCatalogoProdutos($obj_catalogo_produtos){
        $this->obj_catalogo_produtos = $obj_catalogo_produtos;
    }

    public function setPkIdProduto($pk_id_produto){
        $this->pk_id_produto = $pk_id_produto;
    }

    public function setPkCatalogoProduto($pk_catalogo_produto){
        $this->pk_catalogo_produto = $pk_catalogo_produto;
    }
    //GETs
    /**
    * Overloading.
    *
    * Esse método não é chamado diretamente. Ele irá interceptar chamadas
    * a métodos não definidos na classe. Se for um set* ou get* irá realizar
    * as ações necessárias sobre as propriedades da classe.
    * OBS: Os SETs foram comentados para que possa haver validacao independente
    *       Ficando assim so os GETs
    * @param string $metodo O nome do método quer será chamado
    * @param array $parametros Parâmetros que serão passados aos métodos
    * @return mixed
    */
    public function __call ($metodo, $parametros) {
        // se for set*, "seta" um valor para a propriedade
    //     if (substr($metodo, 0, 3) == 'set') {
    //       $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
    //       $this->$var = $parametros[0];
    //     }
        // se for get*, retorna o valor da propriedade
        if (substr($metodo, 0, 3) == 'get') {
        $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
        return $this->$var;
        }
    }

    public function __construct(){
        Log::Msg(2,"Class[ Catalogos ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);


        $this->setPkCatalogo($_REQUEST['pk_catalogo']);
        $this->setFkIdUsuario($_SESSION["id_Usuario"]);
        $this->setQuantidadeMinima($_REQUEST["quantidade_minima"]);
        $this->setQuantidadeTotalProdutos($_REQUEST["quantidade_total_produtos"]);
        $this->setComentario($_REQUEST['comentario']);
        $this->setDtInclusao($_REQUEST['dt_inclusao']);
        $this->setPkIdProduto($_REQUEST["pk_id_produto"]);
        $this->setPkCatalogoProduto($_REQUEST["pk_catalogo_produto"]);
    }

    public function CriaAtualiza(){
        Log::Msg(2,"Class[ Catalogos ] Method[ CriaAtualiza ]");

        // INSERT ou UPDATE ?
        Log::Msg(3,"RegistroId[{$this->pk_catalogo}]");

        if ($this->pk_catalogo != 0) {

            $verifica_se_existe = $this->Verifica_se_Existe();

            if ($verifica_se_existe){
                // UPDATE
                $this->update_catalogo();
            }
            else {
                // INSERT
                $this->insert_catalogo();
            }
        }
        else {
                $this->insert_catalogo();
        }
    }

    public function insert_catalogo(){
        Log::Msg(2,"Class[ Catalogos ] Method[ insert_catalogo ]");

        $record = new Repository();
        $record->setCommit(0);

        // Saber Todos os Pk_id_produtos que satisfacam as condicoes
        $sql_select_ids = "SELECT a.pk_id_produto FROM `tb_produtos` a INNER JOIN tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto WHERE b.quantidade > {$this->quantidade_minima} AND a.url_image <> 'Main/Data/Imagens_Produtos/000000.jpg'";

        $a_produtos = $record->load($sql_select_ids);

        if ($a_produtos->count != 0){
            $this->setQuantidadeTotalProdutos($a_produtos->count);

            $sql_insert_catalogo = "INSERT INTO tb_catalogos (pk_catalogo, fk_id_usuario, quantidade_minima, quantidade_total_produtos, comentario, dt_inclusao) VALUES ('{$this->pk_catalogo}', '{$this->fk_id_usuario}', {$this->quantidade_minima}, {$this->quantidade_total_produtos}, '{$this->comentario}', NOW())";

            $result_catalogo = $record->store($sql_insert_catalogo);

            if ($result_catalogo) {

                $this->setPkCatalogo($result_catalogo);

                foreach ($a_produtos->rows as $produto){

                    $sql_insert_catalogo_produtos = "INSERT INTO tb_catalogo_produtos (pk_catalogo_produto, fk_catalogo, fk_id_produto, excecao) VALUES ('', {$this->pk_catalogo}, {$produto->pk_id_produto}, '')";
                    $result_catalogo_produto = $record->store($sql_insert_catalogo_produtos);


                }
                $record->commit();
                $this->getCatalogoById();
            }
            else {
                // Falha ao inserir
                $record->rollback();
                if ($this->return_json){
                    $aResult['failure'] = "true";
                    $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                    $aResult['code'] = "5005001";
                    die(json_encode($aResult));
                }
                else {
                    return FALSE;
                }
            }
        }
        else {
            // Nao tem produtos a inserir
            $record->rollback();

            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas Não Há Produtos a Colocar no Catalogo,<br> não foi possivel gravar o registro...";
                $aResult['code'] = "5005003";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }


    public function getCatalogos(){
        Log::Msg(2,"Class[ Catalogos ] Method[ getCatalogos ]");

        $record = new Repository();

        $sql = "SELECT * FROM tb_catalogos";

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

    public function getCatalogoById($id_catalogo = null){
        Log::Msg(2,"Class[ Catalogos ] Method[ getCatalogoById ]");

        if ($id_catalogo){
            $this->pk_catalogo = $id_catalogo;
        }

        $record = new Repository();

        $sql = "SELECT * FROM tb_catalogos WHERE pk_catalogo = {$this->pk_catalogo}";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {

            if ($this->return_json){
                // Trata as Datas no retorno do Json
                $data_inclusao = strtotime($results->rows[0]->dt_inclusao);
                $results->rows[0]->dt_inclusao = date('d-m-Y H:i:s',$data_inclusao);

                $data_alteracao = strtotime($results->rows[0]->dt_alteracao);
                $results->rows[0]->dt_alteracao = date('d-m-Y H:i:s',$data_alteracao);


                echo "{success: 'true',data:";
                echo json_encode($results->rows[0]);
                echo "}";
            }
            else {
                return $results->rows[0];
            }
        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
                $aResult['code'] = "5005002";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }

    }

    public function delete_catalogos(){
        Log::Msg(2,"Class[ Catalogos ] Method[ delete_catalogos ]");

        $record = new Repository();
        $record->setCommit(0);

        if (is_array($this->pk_catalogo)) {
            $id = implode(',', $this->pk_catalogo);
        }
        else {
            $id = $this->pk_catalogo;
        }

        // 1°- Todos os produtos do catalogo
        // 2°- Excluir o catalogo

        // catalogo_produtos
        $delete_catalogo_produtos = "DELETE FROM tb_catalogo_produtos WHERE fk_catalogo IN ({$id})";
        $result_catalogo_produtos = $record->delete($delete_catalogo_produtos);

        // Catalogo
        $delete_catalogo = "DELETE FROM tb_catalogos WHERE pk_catalogo IN ({$id})";
        $result_catalogo_produtos = $record->delete($delete_catalogo);


        if ($result_catalogo_produtos && $delete_catalogo) {
            $record->commit();
            if ($this->return_json){
                echo "{success: true}";
            }
            else {
                return TRUE;
            }
        }
        else {
            $record->rollback();
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, <b>NÃO</b> foi possivel excluir o(s) registro(s)...";
                $aResult['code'] = "5005005";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }

    }


    public function getProdutosCatalogoById(){
        Log::Msg(2,"Class[ Catalogos ] Method[ getProdutosCatalogoById ]");

        $record = new Repository();
        $record->setPaginacao(0);

        // Se nao for indicado um catalogo retornar os produtos do catalogo atual
        if ($this->pk_catalogo == 0){
            $this->setReturnJson(FALSE);
            $this->pk_catalogo = $this->getCatalogoAtual();
            $this->setReturnJson(TRUE);
        }

        $sql = "SELECT * FROM tb_catalogo_produtos WHERE fk_catalogo = {$this->pk_catalogo}";

        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json){
                $aResult['success'] = "true";
                $aResult['rows']  = $results->rows;
                die(json_encode($aResult));
            }
            else {
                return $results->rows;
            }
        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
                $aResult['code'] = "5005002";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }

    public function getCatalogoAtual(){
        Log::Msg(2,"Class[ Catalogos ] Method[ getCatalogoAtual ]");

        $record = new Repository();
        $record->setPaginacao(0);
        $sql = "SELECT pk_catalogo, dt_inclusao FROM tb_catalogos ORDER BY dt_inclusao DESC LIMIT 1";

        $results = $record->load($sql);
        if ($results->count != 0) {
            Log::Msg(3,"Catalogo Mais Atual. pk_catalogo[ {$results->rows[0]->pk_catalogo} ]");

            if ($this->return_json){
                $aResult['success'] = "true";
                $aResult['pk_catalogo']  = "{$results->rows[0]->pk_catalogo}";
                $aResult['dt_inclusao'] = "{$results->rows[0]->dt_inclusao}";
                die(json_encode($aResult));
            }
            else {
                return $results->rows[0]->pk_catalogo;
            }

        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Nenhum Registro Encontrado!";
                $aResult['code'] = "";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }


    public function import_catalogo(){
        Log::Msg(2,"Class[ Catalogos ] Method[ import_catalogo ]");

        // INSERT ou UPDATE ?
        Log::Msg(3,"RegistroId[{$this->pk_catalogo}]");

        if ($this->pk_catalogo != 0) {

            $verifica_se_existe = $this->Verifica_se_Existe();

            if ($verifica_se_existe){
                // UPDATE
                return $this->import_catalogo_update();
            }
            else {
                // INSERT
                return $this->import_catalogo_insert();
            }
        }
        else {
            return $this->import_catalogo_insert();
        }


    }


    public function import_catalogo_insert(){
        Log::Msg(2,"Class[ Catalogos ] Method[ import_catalogo_insert ]");

        $record = new Repository();
        $sql_insert_catalogo = "INSERT INTO tb_catalogos (pk_catalogo, fk_id_usuario, quantidade_minima, quantidade_total_produtos, comentario, dt_inclusao) VALUES ('{$this->pk_catalogo}', '{$this->fk_id_usuario}', {$this->quantidade_minima}, {$this->quantidade_total_produtos}, '{$this->comentario}', '{$this->dt_inclusao}')";
        $result_catalogo = $record->store($sql_insert_catalogo);

        if ($result_catalogo) {
            $record->commit();
            return TRUE;
        }
        else {
            $record->rollback();
            return FALSE;
        }
    }


    public function import_catalogo_update(){
        Log::Msg(2,"Class[ Catalogos ] Method[ import_catalogo_update ]");

        $record = new Repository();
        $record->setCommit(0);
        $sql_update_catalogo = "UPDATE tb_catalogos set fk_id_usuario = '{$this->fk_id_usuario}', quantidade_minima = {$this->quantidade_minima}, quantidade_total_produtos = {$this->quantidade_total_produtos}, comentario = '{$this->comentario}', dt_inclusao = '{$this->dt_inclusao}' WHERE pk_catalogo = '{$this->pk_catalogo}'";

        $result_catalogo = $record->store($sql_update_catalogo);

        if ($result_catalogo) {
            // Apagando os Produtos do catalogo
            $sql_delete = "DELETE FROM tb_catalogo_produtos WHERE fk_catalogo = '{$this->pk_catalogo}'";
            $result = $record->delete($sql_delete);

            $record->commit();

            return TRUE;
        }
        else {
            $record->rollback();
            return FALSE;
        }
    }

    public function Verifica_se_Existe(){
        Log::Msg(2,"Class[ Catalogos ] Method[ Verifica_se_Existe ]");

        $record = new Repository();
        $sql = "SELECT pk_catalogo FROM tb_catalogos WHERE pk_catalogo = {$this->pk_catalogo}";
        $result = $record->load($sql);

        if($result->count != 0){
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function import_catalogo_produtos_insert(){
        Log::Msg(2,"Class[ Catalogos ] Method[ insert_catalogo_produtos ]");

        $record = new Repository();
        $sql_insert_catalogo_produtos = "INSERT INTO tb_catalogo_produtos (pk_catalogo_produto, fk_catalogo, fk_id_produto, excecao) VALUES ('{$this->obj_catalogo_produtos->pk_catalogo_produto}', '{$this->pk_catalogo}', '{$this->obj_catalogo_produtos->fk_id_produto}', '{$this->obj_catalogo_produtos->excecao}')";
        $result_catalogo_produto = $record->store($sql_insert_catalogo_produtos);

        if ($result_catalogo_produto) {
            $record->commit();
            return TRUE;
        }
        else {
            $record->rollback();
            return FALSE;
        }

    }



    public function getIdsProdutosCatalogoById(){
        Log::Msg(2,"Class[ Catalogos ] Method[ getIdsProdutosCatalogoById ]");

        $record = new Repository();
        $record->setPaginacao(0);

        // Se nao for indicado um catalogo retornar os produtos do catalogo atual
        if ($this->pk_catalogo == 0){
            $this->setReturnJson(FALSE);
            $this->pk_catalogo = $this->getCatalogoAtual();
            $this->setReturnJson(TRUE);
        }

        $sql = "SELECT fk_id_produto FROM tb_catalogo_produtos WHERE fk_catalogo = {$this->pk_catalogo} ";

        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json){
                $aResult['success'] = "true";
                $aResult['rows']  = $results->rows;
                $aResult['total_count']  = count($results->rows);
                die(json_encode($aResult));
            }
            else {
                return $results->rows;
            }
        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
                $aResult['code'] = "5005002";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }


    /**
     * Retorna um array de objetos produtos com todas a informcoes de
     * de todos os produtos no catalogo
     */
    public function getAllProdutosInCatalogo(){
        Log::Msg(2,"Class[ Catalogos ] Method[ getAllProdutosInCatalogo ]");

        $record = new Repository();
        $record->setPaginacao(0);

        // Se nao for indicado um catalogo retornar os produtos do catalogo atual
        if ($this->pk_catalogo == 0){
            $this->setReturnJson(FALSE);
            $this->pk_catalogo = $this->getCatalogoAtual();
            $this->setReturnJson(TRUE);
        }


        $sql = "SELECT a.*, b.quantidade, b.minimo, b.reposicao, b.maximo, c.preco FROM tb_produtos a INNER JOIN tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto INNER JOIN tb_produtos_preco c ON a.pk_id_produto = c.fk_id_produto  INNER JOIN tb_catalogo_produtos d ON d.fk_id_produto = a.pk_id_produto WHERE d.fk_catalogo = {$this->pk_catalogo}";


        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json){
                $aResult['success'] = "true";
                $aResult['rows']  = $results->rows;
                $aResult['total_count']  = count($results->rows);
                die(json_encode($aResult));
            }
            else {
                return $results->rows;
            }
        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
                $aResult['code'] = "5005002";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }

    }


    /** getProdutosCompletoInCatalogoById()
     * @param: $id_catalogo = chave do catalogo que se quer os produtos
     * Recupera a informacao do catalogo
     * faz uma busca pelos produtos que se encaixam na regra
     * retorna um array de produtos
     */
    public function getProdutosCompletoInCatalogoById($id_catalogo = null){
        Log::Msg(2,"Class[ Catalogos ] Method[ getProdutosCompletoInCatalogoById ]");

        $record = new Repository();

        // Recuperar o Catalogo
        if ($id_catalogo){
            $this->pk_catalogo = $id_catalogo;
            $catalogo = Catalogos::getCatalogoById($id_catalogo);
        }
        else {
            $this->setReturnJson(FALSE);
            $catalogo = Catalogos::getCatalogoById();
            $this->setReturnJson(TRUE);
        }

        $quantidade_minima = $catalogo->quantidade_minima;

        // Saber o Total de Registros
        $sql_count = "SELECT
                        COUNT(*) as total_count
                    FROM
                        tb_produtos a
                            INNER JOIN
                        tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto
                            INNER JOIN
                        tb_produtos_preco c ON a.pk_id_produto = c.fk_id_produto";

        $sql = "SELECT
                    a.pk_id_produto,
                    a.pk_id_produto as codigo,
                    a.fk_id_categoria,
                    a.descricao_curta,
                    a.descricao_longa,
                    a.url_image,
                    b.quantidade,
                    c.preco
                FROM
                    tb_produtos a
                        INNER JOIN
                    tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto
                        INNER JOIN
                    tb_produtos_preco c ON a.pk_id_produto = c.fk_id_produto ";

        $filters[] = "b.quantidade > $quantidade_minima";
        $filters[] = "c.preco > 0";

        $filter = implode(' AND ', $filters);

        $sql = $sql . ' WHERE '. $filter;
        $sql_count = $sql_count . ' WHERE '. $filter;

        // ---------------- QUERY ANTIGA USAVA TABELA AUXILIAR -----------------
        //         // Saber o Total de Registros
        //         $sql = "SELECT COUNT(a.pk_id_produto) as total_count FROM tb_produtos a INNER JOIN tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto INNER JOIN tb_catalogo_produtos c ON c.fk_id_produto = a.pk_id_produto WHERE c.fk_catalogo = {$this->pk_catalogo}";
        //
        //         $count = $record->total_count($sql);

        //$sql = "SELECT a.pk_id_produto, a.descricao_curta, a.descricao_longa, b.quantidade, c.pk_catalogo_produto FROM tb_produtos a INNER JOIN tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto INNER JOIN tb_catalogo_produtos c ON c.fk_id_produto = a.pk_id_produto WHERE c.fk_catalogo = {$this->pk_catalogo}";

        $count = $record->total_count($sql_count);
        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json){
                $aResult['success'] = "true";
                $aResult['rows']  = $results->rows;
                $aResult['total_count']  = $count->total_count;
                die(json_encode($aResult));
            }
            else {
                return $results->rows;
            }
        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
                $aResult['code'] = "5005002";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }

    public function getExcecoesCompletoInCatalogoById(){
        Log::Msg(2,"Class[ Catalogos ] Method[ getExcecoesCompletoInCatalogoById ]");

        $record = new Repository();

        // Saber o Total de Registros
        $sql = "SELECT COUNT(a.pk_id_produto) as total_count FROM tb_produtos a INNER JOIN tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto INNER JOIN tb_catalogo_produtos c ON c.fk_id_produto = a.pk_id_produto WHERE c.fk_catalogo = {$this->pk_catalogo} AND c.excecao = 1";

        $count = $record->total_count($sql);

        $sql = "SELECT a.pk_id_produto, a.descricao_curta, a.descricao_longa, b.quantidade, c.pk_catalogo_produto FROM tb_produtos a INNER JOIN tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto INNER JOIN tb_catalogo_produtos c ON c.fk_id_produto = a.pk_id_produto WHERE c.fk_catalogo = {$this->pk_catalogo} AND c.excecao = 1";

        $results = $record->load($sql);
        if ($results->count != 0) {
            if ($this->return_json){
                $aResult['success'] = "true";
                $aResult['rows']  = $results->rows;
                $aResult['total_count']  = $count->total_count;
                die(json_encode($aResult));
            }
            else {
                return $results->rows;
            }
        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
                $aResult['code'] = "5005002";
                die(json_encode($aResult));
            }
            else {
                return FALSE;
            }
        }
    }

    public function insert_InCatalogoProdutoExcessao(){
        Log::Msg(2,"Class[ Catalogos ] Method[ insert_InCatalogoProdutoExcessao ]");

        $record = new Repository();

        // Verifica se o Produto Ja esta no Catalogo
        if ($this->Verifica_Produto_InCatalogo()) {
            // Retorna Falha
            if ($this->return_json) {
                $aResult['failure'] = "true";
                $aResult['msg']  = "Produto já Está no Catalogo...";
                die(json_encode($aResult));
            }
            else {
                return false;
            }

        }
        else {
            // Procurar se ele ja existe no catalogo
            // testar a quantidade antes de incluir
            $objProduto = new Produtos();
            $objProduto->setPkIdProduto($this->pk_id_produto);
            $quantidade = $objProduto->getQuantidadeById();

            if ($quantidade == 0){
                // Se a quantidade for 0 retorna falha
                if ($this->return_json) {
                    $aResult['failure'] = "true";
                    $aResult['msg']  = "Desculpe mas o produto NÃO possui estoque. <br> Produto Não foi Incluido.";
                    die(json_encode($aResult));
                }
                else {
                    return false;
                }
            }
            else {

                $sql_insert_excessao = "INSERT INTO tb_catalogo_produtos (pk_catalogo_produto, fk_catalogo, fk_id_produto, excecao) VALUES ('', {$this->pk_catalogo}, {$this->pk_id_produto}, '1')";

                $result = $record->store($sql_insert_excessao);

                if ($result){
                    $this->altera_total_produtos_catalogo();
                    //$record->commit();

                    if ($this->return_json) {
                        $aResult['success'] = "true";
                        $aResult['pk_catalogo']    = $this->pk_catalogo;
                        $aResult['pk_id_produto']  = $result;
                        $aResult['quantidade']  = $quantidade;
                        die(json_encode($aResult));
                    }
                    else {
                        return $result;
                    }
                }
                else {
                    $record->rollback();

                    if ($this->return_json) {
                        $aResult['failure'] = "true";
                        $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro..";
                        die(json_encode($aResult));
                    }
                    else {
                        return false;
                    }
                }
            }
        }
    }

    public function delete_CatalogoProduto(){
        Log::Msg(2,"Class[ Catalogos ] Method[ delete_CatalogoProdutoExcessao ]");

        if (is_array($this->pk_catalogo_produto)) {
            $id = implode(',', $this->pk_catalogo_produto);
        }
        else {
            $id = $this->pk_catalogo_produto;
        }


        $record = new Repository();

        $sql_delete = "DELETE FROM tb_catalogo_produtos WHERE pk_catalogo_produto = $id;";

        $result = $record->delete($sql_delete);

        if ($result){
            $this->altera_total_produtos_catalogo();

            $record->commit();

            if ($this->return_json) {
                $aResult['success'] = "true";
                $aResult['pk_catalogo']    = $this->pk_catalogo;
                $aResult['pk_id_produto']  = $result;
                die(json_encode($aResult));
            }
            else {
                return $result;
            }
        }
        else {
            $record->rollback();
            if ($this->return_json) {
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel excluir o registro..";
                die(json_encode($aResult));
            }
            else {
                return false;
            }
        }
    }

    public function altera_total_produtos_catalogo(){
        Log::Msg(2,"Class[ Catalogos ] Method[ altera_total_produtos_catalogo ]");

        $total = $this->getTotalProdutosCatalogo();

        // Atualizando
        $sql_update = "UPDATE tb_catalogos SET quantidade_total_produtos = $total WHERE pk_catalogo = {$this->pk_catalogo};";

        $record = new Repository();
        $result = $record->store($sql_update);

        if ($result){
            return $result;
        }
        else {
            return false;
        }
    }

     public function getTotalProdutosCatalogo(){

        Log::Msg(2,"Class[ Catalogos ] Method[ getTotalProdutosCatalogo ]");

        $record = new Repository();

        // Saber o Total de Produtos
        $sql = "SELECT COUNT(*) as total_count FROM tb_catalogo_produtos WHERE fk_catalogo = {$this->pk_catalogo}";

        $total = $record->total_count($sql);

        return $total->total_count;
    }


    public function Verifica_Produto_InCatalogo(){
        Log::Msg(2,"Class[ Catalogos ] Method[ Verifica_Produto_InCatalogo ]");

        $record = new Repository();
        $sql = "SELECT fk_id_produto FROM tb_catalogo_produtos WHERE fk_catalogo = {$this->pk_catalogo} AND fk_id_produto = {$this->pk_id_produto};";
        $result = $record->load($sql);

        if($result->count != 0){
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

}

?>
