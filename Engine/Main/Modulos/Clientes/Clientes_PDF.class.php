<?php
session_start();
/**
 * @package  :Clientes
 * @name     :Clientes_PDF.class.php
 * @class    :Clientes_PDF
 * @author   :Glauber Costa Vila-Verde
 * @date     :09/01/2013
 * @Diretorio:Main/Modulos/Clientes/
 * Classe Responsavel pela Geracao dos Arquivos PDF de Clientes
 * Erros
 *     5006008 - Falha ao Gerar arquivo PDF
 */
set_time_limit(1800);
ini_set('memory_limit', '16M');
Class Clientes_PDF extends AppReports {


    private $orientacao ='L'; //orientação da DANFE P-Retrato ou L-Paisagem
    private $papel      ='A4';     //formato do papel
    private $unidade    = 'mm';

    private $destino = 'F';  //destivo do arquivo pdf I-borwser, S-retorna o arquivo, D-força download, F-salva em arquivo local
    private $pdfDir  = '';      //diretorio para salvar o pdf com a opção de destino = F
    private $pdfName = 'Clientes.pdf';

    // // Page header
    function Header()
    {

    }

    public function GeraPdf(){

        $this->FPDF($this->orientacao,$this->unidade,$this->papel);

        $this->AliasNbPages();
        $this->AddPage();

        $this->SetMargins(5, 5, 5);
        $this->SetAutoPageBreak(true, 10);

        $this->SetLineWidth(0.1);

        $this->SetDrawColor(100,100,100);
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);

    }



    public function Clientes_Contatos_Pdf (){

        $clientes = new Clientes();

        $rows = $clientes->RelatorioClientes();

        // Se tiver Registros Comeca o PDF
        if ($rows){
                    
            $this->setDataReport($rows);
            
            $this->GeraPdf();

            // Criando a Grid
            $this->DataGrid(0);

            // cria os estilos utilizados no documento
            $this->addStyle('gridTitle', 'Courier', '10', 'B', '#1F497D', '#E0EBFF');
            $this->addStyle('rowP', 'Courier', '10', '',  '#000000', '#FFFFFF', 'T');
            $this->addStyle('rowI', 'Courier', '10', '',  '#000000', '#FFFFFF', 'T');
            //$this->addStyle('rowI', 'Courier', '8', '',  '#000000', '#E0EBFF', 'T');

            // $this->gridAddColumn('pk_id_cliente' , 'PK', 'center', 25);
            // $this->gridAddColumn('nome'          , 'Nome', 'center', 80, 'upper', TRUE);
            // $this->gridAddColumn('telefone_fixo' , 'Tel/Fixo', 'center', 25);
            // $this->gridAddColumn('telefone_movel', 'Celular', 'center', 25);
            // $this->gridAddColumn('cidade'        , 'Cidade', 'center', 35, 'upper', TRUE);
            // $this->gridAddColumn('bairro'        , 'Bairro', 'center', 45, 'upper', TRUE);
            // $this->gridAddColumn('rua'           , 'Logradouro', 'center', 60, 'upper', TRUE);
            // $this->gridAddColumn('numero'        , 'N', 'center', 15, 'upper', TRUE);

            $this->gridAddColumn('nome'          , 'Nome', 'center', 80, 'upper', FALSE, 'left');
            $this->gridAddColumn('telefone_fixo' , 'Tel/Fixo', 'center', 25, 'upper', FALSE, 'left');
            $this->gridAddColumn('telefone_movel', 'Celular', 'center', 25, 'upper', FALSE, 'left');
            $this->gridAddColumn('cidade'        , 'Cidade', 'center', 35, 'upper', FALSE, 'left');
            $this->gridAddColumn('bairro'        , 'Bairro', 'center', 45, 'upper', FALSE, 'left');
            $this->gridAddColumn('rua'           , 'Logradouro', 'center', 60, 'upper', FALSE, 'left');
            $this->gridAddColumn('numero'        , 'N', 'center', 15, 'upper', FALSE, 'left');

            
            $this->simpleGrid();


            $result = $this->Save($this->pdfName);
            Log::Msg(3,"Debug 9");
            echo json_encode($result);
        }
        else {
            // Deu Merda
        }
    }


    public function Clientes_Vendedor_Pdf (){

        $clientes = new Clientes();

        $rows = $clientes->RelatorioClientesVendedor();
        Log::Msg(3,"COUNT "+count($rows));
        // Se tiver Registros Comeca o PDF
        if ($rows){

        
            $this->setDataReport($rows);

            
            $this->GeraPdf();


            // Criando a Grid
            $this->DataGrid(0);

            // cria os estilos utilizados no documento
            //$this->addStyle('gridTitle', 'Courier', '10', 'B', '#1F497D', '#E0EBFF');
            $this->addStyle('gridTitle', 'Times', '10', 'B', '#1D1D1D', '#DCDCDC');
            $this->addStyle('rowP', 'Times', '8', '',  '#000000', '#FFFFFF', 'T');
            $this->addStyle('rowI', 'Times', '8', '',  '#000000', '#FFFFFF', 'T');

            // $this->gridAddColumn('LINENUNBER'    , 'N', 'center', 8, FALSE,TRUE);

            // $this->gridAddColumn('status'        , '', 'center', 5, 'blackList');

            // $this->gridAddColumn('nome'          , 'Nome', 'center', 60, 'cutNomeCliente', TRUE, 'left');

            // $this->gridAddColumn('telefone_fixo' , 'Tel/Fixo', 'center', 25);
            // $this->gridAddColumn('telefone_movel', 'Celular', 'center', 25);

            // $this->gridAddColumn('cidade'        , 'Cidade', 'center', 35, 'upper', TRUE, 'left');
            // $this->gridAddColumn('bairro'        , 'Bairro', 'center', 40, 'upper', TRUE, 'left');
            // $this->gridAddColumn('vendedor'      , 'Vendedor', 'center', 30, 'cutNomeVendedor', TRUE, 'left');
            // $this->gridAddColumn('qtd_pedidos'   , 'T.P', 'center', 12, 'upper', TRUE);
            // $this->gridAddColumn('valor_ultimo_pedido', 'V.U.P', 'center', 20, 'upper', TRUE , 'rigth');
            // $this->gridAddColumn('dt_ultimo_pedido', 'DT.U.P', 'center', 20, 'upper', TRUE);

            $this->gridAddColumn('LINENUNBER'    , 'N', 'center', 8, FALSE,TRUE);
            //$this->gridAddColumn('status'        , '', 'center', 5, 'blackList');
            $this->gridAddColumn('nome'          , 'Nome', 'center', 60, 'cutNomeCliente', FALSE, 'left');
            $this->gridAddColumn('telefone_fixo' , 'Tel/Fixo', 'center', 25);
            $this->gridAddColumn('telefone_movel', 'Celular', 'center', 25);
            $this->gridAddColumn('cidade'        , 'Cidade', 'center', 35, 'upper', FALSE, 'left');
            $this->gridAddColumn('bairro'        , 'Bairro', 'center', 40, 'upper', FALSE, 'left');
            $this->gridAddColumn('vendedor'      , 'Vendedor', 'center', 30, 'cutNomeVendedor', FALSE, 'left');
            $this->gridAddColumn('qtd_pedidos'   , 'T.P', 'center', 12, 'upper', FALSE);
            $this->gridAddColumn('valor_ultimo_pedido', 'V.U.P', 'center', 20, 'upper', FALSE , 'rigth');
            $this->gridAddColumn('dt_ultimo_pedido', 'DT.U.P', 'center', 20, 'upper', FALSE);
            
            $this->simpleGrid();


            $result = $this->Save($this->pdfName);
            echo json_encode($result);
        }
        else {
            // Deu Merda
        }
    }

    public function cutNomeVendedor($nome){
        return strtoupper(substr($nome, 0, 15));
    }

    public function cutNomeCliente($nome){
        Log::Msg(3,"cutNome: $nome");
        return strtoupper(substr($nome, 0, 32));
    }

    public function blackList($status){
        Log::Msg(3,"blackList: $status");
        $return = new StdClass();
        $return->tipo = 'Image';
        switch ($status) {
            case 2:
                $return->value = 'Resources/black_list.gif';
                $return->extensao = 'GIF';
//                 $return->width = "5";
//                 $return->heigth = "5";
            break;

            default:
                $return->value = null;
        }

        //return strtoupper(substr($nome, 0, 15));
        return $return;
    }

}
