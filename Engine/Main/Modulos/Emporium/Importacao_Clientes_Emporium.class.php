<?php
header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Emporium
 * @name     :Importacao_Clientes_Emporium
 * @class    :Importacao_Clientes_Emporium.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :09/02/2011
 * @Diretorio:Main/Modulos/Emporium/
 * Classe Responsavel pela Exportacao do Cadastro de Produtos
 * para arquivo cvs compativel com Excel
 * @revision:
 * Obs: Esta Classe Somente Faz a leitura das informacoes na base do servidor emporium
 *     e escreve um arquivo em formato cvs
 * Query:
 *     SELECT a.plu_key, a.long_description , b.quantity_in_stock, c.price, MAX(c.start)
 *     FROM plu a
 *     INNER JOIN plu_store b
 *     ON a.plu_key = b.plu_key
 *     INNER JOIN pricing c
 *     ON a.plu_key = c.plu_key
 * Mensagens de ERRO
 *      6004001 - Sem Conexao com Servidor Emporium
 *      6004002 - Nao Ha Registros a Exportar
 *      6004003 - Falha na Geracao do Arquivo
 */

class Importacao_Clientes_Emporium {

    private $total_a_exportar = 0;
    private $total_exportados = 0;
    private $total_erros      = 0;

    private $campos           = "";

//  -------------//---------------
    private $obj_integracao   = "";
    private $id_integracao    = "";


    function __construct(){
        Log::Msg(2,"Class[ Importacao_Clientes_Emporium ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->obj_integracao = new Integracao_Emporium();
        $this->id_integracao = $this->obj_integracao->criar_registro(1, 'Clientes');


        $this->campos = "plu_key, long_description , quantity_in_stock, price, start";


    }

    public function importar_clientes(){
        Log::Msg(2,"Class[ Importacao_Clientes_Emporium ] Method[ Exporta_Produtos ]");


        // 1º Passo - Conectar Emporium
        if ($this->Testar_Conexao_Servidor_Emporium()){

            $this->obj_integracao->atualiza_status(1); // Iniciado

            // Importando os tipos de clientes
            $this->importar_tipo_cliente();

            // 2º Passo - Recuperar os Ids dos Registros a Serem Exportados
            $aIds = $this->Recupera_Ids();
            Log::Msg(3,"Iniciando Leitura e Escrita, Total_a_Exportar [ {$this->total_a_exportar} ]");

            // 3º Passo - Para Cada Registro no emporium selecionar todas as informacoes
            // Tratar e Inserir

            $this->obj_integracao->atualiza_status(2); // Em Andamento
            foreach ($aIds as $id){

                $obj_emporium = $this->Get_Cliente_Emporium($id->customer_key);

                if ($obj_emporium){
                    // Lidos com sucesso
                    $result = $this->Insere_Cliente($obj_emporium);
                    if ($result){
                        // Importados
                        $this->total_exportados++;
                    }
                    else {
                        // Nao Importados
                        $this->total_erros++;
                    }
                }
                else {
                    // Erros de Leitura
                }

            }

            Log::Msg(3,"Finalizando Leitura e Escrita, Total_Exportados [ {$this->total_exportados} ]");
            fclose($arq_result);

            $this->obj_integracao->SetTotalExportados($this->total_exportados);


            // 6º Passo - Retornar o Arquivo e o Resultado
            $this->obj_integracao->finaliza_integracao(); // Concluido

            $aResult['success'] = "true";
            $aResult['total'] = "{$this->total_exportados}";
            $aResult['total_erros'] = "{$this->total_erros}";
            echo json_encode($aResult);

        }
        else {
            // SEM CONEXAO COM EMPORIUM RETORNAR MESAGEM DE ERRO e ABORTAR
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas Não Foi Possivel Conectar ao Servidor Emporium";
            $aResult['code'] = "Erro: [6004001]";
            $this->obj_integracao->finaliza_integracao(); // Concluido

            die(json_encode($aResult));
        }

    }


    public function Insere_Cliente($obj){
        Log::Msg(2,"Class[ Importacao_Clientes_Emporium ] Method[ Insere_Cliente ]");


        $record = new Repository();
        // Desligando o auto commit
        $record->setCommit(0);
        // Desligando o Log
        $record->setLog(0);

        //1º Passo - Inserir o Endereco
        $sql_endereco = "INSERT INTO tb_endereco (id_endereco, tipo_endereco, rua, numero, bairro, cidade, uf, cep, complemento, dt_inclusao, Dt_Alteracao, id_referencia, id_referencia_pk) VALUES ('', '{$obj->_tipo_endereco}', '{$obj->_rua}', '{$obj->_numero}', '{$obj->_bairro}', '{$obj->_cidade}', '{$obj->_uf}', '{$obj->_cep}', '{$obj->_complemento}', '{$obj->_dt_inclusao}', '{$obj->_dt_alteracao}', '{$obj->_id_referencia}', {$obj->_id_referencia_pk})";

        $id_endereco = $record->store($sql_endereco);

        $sql_cliente = "INSERT INTO tb_clientes (pk_id_cliente , fk_id_loja, fk_id_endereco, fk_id_usuario, tipo, tipo_cliente, status, nome, cpf, cnpj, rg, inscricao_estadual, dt_nascimento, sexo, profissao, estado_civil, telefone_fixo, telefone_movel, email, dt_inclusao, dt_alteracao, observacoes ) VALUES ({$obj->_pk_id_cliente}, {$obj->_fk_id_loja}, {$id_endereco}, {$obj->_fk_id_usuario}, '{$obj->_tipo}', '{$obj->_tipo_cliente}', {$obj->_status}, '{$obj->_nome}', '{$obj->_cpf}', '{$obj->_cnpj}', '{$obj->_rg}', '{$obj->_inscricao_estadual}', '{$obj->_dt_nascimento}', '{$obj->_sexo}', '{$obj->_profissao}', '{$obj->_estado_civil}', '{$obj->_telefone_fixo}', '{$obj->_telefone_movel}', '{$obj->_email}', '{$obj->_dt_inclusao}', '{$obj->_dt_alteracao}', '{$obj->_observacoes}')";

        $id_cliente = $record->store($sql_cliente);

        $record->commit();

        if ($id_cliente){
            return $id_cliente;
        }
        else {
            return FALSE;
        }
    }



    public function Get_Cliente_Emporium($customer_key){
        Log::Msg(2,"Class[ Importacao_Clientes_Emporium ] Method[ Get_Cliente_Emporium ]");

        $record = new Repository("emporium");
        // Desligando o Log
        $record->setLog(0);

        //1º Passo - Criar um Objeto com todos os Atributos
        $obj_cliente_emporium = new StdClass();

        $obj_cliente_emporium->_pk_id_cliente        = ""; // ok
        $obj_cliente_emporium->_fk_id_loja           = 0;  // ok
        $obj_cliente_emporium->_fk_id_endereco       = 0;
        $obj_cliente_emporium->_fk_id_usuario        = 9999;
        $obj_cliente_emporium->_tipo                 = ""; // ok

        $obj_cliente_emporium->_tipo_cliente         = ""; // ok
        $obj_cliente_emporium->_cpf                  = ""; // ok
        $obj_cliente_emporium->_cnpj                 = ""; // ok
        $obj_cliente_emporium->_rg                   = ""; // ok

        $obj_cliente_emporium->_inscricao_estadual   = ""; // ok
        $obj_cliente_emporium->_status               = 0;  // ok
        $obj_cliente_emporium->_nome                 = ""; // ok
        $obj_cliente_emporium->_dt_nascimento        = ""; // ok
        $obj_cliente_emporium->_sexo                 = ""; // ok

        $obj_cliente_emporium->_profissao            = ""; // ok
        $obj_cliente_emporium->_estado_civil         = ""; // ok
        $obj_cliente_emporium->_telefone_fixo        = ""; // ok
        $obj_cliente_emporium->_telefone_movel       = ""; // ok
        $obj_cliente_emporium->_email                = ""; // ok

        $obj_cliente_emporium->_dt_inclusao          = "";
        $obj_cliente_emporium->_dt_alteracao         = "";
        $obj_cliente_emporium->_observacoes          = "";

        // ENDERECO

        $obj_cliente_emporium->_tipo_endereco        = "";
        $obj_cliente_emporium->_rua                  = "";
        $obj_cliente_emporium->_numero               = "";
        $obj_cliente_emporium->_bairro               = "";
        $obj_cliente_emporium->_cidade               = "";
        $obj_cliente_emporium->_uf                   = "";
        $obj_cliente_emporium->_cep                  = "";
        $obj_cliente_emporium->_complemento          = "";
        $obj_cliente_emporium->_id_referencia        = "";
        $obj_cliente_emporium->_id_referencia_pk     = "";


        //2º Passo - Recuperar os dados da Customer
        $sql = "SELECT a.customer_key, a.customer_name, a.customer_email, a.customer_phone1, a.customer_phone2, a.customer_date_inc, a.customer_date_alt, a.customer_type, a.customer_gender, a.customer_civil_status, a.customer_birthday, a.store_key, a.customer_job_title, b.cst_type_key FROM `customer` a INNER JOIN customer_category b ON a.customer_key = b.customer_key";
        $sql .= " WHERE a.customer_key = {$customer_key}";

        $result = $record->load($sql);

        if ($result->count != 0) {
            Log::Msg(3,"Importacao Customer. STATUS[ OK ] customer_key[ $customer_key ]");

            $obj_customer = $result->rows[0];
            //PK_ID_CLIENTE
            $obj_cliente_emporium->_pk_id_cliente  = $obj_customer->customer_key;

            // SETANDO REFERENCIA DE ENDERECO
            $obj_cliente_emporium->_id_referencia    = "tb_clientes";
            $obj_cliente_emporium->_id_referencia_pk = $obj_customer->customer_key;


            //FK_ID_LOJA
	     if ($obj_customer->store_key) {
	            $obj_cliente_emporium->_fk_id_loja = $obj_customer->store_key;
	     }
	     else {
	            $obj_cliente_emporium->_fk_id_loja  = 0;
	     }
            //TIPO
            $obj_cliente_emporium->_tipo           = $obj_customer->customer_type;
            //TIPO_CLIENTE
            $obj_cliente_emporium->_tipo_cliente   = $obj_customer->cst_type_key;
            //NOME
            $obj_cliente_emporium->_nome           = $obj_customer->customer_name;
            //DATA_NASCIMENTO
            $obj_cliente_emporium->_dt_nascimento  = $obj_customer->customer_birthday;
            //SEXO
            if ($obj_customer->customer_gender) {
                $obj_cliente_emporium->_sexo       = $obj_customer->customer_gender == 1 ? 'F' : 'M';
            }
            //PROFISSAO
            $obj_cliente_emporium->_profissao      = $obj_customer->customer_job_title;
            //ESTADO_CIVIL
            $obj_cliente_emporium->_estado_civil   = $obj_customer->customer_civil_status;
            //TELEFONE_FIXO
            $obj_cliente_emporium->_telefone_fixo  = $obj_customer->customer_phone1;
            //TELEFONE_MOVEL
            $obj_cliente_emporium->_telefone_movel = $obj_customer->customer_phone2;
            //EMAIL
            $obj_cliente_emporium->_email          = $obj_customer->customer_email;
            //DT_INCLUSAO
            $obj_cliente_emporium->_dt_inclusao    = $obj_customer->customer_date_inc;
            //DT_ALTERACAO
            $obj_cliente_emporium->_dt_alteracao   = $obj_customer->customer_date_alt;


            //3º Passo - Saber se e Pessoa Fisica ou Juridica
            // Recuperar as informacoes da customer_sku (cpf/cnpj,ie/rg,status)
            $sql = "SELECT customer_sku_id, customer_sku_type_key,  customer_sku_status FROM `customer_sku` WHERE customer_key = {$obj_customer->customer_key}";

            $result = $record->load($sql);

            if ($result->count != 0) {
                Log::Msg(3,"Importacao Customer.sku STATUS[ OK ] customer_key[ $customer_key ]");

                // SE TIVER ALGUM
                foreach ($result->rows as $sku){
                    if ($sku->customer_sku_type_key == 1){
                        //CPF
                        $obj_cliente_emporium->_cpf = $sku->customer_sku_id;
                    }
                    elseif ($sku->customer_sku_type_key == 6){
                        //RG
                        $obj_cliente_emporium->_rg = $sku->customer_sku_id;
                    }
                    elseif ($sku->customer_sku_type_key == 2){
                        //CNPJ
                        $obj_cliente_emporium->_cnpj = $sku->customer_sku_id;
                    }
                    elseif ($sku->customer_sku_type_key == 8){
                        //CNPJ
                        $obj_cliente_emporium->_inscricao_estadual = $sku->customer_sku_id;
                    }
                }
                // STATUS
                $obj_cliente_emporium->_status = $sku->customer_sku_status;
            }
            else {
                Log::Msg(3,"Importacao Customer.sku  STATUS[ ERROR ] customer_key[ $customer_key ]");
            }

            // SELECIONANDO ENDERECO
            $sql = "SELECT `custaddr_type`, `custaddr_address`, `custaddr_number`, `custaddr_comple` , `custaddr_neig`, `custaddr_city`, `custaddr_state`, `custaddr_zip` FROM customer_address WHERE customer_key = {$obj_customer->customer_key}";
            $result = $record->load($sql);

            if ($result->count != 0) {
                Log::Msg(3,"Importacao Customer.address. STATUS[ OK ] customer_key[ $customer_key ]");
                // SE TIVER ALGUM ENDERECO
                foreach ($result->rows as $address){
                    // OBS POR ENQUANTO SO ACEITA 1 ENDERECO
                    // PARA ACEITAR MAIS E SO FAZER O TRATAMENTO NESTA PARTE

                    // SE o TIPO FISCAL FOR IGUAL AO TIPO DE ENDERECO
                    // 1 - FISICA == 1 - RESIDENCIAL ou 2 - JURIDICA == 2 - COMERCIAL
                    if ($obj_cliente_emporium->_tipo == $address->custaddr_type) {

                        $obj_cliente_emporium->_tipo_endereco    = $address->custaddr_type;
                        $obj_cliente_emporium->_rua              = $address->custaddr_address;
                        $obj_cliente_emporium->_numero           = $address->custaddr_number;
                        $obj_cliente_emporium->_bairro           = $address->custaddr_neig;
                        $obj_cliente_emporium->_cidade           = $address->custaddr_city;
                        $obj_cliente_emporium->_uf               = $address->custaddr_state;
                        $obj_cliente_emporium->_cep              = $address->custaddr_zip;
                        $obj_cliente_emporium->_complemento      = $address->custaddr_comple;
                        $obj_cliente_emporium->_id_referencia    = "tb_clientes";
                        $obj_cliente_emporium->_id_referencia_pk = $obj_customer->customer_key;
                    }
                }
            }
            else {
                Log::Msg(3,"Importacao Customer.address. STATUS[ ERROR ] customer_key[ $customer_key ]");
            }
            //var_dump($obj_cliente_emporium);
            return $obj_cliente_emporium;
        }
        else {
            // SE NAO TIVER O ID NA CUSTOMER ABORTAR E PASSAR PARA O PROXIMO REGISTRO
            Log::Msg(3,"Importacao Customer. STATUS[ ERROR ] customer_key[ $customer_key ]");
            return FALSE;
        }

    }


    public function Monta_Where(){
        Log::Msg(2,"Class[ Importacao_Clientes_Emporium ] Method[ Monta_Where ]");

    }

    public function Recupera_Ids(){
        Log::Msg(2,"Class[ Importacao_Clientes_Emporium ] Method[ Recupera_Ids ]");

        $sql = "SELECT customer_key FROM `customer`";
        $sql .= $this->Monta_Where();

        $record = new Repository("emporium");
        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0){
            //Saber o Total de Registros
            Log::Msg(3,"Total_a_Exportar [ {$results->count} ]");
            $this->total_a_exportar = $results->count;
            $this->obj_integracao->SetTotal($this->total_a_exportar);
            return $results->rows;
        }
        else {
            // NENHUM REGISTRO A EXPORTAT RETORNAR MESAGEM DE ERRO e ABORTAR
            Log::Msg(3,"Total_a_Exportar [ 0 ]");
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas Há Registros a Exportar";
            $aResult['code'] = "Erro: [6004002]";
            $this->obj_integracao->finaliza_integracao(); // Concluido

            die(json_encode($aResult));

        }

    }

    public function Testar_Conexao_Servidor_Emporium(){
        Log::Msg(2,"Class[ Importacao_Clientes_Emporium ] Method[ Testar_Conexao_Servidor_Emporium ]");

        $sql = "SELECT * FROM customer WHERE 1 LIMIT 1";
        $record = new Repository("emporium");
        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0){
            Log::Msg(3,"Teste_Conexao [ TRUE ]");
            return TRUE;
        }
        else {
            Log::Msg(3,"Teste_Conexao [ FALSE ]");
            return FALSE;
        }

    }

    public function importar_tipo_cliente(){
        Log::Msg(2,"Class[ Importacao_Clientes_Emporium ] Method[ importar_tipo_cliente ]");

        //1º Passo - Ler a Tabela do emporium
        $sql = "SELECT cst_type_key, cst_name FROM cst_type";
        $record = new Repository("emporium");
        $results = $record->load($sql);

        if ($results->count != 0){
            Log::Msg(3,"Importando Tabela de Tipo de Clientes");
            // Se Tiver Tipos de Clientes
            //2º Passo - Apagar a tabela
            $sql    = "DELETE FROM tb_tipo_cliente;";
            $record = new Repository();
            $record->delete($sql);

            //3º Passo - Gravar na tabela
            $record = new Repository();

            foreach ($results->rows as $row){
                $sql = "INSERT INTO tb_tipo_cliente (pk_tipo_cliente, tipo_cliente) VALUES ({$row->cst_type_key},'{$row->cst_name}')";
                $record->store($sql);
            }
        }
        else {
            Log::Msg(3,"Nao Ha Tipos de Clientes no Emporium");
            Log::Msg(3,"Criando Tipo Padrao");

            $record = new Repository();
            $sql = "INSERT INTO tb_tipo_cliente (pk_tipo_cliente, tipo_cliente) VALUES (0,'Cliente Comum')";
            $record->store($sql);
        }
    }
}



?>