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
        $result->entidade = 'vendedores';

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

        $result = new StdClass();
        $result->entidade = 'departamentos';

        $aDepartamentos = Categoria::getCategorias(false);

        foreach ($aDepartamentos as $departamento){

            $record = new StdClass();
            $record->_id = $departamento->pk_id_categoria;
            $record->departamento = ucwords(strtolower($departamento->categoria));

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

        $result = new StdClass();
        $result->entidade = 'produtos';

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


//             $aRecords[] = $record;
        }
        $record = new StdClass();
        $record->_id = 291;
        $record->codigo = 291;
        $record->categoria_id = 1000;
        $record->descricao_curta = "CADERNO 48F 1.4 HZ C";
        $record->descricao = "CADERNO 48F 1.4 HZ CALIGRAFIA KAJOMA";
        $record->quantidade = 80;
        $record->preco = 0.89;
        $record->image_name = str_pad($record->_id, 6, "0", STR_PAD_LEFT);
        $record->image_size = 0;
        $aRecords[] = $record;

        $record = new StdClass();
        $record->_id = 376;
        $record->codigo = 376;
        $record->categoria_id = 1000;
        $record->descricao_curta = "CADERNO 48F 1.4 HZ C";
        $record->descricao = "CADERNO 48F 1.4 HZ CALIGRAFIA KAJOMA";
        $record->quantidade = 80;
        $record->preco = 0.89;
        $record->image_name = str_pad($record->_id, 6, "0", STR_PAD_LEFT);
        $record->image_size = 0;
        $aRecords[] = $record;


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

    /** getClientes()
     * Recupera os Clientes para um vendedor
     * @param: int id_vendedor
     * @return: array() de obj Clientes
     */
    public function getClientes($data){

        $result = new StdClass();
        $result->entidade = 'pullclientes';

//         $aDepartamentos = Categoria::getCategorias(false);
// 
//         foreach ($aDepartamentos as $departamento){
// 
//             $record = new StdClass();
//             $record->_id = $departamento->pk_id_categoria;
//             $record->departamento = ucwords(strtolower($departamento->categoria));
// 
//             $aRecords[] = $record;
//         }
// 
//         if (count($aRecords) > 0){
//             $result->success = true;
//             $result->rows = $aRecords;
//         }
//         else {
//             $result->success = false;
//         }
        $result->success = true;
        $result->rows = $aRecords;

        echo json_encode($result);
    }


// -----------------------< SETERS >----------------------- //

    /** setPedidos()
     */
    public function setPedidos($data){
        Log::Msg(2,"Class[ ClientAndroid ] Method[ setPedidos() ]");
        Log::Msg(4, $_REQUEST);
        $data = json_decode($_REQUEST['data']);

        $result = new StdClass();
        $result->entidade = 'pedidos';

        $pedido = new StdClass();
        $pedido->id_original   = $data->id;
        $pedido->fk_id_usuario = $data->id_usuario;
        $pedido->fk_id_cliente = $data->id_cliente;
        $pedido->status        = 3; //À Separar
        $pedido->qtd_itens     = $data->qtd_itens;
        $pedido->valor_total   = $data->valor_total;
        $pedido->finalizadora  = $data->finalizadora;
        $pedido->parcelamento  = $data->parcelamento;
        $pedido->nfe           = $data->nfe;
        $pedido->dt_inclusao   = $data->dt_inclusao;
        $pedido->dt_envio      = date('Y-m-d');
        $pedido->observacao    = $data->observacao;


        $strProdutos = str_replace('\\','',$data->produtos);
        $str = "[$strProdutos]";
        $aProdutos = json_decode($str);
        $pedido->produtos      = $aProdutos;

        $record = new Repository();
        $record->setCommit(0);

        $sql_insert = "INSERT INTO tb_orcamentos (`fk_id_cliente`, `fk_id_usuario`, `status`, `qtd_itens`,`valor_total`,`finalizadora`,`parcelamento`,`nfe`,`dt_inclusao`,`dt_envio`, `observacao`) VALUES ('{$pedido->fk_id_cliente}', '{$pedido->fk_id_usuario}', {$pedido->status}, '{$pedido->qtd_itens}', {$pedido->valor_total},'{$pedido->finalizadora}','{$pedido->parcelamento}','{$pedido->nfe}','{$pedido->dt_inclusao}','{$pedido->dt_envio}','{$pedido->observacao}')";

        $pedido_id = $record->store($sql_insert);

        if ($pedido_id){
            foreach ($pedido->produtos as $produto) {
                Log::Msg(3, "PRODUTO [ {$produto->id_produto} ]");

                $sql_insert = "INSERT INTO `tb_orcamento_produtos` (`pk_orcamento_produto` ,`fk_orcamento` ,`fk_id_produto` ,`quantidade` ,`preco` ,`valor_total` ,`observacao_produto`) VALUES (NULL , '{$pedido_id}', '{$produto->id_produto}', '{$produto->quantidade}', '{$produto->valor}', '{$produto->valor_total}', '{$produto->observacao}');";

                if (!$record->store($sql_insert)){
                    $erro = "Falha ao Inserir o Produto [ {$produto->id_produto} ] QUERY [ $sql_insert ]";
                    break;
                }
            }
        }
        else {
            $erro = "Falha ao Inserir o Pedido [ {$pedido_id} ] QUERY [ $sql_insert ]";
            break;
        }
        if ($erro) {
            $record->rollback();
            $result->success = false;
            $result->msg  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
            $result->descricao = $erro;
            $result->status = 9; // Enviado Com Erro
            die(json_encode($result));
        }
        else {
            $record->commit();
            $result->success = true;
            $result->id = $pedido->id_original;
            $result->id_servidor = $pedido_id;
            $result->dt_envio = $pedido->dt_envio;
            $result->status = 2; // Enviado
        }
        echo json_encode($result);
    }

    /** setClientes()
     */
    public function setClientes(){
        Log::Msg(2,"Class[ ClientAndroid ] Method[ setClientes() ]");
        Log::Msg(4, $_REQUEST);

        $data = json_decode($_REQUEST['data']);

        $result = new StdClass();
        $result->entidade = 'clientes';

        $cliente = new StdClass();
        $cliente->id_original  = $data->id;
        $cliente->fk_id_usuario = $data->id_usuario;
        $cliente->tipo = $data->tipo;
        //$cliente->tipo_cliente = $data->tipo; ???
        //$cliente->status = $data->nome; ???
        $cliente->nome = $data->nome;
        $cliente->cpf = $data->cpf;
        $cliente->cnpj = $data->cnpj;
        $cliente->rg = $data->rg;
        $cliente->inscricao_estadual = $data->inscricao_estadual;
        $cliente->telefone_fixo = $data->telefone_fixo;
        $cliente->telefone_movel = $data->telefone_movel;
        $cliente->email = $data->email;
        $cliente->status_servidor = $data->status_servidor;
        $cliente->responsavel = $data->responsavel;
        $cliente->dt_inclusao = $data->dt_inclusao;
        $cliente->observacoes = $data->observacoes;
        //$cliente->vendedor = $data->vendedor;???
        $cliente->rua = $data->rua;
        $cliente->numero = $data->numero;
        $cliente->bairro = $data->bairro;
        $cliente->cidade = $data->cidade;
        $cliente->ud = $data->uf;
        $cliente->cep = $data->cep;
        $cliente->complemento = $data->complemento;
        //$cliente->id_referencia = $data->id_referencia; ???
        //$cliente->id_referencia_pk = $data->id_referencia_pk;??/

        Log::Msg(3, "Nome = {$data->nome}");

        $record = new Repository();
        // Saber se o Cliente ja e cadastrado
        if ($cliente->tipo == 1) {
            // Pessoa Fisica
            $sql = "SELECT pk_id_cliente FROM tb_clientes WHERE cpf = {$cliente->cpf};";
        }
        else {
            // Pessoa Juridica
            $sql = "SELECT pk_id_cliente FROM tb_clientes WHERE cnpj = {$cliente->cnpj};";
        }
        $results = $record->load($sql);

        $record->setCommit(0);

        if ($results->count != 0) {
            // Cliente ja cadastrado faz UPDATE
            $id_cliente = $results->rows[0]->pk_id_cliente;
            Log::Msg(3, "JA CADASTRADO - ID_CLIENTE[$id_cliente]");

            $sql_update = "UPDATE tb_clientes SET  fk_id_loja = '{$data->fk_id_loja}', fk_id_usuario = '{$data->fk_id_usuario}', fk_id_endereco = '{$data->fk_id_endereco}', tipo = '{$data->tipo}', tipo_cliente = '{$data->tipo_cliente}', status = '{$data->status}', nome = '{$data->nome}', cpf = '{$data->cpf}', cnpj = '{$data->cnpj}', rg = '{$data->rg}', inscricao_estadual = '{$data->inscricao_estadual}', dt_nascimento = '{$data->dt_nascimento}', sexo = '{$data->sexo}', profissao = '{$data->profissao}', estado_civil = '{$data->estado_civil}', telefone_fixo = '{$data->telefone_fixo}', telefone_movel = '{$data->telefone_movel}', email = '{$data->email}', status_servidor = '{$data->status_servidor}',  dt_alteracao = NOW(), observacoes = '{$data->observacoes}', vendedor = '{$data->vendedor}' WHERE pk_id_cliente = '{$id_cliente}'";

            if ($record->store($sql_update)){
                $endereco = "UPDATE tb_endereco SET tipo_endereco = '{$data->tipo}', rua = '{$data->rua}', numero = '{$data->numero}', bairro = '{$data->bairro}', cidade = '{$data->cidade}', uf = '{$data->uf}', cep = '{$data->cep}', complemento = '{$data->complemento}', dt_alteracao = NOW() WHERE id_referencia = 'tb_clientes' AND id_referencia_pk = '{$id_cliente}';";

                if ($record->store($endereco)){

                    $record->commit();
                    $result->success = true;
                    $result->id = $cliente->id_original;
                    $result->id_servidor = $id_cliente;
                }
                else {
                    $erro = "Falha ao Alterar o Cliente [ {$data->nome} ] QUERY [ $endereco ]";
                    $record->rollback();
                    $result->success = false;
                    $result->msg  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                    $result->descricao = $erro;
                }
            }
            else {

                $erro = "Falha ao Alterar o Cliente [ {$data->nome} ] QUERY [ $sql_update ]";
                $record->rollback();
                $result->success = false;
                $result->msg  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                $result->descricao = $erro;
            }

        }
        else {
            // Cliente Nao Cadastrado FAZ INSERT
            Log::Msg(3, "NAO CADASTRADO");

            $sql_insert = "INSERT INTO tb_clientes (pk_id_cliente , fk_id_loja, fk_id_endereco, fk_id_usuario, tipo, tipo_cliente, status, nome, cpf, cnpj, rg, inscricao_estadual, dt_nascimento, sexo, profissao, estado_civil, telefone_fixo, telefone_movel, email, status_servidor, dt_inclusao, dt_alteracao, observacoes, vendedor ) VALUES (NULL, '{$data->fk_id_loja}', '0', '{$data->fk_id_usuario}', '{$data->tipo}', '{$data->tipo_cliente}', '{$data->status}', '{$data->nome}', '{$data->cpf}', '{$data->cnpj}', '{$data->rg}', '{$data->inscricao_estadual}', '{$data->dt_nascimento}', '{$data->sexo}', '{$data->profissao}', '{$data->estado_civil}', '{$data->telefone_fixo}', '{$data->telefone_movel}', '{$data->email}', '{$data->status_servidor}', NOW(), NOW(), '{$data->observacoes}', '{$data->vendedor}')";

            $id_cliente = $record->store($sql_insert);
            if ($id_cliente){

                $endereco = "INSERT INTO tb_endereco (id_endereco, tipo_endereco, rua, numero, bairro, cidade, uf, cep, complemento, dt_inclusao, dt_alteracao, id_referencia, id_referencia_pk) VALUES (NULL, '{$data->tipo}', '{$data->rua}',  '{$data->numero}', '{$data->bairro}', '{$data->cidade}', '{$data->uf}', '{$data->cep}', '{$data->complemento}', NOW(), '', 'tb_clientes', '$id_cliente' );";

                $id_endereco = $record->store($endereco);

                if ($id_endereco){

                    $record->commit();
                    $result->success = true;
                    $result->id = $cliente->id_original;
                    $result->id_servidor = $id_cliente;
                }
                else {
                    $erro = "Falha ao Inserir o Cliente [ {$data->nome} ] QUERY [ $endereco ]";
                    $record->rollback();
                    $result->success = false;
                    $result->msg  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                    $result->descricao = $erro;
                }
            }
            else {

                $erro = "Falha ao Inserir o Cliente [ {$data->nome} ] QUERY [ $sql_insert ]";
                $record->rollback();
                $result->success = false;
                $result->msg  = "Desculpe mas houve uma Falha, não foi possivel gravar o registro...";
                $result->descricao = $erro;
            }
        }
        die(json_encode($result));
    }

    public function codificacao($string) {
        return mb_detect_encoding($string.'x', 'UTF-8, ISO-8859-1');
    }
}