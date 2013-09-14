<?php
/**
 * @package  :ClientAndroid
 * @name     :ClientAndroid
 * @class    :ClientAndroid.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :12/03/2013
 * @Diretorio:Main/Modulos/ClientAndroid/
 * Classe Responsavel por disponibilizar as informacoes do via json para o client Android
 */

class ClientAndroid {


    /** getVendedores()
     * Retorna um lista com o id e o nome dos usuarios cadastrados no grupo vendedores
     * @return: array() de obj vendedores com
     * $id: chave do vendedor
     * $nome: nome do vendedor
     */
    public function getVendedores($data){

        $grupo_vendedores = Common::getParametro("chave_vendedores");

        $vendedores = Usuarios::getUsuarioByGrupo($grupo_vendedores, false);

        $result = new StdClass();


        if (count($vendedores) > 0){
            $result->success = true;
            $result->rows = $vendedores;
        }
        else {
            $result->success = false;
        }
        echo json_encode($result);
    }

    /** getDepartamentos()
     * Recupera os Departamentos dos produtos
     * @return: array() de obj Categoria com
     * $id: chave da Categoria
     * $categoria: no da Categoria de produto
     */
    public function getDepartamentos($data){

        $aDepartamentos = Categoria::getCategorias(false);

        foreach ($aDepartamentos as $departamento){

            $record = new StdClass();

            $record->_id = $departamento->pk_id_categoria;
            $record->categoria = ucwords(strtolower($departamento->categoria));

            $aRecords[] = $record;
        }

        if (count($aRecords) > 0){
            $result->success = true;
            $result->rows = $aRecords;
        }
        else {
            $result->success = false;
        }
        echo json_encode($result);
    }


    /** getProdutos()
     * Recupera a informaçao do catalogo atual,
     * busca os produtos que estao no catalogo,
     * Retorna um lista com os produtos
     * @return: array() de obj produtos com
     * $id: chave do produto
     * $codigo: codigo do produto
     * $categoria_id: chave da categoria do produto
     * $descricao_curta: descricao curta do produto
     * $descricao: descricao longa do produto
     * $quantidade: Quantidade em estoque do produto
     * $preco: preco de venda do produto
     */
    public function getProdutos($data){

        // Recuperar o catalogo atual
        $id_catalogo = Catalogos::getCatalogoAtual();


        // Buscar todos os produtos que correspondem a regra do catalogo
        $aProdutos = Catalogos::getProdutosCompletoInCatalogoById($id_catalogo);

        foreach ($aProdutos as $produto){

            $record = new StdClass();

            // Setando o _id chave para o android
            $record->_id = $produto->pk_id_produto;
            $record->codigo = $produto->pk_id_produto;
            $record->categoria_id = $produto->fk_id_categoria;
            $record->descricao_curta = $produto->descricao_curta;
            $record->descricao = $produto->descricao_longa;
            $record->quantidade = $produto->quantidade;
            $record->preco = $produto->preco;
            $record->image_name = str_pad($produto->pk_id_produto, 6, "0", STR_PAD_LEFT);
            $record->image_size = 0;


            $aRecords[] = $record;
        }
//         $record = new StdClass();
//         $record->_id = 291;
//         $record->codigo = 291;
//         $record->categoria_id = 1000;
//         $record->descricao_curta = "CADERNO 48F 1.4 HZ C";
//         $record->descricao = "CADERNO 48F 1.4 HZ CALIGRAFIA KAJOMA";
//         $record->quantidade = 80;
//         $record->preco = 0.89;
//         $record->image_name = str_pad($record->_id, 6, "0", STR_PAD_LEFT);
//         $record->image_size = 0;
//         $aRecords[] = $record;
// 
//         $record = new StdClass();
//         $record->_id = 376;
//         $record->codigo = 376;
//         $record->categoria_id = 1000;
//         $record->descricao_curta = "CADERNO 48F 1.4 HZ C";
//         $record->descricao = "CADERNO 48F 1.4 HZ CALIGRAFIA KAJOMA";
//         $record->quantidade = 80;
//         $record->preco = 0.89;
//         $record->image_name = str_pad($record->_id, 6, "0", STR_PAD_LEFT);
//         $record->image_size = 0;
//         $aRecords[] = $record;


        if (count($aRecords) > 0){
            $result->success = true;
            $result->rows = $aRecords;
        }
        else {
            $result->success = false;
        }
        //$result->success = false;
        echo json_encode($result);
    }




}