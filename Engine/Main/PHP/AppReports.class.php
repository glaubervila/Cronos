<?php
/** Class appReports
*
**/

class AppReports extends FPDF{
//class AppReports extends PDF_Code128 {

    public  $data_report;

    public  $repeatHeader = true;

    private $columns;
    private $colcounter;

    private $lastLineH;

    public function setDataReport($data_report){
        $this->data_report = $data_report;
    }

    public function getDataReport(){
        return $this->data_report;
    }

    public function setDataFilter($data_filter){
        $this->data_filter = $data_filter;
    }

    public function getDataFilter(){
        return $this->data_filter;
    }

    public function setRepeatHeader($repeat){
        $this->repeatHeader = $repeat;
    }

    public function getRepeatHeader(){
        return $this->repeatHeader;
    }

    public function setRepeatColumsHeader($repeat){
        $this->repeatColumsHeader = $repeat;
    }

    public function getRepeatColumsHeader(){
        return $this->repeatColumsHeader;
    }

    function Save($filename,$mode = 'F'){

        try
        {
            if(PHP_OS == 'WINNT') {
                $base_path = BASE_PATH;
                $path = $base_path.'\\tmp\\';
            }
            else {
                $path = Common::Verifica_Diretorio_Work();
                //$path = "../tmp/";
            }

            $arq = $path.$filename;
            //echo $arq;
            //echo getcwd();
            $this->Output($arq,$mode);


            if (file_exists($arq)){
                $aResult['success'] = "true";
                $aResult['file']  = "{$filename}";
                $aResult['path']  = "{$path}";
                $aResult['filename']  = "{$filename}";
                $aResult['mime']  = "pdf";
            }
            else {
                $aResult['failure'] = "true";
                $aResult['msg']  = "Desculpe mas Houve uma Falha ao Criar o Relatorio...<br>Arquivo não pode ser criado.";
            }
        }
        catch (Exception $e)
        {
            $aResult['failure'] = "true";
            $aResult['msg']  = $e->getMessage();
        }

        return $aResult;
    }

    function Footer(){
        // Position at 1.5 cm from bottom
        $this->SetY(-10);
        $this->SetTextColor(100,100,100);
        // Arial italic 8
        $this->SetFont('Arial','I',8);

        $this->Cell(200,10,utf8_decode('Cronos - Sistema de Pedidos'),0,0,'L');
        // Page number
        $this->Cell(0,10,utf8_decode('Pág '.$this->PageNo().'/{nb}'),0,0,'R');
    }

    // Criacao da Grid
    public function DataGrid($border = 1){

        // armazena as larguras
        $this->columns = array();
        // inicializa atributos
        $this->styles = array();

        $this->colcounter = 0;

        $this->gridStartX = $this->GetX();
        $this->gridStartY = $this->GetY();

        $this->gridBorder = $border;
    }

     /** Adiciona um novo estilo
     * @param @stylename nome do estilo
     * @param @fontface  nome da fonte
     * @param @fontsize  tamanho da fonte
     * @param @fontstyle estilo da fonte (B=bold, I=italic)
     * @param @fontcolor cor da fonte
     * @param @fillcolor cor de preenchimento
     */
    public function addStyle($stylename, $fontface, $fontsize, $fontstyle, $fontcolor, $fillcolor, $border)
    {
        $this->styles[$stylename] = array($fontface, $fontsize, $fontstyle, $fontcolor, $fillcolor, $border);
    }

    /**
     * Aplica um estilo
     * @param $stylename nome do estilo
     */
    public function applyStyle($stylename)
    {
        // verifica se o estilo existe
        if (isset($this->styles[$stylename]))
        {
            $style = $this->styles[$stylename];
            // obtém os atributos do estilo
            $fontface    = $style[0];
            $fontsize    = $style[1];
            $fontstyle   = $style[2];
            $fontcolor   = $style[3];
            $fillcolor   = $style[4];
            $border      = $style[5];

            // aplica os atributos do estilo
            $this->SetFont($fontface, $fontstyle); // fonte
            $this->SetFontSize($fontsize); // estilo
            $colorarray = self::rgb2int255($fontcolor);
            // cor do texto
            $this->SetTextColor($colorarray[0], $colorarray[1], $colorarray[2]);
            $colorarray = self::rgb2int255($fillcolor);
            // cor de preenchimento
            $this->SetFillColor($colorarray[0], $colorarray[1], $colorarray[2]);
        }
    }

    /**
     * Converte uma cor em RGB para um vetor de decimais
     * @param $rgb uma string contendo uma cor em RGB
     */
    private function rgb2int255($rgb)
    {
        $red   = hexdec(substr($rgb,1,2));
        $green = hexdec(substr($rgb,3,2));
        $blue  = hexdec(substr($rgb,5,2));

        return array($red, $green, $blue);
    }




    /**
     * Adiciona uma nova linha na tabela
     */
    public function gridAddRow()
    {
        $this->Ln(); // quebra de linha
        $this->colcounter = 0;
    }

    /**
     * Adiciona uma coluna ao relatório
     * @param $alias nome da coluna na consulta SQL
     * @param $label rótulo do campo (título da coluna)
     * @param $align alinhamento do Titulo da Coluna
     * @param $width tamanho da coluna
     * @param $format nome de uma funcao de formatacao a ser executada nas linhas
     * @param $scale  escala a fonte para caber na caixa de texto
     * @param $ralign  alinhamento do Conteudo da Coluna
     */
    public function gridAddColumn($alias, $label, $align, $width, $format, $scale, $ralign = 'center')
    {
        $this->columns[] = array('alias'=>$alias, 'label'=>utf8_decode($label), 'align'=>$align, 'width'=>$width, 'format'=>$format, 'scale'=>$scale, 'ralign'=>$ralign);

        $this->colWidths[] = $width;

        if ($alias == 'LINENUNBER'){
            $this->lineNumber = true;
        }
    }


    /**
     * Adiciona uma nova célula na linha atual da tabela
     * @param $content   conteúdo da célula
     * @param $align     alinhamento da célula
     * @param $stylename nome do estilo a ser utilizado
     * @param $colspan   quantidade de células a serem mescladas
     * @param $border    borda da celula
     * @param $scale     Escalona a fonte para caber na cell
     */
    public function gridAddCell($content, $align, $stylename = NULL, $colspan = 1, $border = 1, $scale = false)
    {
        $this->applyStyle($stylename); // aplica o estilo
   
        $fontsize = $this->styles[$stylename][1]; // obtém a fonte

        // Alinhamento Horizontal
        $alignH = strtoupper(substr($align,0,1));

        // obtém a Borda do Estilo
        if ($this->styles[$stylename][5]) {
            $border = $this->styles[$stylename][5];
        }

        // Largura
        $width = 0;
        // Altura
        $heigh = $fontsize * 0.5;

        // calcula a largura da célula (incluindo as mescladas)
        for ($n = $this->colcounter; $n < $this->colcounter + $colspan; $n++)
        {
            $width += $this->colWidths[$n];
        }

        // Saber se o Content ainda precisa de tratamentos
        if (is_object($content)){

            switch ($content->tipo) {
                case 'Image':

                    $x = $this->GetX();
                    $y = $this->GetY();
                    $this->Cell( $width, $heigh, '', $border, 0, $alignH, true);

                    if ($content->value){
                        $image = "{$content->value}";
                        $extensao = "{$content->extensao}";
                        $this->Image($image, $x, $y, null , $heigh, $extensao);
                    }
                break;
            }
        }
        else if (is_string($content)){
            // UTF8 DECODE
            $content = utf8_decode($content);
            // Verificar se é nessessario o ajuste
            $str_width=$this->GetStringWidth($content);

            if (($str_width > $width) && ($scale)){
                // Escala a Fonte para Caber no campo.
                $this->CellFit($width, $heigh, $content, $border, 0, $alignH, false,false,true);
            }
            else {
                // String Normal
                $this->Cell( $width, $heigh, $content, $border, 0, $alignH, true);
            }
        }
        else {
            // Celula Normal
            $this->Cell( $width, $heigh, $content, $border, 0, $alignH, true);
        }

        $this->colcounter++;
    }

    public function addColumnsHeader(){
        $this->gridAddRow();
        foreach ($this->columns as $column)
        {
            // adiciona as colunas de cabeçalho
            $this->gridAddCell($column['label'], $column['align'], 'gridTitle');
        }
    }


    public function simpleGrid()
    {
        // verifica se foram definidas as colunas do relatório
        if (count($this->columns) == 0){
            throw new Exception('Colunas do relatório não definidas');
        }

        // adiciona uma linha para o cabeçalho (títulos das colunas)
        $this->addColumnsHeader();

        // Verificar se tem Dados para Grid
        if ($this->data_report){
            $rows = $this->data_report;
        }

        //var_dump($rows);
        $colore = FALSE;
        // percorre os resultados
        $ln = 1;
        foreach ($rows as $row){
            Log::Msg(3,"ROW $ln");
            // define o estilo a ser utilizado
            $style = $colore ? 'rowI' : 'rowP';

            // adiciona uma linha para os dados
            $this->gridAddRow();

            // adiciona as colunas com os dados
            foreach ($this->columns as $column)
            {
                $alias   = $column['alias'];

                // Se estiver flagado lineNumber
                if ($alias == 'LINENUNBER'){
                    $row->$alias = $ln;
                }

                //var_dump($alias);
                if ($column['format']){
                    $function = $column['format'];
                    $value   = $row->$alias;
                    $content = $this::$function($value);
                }
                else {
                    $content = $row->$alias;
                }
                Log::Msg(3,"    $content");
                $scale   = $column['scale'];
                $ralign  = strtoupper(substr($column['ralign'],0,1));

                
                $this->gridAddCell($content, $ralign, $style, 1, $border, $scale);

                $content = null;
            }

            // alterna variável de controle para cor de fundo
            $colore = !$colore;

            $ln++;
        }

        // Closing line
        //$this->Cell(array_sum($this->colWidths),0,'','B');

    }


    function FancyTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        $w = array(40, 35, 40, 45);
        for($i=0;$i<count($header);$i++)
                $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        //var_dump($data);
        foreach($data as $row)
        {
            $row = (array)$row;
                $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
                $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
                $this->Cell($w[2],6,$row[2],'LR',0,'R',$fill);
                $this->Cell($w[3],6,$row[3],'LR',0,'R',$fill);
               // $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
               // $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
                $this->Ln();
                $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }




    public function dateBr($data){
        if ($data){
            $aData = explode('-',$data);
            $yy = $aData[0];
            $mm = $aData[1];
            $dd = $aData[2];

            $y =  substr($yy, -2);
            $data_nova = "$dd/$mm/$y";
            return $data_nova;
        }
    }

    public function upper($string){

        return strtoupper($string);
    }
}