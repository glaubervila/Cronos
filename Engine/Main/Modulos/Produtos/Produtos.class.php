<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();
/**
 * @package  :Produtos
 * @name     :Manutencao de Produtos
 * @class    :Produtos.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :24/11/2010
  * @Diretorio:Main/Modulos/Produtos/
 * Classe Responsavel pela Manutencao de Produtos
 *
 * Tabelas envolvidas
 * tb_produtos
 * tb_produtos_ean
 * tb_produtos_estoque
 * tb_produtos_preco
 * tb_produtos_categoria
 * tb_produtos_fabricantes

 * - Um Produto Pode ter mais de um EAN13
 * - Produto so pode ter 1 SKU
 * - os Unidade e tributacao recebem os valores reais e Não a chave estrangeira das suas tabelas

 * 31/03/2011 - Continuacao do desenvolvimento da classe
 * 06/04/2011 - Criacao dos metodos para incluir e alterar
 * 08/04/2011 - Classe Pronta para uso, insere, le, atualiza e deleta produtos, com opção de foto
 * 18/04/2011 - inclusao dos metodos acessores, alteracao no metodo criaatualiza, inclusao da opcao de modo de importacao
*/

class Produtos {

    // Parametros
    public $return_json = true;

    private $dir_images        = "Main/Data/Imagens_Produtos/";
    private $imagem_default    = "Main/Data/Imagens_Produtos/000000.jpg";
    private $importacao        = FALSE;


    // PRODUTO
    private $pk_id_produto     = 0;
    private $fk_id_categoria   = 0;
    private $fk_id_fabricante  = 0;
    private $descricao_curta   = "";
    private $descricao_longa   = "";
    private $unidade           = "";
    private $tributacao        = "";
    private $garantia          = "";
    private $url_image         = "";
    private $name_image        = "";
    private $dt_inclusao       = "";
    private $dt_alteracao      = "";

    // ESTOQUE
    private $pk_estoque          = 0;
    private $quantidade          = 0;
    private $dt_inclusao_estoque = '';

    // PRECO
    private $pk_preco            = 0;
    private $preco               = 0;
    private $dt_inclusao_preco   = '';

    // EAN
    private $pk_id_ean           = 0;
    private $ean                 = 0;


    // Acessores

    // SETs
    // Parametros
    public function setImportacao($importacao){
        $this->importacao = $importacao ? (bool)$importacao : FALSE;
    }

    public function setReturnJson($value){
        $this->return_json = $value;
    }

    // PRODUTO
    public function setPkIdProduto($pk_id_produto){
        $this->pk_id_produto = $pk_id_produto ? $pk_id_produto : '';
    }
    public function setFkIdCategoria($fk_id_categoria){
        $this->fk_id_categoria = $fk_id_categoria  ? (int)$fk_id_categoria  : 0;
    }
    public function setFkIdFabricante($fk_id_fabricante){
        $this->fk_id_fabricante = $fk_id_fabricante  ? (int)$fk_id_fabricante  : 0;
    }
    public function setDescricaoCurta($descricao_curta){
        $this->descricao_curta = $descricao_curta ? (string)$descricao_curta : '';
    }
    public function setDescricaoLonga($descricao_longa){
        $this->descricao_longa = $descricao_longa ? (string)$descricao_longa : '';
    }
    public function setUnidade($unidade){
        $this->unidade = $unidade ? (string)$unidade : '';
    }
    public function setTributacao($tributacao){
        $this->tributacao = $tributacao ? (string)$tributacao : '';
    }
    public function setGarantia($garantia){
        $this->garantia = $garantia ? (string)$garantia : '';
    }
    public function setUrlImage($url_image){
        $this->url_image = $url_image ? (string)$url_image : $this->imagem_default;
    }
    public function setNameImage($name_image){
        $this->name_image = $name_image ? (string)$name_image : '';
    }
    public function setDtInclusao($dt_inclusao){
        $this->dt_inclusao = $dt_inclusao ? (string)$dt_inclusao : '';
    }
    public function setDtAlteracao($dt_alteracao){
        $this->dt_alteracao = $dt_alteracao ? (string)$dt_alteracao : '';
    }

    // ESTOQUE
    public function setPkEstoque($pk_estoque){
        $this->pk_estoque = $pk_estoque ? $pk_estoque : '';
    }
    public function setQuantidade($quantidade){
        $this->quantidade = $quantidade ? (float)$quantidade : 0;
    }
    public function setDtInclusaoEstoque($dt_inclusao_estoque){
        $this->dt_inclusao_estoque = $dt_inclusao_estoque ? (string)$dt_inclusao_estoque : '';
    }

    // PRECO
    public function setPkPreco($pk_preco){
        $this->pk_preco = $pk_preco ? $pk_preco : '';
    }
    public function setPreco($preco){
        $this->preco = $preco ? (float)$preco : 0;
    }
    public function setDtInclusaoPreco($dt_inclusao_preco){
        $this->dt_inclusao_preco = $dt_inclusao_preco ? (string)$dt_inclusao_preco : '';
    }

    // EAN
    public function setEAN($ean){
        $this->ean = $ean ? (float)$ean : 0;
    }

    // GETs
    public function getDirImages(){
        return $this->dir_images;
    }


    function __construct(){
        Log::Msg(2,"Class[ Produtos ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        // Produto
        $this->setPkIdProduto($_REQUEST['pk_id_produto']);
        $this->setFkIdCategoria($_REQUEST['fk_id_categoria']);
        $this->setFkIdFabricante($_REQUEST['fk_id_fabricante']);
        $this->setDescricaoCurta($_REQUEST['descricao_curta']);
        $this->setDescricaoLonga($_REQUEST['descricao_longa']);
        $this->setUnidade($_REQUEST['unidade']);
        $this->setTributacao($_REQUEST['tributacao']);
        $this->setGarantia($_REQUEST['garantia']);
        $this->setUrlImage($_REQUEST['url_image']);
        $this->setNameImage($_REQUEST['name_image']);
        $this->setDtInclusao($_REQUEST['dt_inclusao']);
        $this->setDtAlteracao($_REQUEST['dt_alteracao']);

        // Estoque
        $this->setPkEstoque($_REQUEST['pk_estoque']);
        $this->setQuantidade($_REQUEST['quantidade']);
        $this->setDtInclusaoEstoque($_REQUEST['dt_inclusao_estoque']);

        // Preco
        $this->setPkPreco($_REQUEST['pk_preco']);
        $this->setPreco($_REQUEST['preco']);
        $this->setDtInclusaoPreco($_REQUEST['dt_inclusao_preco']);
    }


    public function CriaAtualiza(){
        Log::Msg(2,"Class[ Produtos ] Method[ CriaAtualiza ]");

        $record = new Repository();
        // Desligando o auto commit
        $record->setCommit(0);

        // INSERT ou UPDATE ?
        Log::Msg(3,"RegistroId[{$this->pk_id_produto}]");

        if ($this->pk_id_produto != 0) {

            $verifica_se_existe = $this->Verifica_se_Existe();

            if ($verifica_se_existe){
                // UPDATE
               $result = $this->update_produto();
            }
            else {
                // INSERT
               $result = $this->insert_produto();
            }

        }
        else {

            $result = $this->insert_produto();

        }
        return $result;
    }

    public function update_produto(){
        Log::Msg(2,"Class[ Produtos ] Method[ update ]");

        // UPDATE
        // 1º Passo - Alterar Imagem
        // 2º Passo - Alterar Produto
        // 3º Passo - Alterar Estoque
        // 4º Passo - Alterar Preco

        $record = new Repository();
        // Desligando o auto commit
        $record->setCommit(0);
        if($this->importacao){$record->setLog(0);}

        // Imagem
        // se a imagem nao for a padrao
        //if($this->url_image != $this->imagem_default){
        //    $result_imagem = $this->salvar_imagem();
        //}

        $query_update = "UPDATE tb_produtos SET fk_id_categoria = {$this->fk_id_categoria}, fk_id_fabricante = {$this->fk_id_fabricante}, descricao_curta = '{$this->descricao_curta}', descricao_longa = '{$this->descricao_longa}', unidade = '{$this->unidade}', tributacao = '{$this->tributacao}', garantia = '{$this->garantia}', url_image = '{$this->url_image}', name_image = '{$this->name_image}', dt_alteracao = NOW() WHERE pk_id_produto = {$this->pk_id_produto};";

        $result_produto = $record->store($query_update);

        $query_update_estoque = "UPDATE tb_produtos_estoque SET quantidade = {$this->quantidade}, dt_alteracao = NOW() WHERE fk_id_produto = {$this->pk_id_produto}";

        $result_estoque = $record->store($query_update_estoque);

        // PARA MANTER O HISTORICO DO PRECO NAO FAZER UPDATE CRIA UM NOVO REGISTRO
        // Preco
        $query_update_preco = "UPDATE tb_produtos_preco SET preco = {$this->preco}, dt_inclusao = NOW() WHERE fk_id_produto = {$this->pk_id_produto}";

        $result_preco = $record->store($query_update_preco);


        if ($result_produto && $result_estoque && $result_preco){
            // Salvo o Registro
            $record->commit();
            // Se Nao For Importacao Não Mostra
            if(!$this->importacao){
                $this->load_produto_id();
            }
            return true;
        }
        else {
            // nao salva o registro
            $record->rollback();

            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Atualizar o registro...";
                $aResult['code'] = "5001006";
                die(json_encode($aResult));
            }
            else {
                return false;
            }
        }

    }


    public function insert_produto(){
        Log::Msg(2,"Class[ Produtos ] Method[ insert ]");

        // INSERT
        // 1º Passo - Mover a Imagem
        // 2º Passo - Criar O Produto
        // 3º Passo - Criar Estoque Zerado
        // 4° Passo - Criar Preco Zerado

        $record = new Repository();
        // Desligando o auto commit
        $record->setCommit(0);
        if($this->importacao){$record->setLog(0);}

        // Se For Importacao Nao trata Imagem
        if(!$this->importacao){
            // Imagem
            // se a imagem nao for a padrao
            if($this->url_image != $this->imagem_default){

                //$result_imagem = $this->salvar_imagem();
            }
        }

        // Produto
        $query_insert = "INSERT INTO tb_produtos (pk_id_produto, fk_id_categoria, fk_id_fabricante, descricao_curta, descricao_longa, unidade, tributacao, garantia, url_image, name_image, dt_inclusao) VALUES ('{$this->pk_id_produto}', {$this->fk_id_categoria}, {$this->fk_id_fabricante}, '{$this->descricao_curta}', '{$this->descricao_longa}', '{$this->unidade}', '{$this->tributacao}', '{$this->garantia}', '{$this->url_image}', '{$this->name_image}', NOW() )";

        $result_produto = $record->store($query_insert);
        $this->pk_id_produto = $result_produto;

        // Estoque
        $query_insert_estoque =  "INSERT INTO tb_produtos_estoque (pk_estoque, fk_id_produto, quantidade, dt_inclusao) VALUES ('{$this->pk_id_estoque}', '{$this->pk_id_produto}', {$this->quantidade}, NOW())";

        $result_estoque = $record->store($query_insert_estoque);

        // Preco
        $query_insert_preco = "INSERT INTO tb_produtos_preco ( pk_preco, fk_id_produto, preco, dt_inclusao ) VALUES ('{$this->pk_preco}', '{$this->pk_id_produto}', {$this->preco}, NOW())";

        $result_preco = $record->store($query_insert_preco);

        if ($result_produto && $result_estoque && $result_preco){
            // Salvo o Registro
            $record->commit();
            // Carrego o Registro Salvo
            // Se Nao For Importacao Não Mostra
            if(!$this->importacao){
                $this->load_produto_id();
            }
            return true;
        }
        else {
            // nao salva o registro
            $record->rollback();

            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                $aResult['code'] = "5001001";
                die(json_encode($aResult));
            }
            else {
                return false;
            }
        }
    }


    public function Verifica_se_Existe(){
        Log::Msg(2,"Class[ Produtos ] Method[ Verifica_se_Existe ]");

        $record = new Repository();
        if($this->importacao){$record->setLog(0);}
        $sql = "SELECT pk_id_produto FROM tb_produtos WHERE pk_id_produto = {$this->pk_id_produto}";
        $result = $record->load($sql);

        if($result->count != 0){
            return TRUE;
        }
        else {
            return FALSE;
        }

    }

    function salvar_imagem(){
        Log::Msg(2,"Class[ Produtos ] Method[ salvar_imagem ]");

        //$extensao = Common::getExtensao($this->name_image);
        $extensao = "JPG";
        $nome_imagem = $this->pk_id_produto . '.' . $extensao;

        $origem  = $this->url_image;
        $destino = $this->dir_images . $nome_imagem;

        // Se a Imagem for a Mesma
        if ($origem == $destino){
            return TRUE;
        }
        else {
            // Mover a Imagem de WORK para Data
            Log::Msg(3,"Movendo Imagem Origem [ {$origem} ] Destino [ {$destino} ]");

            if (copy($origem, $destino)){
                Log::Msg(3,"Copia de arquivo OK, apagando Origem [ $origem ]");
                if(unlink($origem)){
                    Log::Msg(3,"Arquivo de Origem Apagado.");
                    $this->url_image = $destino;
                    return TRUE;
                }
                else {
                    Log::Msg(3,"Não Foi Possivel Apagar Arquivo de Origem.");
                    return FALSE;
                }
            }
            else {
                Log::Msg(3,"Não Foi Possivel Copiar Arquivo de Origem.");
                return FALSE;
            }
        }
    }

    function load_produtos(){
        Log::Msg(2,"Class[ Produtos ] Method[ load_produtos ]");

        $record = new Repository();

        //$sql = "SELECT COUNT(*) as total_count FROM tb_produtos ";
        $sql = "SELECT COUNT(*) as total_count FROM tb_produtos a INNER JOIN tb_produtos_preco b ON a.pk_id_produto = b.fk_id_produto";
        $total = $record->total_count($sql);

        // $sql = "SELECT * FROM tb_produtos ";
        $sql = "SELECT a.*, b.preco FROM tb_produtos a INNER JOIN tb_produtos_preco b ON a.pk_id_produto = b.fk_id_produto";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$total->total_count}}";
            echo $result;
        }
        else {
            echo "{failure:true}";
        }

    }

    function load_produto_id(){
        Log::Msg(2,"Class[ Produtos ] Method[ load_produto_id ]");

        $record = new Repository();
        if($this->importacao){$record->setLog(0);}

        $sql = "SELECT a.*, b.quantidade, b.minimo, b.reposicao, b.maximo, c.preco FROM tb_produtos a INNER JOIN tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto INNER JOIN tb_produtos_preco c ON a.pk_id_produto = c.fk_id_produto WHERE pk_id_produto = {$this->pk_id_produto}";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            // Colocando Preco com decimais
            $results->rows[0]->preco =  number_format($results->rows[0]->preco, 2, '.', '.');
            // Verificando se a imagem existe
            if(!file_exists($results->rows[0]->url_image)){
                $results->rows[0]->url_image = $this->imagem_default;
            }

            if ($this->return_json){
                $aResult['success'] = "true";
                $aResult['data']  = $results->rows[0];
                die(json_encode($aResult));
            }
            else {
                return $results->rows[0];
            }
        }
        else {
            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
                $aResult['code'] = "5001002";
                die(json_encode($aResult));
            }
            else {
                return false;
            }
        }
    }

    function alterar_produto_ean(){
        Log::Msg(2,"Class[ Produtos ] Method[ alterar_produto_ean ]");

        $record = new Repository();

        if($this->importacao){$record->setLog(0);}

        $sql = "SELECT ean FROM tb_produtos_ean WHERE ean = {$this->ean}";
        $result = $record->load($sql);

        if($result->count == 0){
            // Recuperando o Proximo ID
            $sql = "SELECT MAX(pk_id_ean) as id_atual from tb_produtos_ean;";
            $result = $record->load($sql);
            $pk = $result->rows[0]->id_atual + 1;

            $sql =  "INSERT INTO tb_produtos_ean (pk_id_ean, ean, fk_id_produto) VALUES ('$pk','{$this->ean}', {$this->pk_id_produto});";

        }
        else {
            $sql = "UPDATE tb_produtos_ean SET ean = {$this->ean}, fk_id_produto = '{$this->pk_id_produto}' WHERE ean = '{$this->ean}';";
            // Se ja existir verificar se e o mesmo produto so por garantia
            //$sql = "SELECT ean FROM tb_produtos_ean WHERE ean = '{$this->ean}' AND fk_id_produto = '{$this->pk_id_produto}';";
            //$result = $record->load($sql);

            //if($result->count == 1){
            //    $sql = "UPDATE tb_produtos_ean SET ean = {$this->ean}, fk_id_produto = '{$this->pk_id_produto}' WHERE ean = '{$this->ean}' AND fk_id_produto = '{$this->pk_id_produto}'; ";
            //}
            //else {
                // Algo errado com o ean, o ean de um produto esta com id de outro
                //  Log::Msg(0,"Problema com EAN e ID do produto EAN [{$this->ean}] ID [{$this->pk_id_produto}]");
                //  return false;
            //}
        }
        $result = $record->store($sql);

        if ($result){
            // Salvo o Registro
            $record->commit();
            // Se Nao For Importacao Não Mostra
            if(!$this->importacao){
                $this->load_produto_id();
            }
            return true;
        }
        else {
            // nao salva o registro
            $record->rollback();

            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Atualizar o registro...";
                $aResult['code'] = "5001006";
                die(json_encode($aResult));
            }
            else {
                return false;
            }
        }
    }


    function load_produto_ean(){
        Log::Msg(2,"Class[ Produtos ] Method[ load_produto_ean ]");

        $record = new Repository();

        $sql = "SELECT ean.pk_id_ean, produtos.pk_id_produto as fk_id_produto, produtos.sku as fk_sku_produto, ean.ean FROM tb_produtos produtos INNER JOIN tb_produtos_ean ean ON produtos.pk_id_produto = ean.fk_id_produto AND produtos.sku = ean.fk_sku_produto WHERE produtos.pk_id_produto = {$this->pk_id_produto}";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
            $aResult['code'] = "5001003";
            die(json_encode($aResult));
        }

    }

    function alterar_produto_estoque(){
        Log::Msg(2,"Class[ Produtos ] Method[ alterar_produto_estoque ]");

        $record = new Repository();

        if($this->importacao){$record->setLog(0);}
            $sql = "SELECT fk_id_produto FROM tb_produtos_estoque WHERE fk_id_produto = {$this->pk_id_produto}";
            $result = $record->load($sql);

            if($result->count == 0){
                // Recuperando o Proximo ID
                $sql = "SELECT MAX(pk_estoque) as id_atual from tb_produtos_estoque;";
                $result = $record->load($sql);
                $pk = $result->rows[0]->id_atual + 1;

                $sql =  "INSERT INTO tb_produtos_estoque (pk_estoque, fk_id_produto, quantidade, dt_inclusao) VALUES ('$pk','{$this->pk_id_produto}', {$this->quantidade}, NOW())";

            }
            else {
                $sql = "UPDATE tb_produtos_estoque SET quantidade = {$this->quantidade}, dt_alteracao = NOW() WHERE fk_id_produto = '{$this->pk_id_produto}'";
            }
        $result = $record->store($sql);

        if ($result){
            // Salvo o Registro
            $record->commit();
            // Se Nao For Importacao Não Mostra
            if(!$this->importacao){
                $this->load_produto_id();
            }
            return true;
        }
        else {
            // nao salva o registro
            $record->rollback();

            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Atualizar o registro...";
                $aResult['code'] = "5001006";
                die(json_encode($aResult));
            }
            else {
                return false;
            }
        }
    }

    function load_produto_estoque(){
        Log::Msg(2,"Class[ Produtos ] Method[ load_produto_ean ]");

        $record = new Repository();

        $sql = "SELECT quantidade, minimo, reposicao, maximo From tb_produtos_estoque WHERE fk_id_produto = {$this->pk_id_produto}";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
            $aResult['code'] = "5001004";
            die(json_encode($aResult));
        }

    }

    function alterar_produto_preco(){
        Log::Msg(2,"Class[ Produtos ] Method[ alterar_produto_preco ]");

        $record = new Repository();

        if($this->importacao){$record->setLog(0);}
            $sql = "SELECT fk_id_produto FROM tb_produtos_preco WHERE fk_id_produto = {$this->pk_id_produto}";
            $result = $record->load($sql);

            if($result->count == 0){
                // Recuperando o Proximo ID
                $sql = "SELECT MAX(pk_preco) as id_atual from tb_produtos_preco;";
                $result = $record->load($sql);
                $pk = $result->rows[0]->id_atual + 1;

                $sql =  "INSERT INTO tb_produtos_preco (pk_preco, fk_id_produto, preco, dt_inclusao) VALUES ('$pk','{$this->pk_id_produto}', {$this->preco}, NOW())";

            }
            else {
                $sql = "UPDATE tb_produtos_preco SET preco = {$this->preco}, dt_alteracao = NOW() WHERE fk_id_produto = '{$this->pk_id_produto}'";
            }
        $result = $record->store($sql);

        if ($result){
            // Salvo o Registro
            $record->commit();
            // Se Nao For Importacao Não Mostra
            if(!$this->importacao){
                $this->load_produto_id();
            }
            return true;
        }
        else {
            // nao salva o registro
            $record->rollback();

            if ($this->return_json){
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel Atualizar o registro...";
                $aResult['code'] = "5001006";
                die(json_encode($aResult));
            }
            else {
                return false;
            }
        }
    }

    public function getPreco($pk_id_produto){
        Log::Msg(2,"Class[ Produtos ] Method[ getPreco ]");

        if (!$pk_id_produto){
            $pk_id_produto = $this->pk_id_produto;
        }
        $record = new Repository();
        $sql = "SELECT preco FROM tb_produtos_preco WHERE fk_id_produto = $pk_id_produto ORDER BY dt_inclusao DESC";
        $precos = $record->load($sql);

        if ($precos->count != 0){
            return $precos->rows[0]->preco;
        }
    }


    function excluir_imagem($id){
        Log::Msg(2,"Class[ Produtos ] Method[ excluir_imagem ]");

        $record = new Repository();
        $select_fotos = "SELECT url_image FROM tb_produtos WHERE pk_id_produto IN ({$id})";
        $result_fotos = $record->load($select_fotos);

        if ($result_fotos->count != 0){
            foreach ($result_fotos->rows as $imagem){
                // Se a Imagem e Diferente da Padrao
                if($imagem->url_image != $this->imagem_default){
                    if(unlink($imagem->url_image)){
                        $result = ' OK ';
                    }
                    else{
                        $result = 'FALHA';
                    }
                    Log::Msg(3,"Excluindo Arquivo de Imagem url_image[ {$imagem->url_image} ] STATUS [ $result ]");
                }
            }
        }
    }



    public function delete_Produtos(){
        Log::Msg(2,"Class[ Produtos ] Method[ delete_Produtos ]");

        $record = new Repository();
        // Desligando AutoCommit
        $record->setCommit(0);

        if (is_array($this->pk_id_produto)) {
            $id = implode(',', $this->pk_id_produto);
        }
        else {
            $id = $this->pk_id_produto;
        }
            //var_dump($this->pk_id_produto);


        // 1°- Excluir Foto
        // 2°- Excluir Precos
        // 3º- Excluir Estoque
        // 4º- Excluir Eans
        // 5º- Excluir Produtos

        $result_fotos = $this->excluir_imagem($id);


        // Precos
        $delete_precos = "DELETE FROM tb_produtos_preco WHERE fk_id_produto IN ({$id})";
        $result_preco = $record->delete($delete_precos);

        // Estoque
        $delete_estoque = "DELETE FROM tb_produtos_estoque WHERE fk_id_produto IN ({$id})";
        $result_estoque = $record->delete($delete_estoque);

        // EANs
        $delete_eans = "DELETE FROM tb_produtos_ean WHERE fk_id_produto IN ({$id})";
        $result_eans = $record->delete($delete_eans);

        // Produtos
        $delete_produtos = "DELETE FROM tb_produtos WHERE pk_id_produto IN ({$id})";
        $result_produtos = $record->delete($delete_produtos);


        if ($result_produtos) {
            $record->commit();
            echo "{success: true}";
        }
        else {
            $record->rollback();
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, <b>NÃO</b> foi possivel excluir o registro...";
            $aResult['code'] = "5001005";
            die(json_encode($aResult));
        }

    }


    public function localizar_imagem(){
        Log::Msg(2,"Class[ Produtos ] Method[ localizar_imagem ]");

        // 1 Passo - Saber o Diretorio Das Imagens_Produtos
        $dir_images = $this->getDirImages();

        // 2 Passo - Saber o Codigo do Produto
        $produto = $this->pk_id_produto;

        // 3 Passo - Varrer Todas as Pastas e Subpastas Para Achar A Imagem
        //      Se achar Colocar o Caminho Todo da Imagem
        //      Se NAO achar Colocar Imagem Padrao
        // ex: Dir_Imagens/Categorias/Subcategorias

        $produto =  str_pad($produto, 6, "0", STR_PAD_LEFT);

        Log::Msg(2,"Procurando Imagem, Dir [ $dir_images ], Pattern [ $pattern ] ");

        $img = $produto;
        $img_file = $dir_images . $img;

        Log::Msg(2,"Procurando Imagem, IMG_File [ $img_file ]");

        if (file_exists("$img_file.jpg")) {
            $this->setUrlImage("$img_file.jpg");
            Log::Msg(2,"Imagem Encontrada, File [ $img_file.jpg ]");
        }
        else if (file_exists("$img_file.JPG")){
            $this->setUrlImage("$img_file.JPG");
            Log::Msg(2,"Imagem Encontrada, File [ $img_file.JPG ]");
        }
        else {
            $this->setUrlImage($this->imagem_default);
            Log::Msg(2,"Imagem NAO Encontrada, File [ $img_file ]");
        }

    }


    public function getTotalProdutos(){
        Log::Msg(2,"Class[ Produtos ] Method[ getTotalProdutos ]");



    }

    /** METHOD: getProdutoByIdInCatalogo()
    * Metodo Usado pela janela produtos detalhes
    * Recupera as informaçoes de um produto pelo id
    * igual ao metodo load_produto_id() so que leva em consideracao
    * o catalogo que o produto esta
    * retorna o id do produto anterior a ele e o posterior
    */
    function getProdutoByIdInCatalogo(){
        Log::Msg(2,"Class[ Produtos ] Method[ getProdutoByIdInCatalogo ]");

        $record = new Repository();
        if($this->importacao){$record->setLog(0);}

        $sql = "SELECT a.*, b.quantidade, b.minimo, b.reposicao, b.maximo, c.preco FROM tb_produtos a INNER JOIN tb_produtos_estoque b ON a.pk_id_produto = b.fk_id_produto INNER JOIN tb_produtos_preco c ON a.pk_id_produto = c.fk_id_produto WHERE pk_id_produto = {$this->pk_id_produto}";

        $results = $record->load($sql);
        //var_dump(get_object_vars($results));
        Log::Msg(5,$results);


        if ($results->count != 0) {
            // Colocando Preco com decimais
            $results->rows[0]->preco =  number_format($results->rows[0]->preco, 2, '.', '.');
            // Verificando se a imagem existe
            if(!file_exists($results->rows[0]->url_image)){
                $results->rows[0]->url_image = $this->imagem_default;
            }

            // RECUPERANDO ID DO ITEM ANTERIOR
            Log::Msg(3,"Buscando ID Anterior");
            $sql = "SELECT a.pk_id_produto FROM tb_produtos a INNER JOIN tb_catalogo_produtos b ON a.pk_id_produto = b.fk_id_produto WHERE a.pk_id_produto < {$this->pk_id_produto} AND a.fk_id_categoria = {$results->rows[0]->fk_id_categoria} ORDER BY a.pk_id_produto DESC LIMIT 1";
            $anterior = $record->load($sql);
            if ($anterior->count != 0){
                $results->rows[0]->id_anterior = $anterior->rows[0]->pk_id_produto;
            }
            else {
                $results->rows[0]->id_anterior = 0;
            }
            Log::Msg(3,"ID Anterior [ {$results->rows[0]->id_anterior} ]");

            // RECUPERANDO ID DO PROXIMO ITEM
            Log::Msg(3,"Buscando Proximo ID");
            $sql = "SELECT a.pk_id_produto FROM tb_produtos a INNER JOIN tb_catalogo_produtos b ON a.pk_id_produto = b.fk_id_produto WHERE a.pk_id_produto > {$this->pk_id_produto} AND a.fk_id_categoria = {$results->rows[0]->fk_id_categoria} ORDER BY a.pk_id_produto ASC LIMIT 1";
            $proximo = $record->load($sql);
            if ($proximo->count != 0){
                $results->rows[0]->id_proximo = $proximo->rows[0]->pk_id_produto;
            }
            else {
                $results->rows[0]->id_proximo = 0;
            }

            // RETORNO
            echo "{success: true,data:";
            echo json_encode($results->rows[0]);
            echo "}";
        }
        else {
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha, não foi possivel carregar o registro..";
            $aResult['code'] = "5001002";
            die(json_encode($aResult));
        }
    }

    public function getQuantidadeById(){
        Log::Msg(2,"Class[ Produtos ] Method[ getQuantidadeByID ]");

        $record = new Repository();

        $sql = "SELECT quantidade FROM tb_produtos_estoque WHERE fk_id_produto = {$this->pk_id_produto}";
        $result = $record->load($sql);

        if($result->count != 0){
            return $result->rows[0]->quantidade;
        }
        else {
            return FALSE;
        }
    }

    public function LimparBaseProdutos($fotos= true, $precos = true, $estoque = true, $eans = true, $produtos = true, $categorias = true){
        Log::Msg(2,"Class[ Produtos ] Method[ LimparBaseProdutos ]");

        $record = new Repository();
        // Desligando AutoCommit
        $record->setCommit(0);

        // 1°- Excluir Foto
        // 2°- Excluir Precos
        // 3º- Excluir Estoque
        // 4º- Excluir Eans
        // 5º- Excluir Produtos
        // 6º- Excluir Categorias

        //$result_fotos = $this->excluir_imagem($id);


        // Precos
        if ($precos){
            $delete_precos = "DELETE FROM tb_produtos_preco;";
            $result_preco = $record->delete($delete_precos);
        }

        // Estoque
        if ($estoque){
            $delete_estoque = "DELETE FROM tb_produtos_estoque;";
            $result_estoque = $record->delete($delete_estoque);
        }

        // EANs
        if ($eans) {
            $delete_eans = "DELETE FROM tb_produtos_ean;";
            $result_eans = $record->delete($delete_eans);
        }

        // Produtos
        if ($produtos) {
            $delete_produtos = "DELETE FROM tb_produtos;";
            $result_produtos = $record->delete($delete_produtos);
        }

        // Categorias/Departamentos
        if ($categorias) {
            $delete_categorias = "DELETE FROM tb_produtos_categoria;";
            $result_categorias = $record->delete($delete_categorias);
        }


        $record->commit();
        return TRUE;

    }

// Procurar imagem usando o comando find
/*        ob_start();
        $findfile = Common::find_files($dir_images, $pattern);
        $img_file = ob_get_contents();
        ob_end_clean();
*/
//  $comando = "find -name $produto*";
//  Log::Msg(3,"Comando [ $comando ]");
//  exec($comando, $verbose);
//  Log::Msg(3,"Verbose [ $verbose[0] ]");
//  $caminho = str_replace("./", '', $verbose[0]);
//  Log::Msg(3,"Caminho [ $caminho ]");
//  if($caminho){
        // Gambiarra
//         $caminho = "Main/Data/Imagens_Produtos/";
//         $img_file = $caminho . $produto . '.JPG';

//        $img_file = $caminho;
//        Log::Msg(3,"ImgFile [ $img_file ]");

}
?>
