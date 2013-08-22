<?php
header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Emporium
 * @name     :Exportacao_Produtos_Emporium
 * @class    :Exportacao_Produtos_Emporium.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :07/02/2011
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
 *      6003001 - Sem Conexao com Servidor Emporium
 *      6003002 - Nao Ha Registros a Exportar
 *      6003003 - Falha na Geracao do Arquivo
 */

class Exportacao_Produtos_Emporium {

    private $total_a_exportar = 0;
    private $total_exportados = 0;
    private $total_erros      = 0;

    private $quebra_linha     = "\n";
    private $separador        = "; ";
    private $file_name        = "Cadastro_Produtos_Emporium";
    private $file_path        = "";
    private $file_result      = "";
    private $file_percent     = "";

    private $campos           = "";
    private $compactar        = "";
    private $backup           = "";


//  -------------//---------------
    private $obj_integracao   = "";
    private $id_integracao    = "";


    function __construct(){
        Log::Msg(2,"Class[ Exportacao_Produtos_Emporium ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->obj_integracao = new Integracao_Emporium();
        $this->id_integracao = $this->obj_integracao->criar_registro(2, 'Produtos');


        $this->campos = "plu_key, long_description , quantity_in_stock, price, start";
        $this->compactar        = $_REQUEST['compactar'];
        $this->backup           = $_REQUEST['backup'];

        // Recuperando Diretorios
        $this->file_path   =  Common::Verifica_Diretorio_Work();
        $this->file_result = "{$this->file_path}{$this->file_name}.cvs";
        $this->backup_path =  Common::Verifica_Diretorio_BackUp();

        $this->file_percent  =  Common::Verifica_Diretorio_Work();
        $this->file_percent .=  "{$this->file_name}.pct";
    }

    public function Exporta_Produtos(){
        Log::Msg(2,"Class[ Exportacao_Produtos_Emporium ] Method[ Exporta_Produtos ]");


        // 1º Passo - Conectar Emporium
        if ($this->Testar_Conexao_Servidor_Emporium()){

            $this->obj_integracao->atualiza_status(1); // Iniciado

            // 2º Passo - Criar o Arquivo
            Log::Msg(3,"Arquivo_Result [ {$this->file_result} ]");
            $arq_result = fopen($this->file_result, "w");

            $acabecalho = $this->Criar_Cabecalhos();
            $cabecalho = implode($this->separador, $acabecalho);
            fputs($arq_result, "$cabecalho\n");

            // 3º Passo - Recuperar os Ids dos Registros a Serem Exportados
            $aIds = $this->Recupera_Ids();
            Log::Msg(3,"Iniciando Leitura e Escrita, Total_a_Exportar [ {$this->total_a_exportar} ]");

            // 4º Passo - Para Cada Registro Escrever no Arquivo
            $record = new Repository("emporium");
            $record->setLog(0);
            $record->setCharset('latin1');

            $this->obj_integracao->atualiza_status(2); // Em Andamento

            foreach ($aIds as $id){
                Log::Msg(3,"Exportando Produto. plu_key[ {$id->plu_key} ]");

                $sql = "SELECT a.plu_key, a.long_description , b.quantity_in_stock, c.price, MAX(c.start) as start FROM plu a INNER JOIN plu_store b ON a.plu_key = b.plu_key INNER JOIN pricing c ON a.plu_key = c.plu_key";
                $sql .= " WHERE a.plu_key = {$id->plu_key}";
                $sql .= " GROUP BY a.plu_key";

                $result = $record->load($sql);


                if ($result->count != 0) {
                    // Passando o Objeto para um array
                    $props = get_object_vars($result->rows[0]);

                    // Gerando linha com campos separados pelo separador :P
                    $linha = implode($this->separador, $props);
                    $linha .= $this->quebra_linha;

                    fputs($arq_result, $linha);

                    $this->total_exportados++;

                    $this->porcentagem();

                }
                else {
                    $this->total_erros++;
                }
            }

            Log::Msg(3,"Finalizando Leitura e Escrita, Total_Exportados [ {$this->total_exportados} ]");
            fclose($arq_result);

            $this->obj_integracao->SetTotalExportados($this->total_exportados);


            if (file_exists($this->file_result)) {

                // 5º Passo - Compactar o Arquivo / Fazer BackUp
                if ($this->backup == "on") {
                    $data = date("d-m-Y_H-i-s");
                    $backup_file = "{$this->backup_path}{$this->file_name}_$data.zip";
                    Log::Msg(3,"Gerando BackUp, BackUp_File [ $backup_file ]");
                    exec("zip -rj9 $backup_file {$this->file_result}");

                    $this->obj_integracao->SetArquivo($backup_file);
                }
                if ($this->compactar == "on") {
                    $zip_file = "{$this->file_result}.zip";
                    Log::Msg(3,"Compactando Resultado, Zip_File [ $zip_file ]");
                    exec("zip -rj9 $zip_file {$this->file_result}");
                    $this->file_result = $zip_file;
                    $this->file_name .=  ".cvs.zip";
                }
                else {
                    $this->file_name .=  ".cvs";
                }

                // 6º Passo - Retornar o Arquivo e o Resultado
                $this->obj_integracao->finaliza_integracao(); // Concluido

                $aResult['success'] = "true";
                $aResult['file']  = "{$this->file_name}";
                $aResult['path']  = "{$this->file_path}";
                $aResult['total_a_exportar'] = "{$this->total_a_exportar}";
                $aResult['total_exportados'] = "{$this->total_exportados}";
                $aResult['total_erros'] = "{$this->total_erros}";
                echo json_encode($aResult);

            }
            else {
                // Erro na Geracao do Arquivo
                // ABORTAR E RETORNAR MENSAGEM DE ERRO
                $this->obj_integracao->finaliza_integracao(); // Concluido

                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha na Geração do Arquivo.";
                $aResult['code'] = "Erro: [6003003]";
                die(json_encode($aResult));
            }

        }
        else {
        }

    }



    public function Criar_Cabecalhos() {
        Log::Msg(2,"Class[ Exportacao_Produtos_Emporium ] Method[ Criar_Cabecalhos ]");

        $labels = array();
        $labels['plu_key']            = "Codigo";
        $labels['long_description']   = "Descrição Longa";
        $labels['quantity_in_stock']  = "Quatidade";
        $labels['price']              = "Preço";
        $labels['start']              = "Dt Alteracao Preco";


        $acampos = explode(', ', $this->campos);

        foreach ($acampos as $campos) {
            $cabecalho[] = $labels[trim($campos)];
        }
        return $cabecalho;
    }


    public function Monta_Where(){
        Log::Msg(2,"Class[ Exportacao_Produtos_Emporium ] Method[ Monta_Where ]");

    }

    public function Recupera_Ids(){
        Log::Msg(2,"Class[ Exportacao_Produtos_Emporium ] Method[ Recupera_Ids ]");

        $sql = "SELECT plu_key FROM `plu`";
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
            $aResult['succes'] = "false";
            $aResult['msg']  = "Desculpe mas Há Registros a Exportar";
            $aResult['code'] = "Erro: [6003002]";
            $this->obj_integracao->finaliza_integracao(); // Concluido

            die(json_encode($aResult));

        }

    }

    public function Testar_Conexao_Servidor_Emporium(){
        Log::Msg(2,"Class[ Exportacao_Produtos_Emporium ] Method[ Testar_Conexao_Servidor_Emporium ]");

        $sql = "SELECT * FROM plu WHERE 1 LIMIT 1";
        $record = new Repository("emporium");
        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0){
            Log::Msg(3,"Teste_Conexao [ TRUE ]");
            return TRUE;
        }
        else {
            Log::Msg(3,"Teste_Conexao [ FALSE ]");
            // SEM CONEXAO COM EMPORIUM RETORNAR MESAGEM DE ERRO e ABORTAR
            $aResult['success'] = "false";
            $aResult['msg']  = "Desculpe mas Não Foi Possivel Conectar ao Servidor Emporium";
            $aResult['code'] = "Erro: [6003001]";
            $this->obj_integracao->finaliza_integracao(); // Concluido

            die(json_encode($aResult));

        }

    }

    public function porcentagem(){
        $valor = $this->total_exportados;
        $total = $this->total_a_exportar;
        $resultado = ($valor / $total) * 100;
        $resultado = number_format($resultado, 2, '.', '');
        //$resultado = "$resultado"."%";

        // Cria o Arquivo com a Porcentagem
        $arq_result = fopen($this->file_percent, "w");
        fputs($arq_result, $resultado);
        fclose($arq_result);
    }

}

?>