<?php
header('Content-Type: text/javascript; charset=UTF-8');
session_start();
/**
 * @package  :Emporium
 * @name     :Exportacao Clientes Emporium
 * @class    :Exportacao_Clientes_Emporium.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :09/12/2010
 * @version  :1.0
 * @revision :
 * @Diretorio:Main/Modulos/Emporium/
 * Classe Responsavel pela Geracao dos arquivos de exportacao para o Sistema de Retaguarda Emporium da Conecto Sistemas
 *
 * Tabelas envolvidas
 * tb_integracao_emporium
 *
 * Tipo Exportacao
 * 1 - Alterados
 * 2 - Completa
 * 3 - Por Periodo
 * 4 - Por Identificacao
 *
*/

class Exportacao_Clientes_Emporium {

    private $loja_origem      = "";
    private $tipo_exportacao  = "";
    private $dt_inicial       = "";
    private $dt_final         = "";
    private $valor_inicial    = "";
    private $valor_final      = "";
    private $compactar        = "";
    private $apagar           = "";
    private $backup           = "";
    private $todos_mesma_loja = "";

//  -------------//---------------
    private $obj_integracao   = "";
    private $id_integracao    = "";

//  -------------//---------------
    private $total_a_exportar = 0;
    private $total_exportados = 0;
    private $null             = '';
    private $quebra_linha     = "\n";
    private $file_name        = "Cadastro_Clientes";
    private $file_path        = "";
    private $file_result      = "";
    private $backup_path      = "";
    private $backup_file      = "";
    //private $path_emporium    = "/var/emporium/pos/RCV/0000/000/";
    private $path_emporium    = "/tmp/";

    function __construct(){
        Log::Msg(2,"Class[ Exportacao_Clientes_Emporium ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->obj_integracao = new Integracao_Emporium();
        $this->id_integracao = $this->obj_integracao->criar_registro(2, 'Clientes');

        $this->loja_origem      = $_REQUEST['loja'];
        $this->tipo_exportacao  = $_REQUEST['tipo_exportacao'];
        $this->dt_inicial       = $_REQUEST['dt_inicial'];
        $this->dt_final         = $_REQUEST['dt_final'];
        $this->valor_inicial    = $_REQUEST['valor_inicial'];
        $this->valor_final      = $_REQUEST['valor_final'];
        $this->compactar        = $_REQUEST['compactar'];
        $this->apagar           = $_REQUEST['apagar'];
        $this->backup           = $_REQUEST['backup'];
        $this->todos_mesma_loja = $_REQUEST['todos_mesma_loja'];


        // Recuperando Diretorios
        $this->file_path   =  Common::Verifica_Diretorio_Work();
        $this->file_result = "{$this->file_path}{$this->file_name}.cvs";
        $this->backup_path =  Common::Verifica_Diretorio_BackUp();
    }

    public function Exporta_Clientes(){
        Log::Msg(2,"Class[ Exportacao_Clientes_Emporium ] Method[ exporta_clientes ]");

        $this->obj_integracao->atualiza_status(1); // Iniciado

        Log::Msg(3,"Parametros da Exportacao");
        Log::Msg(3,"Loja_Origem      [ {$this->loja_origem} ]");
        Log::Msg(3,"Tipo_Exportacao  [ {$this->tipo_exportacao} ]");
        Log::Msg(3,"Data_Inicial     [ {$this->dt_inicial}    ] Data_Final  [ {$this->dt_final} ]");
        Log::Msg(3,"Valor_Inicial    [ {$this->valor_inicial} ] Valor_Final [ {$this->valor_final} ]");
        Log::Msg(3,"Compactar        [ {$this->compactar} ]");
        Log::Msg(3,"Apagar_Emporium  [ {$this->apagar} ]");
        Log::Msg(3,"BackUp           [ {$this->backup} ]");
        Log::Msg(3,"Todos_Mesma_Loja [ {$this->todos_mesma_loja} ]");

        // 1º Passo - saber os ids dos registros a serem exportados
        $aIds = $this->get_ids();

        // 2º Passo - Apagar arquivo antigo / Criar o Arquivo
        $comando = "rm -rf {$this->file_result}";
        Log::Msg(3,"Apagando Arquivo Antigo, Comando [ $comando ]");
        exec($comando, $verbose);

        Log::Msg(3,"Arquivo_Result [ {$this->file_result} ]");
        $arq_result = fopen($this->file_result, "w");

        // 3º Passo - Para Cada Id Buscar as informacoes
        Log::Msg(3,"Iniciando Leitura e Escrita, Total_a_Exportar [ {$this->total_a_exportar} ]");

        $record = new Repository();
        $record->setLog(0);
        $record->setCharset('latin1');

        $this->obj_integracao->atualiza_status(2); // Em Andamento
        foreach ($aIds as $id){

            $sql = "SELECT a.pk_id_cliente, a.fk_id_loja, a.tipo, a.status, a.tipo_cliente, a.nome, a.cpf, a.cnpj, a.rg, a.inscricao_estadual, DATE_FORMAT(a.dt_nascimento,'%d%m%Y') as dt_nascimento, a.profissao, a.sexo, a.estado_civil, a.telefone_fixo, a.telefone_movel, a.email, b.tipo_endereco, b.rua, b.numero, b.bairro, b.cidade, b.uf, b.cep, b.complemento, b.tipo_endereco, a.observacoes, DATE_FORMAT(a.dt_inclusao,'%d%m%Y') as dt_inclusao FROM tb_clientes a INNER JOIN tb_endereco b ON a.fk_id_endereco = b.id_endereco";
            $sql .= " WHERE a.pk_id_cliente = {$id->pk_id_cliente}";

            $result = $record->load($sql);

            // Gerar Linha CVS
            $aResultados = $this->Gerar_Linha_CVS($result->rows[0]);

            foreach ($aResultados as $linha) {
                fputs($arq_result, $linha);
            }
            $this->total_exportados++;
        }

        Log::Msg(3,"Finalizando Leitura e Escrita, Total_Exportados [ {$this->total_exportados} ]");
        fclose($arq_result);

        $this->obj_integracao->SetTotalExportados($this->total_exportados);

        if (file_exists($this->file_result)) {

            // 4º Passo - Compactar o Arquivo / Fazer BackUp
            if ($this->backup == "on") {
                $data = date("d-m-Y_H-i-s");
                $backup_file = "{$this->backup_path}{$this->file_name}_$data.zip";
                Log::Msg(3,"Gerando BackUp, BackUp_File [ $backup_file ]");
                exec("zip -rj9 $backup_file {$this->file_result}");

                $this->obj_integracao->SetArquivo($backup_file);
            }
            if ($this->compactar == "on") {
                $zip_file = "{$this->file_result}.zip";
                $comando = "rm -rf $zip_file";
                Log::Msg(3,"Apagando Resultado Antigo, Comando [ $comando ]");
                exec($comando, $verbose);
                Log::Msg(3,"Compactando Resultado, Zip_File [ $zip_file ]");
                exec("zip -rj9 $zip_file {$this->file_result}");
                $this->file_result = $zip_file;
                $this->file_name .=  ".cvs.zip";
            }
            else {
                $this->file_name .=  ".cvs";
            }


            // 5º Passo - Replicar o Arquivo para Servidor Emporium
            $replica = Common::getParametro("emporium_exportacao_cliente_replica");
            if ($replica){
                $result = $this->replica_servidor_emporium();
            }
            else {
                $result = false;
            }
            // 6º Passo - Finalizar tarefa e retornar os relatorios
            $this->obj_integracao->finaliza_integracao(); // Concluido

            if (!$result){
                // Se nao foi possivel copiar para emporim permitir download
                $aResult['success'] = "true";
                $aResult['file']  = "{$this->file_name}";
                $aResult['path']  = "{$this->file_path}";
                $aResult['total'] = "{$this->total_exportados}";
                echo json_encode($aResult);
            }
            else {
                $aResult['success'] = "true";
                $aResult['msg']     = "Exportação Concluida, arquivo já foi enviado ao servidor emporium, por favor aguarde o processamento.";
                echo json_encode($aResult);
            }
        }
        else {
            // Erro na Geracao do Arquivo
            // ABORTAR E RETORNAR MENSAGEM DE ERRO
            $this->obj_integracao->finaliza_integracao(); // Concluido

            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas houve uma Falha na Geração do Arquivo.";
            die(json_encode($aResult));
        }

    }


    public function get_ids(){
        Log::Msg(2,"Class[ Exportacao_Clientes_Emporium ] Method[ get_ids ]");

        $record = new Repository();

        $sql_select_id  = "SELECT pk_id_cliente FROM tb_clientes a";
        $sql_select_id .= $this->Monta_Where();

        $results = $record->load($sql_select_id);
        Log::Msg(5,$results);

        if ($results->count != 0){
            // Guardando Total a ser Exportado
            $this->total_a_exportar = $results->count;
            $this->obj_integracao->SetTotal($this->total_a_exportar);

            return $results->rows;
        }
        else {
            // NAO TEM REGISTROS A EXPORTAR
            // ABORTAR E RETORNAR MENSAGEM DE ERRO
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas Não Há Registros a Serem Exportados";

            $this->obj_integracao->finaliza_integracao(); // Concluido

            die(json_encode($aResult));
        }
    }


    public function Monta_Where(){
        Log::Msg(2,"Class[ Exportacao_Clientes_Emporium ] Method[ Monta_Where ]");

        $where = " WHERE";

        // 1º Passo - Saber a loja de origem
        if ($this->loja_origem == 0){
            // Todas as Lojas
        }
        else {
            $where .= " a.fk_id_loja = {$this->loja_origem} AND";
        }
        // 2º Passo - Saber o Tipo de Exportacao
        // ALTERADOS
        if ($this->tipo_exportacao == 1) {
            $ultima_exportacao = $this->Get_Data_Ultima_Exportacao();
            if ($ultima_exportacao){
                $where .= " a.dt_inclusao >= '$ultima_exportacao' OR a.dt_alteracao >= '$ultima_exportacao'";
            }
            else {
                // Se Nao Tiver Data da Ultima Exportacao
                // Fazer uma Completa
                $where .= " a.pk_id_cliente > 0";
            }

        }
        // COMPLETA
        elseif($this->tipo_exportacao == 2){
            $where .= " a.pk_id_cliente > 0";
        }
        // PERIODO
        elseif($this->tipo_exportacao == 3){


        }
        // IDENTIFICACAO
        elseif($this->tipo_exportacao == 4){

        }
        return $where;
    }

    public function Gerar_Linha_CVS($linha){
        //Log::Msg(2,"Class[ Exportacao_Clientes_Emporium ] Method[ Gerar_Linha_CVS ]");

        $null = $this->null;
        $quebra_linha = $this->quebra_linha;

        // Tratamento de Campos
        $status      = $this->apagar == 'on' ? 1 : 0;
        //$dt_cadastro = Common::converte_data($linha->dt_inclusao,'ddmmyyyy');

        if ($linha->tipo == 1){
            // Pessoa Fisica
            $sku_type1 = 1;
            $sku1      = $linha->cpf;
            $sku_type2 = 6;
            $sku2      = $linha->rg;
        }
        else {
            // Pessoa Juridica
            $sku_type1 = 2;
            $sku1      = $linha->cnpj;
            $sku_type2 = 8;
            $sku2      = $linha->inscricao_estadual;
        }

        if ($this->todos_mesma_loja == "on"){
            $loja_origem = 1;
        }
        else {
            $loja_origem = $linha->fk_id_loja;
        }

        $k = 0;

        // Record_Customer
        $linha1[0]  = $this->completa($status, 1);         // 1 Status
        $linha1[1]  = $this->completa(20, 2);              // 2 Record
        $linha1[2]  = $this->completa(7, 2);               // 2 Code_Type
        $linha1[3]  = $this->completa($linha->pk_id_cliente, 20); // 20 Code
        $linha1[4]  = $this->completa($linha->tipo, 3);    // 3 Customer_Type
        $linha1[5]  = $this->completa($linha->nome, 60);   // 60 Customer_Name
        $linha1[6]  = $this->completa($linha->nome, 60);   // 60 Customer_Name_Alt
        $linha1[7]  = $this->completa($linha->email, 50);  // 50 email
        $linha1[8]  = $this->completa($linha->telefone_fixo, 15);  // 15 Customer_Phone1
        $linha1[9]  = $this->completa($linha->telefone_movel, 15); // 15 Customer_Phone2
        $linha1[10] = $this->completa($linha->dt_inclusao,  8);   // 8 Customer_Date_Inc

        $linha1[11] = $this->completa($null, 25);          // 25 Customer_Job_Id
        $linha1[12] = $this->completa($null, 60);          // 60 Customer_Job_Name
        $linha1[13] = $this->completa($null, 15);          // 15 Customer_Job_Phone
        $linha1[14] = $this->completa($null, 30);          // 30 Customer_Title
        $linha1[15] = $this->completa($null, 15);          // 15 Customer_Revenue
        $linha1[16] = $this->completa($null, 8);           // 8  Customer_Job_Date
        $linha1[17] = $this->completa($null, 2);           // 2  Customer_Job_Type
        $linha1[18] = $this->completa($null, 60);          // 60 Customer_Job_Name2
        $linha1[19] = $this->completa($null, 15);          // 15 Customer_Job_Phone2
        $linha1[20] = $this->completa($null, 8);           // 8  Customer_Job_In
        $linha1[21] = $this->completa($null, 8);           // 8  Customer_Job_Out
        $linha1[22] = $this->completa($linha->dt_nascimento, 8);  // 8  Customer_Birthday
        $linha1[23] = $this->completa($loja_origem, 10);  // 10  Store_Key (Loja de Cadastramento)
//        $linha1[24] = $this->completa($null, 80);  // 80  Password
//        $linha1[25] = $this->completa($null, 80);  // 80  Crypt_Password
        $linha1[26] = $quebra_linha;

        $aResultados[$k] = implode('|',$linha1);
        $k++;

        // Record_address
        $linha2[0]  = $this->completa($status, 1);          // 1 Status
        $linha2[1]  = $this->completa(21, 2);               // 2 Record
        $linha2[2]  = $this->completa(7, 2);                // 2 Code_Type
        $linha2[3]  = $this->completa($linha->pk_id_cliente, 20); // 20 Code
        $linha2[4]  = $this->completa($linha->tipo_endereco, 3);  // 3 Address_Type
        $linha2[5]  = $this->completa($linha->rua, 60);     // 60 Address
        $linha2[6]  = $this->completa($linha->numero, 20);  // 20 Number
        $linha2[7]  = $this->completa($linha->complemento, 20);  // 20 Complement
        $linha2[8]  = $this->completa($linha->bairro, 20);  // 20 Neig
        $linha2[9]  = $this->completa($linha->cidade, 30);  // 30 City
        $linha2[10] = $this->completa($linha->uf, 2);       // 2 State
        $linha2[11] = $this->completa($linha->cep, 12);     // 12 Zip
        $linha2[12] = $this->completa($null, 60);           // 60 Reference
        $linha2[13] = $this->completa($null, 14);           // 14 Phone_Area_Code
        $linha2[14] = $this->completa($linha->telefone_fixo, 50);// 50 Phone_Number
        $linha2[15] = $this->completa($null, 3);            // 3 Addres_Time
        $linha2[16] = $quebra_linha;

        $aResultados[$k] = implode('|',$linha2);
        $k++;

        // GERAR 2 Linhas uma para CPF ou CNPJ
        // E Outra para RG ou IE
        // Record_Customer_Sku
        if (!empty($sku1)) {
            Log::Msg(3,"Escreveu Linha");
            $linha3[0]  = $this->completa($status, 1);          // 1 Status
            $linha3[1]  = $this->completa(22, 2);               // 2 Record
            $linha3[2]  = $this->completa(7 , 2);               // 2 Code_Type
            $linha3[3]  = $this->completa($linha->pk_id_cliente, 20); // 20 Code
            $linha3[4]  = $this->completa($sku_type1, 2);       // 2  Sku_Type
            $linha3[5]  = $this->completa($sku1, 30);           // 30 Sku
            $linha3[6]  = $this->completa($linha->status, 1);   // 1 Sku_Status (0 - Ativo, 1 - Inativo)
            $linha3[7]  = $this->completa($null, 13);           // 13 Amount_Left
            $linha3[8]  = $this->completa($null, 13);           // 13 Limit
            $linha3[10] = $this->completa($null, 8);            // 8  Points
            $linha3[11] = $quebra_linha;

            $aResultados[$k] = implode('|',$linha3);
            $k++;
        }

        if (!empty($sku2)) {
            // Record_Customer_Sku
            $linha4[0]  = $this->completa($status, 1);          // 1 Status
            $linha4[1]  = $this->completa(22, 2);               // 2 Record
            $linha4[2]  = $this->completa(7 , 2);               // 2 Code_Type
            $linha4[3]  = $this->completa($linha->pk_id_cliente, 20); // 20 Code
            $linha4[4]  = $this->completa($sku_type2, 2);       // 2  Sku_Type
            $linha4[5]  = $this->completa($sku2, 30);           // 30 Sku
            $linha4[6]  = $this->completa($linha->status, 1);   // 1 Sku_Status (0 - Ativo, 1 - Inativo)
            $linha4[7]  = $this->completa($null, 13);           // 13 Amount_Left
            $linha4[8]  = $this->completa($null, 13);           // 13 Limit
            $linha4[10] = $this->completa($null, 8);            // 8  Points
            $linha4[11] = $quebra_linha;

            $aResultados[$k] = implode('|',$linha4);
            $k++;
        }

        // Record_Customer_Category
        $linha5[0]  = $this->completa($status, 1);          // 1 Status
        $linha5[1]  = $this->completa(24, 2);               // 2 Record
        $linha5[2]  = $this->completa(7 , 2);               // 2 Code_Type
        $linha5[3]  = $this->completa($linha->pk_id_cliente, 20); // 20 Code
        $linha5[4]  = $this->completa($linha->tipo_cliente, 2);  // 2 Cst_Type
        $linha5[5]  = $quebra_linha;

        $aResultados[$k] = implode('|',$linha5);
        $k++;

        return $aResultados;

    }

    public function completa($string, $tamanho) {
        $quantidade = strlen($string);
        $retorno = $string;
        if ($quantidade < $tamanho) {
            for ($i=$quantidade; $i < $tamanho; $i++) {
                $retorno .= ' ';
            }
        }
        else {
            $retorno = substr($string, 0, $tamanho);
        }
        return $retorno;
    }

    public function Get_Data_Ultima_Exportacao(){
        Log::Msg(2,"Class[ Exportacao_Clientes_Emporium ] Method[ Get_Data_Ultima_Exportacao ]");

        $sql = "SELECT MAX(`dt_termino`) as ultima_exportacao FROM `tb_integracao_emporium` WHERE  entidade = 'Clientes' AND tipo = 2  AND status IN(3,5)";

        $record = new Repository();
        $results = $record->load($sql);

        if ($results->count != 0){
            Log::Msg(3,"Data_Ultima_Exportacao [{$results->rows[0]->ultima_exportacao}]");
            return $results->rows[0]->ultima_exportacao;
        }
        else {
            // Nao tem data da ultima exportacao
            return FALSE;
        }
    }

    public function replica_servidor_emporium(){
        Log::Msg(2,"Class[ Exportacao_Clientes_Emporium ] Method[ replica_servidor_emporium ]");

        $arquivo_result = "{$this->file_path}{$this->file_name}";

        // Recuperar a Pasta Compartilhada com Emporium Rcv
        $this->path_emporium = Common::getParametro('emporium_path_rcv');

        Log::Msg(3,"Copiando Arquivo Para Emporium. File [ $arquivo_result ], Path [ {$this->path_emporium} ]");
        exec("cp $arquivo_result {$this->path_emporium}", $verbose);

        if ($verbose){
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

}
?>