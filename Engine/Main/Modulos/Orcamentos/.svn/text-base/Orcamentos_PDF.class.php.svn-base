<?php
session_start();
/**
 * @package  :Orcamentos
 * @name     :Orcamentos_PDF.class.php
 * @class    :Orcamentos_PDF
 * @author   :Glauber Costa Vila-Verde
 * @date     :19/05/2011
 * @Diretorio:Main/Modulos/Orcamentos/
 * Classe Responsavel pela Geracao dos Arquivos PDF de Orcamentos
 * Erros
 *     5006008 - Falha ao Gerar arquivo PDF
 */
set_time_limit(1800);
Class Orcamentos_PDF {

    private $pdf;            // objeto fpdf()
    private $orientacao='P'; //orientação da DANFE P-Retrato ou L-Paisagem
    private $papel='A4';     //formato do papel
    private $unidade = 'mm';
    private $destino = 'F';  //destivo do arquivo pdf I-borwser, S-retorna o arquivo, D-força download, F-salva em arquivo local
    private $pdfDir='';      //diretorio para salvar o pdf com a opção de destino = F
    private $pdfName='Pedido.pdf';
    private $fontePadrao='Times'; //Nome da Fonte para gerar o DANFE

    private $margSup = 2;
    private $margEsq = 2;
    private $margDir = 2;

    private $startX = 0;
    private $startY = 0;

    private $larguraTotal = 205; // Largura Total de Area Util em uma folha A4
    private $alturaTotal = 280;  // Altura Total de Area UTIL em una folha A4

    private $maxCharLinha = 100; // Maximo de Caracteres por linha


    private $HmaxHeader1Pag = 0;
    private $HmaxHeader2Pag = 0;


    private $x = 0;
    private $y = 0;

    private $aFont = array();

    private $pk_orcamento  = '';
    private $tipo_pdf      = 1;
    private $orcamento     = null;
    private $pagAtual      = 0;
    private $total_paginas = 0;
    private $total_produtos= 0;
    private $produto_atual = 0;
    private $produtos_exibidos = 0;

    private $observacoes_produtos = null;

    public function __construct(){

        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->pk_orcamento = $_REQUEST['pk_orcamento'];
        $this->tipo_pdf = $_REQUEST['tipo_pdf'];

        $this->pdfDir = Common::Verifica_Diretorio_Work();

        // Declarando Fontes
        $this->aFont[0] = array('font'=>$this->fontePadrao,'size'=>7,'style'=>'');
        $this->aFont[1] = array('font'=>$this->fontePadrao,'size'=>14,'style'=>'B');
        $this->aFont[2] = array('font'=>$this->fontePadrao,'size'=>10,'style'=>'');
        $this->aFont[3] = array('font'=>$this->fontePadrao,'size'=>6,'style'=>'');
        $this->aFont[4] = array('font'=>$this->fontePadrao,'size'=>7,'style'=>'B');
        $this->aFont[5] = array('font'=>$this->fontePadrao,'size'=>10,'style'=>'B');

        // posição inicial do relatorio
        $this->startX = 5;
        $this->startY = 5;

    }


    public function Monta_Orcamento_Pdf(){
        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ Monta_Orcamento_Pdf ]");


            // estancia um objeto orcamento
            $orcamento = new Orcamentos_Servidor();
            $orcamento->setPkOrcamento($this->pk_orcamento);
            $orcamento->setTipoPdf($this->tipo_pdf);
            $this->orcamento = $orcamento->Gera_Obj_Pdf_Orcamento();


            $this->pdf = new PDF_Code128($this->orientacao, $this->unidade ,$this->papel);

            // estabelece contagem de paginas
            $this->pdf->AliasNbPages();

            // fixa as margens
            $this->pdf->SetMargins($this->margEsq,$this->margSup,$this->margDir);

            $this->pdf->SetDrawColor(100,100,100);
            $this->pdf->SetFillColor(255,255,255);

            $this->pdf->Open();

            $this->pdf->SetAuthor("Cronos Sistema de Retaguarda");

            $this->pdf->SetDisplayMode(100, 'continuous'); //define o nivel de zoom do documento PDF

            $this->pdf->SetLineWidth(0.1);

            $this->pdf->SetTextColor(0,0,0);

            $this->pdf->AddPage();

            $this->pagAtual       = 1;
            $this->produto_atual  = 0;
            $this->total_produtos = count($this->orcamento['produtos']);
            $this->observacoes_produtos = $this->getObservacoesProdutos($this->orcamento['produtos']);

            $this->total_paginas  = $this->total_paginas();

            // Cabecalho
            $this->Cabecalho();

            // Emitente
            //$this->Emitente($this->orcamento['emitente']);

            // Destinatario
            $this->Destinatario($this->orcamento['destinatario']);

            // Fatura
            $this->Fatura($this->orcamento['orcamento']);

            // Produtos Cabecalho
            $this->ProdutosCabecalho();

            // Produtos Detalhes
            $this->Produtos($this->orcamento['produtos']);

            // Observacoes do Orcamento
            $this->Observacoes_Orcamento();

            // Observacoes dos Produtos
            $this->Observacoes_Produtos();

            // Canhoto do Pedido
            $this->Canhoto($this->orcamento);

            $arquivo = $this->pdfDir . $this->pdfName;
            Log::Msg(2,"Pdf_Name [ $arquivo ]");


            $this->pdf->Output($arquivo, $this->destino);

            // Verifico se o Arquivo foi Gerado
            if (file_exists($arquivo)){

				// GUARDAR COPOIA DO PDF
				$file_bck = "Orcamento_" . $this->pk_orcamento .$this->pk_orcamento. ".pdf";
				$bck_dir = Common::Verifica_Diretorio_BackUp();
				$newfile = $bck_dir . $file_bck;
				Log::Msg(3,"BACKUP Arquivo[ $arquivo ], Bck [ $newfile ]");
				copy($arquivo, $newfile);

                $filename = "Orcamento_" . $this->pk_orcamento . ".pdf";
                $aResult['success'] = "true";
                $aResult['file']  = "{$this->pdfName}";
                $aResult['path']  = "{$this->pdfDir}";
                $aResult['filename']  = "$filename";
                echo json_encode($aResult);
            }
            else {
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas houve uma Falha, <b>NÃO</b> foi possivel gerar o arquivo...";
                $aResult['code'] = "5006008";
                die(json_encode($aResult));
            }




    }


    public function calculaHmaxHeader1Pag(){
        // 2 Linhas de 10 + Margem
        $HmaxCabecalho    = 20 + 2;
        // 1 Linhas de 32 + Margem
        //$HmaxEmitente     = 32 + 2;
        $HmaxEmitente     = 0;
        // 3 Linhas de 8 + Margem + Cabecalho
        $HmaxDestinatario = (3 * 8) + 2 + 4;
        // 1 Linhas de 8 + Cabecalho
        $HmaxFatura       = 8 + 4;

        $this->HmaxHeader1Pag = ($this->startY + $HmaxCabecalho + $HmaxEmitente + $HmaxDestinatario + $HmaxFatura);
        $this->__HdashedLine(0,$this->HmaxHeader1Pag,201,0.1,80);

    }

    public function calculaHmaxHeader2Pag(){
        // 1 Linhas de 32 + Margem
        //$HmaxEmitente     = 32 + 2;
        $HmaxCabecalho = 16 + 2;
        // 3 Linhas de 8 + Margem + Cabecalho
        $HmaxDestinatario = (3 * 8) + 2 + 4;

        $this->HmaxHeader1Pag = ($this->startY + $HmaxEmitente + $HmaxDestinatario);
        //$this->__HdashedLine(0,$this->HmaxHeader1Pag,201,0.1,80);

    }



    public function total_paginas(){

        //$this->calculaHmax();

        $produtos_na_1_pagina = 28;
        $produtos_nas_demais_pagina = 40;
        $total_paginas = ceil(($this->total_produtos - $produtos_na_1_pagina)/$produtos_nas_demais_pagina);
        $total_paginas += 1;

        return $total_paginas;
    }



   /**
    *
    */
    public function Produtos ($produtos){
        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ Produtos ]");

        // Campo 1 - Codigo Interno
        // Campo 2 - Descricao
        // Campo 3 - Unidade
        // Campo 4 - Quantidade
        // Campo 5 - Valor Unitario
        // Campo 6 - Valor Total
        for ($pag = $this->pagAtual; $pag <= $this->total_paginas; $pag++){

            if ($this->pagAtual != 1){
                $this->pdf->AddPage();
                $this->y = $this->startY;
                $this->x = $this->startX;

                // Emitente
                //$this->Emitente($this->orcamento['emitente']);
                // Cabecalho
                $this->Cabecalho($this->orcamento);
                // Produtos Cabecalho
                $this->ProdutosCabecalho();
            }

            $x = $this->startX;
            $y = $this->y;
            $hmax = ($this->alturaTotal - $this->y );
            $w = 25;
            $h = 6;

            $maxrow = 0;
            $i = 0;

            // Saber a Quantidade de Linhas que cabe
            $maxrow = floor($hmax / $h);
            $produtos_faltam = ceil($this->total_produtos - $this->produtos_exibidos);
            if ($produtos_faltam < $maxrow){
                $maxrow = $produtos_faltam;
            }
            $hmax = $maxrow * $h;


            for ($i = 0; $i < $maxrow; $i++ ) {
                Log::Msg(2,"Total_Paginas[ {$this->total_paginas} ], Pagina_Atual[ {$this->pagAtual} ], Total_Produtos[ {$this->total_produtos} ], Produto_Atual[ {$this->produto_atual} ], MaxRow[ $maxrow ]");

                $w = 5;
                $h = 6;
                // Trocar a Cor da Fonte Para Serparar as Categorias
                $red   = $produtos[$this->produto_atual]->cod_rgb['Red'];
                $green = $produtos[$this->produto_atual]->cod_rgb['Green'];
                $blue  = $produtos[$this->produto_atual]->cod_rgb['Blue'];

                $this->pdf->SetTextColor($red,$green,$blue);
                if ($produtos[$this->produto_atual]->codigo_cor) {
                    $this->pdf->SetFillColor($red,$green,$blue);
                    $this->pdf->SetTextColor(0,0,0);
                }
                else {
                    $this->pdf->SetFillColor(255,255,255);
                }
//                $this->pdf->SetDrawColor($red,$green,$blue);

                $label1 = strtoupper(utf8_decode($this->produto_atual+1));
                $this->__textBox($x,$y,$w,$h,$label1,$this->aFont[0],'C','C',true,'',null,null,null,'DF');

                // voltando a cor Padrao
                $this->pdf->SetTextColor(0,0,0);
                $this->pdf->SetFillColor(255,255,255);
//                $this->pdf->SetDrawColor(100,100,100);

                $x += $w;
                $w = 25;

                //$this->pdf->SetTextColor($red,$green,$blue);
                //$this->pdf->SetDrawColor($red,$green,$blue);

                $label1 = strtoupper(utf8_decode($produtos[$this->produto_atual]->fk_id_produto));
                $this->__textBox($x,$y,$w,$h,$label1,$this->aFont[0],'C','C',true,'');

                //$this->pdf->SetTextColor(0,0,0);
                //$this->pdf->SetDrawColor(100,100,100);

                // Campo 2
                $x += $w;
                $w = 82;

                $label2 = strtoupper(utf8_decode($produtos[$this->produto_atual]->descricao_longa));
                $this->__textBox($x,$y,$w,$h,$label2,$this->aFont[0],'C','L',true,'');

                // Campo 3
//                 $x += $w;
//                 $w = 8;
//
//                 $label3 = strtoupper(utf8_decode('Un'));
//                 $this->__textBox($x,$y,$w,$h,$label3,$this->aFont[0],'C','C',true,'');
                $x += $w;
                $w = 8;
                if ($produtos[$this->produto_atual]->observacao_produto){
                    $campo3 = '*';
                }
                else {
                    $campo3 = '';
                }

                $label3 = strtoupper(utf8_decode($campo3));
                $this->__textBox($x,$y,$w,$h,$label3,$this->aFont[1],'C','C',true,'');

                // Campo 4
                $x += $w;
                $w = 20;

                $label4 = strtoupper(utf8_decode($produtos[$this->produto_atual]->quantidade));
                $this->__textBox($x,$y,$w,$h,$label4,$this->aFont[0],'C','C',true,'');

                // Campo 5
                $x += $w;
                $w = 30;

				//$preco = number_format($produtos[$this->produto_atual]->preco, 2, '.','.');
				$preco = $produtos[$this->produto_atual]->preco;
                $label5 = strtoupper(utf8_decode("R$ ".$preco));
                $this->__textBox($x,$y,$w,$h,$label5,$this->aFont[0],'C','C',true,'');

                // Campo 6
                $x += $w;
                $w = 30;
				//$valor_total = number_format($produtos[$this->produto_atual]->valor_total, 2, '.','.');
				$valor_total = $produtos[$this->produto_atual]->valor_total;
                $label6 = strtoupper(utf8_decode("R$ ".$valor_total));
                $this->__textBox($x,$y,$w,$h,$label6,$this->aFont[0],'C','C',true,'');

                $this->y = $y + $h;

                $y = $this->y;
                $x = $this->startX;

                $this->produto_atual +=1;
                $this->produtos_exibidos += 1;
            }
            $this->pagAtual += 1;
        }

        return true;
    }

   /**
    *
    */
    public function ProdutosCabecalho (){
        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ ProdutosCabecalho ]");

        // Campo 1 - Codigo Interno
        // Campo 2 - Descricao
        // Campo 3 - Unidade
        // Campo 4 - Quantidade
        // Campo 5 - Valor Unitario
        // Campo 6 - Valor Total

        $x = $this->startX;
        $y = $this->y;
        $y += 2;

        $w = 64;
        $h = 4;

        // Label
        $label = strtoupper(utf8_decode('DADOS DOS PRODUTOS / SERVIÇOS'));
        $this->__textBox($x,$y,$w,$h,$label,$this->aFont[0],'T','L',false,'');

        $y += 3;
        $w = 200;

        // Caixa Onde Ficaram os Produtos
        $hmax = ($this->alturaTotal - $this->y );
        $h_produtos = 6;
        // Saber a Quantidade de Linhas que e cabe
        $maxrow = floor($hmax / $h_produtos);
        $produtos_faltam = ceil($this->total_produtos - $this->produtos_exibidos);
        if ($produtos_faltam < $maxrow){
            $maxrow = $produtos_faltam;
        }

        $hmax = ($maxrow * $h_produtos);

        $this->__textBox($x,$y,$w,$hmax);

        // Campo 1
        $w = 5;
        $h = 6;

        $label = strtoupper(utf8_decode('Nº'));
        $this->__textBox($x,$y,$w,$h,$label,$this->aFont[0],'C','C',true,'');
        $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);

        $x += $w;
        $w = 25;

        $label1 = strtoupper(utf8_decode('CÓDIGO PRODUTO'));
        $this->__textBox($x,$y,$w,$h,$label1,$this->aFont[0],'C','C',true,'');
        $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);

        // Campo 2
        $x += $w;
        $w = 82;

        $label2 = strtoupper(utf8_decode('DESCRIÇÃO DO PRODUTO / SERVIÇO'));
        $this->__textBox($x,$y,$w,$h,$label2,$this->aFont[0],'C','C',true,'');
        $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);

        // Campo 3
//         $x += $w;
//         $w = 8;
//
//         $label3 = strtoupper(utf8_decode('UN'));
//         $this->__textBox($x,$y,$w,$h,$label3,$this->aFont[0],'C','C',true,'');
//         $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);
        $x += $w;
        $w = 8;

        $label3 = strtoupper(utf8_decode('OBS'));
        $this->__textBox($x,$y,$w,$h,$label3,$this->aFont[0],'C','C',true,'');
        $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);

        // Campo 4
        $x += $w;
        $w = 20;

        $label4 = strtoupper(utf8_decode('QUANTIDADE'));
        $this->__textBox($x,$y,$w,$h,$label4,$this->aFont[0],'C','C',true,'');
        $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);

        // Campo 5
        $x += $w;
        $w = 30;

        $label5 = strtoupper(utf8_decode('VALOR UNITÁRIO'));
        $this->__textBox($x,$y,$w,$h,$label5,$this->aFont[0],'C','C',true,'');
        $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);

        // Campo 6
        $x += $w;
        $w = 30;

        $label6 = strtoupper(utf8_decode('VALOR TOTAL'));
        $this->__textBox($x,$y,$w,$h,$label6,$this->aFont[0],'C','C',true,'');
        $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);

        $this->y = $y + $h;

    }

   /**
    *
    */
    public function Fatura ($orcamento){
        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ Fatura ]");

        // Campo 1 - Forma de Pagamento
        // Campo 2 - Parcelamento
        // Campo 3 - Frete
        // Campo 4 - Desconto
        // Campo 5 - Total de Produtos
        // Campo 6 - Valor Total do Orcamento

        $x = $this->startX;
        $y = $this->y;
        $y += 2;

        $w = 28;
        $h = 7;
        // Label
        $label = strtoupper(utf8_decode('FATURA'));
        $this->__textBox($x,$y,$w,$h,$label,$this->aFont[0],'T','L',false,'');

        // Campo 1
        $y += 3;
        $h += 1;

        $label1 = strtoupper(utf8_decode('FORMA DE PAGAMENTO'));
        $this->__textBox($x,$y,$w,$h,$label1,$this->aFont[3],'T','L',true,'');

        $campo1 = strtoupper(utf8_decode($orcamento->forma_pagamento));
        $this->__textBox($x,$y,$w,$h,$campo1,$this->aFont[2],'C','L',false,'');

        // Campo 2
        $x = $x + $w;
        $label2 = strtoupper(utf8_decode('PARCELAMENTO'));
        $this->__textBox($x,$y,$w,$h,$label2,$this->aFont[3],'T','L',true,'');

        $campo2 = strtoupper(utf8_decode("{$orcamento->parcelamento}.x"));
        $this->__textBox($x,$y,$w,$h,$campo2,$this->aFont[2],'C','L',false,'');

        // Campo 7
        $x = $x + $w;
        $label7 = strtoupper(utf8_decode('DESEJA NFE'));
        $this->__textBox($x,$y,$w,$h,$label7,$this->aFont[3],'T','L',true,'');

        $label7_1 = strtoupper(utf8_decode("0 - NÃO\n1 - SIM"));
        $this->__textBox($x,$y,$w,$h,$label7_1,$this->aFont[3],'C','L',false,'');

        $x7 = ($x + $w - 7);
        $y7 = ($y + 1);

        $campo7 = strtoupper(utf8_decode($orcamento->nfe));
        $this->__textBox($x7,$y7,6,6,$campo7,$this->aFont[2],'C','C',true,'');

        // Campo 3
        $x = $x + $w;
        $label3 = strtoupper(utf8_decode('FRETE POR CONTA'));
        $this->__textBox($x,$y,$w,$h,$label3,$this->aFont[3],'T','L',true,'');

        $label3_1 = strtoupper(utf8_decode("0 - EMITENTE\n1 - DESTINATÁRIO"));
        $this->__textBox($x,$y,$w,$h,$label3_1,$this->aFont[3],'C','L',false,'');

        $x3 = ($x + $w - 7);
        $y3 = ($y + 1);

        $campo3 = strtoupper(utf8_decode($orcamento->frete_por_conta));
        $this->__textBox($x3,$y3,6,6,$campo3,$this->aFont[2],'C','C',true,'');

        // Campo 4
        $x = $x + $w;
        $label4 = strtoupper(utf8_decode('DESCONTO'));
        $this->__textBox($x,$y,$w,$h,$label4,$this->aFont[3],'T','L',true,'');

		$desconto = $orcamento->desconto;
        $campo4 = strtoupper(utf8_decode("R$ {$desconto}"));
        $this->__textBox($x,$y,$w,$h,$campo4,$this->aFont[2],'C','R',false,'');

        // Campo 5
        $x = $x + $w;
        $label5 = strtoupper(utf8_decode('VALOR TOTAL PRODUTOS'));
        $this->__textBox($x,$y,$w,$h,$label5,$this->aFont[3],'T','L',true,'');

		$valor_total = $orcamento->valor_total;
        $campo5 = strtoupper(utf8_decode("R$ {$valor_total}"));
        $this->__textBox($x,$y,$w,$h,$campo5,$this->aFont[2],'C','R',false,'');

        // Campo 6
        $x = $x + $w;
        $w += 4;
        $label6 = strtoupper(utf8_decode('VALOR TOTAL'));
        $this->__textBox($x,$y,$w,$h,$label6,$this->aFont[3],'T','L',true,'');

		$valor_pagar = $orcamento->valor_pagar;
        $campo6 = strtoupper(utf8_decode("R$ {$valor_pagar}"));
        $this->__textBox($x,$y,$w,$h,$campo6,$this->aFont[2],'C','R',false,'');




        $this->y = $y + $h;
    }


   /**
    *
    */
    public function Destinatario ($destinatario){
        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ Destinatario ]");

        // Campo 1 - Nome/Razao Social
        // Campo 2 - CNPJ/CPF
        // Campo 3 - Data de Emissao
        // Campo 4 - Endereco
        // Campo 5 - Bairro
        // Campo 6 - Cep
        // Campo 7 - Municipio
        // Campo 8 - UF
        // Campo 9 - TELEFONE FIXO
        // Campo 10 - E-mail
		// Campo 11 - Inscricao Estadual

        $x = $this->startX;
        $y = $this->y;
        $y += 2;

        $w = 67;
        $h = 7;

        // Label
        $label = strtoupper(utf8_decode('DESTINATÁRIO / REMETENTE'));
        $this->__textBox($x,$y,$w,$h,$label,$this->aFont[0],'T','L',false,'');

        // Campo 1
        $y += 3;
        $w = 120;
        $h += 1;

        $label1 = strtoupper(utf8_decode('NOME / RAZÃO SOCIAL'));
        $this->__textBox($x,$y,$w,$h,$label1,$this->aFont[3],'T','L',true,'');

        $campo1 = strtoupper(utf8_decode($destinatario->nome));
        $this->__textBox($x,$y,$w,$h,$campo1,$this->aFont[2],'C','L',false,'');

        // Campo 2
        $x += $w;
        $w = 46;

        $label2 = strtoupper(utf8_decode('CNPJ / CPF'));
        $this->__textBox($x,$y,$w,$h,$label2,$this->aFont[3],'T','L',true,'');

        $campo2 = strtoupper(utf8_decode($destinatario->cpf_cnpj));
        $this->__textBox($x,$y,$w,$h,$campo2,$this->aFont[2],'C','L',false,'');

        // Campo 3
        $x += $w;
        $w = 34;

        $label3 = strtoupper(utf8_decode('DATA DA EMISSÃO'));
        $this->__textBox($x,$y,$w,$h,$label3,$this->aFont[3],'T','L',true,'');

        $campo3 = strtoupper(utf8_decode($this->orcamento['orcamento']->data_emissao));
        $this->__textBox($x,$y,$w,$h,$campo3,$this->aFont[2],'C','L',false,'');

        // Campo 4
        $w = 98;
        $y += $h;
        $x = $this->startX;

        $label4 = strtoupper(utf8_decode('ENDEREÇO'));
        $this->__textBox($x,$y,$w,$h,$label4,$this->aFont[3],'T','L',true,'');


        $endereco = "{$destinatario->rua}, Nº {$destinatario->numero} {$destinatario->complemento}";
        $campo4 = strtoupper(utf8_decode($endereco));
        $this->__textBox($x,$y,$w,$h,$campo4,$this->aFont[2],'C','L',false,'');

        // Campo 5
        $x += $w;
        $w = 79;

        $label5 = strtoupper(utf8_decode('BAIRRO / DISTRITO'));
        $this->__textBox($x,$y,$w,$h,$label5,$this->aFont[3],'T','L',true,'');

        $campo5 = strtoupper(utf8_decode($destinatario->bairro));
        $this->__textBox($x,$y,$w,$h,$campo5,$this->aFont[2],'C','L',false,'');

        // Campo 6
        $x += $w;
        $w = 23;

        $label6 = strtoupper(utf8_decode('CEP'));
        $this->__textBox($x,$y,$w,$h,$label6,$this->aFont[3],'T','L',true,'');

        $campo6 = strtoupper(utf8_decode($destinatario->cep));
        $this->__textBox($x,$y,$w,$h,$campo6,$this->aFont[2],'C','L',false,'');

        // Campo 7
        $w = 94;
        $y += $h;
        $x = $this->startX;

        $label7 = strtoupper(utf8_decode('MUNICÍPIO'));
        $this->__textBox($x,$y,$w,8,$label7,$this->aFont[3],'T','L',true,'');

        $campo7 = strtoupper(utf8_decode($destinatario->cidade));
        $this->__textBox($x,$y,$w,$h,$campo7,$this->aFont[2],'C','L',false,'');

        // Campo 8
        $x += $w;
        $w = 8;

        $label8 = strtoupper(utf8_decode('UF'));
        $this->__textBox($x,$y,$w,8,$label8,$this->aFont[3],'T','L',true,'');

        $campo8 = strtoupper(utf8_decode($destinatario->uf));
        $this->__textBox($x,$y,$w,$h,$campo8,$this->aFont[2],'C','L',false,'');

        // Campo 9
        $x += $w;
        $w = 34;

        $label9 = strtoupper(utf8_decode('TELEFONE FIXO'));
        $this->__textBox($x,$y,$w,8,$label9,$this->aFont[3],'T','L',true,'');

        $campo9 = strtoupper(utf8_decode($destinatario->telefone_fixo));
        $this->__textBox($x,$y,$w,$h,$campo9,$this->aFont[2],'C','L',false,'');

        // Campo 10
        // $x += $w;
        // $w = 64;

        // $label10 = strtoupper(utf8_decode('E-MAIL'));
        // $this->__textBox($x,$y,$w,8,$label10,$this->aFont[3],'T','L',true,'');

        // $campo10 = strtolower(utf8_decode($destinatario->email));
        // $this->__textBox($x,$y,$w,$h,$campo10,$this->aFont[2],'C','L',false,'');
		// Campo 11
		$x += $w;
        $w = 64;

        $label11 = strtoupper(utf8_decode('INSCRIÇÃO ESTADUAL / RG'));
        $this->__textBox($x,$y,$w,8,$label11,$this->aFont[3],'T','L',true,'');

        $campo11 = strtoupper(utf8_decode($destinatario->rg_ie));
        $this->__textBox($x,$y,$w,$h,$campo11,$this->aFont[2],'C','L',false,'');

        $this->y = $y + $h;

    }
   /**
    *
    */
    private function Emitente($emitente){
        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ Emitente ]");

        // Coluna 1 - Emitente
        // Campo 1 - Logotipo
        // Campo 2 - Razao Social
        // Campo 3 - Endereco Completo

        // Coluna 2 - Informacoes ao Cliente
        // Campo 2_1 - Informacoes

        // Coluna 3 - Informacoes do Orcamento
        // Campo 1 - Numero do Orcamento
        // Campo 2 - Codigo de Barras
        // Campo 3 - Paginas ex.: 1 de 10

        $x = $this->startX;
        $y = $this->y;

        // Coluna 1
        $w=80;
        $h=32;

        $this->__textBox($x,$y,$w,$h);

        // Campo 1 - Logotipo
        $w1 = 80;
        $h1 = 20;
        $this->__textBox($x,$y,$w1,$h1);

        $logomarca = $emitente->img_logotipo;

        if (is_file($logomarca)){
            $logoInfo=getimagesize($logomarca);
            $logoW=$logoInfo[0];
            $logoH=$logoInfo[1];
            $logoWmm = ($logoW/72)*25.4;
            $imgW = $logoWmm;
            $logoHmm = ($logoH/72)*25.4;
            $imgH = $logoHmm;
            if ( $logoWmm > $w/2 ){
                $imgW = $w/2;
                $imgH = $logoHmm * ($imgW/$logoWmm);
            }
            //$this->pdf->Image($logomarca,$x+($w1/4),$y+($h1/12),$imgW,0,'','jpeg');
            $this->pdf->Image($logomarca,$x+($w1/4),$y+($h1/12),$imgW,0,'','jpeg');
        }

        // // Campo 2 - Razao Social
        // $x2 = $x + $w1;
        // $h2 = $h1;
        // $w2 = 45;

        // $campo2 = strtoupper(utf8_decode($emitente->razao_social));
        // $this->__textBox($x2,$y,$w2,$h2,$campo2,$this->aFont[5], 'C','C',true,'');

        // Campo 3 - Endereco Completo
        $y3 += $y + $h1;
        $h3 = 12;

        $endereco = $emitente->rua . ", Nº " . $emitente->numero . $emitente->complemento . ' - ' .$emitente->bairro . ' - ' .  $emitente->uf . ' - CEP: ' . $emitente->cep ;
        $cnpj     = "CNPJ: " . $emitente->cnpj;
        $telefone = "TEL: " . $emitente->telefone_fixo;
        $email    = "EMAIL: ". strtolower($emitente->email);

        $endereco = strtoupper(utf8_decode($endereco));
        $cnpj     = strtoupper(utf8_decode($cnpj));
        $telefone = strtoupper(utf8_decode($telefone));
        $email    = utf8_decode($email);


        $campo3 = $endereco . "\n" . $cnpj . "\n" . $telefone . "\n" . $email;
        $this->__textBox($x,$y3,$w,$h3,$campo3,$this->aFont[0], 'T','C',true,'');


        // Coluna 2
        $x = $x +$w;
        $w = 60;
        $this->__textBox($x,$y,$w,$h);
        //Label 2_1
        $label2_1 = strtoupper(utf8_decode('PEDIDO'));
        $this->__textBox($x,$y,$w,8,$label2_1,$this->aFont[1],'T','C',false,'');
        // Campo 1
        $campo2_1 = strtoupper(utf8_decode('Texto Informativo'));
        $this->__textBox($x,$y,$w,$h,$campo2_1,$this->aFont[0],'C','C',false,'');

        // Coluna 3
        $x = $x +$w;
        $w = 60;
        $this->__textBox($x,$y,$w,$h);

        // Campo 1
        $h3_1 = 8;

        $campo3_1 = strtoupper(utf8_decode("Nº {$this->orcamento[orcamento]->pk_orcamento}"));
        $this->__textBox($x,$y,$w,$h3_1,$campo3_1,$this->aFont[2],'C','C',true,'');

        // Campo 2 - Codigo de Barras
        $h3_2 = 16;
        $y3_2 = $y + $h3_1;

        $campo3_2 = strtoupper(utf8_decode($this->orcamento['orcamento']->pk_orcamento));
        $this->pdf->SetFillColor(0,0,0);
        //$this->__textBox($x,$y3_2,$w,$h3_2,$campo3_2,$this->aFont[2],'C','C',true,'');
        $bW = 56;
        $bH = 12;
        $this->pdf->Code128($x+(($w-$bW)/2),$y3_2+2,$campo3_2,$bW,$bH);
        $this->pdf->SetFillColor(255,255,255);
        // Campo 3
        $h3_3 = 8;
        $y3_3 = $y3_2 + $h3_2;

        $campo3_3 = utf8_decode("Página {$this->pagAtual} de {$this->total_paginas}");
        $this->__textBox($x,$y3_3,$w,$h3_3,$campo3_3,$this->aFont[2],'C','C',true,'');


        $this->y = $y + $h;
    }

   /**
    *
    */
    private function Cabecalho(){
        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ Cabecalho ]");

        // Campo 1 - informacoes sonbre o orcamento
        // Campo 2 - Numero do Orcamento
        // Campo 3 - Data da Emissao
        // Campo 4 - Identificacao do Vendedor
        $x = $this->startX;
        $y = $this->startY;
        $w = 200;
        $h = 4;

        $pagina = utf8_decode("Página {$this->pagAtual} de {$this->total_paginas}");
        $this->__textBox($x,$y,$w,4,$pagina,$this->aFont[0],'B','R',false,'');


        // Campo 3 - x = 5, y = 15, w = 35, h = 10
        $y += $h;
        $w = 35;

        $label2 = utf8_decode('Nº PEDIDO');
        $this->__textBox($x,$y,$w,10,$label2,$this->aFont[3],'T','L',true,'');

        $campo2 = utf8_decode($this->orcamento['orcamento']->pk_orcamento);
        $this->__textBox($x,$y,$w,10,$campo2,$this->aFont[2],'C','L',false,'');

        $x += $w;
        $label3 = utf8_decode('DATA DE EMISSÃO');
        $this->__textBox($x,$y,$w,10,$label3,$this->aFont[3],'T','L',true,'');

        $campo3 = utf8_decode($this->orcamento['orcamento']->data_emissao);
        $this->__textBox($x,$y,$w,10,$campo3,$this->aFont[2],'C','L',false,'');

        // Campo 4 - x = 40, y = 15, w = 125, h = 10
        $x += $w;
        $w = 90;
        $label4 = utf8_decode('IDENTIFICAÇÃO DO VENDENDOR');
        $this->__textBox($x,$y,$w,10,$label4,$this->aFont[3],'T','L',true,'');

        $campo4 = utf8_decode($this->orcamento['orcamento']->vendedor);
        $this->__textBox($x,$y,$w,10,$campo4,$this->aFont[2],'C','L',false,'');

        $x1 = $x + $w;

        $campo2_1 = utf8_decode("Nº {$this->orcamento[orcamento]->pk_orcamento}");
        $this->__textBox($x1,$y,40,10,$campo2_1,$this->aFont[0],'B','C',true,'');

        $this->pdf->SetFillColor(0,0,0);
        $this->pdf->Code128($x1+2,$y+1,$this->orcamento[orcamento]->pk_orcamento,36,6);
        $this->pdf->SetFillColor(255,255,255);

        $y += 10;

        $y += 2;

        $this->y = $y;

    }


    public function Observacoes_Orcamento(){

        // 1 Passo Saber se tem Observacao
        if ($this->orcamento['orcamento']->observacao){

            $x = $this->startX;
            $y = $this->y + 2;

            $this->__HdashedLine($x,$y,201,0.1,80);

            $this->y = $y + 2;

            $w = 200;


            // Altura da linha
            $h = 3;

            $campo1 = strtoupper(utf8_decode($this->orcamento['orcamento']->observacao));

            // Contar quantas quebras de linhas
            $cnt_linhas = substr_count($campo1, "\n");
            // Se nao tiver pelo menos uma linha
            if ($cnt_linhas < 1) {$cnt_linhas = 1;}
            // Calculando a ALtura da Caixa de Texto (linhas * altura linha + label)
            $h = 7;
            $htotal = ($cnt_linhas * $h);

            //  Calulando se a Area Disponivel
            $hmax = ($this->alturaTotal - $y);

            // 2 passo - Saber se a Observacao do Cabe na Area Disponivel
            if ($hmax < $htotal){
                // Nao Cabe Nao posso Escrever, Adicionar outra Pagina
                $this->pdf->AddPage();
                $this->y = $this->startY;
                $this->x = $this->startX;

                // Emitente
                $this->Emitente($this->orcamento['emitente']);
            }

            $y = $this->y;

            // Escrevendo
            // Label
            $label = strtoupper(utf8_decode('OBSERVAÇÕES DO PEDIDO'));
            $this->__textBox($x,$y,$w,$h,$label,$this->aFont[0],'C','C',true,'');

            $y+= $h;
            $h = 4;

            //$campo1 = strtoupper(utf8_decode($this->orcamento['orcamento']->observacao));
            // Contar quantas quebras de linhas
            $cnt_linhas = substr_count($campo1, "\n");
            // Se nao tiver pelo menos uma linha
            if ($cnt_linhas < 1) {$cnt_linhas = 1;}

            // Calculando a ALtura da Caixa de Texto
            $h1 = ($cnt_linhas * $h);

            //$this->__textBox($x,$y,$w,$h1);
            $this->__textBox($x,$y,$w,$h1,$campo1,$this->aFont[0],'T','L',true,'');

            $this->y = $y + 2 + $h1;

        }
    }

    public function Observacoes_Produtos(){

        // Saber se Existe Produto Com Observacao
        if ($this->observacoes_produtos){

            $total_produtos = count($this->observacoes_produtos);
            $produtos_exibidos = 0;
            $produto_atual = 0;

            While ($produtos_exibidos < $total_produtos){

                $x = $this->startX;
                $y = $this->y +2;

                $hmax = ($this->alturaTotal - $this->y );

                $w = 200;
                $h = 4;

                $maxrow = 0;
                $i = 0;

                // Saber a Quantidade de Linhas que cabe
                $maxrow = floor($hmax / $h);

                if ($maxrow < 1){
                    // Nao Cabe Nao posso Escrever, Adicionar outra Pagina
                    $this->pdf->AddPage();
                    $this->y = $this->startY;
                    $this->x = $this->startX;

                    // Emitente
                    $this->Emitente($this->orcamento['emitente']);
                }

                $produtos_faltam = ceil($total_produtos - $produtos_exibidos);
                if ($produtos_faltam < $maxrow){
                    $maxrow = $produtos_faltam;
                }
                $hmax = $maxrow * $h;

                $w = 25;

                $label1 = strtoupper(utf8_decode('CÓDIGO PRODUTO'));
                $this->__textBox($x,$y,$w,$h,$label1,$this->aFont[0],'C','C',true,'');
                $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);

                // Campo 2
                $x += $w;
                $w = 175;

                $label2 = strtoupper(utf8_decode('OBSERVAÇÕES'));
                $this->__textBox($x,$y,$w,$h,$label2,$this->aFont[0],'C','C',true,'');
                $this->pdf->Line($x+$w, $y, $x+$w, $y+$hmax);


                $y += $h;
                $x = $this->startX;


                for ($i = 0; $i < $maxrow; $i++ ) {

                    $w = 25;
                    $h = 4;

                    $campo = strtoupper(utf8_decode($this->observacoes_produtos[$produto_atual]->fk_id_produto));
                    $this->__textBox($x,$y,$w,$h,$campo,$this->aFont[3],'C','C',true,'');

                    $x += $w;
                    $w = 175;

                    $campo1 = str_replace("\n", '', $this->observacoes_produtos[$produto_atual]->observacao_produto);
                    $campo1 = strtoupper(utf8_decode($campo1));
                    $this->__textBox($x,$y,$w,$h,$campo1,$this->aFont[3],'C','L',true,'');

                    $this->y = $y + $h;

                    $y = $this->y;
                    $x = $this->startX;

                    $produto_atual +=1;
                    $produtos_exibidos += 1;
                }
            }
        }

    }


    public function getObservacoesProdutos($produtos){
        // Separar so os Produtos com Observacoes
        foreach ($produtos as $produto){
            if($produto->observacao_produto){
                $aProdutosObservacoes[] = $produto;
            }
        }
        return $aProdutosObservacoes;
    }


   /**
    *
    */
    private function Canhoto($emitente){
        Log::Msg(2,"Class[ Orcamentos_PDF ] Method[ Canhoto ]");

        $x = $this->startX;
        $y = $this->y;

        $hCanhoto = 32;
        //$hTotal = $hCanhoto + 12 + 6;
        $hTotal = $hCanhoto + 6;

        $y += 6;

        // Linha Separadora
        //$x -= 35;
        $this->__HdashedLine($x,$y,201,0.1,80);
        $y += 6;

        // Coluna 1
        $w=90;
        $h=$alturaCanhoto;

        $this->__textBox($x,$y,$w,$h);

        // Campo 1 - Logotipo
        $w1 = 45;
        $h1 = 16;
        $this->__textBox($x,$y,$w1,$h1);

        $logomarca = $this->orcamento[emitente]->img_logotipo;

        if (is_file($logomarca)){
            $logoInfo=getimagesize($logomarca);
            $logoW=$logoInfo[0];
            $logoH=$logoInfo[1];
            $logoWmm = ($logoW/72)*25.4;
            $imgW = $logoWmm;
            $logoHmm = ($logoH/72)*25.4;
            $imgH = $logoHmm;
            if ( $logoWmm > $w/2 ){
                $imgW = $w/2;
                $imgH = $logoHmm * ($imgW/$logoWmm);
            }
            //$this->pdf->Image($logomarca,$x+($w1/4),$y+($h1/12),$imgW,0,'','jpeg');
            $this->pdf->Image($logomarca,$x,$y,$imgW,0,'','jpeg');
        }

        // // Campo 2 - Razao Social
        $x2 = $x + $w1;
        $h2 = 8;
        $w2 = 45;

        $label1_2 = utf8_decode('Nº PEDIDO');
        $this->__textBox($x2,$y,$w2,$h2,$label1_2,$this->aFont[3],'T','L',true,'');

        $campo1_2 = strtoupper(utf8_decode($this->orcamento['orcamento']->pk_orcamento));
        $this->__textBox($x2,$y,$w2,$h2,$campo1_2,$this->aFont[2],'C','L',false,'');

        $y1_3 = $y + $h2;
        $label1_3 = utf8_decode('DATA DE EMISSÃO DO PEDIDO');
        $this->__textBox($x2,$y1_3,$w2,$h2,$label1_3,$this->aFont[3],'T','L',true,'');

        $campo1_3 = strtoupper(utf8_decode($this->orcamento['orcamento']->data_emissao));
        $this->__textBox($x2,$y1_3,$w2,$h2,$campo1_3,$this->aFont[2],'C','L',false,'');


        // Campo 3 - Endereco Completo
        $y3 += $y + $h1;
        $h3 = 8;

        $label3 = utf8_decode('IDENTIFICAÇÃO DO VENDENDOR');
        $this->__textBox($x,$y3,$w,$h3,$label3,$this->aFont[3],'T','L',true,'');

        $campo3 = strtoupper(utf8_decode($this->orcamento['orcamento']->vendedor));
        $this->__textBox($x,$y3,$w,$h3,$campo3,$this->aFont[2],'C','L',false,'');

        $y4 = $y3 + $h3;
        $label4 = utf8_decode('IDENTIFICAÇÃO DO CLIENTE');
        $this->__textBox($x,$y4,$w,$h3,$label4,$this->aFont[3],'T','L',true,'');

        $campo4 = strtoupper(utf8_decode($this->orcamento['destinatario']->nome));
        $this->__textBox($x,$y4,$w,$h3,$campo4,$this->aFont[2],'C','L',false,'');

        // Coluna 2
        $x = $x +$w;
        $w = 70;
        $this->__textBox($x,$y,$w,$h);
        $h2 = 8;
        //Label 2_1
        $label2_1 = utf8_decode('Nº ORÇAMENTO');
        $this->__textBox($x,$y,$w,$h2,$label2_1,$this->aFont[3],'T','L',true,'');

        $campo2_1 = '';
        $this->__textBox($x,$y,$w,$h2,$campo2_1,$this->aFont[2],'C','L',false,'');

        $y2 += $y + $h2;
        $w2_2 = ($w / 2);

        $label2_2 = utf8_decode('TICKET');
        $this->__textBox($x,$y2,$w2_2,$h2,$label2_2,$this->aFont[3],'T','L',true,'');

        $label2_3 = utf8_decode('Nº PDV');
        $this->__textBox($x+$w2_2,$y2,$w2_2,$h2,$label2_3,$this->aFont[3],'T','L',true,'');

        $y3 = $y2+$h2;
        $w2_4 = ($w / 2);

        $label2_4 = utf8_decode('Nº ENTREGA');
        $this->__textBox($x,$y3,$w2_2,$h2,$label2_4,$this->aFont[3],'T','L',true,'');

        $label2_5 = utf8_decode('DATA');
        $this->__textBox($x+$w2_2,$y3,$w2_2,$h2,$label2_5,$this->aFont[3],'T','L',true,'');

        $y4 = $y3 + $h2;

//         $label2_6 = utf8_decode('VALOR PEDIDO R$');
//         $this->__textBox($x,$y4,$w2_2,$h2,$label2_6,$this->aFont[3],'T','L',true,'');
//
//         $valor_pagar = $this->orcamento['orcamento']->valor_pagar;
//         $campo2_6 = strtoupper(utf8_decode("R$ {$valor_pagar}"));
//         $this->__textBox($x,$y4,$w2_2,$h2,$campo2_6,$this->aFont[2],'C','L',false,'');

        $label2_7 = utf8_decode('VALOR TOTAL TICKET R$');
        $this->__textBox($x,$y4,$w,$h2,$label2_7,$this->aFont[3],'T','L',true,'');


        // Coluna 3
        $x = $x +$w;
        $w = 40;
        $this->__textBox($x,$y,$w,$h);

        // Campo 1
        $h3_1 = 8;

        $campo3_1 = strtoupper(utf8_decode("PEDIDO"));
        $this->__textBox($x,$y,$w,$h3_1,$campo3_1,$this->aFont[2],'C','C',true,'');

        // Campo 2 - Codigo de Barras
        $h3_2 = 16;
        $y3_2 = $y + $h3_1;


        $this->pdf->SetFillColor(0,0,0);
        $this->pdf->Code128($x+2,$y3_2+4,$this->orcamento[orcamento]->pk_orcamento,36,14);
        $this->pdf->SetFillColor(255,255,255);

        $campo2_1 = utf8_decode("Nº {$this->orcamento[orcamento]->pk_orcamento}");
        $this->__textBox($x,$y3_2,40,24,$campo2_1,$this->aFont[2],'B','C',true,'');


        $y = $y + $hTotal;

        $x = $this->startX;

        $this->__HdashedLine($x,$y,201,0.1,80);

        $this->y = $y;

    }



    /**
     *__HdashedLine
     * Desenha uma linha horizontal tracejada com o FPDF
     *
     * @package NFePHP
     * @name __HdashedLine
     * @version 1.0
     * @author Roberto L. Machado <roberto.machado@superig.com.br>
     * @param number $x Posição horizontal inicial, em mm
     * @param number $y Posição vertical inicial, em mm
     * @param number $w Comprimento da linha, em mm
     * @param number $h Espessura da linha, em mm
     * @param number $n Numero de traços na seção da linha com o comprimento $w
     * @return none
     */
    private function __HdashedLine($x,$y,$w,$h,$n) {
        $this->pdf->SetLineWidth($h);
        $wDash=($w/$n)/2; // comprimento dos traços
        for( $i=$x; $i<=$x+$w; $i += $wDash+$wDash ) {
            for( $j=$i; $j<= ($i+$wDash); $j++ ) {
                if( $j <= ($x+$w-1) ) {
                    $this->pdf->Line($j,$y,$j+1,$y);
                }
            }
        }
    }



    /**
     *__textBox
     * Cria uma caixa de texto com ou sem bordas. Esta função perimite o alinhamento horizontal
     * ou vertical do texto dentro da caixa.
     * Atenção : Esta função é dependente de outras classes de FPDF
     *
     * Ex. $this->__textBox(2,20,34,8,'Texto',array('fonte'=>$this->fontePadrao,'size'=>10,'style='B'),'C','L',FALSE,'http://www.nfephp.org')
     *
     * @package NFePHP
     * @name __textBox
     * @version 1.0
     * @param number $x Posição horizontal da caixa, canto esquerdo superior
     * @param number $y Posição vertical da caixa, canto esquerdo superior
     * @param number $w Largura da caixa
     * @param number $h Altura da caixa
     * @param string $text Conteúdo da caixa
     * @param array $aFont Matriz com as informações para formatação do texto com fonte, tamanho e estilo
     * @param string $vAlign Alinhamento vertical do texto, T-topo C-centro B-base
     * @param string $hAlign Alinhamento horizontal do texto, L-esquerda, C-centro, R-direita
     * @param boolean $border TRUE ou 1 desenha a borda, FALSE ou 0 Sem borda
     * @param string $link Insere um hiperlink
     * @return none
     */
    private function __textBox($x,$y,$w,$h,$text='',$aFont=array('font'=>'Times','size'=>8,'style'=>''),$vAlign='T',$hAlign='L',$border=1,$link='',$force=TRUE,$hmax=0,$hini=0,$style='D'){
        $oldY = $y;
        $temObs = FALSE;
        $resetou = FALSE;
        //desenhar a borda
        if ( $border ) {
            $this->pdf->RoundedRect($x,$y,$w,$h,0.8,$style);
        }
        //estabelecer o fonte
        $this->pdf->SetFont($aFont['font'],$aFont['style'],$aFont['size']);
        //calcular o incremento
        $incY = $this->pdf->FontSize; //$aFont['size']/3;//$this->pdf->FontSize;
        if ( !$force ) {
            //verificar se o texto cabe no espaço
            $n = $this->pdf->WordWrap($text,$w);
        } else {
            $n = 1;
        }
        //calcular a altura do conjunto de texto
        $altText = $incY * $n;
        //separar o texto em linhas
        $lines = explode("\n", $text);
        //verificar o alinhamento vertical
        If ( $vAlign == 'T' ) {
            //alinhado ao topo
            $y1 = $y+$incY;
        }
        If ( $vAlign == 'C' ) {
            //alinhado ao centro
            $y1 = $y + $incY + (($h-$altText)/2);
        }
        If ( $vAlign == 'B' ) {
            //alinhado a base
            $y1 = ($y + $h)-0.5; //- ($altText/2);
        }
        //para cada linha
        foreach( $lines as $line ) {
            //verificar o comprimento da frase
            $texto = trim($line);
            $comp = $this->pdf->GetStringWidth($texto);
            if ( $force ) {
                $newSize = $aFont['size'];
                while ( $comp > $w ) {
                    //estabelecer novo fonte
                    $this->pdf->SetFont($aFont['font'],$aFont['style'],--$newSize);
                    $comp = $this->pdf->GetStringWidth($texto);
                }
            }
            //ajustar ao alinhamento horizontal
            if ( $hAlign == 'L' ) {
                $x1 = $x+1;
            }
            if ( $hAlign == 'C' ) {
                $x1 = $x + (($w - $comp)/2);
            }
            if ( $hAlign == 'R' ) {
                $x1 = $x + $w - ($comp+0.5);
            }

            //escrever o texto
            if ($hini >0){
               if ($y1 > ($oldY+$hini)){
                  if (!$resetou){
                     $y1 = oldY;
                     $resetou = TRUE;
                  }
                  $this->pdf->Text($x1, $y1, $texto);
               }
            } else {
               $this->pdf->Text($x1, $y1, $texto);
            }
            //incrementar para escrever o proximo
            $y1 += $incY;

            if (($hmax > 0) && ($y1 > ($y+($hmax-1)))){
               $temObs = TRUE;
               break;
            }

        }
        return ($y1-$y)-$incY;
    } // fim função __textBox


}
