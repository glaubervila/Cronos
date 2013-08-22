<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Clientes
 * @name     :Clientes_Etiquetas
 * @class    :Clientes_Etiquetas.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :31/01/2011
 * @Diretorio:Main/Modulos/Clientes/
 * Classe Responsavel pela Geracao de Etiquetas de Maladireta de Clientes
 * @revision:
 * @Obs:
 *   Se a Loja for igual a 0 nao usar o campo loja como filtro.
 */

class Clientes_Etiquetas {

    private $file_name      = "Etiquetas_Clientes";
    private $file_path      = "";
    private $arquivo_result = "";

    private $arquivo_backup = "";
    private $backup_path    = "";


    private $loja_origem  = "";
    private $tipo_cliente = "";
    private $campo        = "";
    private $valor_inicial= "";
    private $valor_final  = "";
    private $tipo_etiqueta= "";

    private $prm_paginas_por_lote = 10;
    private $prm_backup     = "on";


    function __construct(){
        Log::Msg(2,"Class[ Clientes_Etiquetas ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->loja_origem   = $_REQUEST['fk_id_loja'];
        $this->tipo_cliente  = $_REQUEST['tipo_cliente'];
        $this->campo         = $_REQUEST['campo'];
        $this->valor_inicial = $_REQUEST['valor_inicial'];
        $this->valor_final   = $_REQUEST['valor_final'];
        $this->tipo_etiqueta = $_REQUEST['tipo_etiqueta'];

        // Diretorios e arquivos de trabalho
        $this->file_path =  Common::Verifica_Diretorio_Work();

        $this->backup_path = Common::Verifica_Diretorio_BackUp();
    }



    public function load_clientes(){
        Log::Msg(2,"Class[ Clientes_Etiquetas ] Method[ load_clientes ]");

        $record = new Repository();

        $sql = "SELECT a.pk_id_cliente, a.fk_id_loja, a.fk_id_endereco, a.nome, b.rua, b.numero, b.bairro, b.cidade, b.uf, b.cep, b.complemento FROM tb_clientes a INNER JOIN tb_endereco b on a.fk_id_endereco = b.id_endereco";

        $sql .= $this->Monta_Where();

        $results = $record->load($sql);
        Log::Msg(5,$results);

        $total_count = $this->Total_Clientes(false);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$total_count}}";
            echo $result;
        }
        else{

        }
    }

    public function Total_Clientes($prm_json = true) {
        Log::Msg(2,"Class[ Clientes_Etiquetas ] Method[ Total_Clientes ]");

        $sql = "SELECT count(a.pk_id_cliente) as total_count FROM tb_clientes a";
        $sql .= $this->Monta_Where();

        $record = new Repository();
        $results = $record->total_count($sql);
        Log::Msg(5,$results);

        if ($results->total_count != 0) {
            if ($prm_json == true) {
                echo "{success: true,data:";
                echo json_encode($results->total_count);
                echo "}";
            }
            else {
                return $results->total_count;
            }
        }
        else {
            if ($prm_json == true) {
                Log::Msg(3,"Total_Clientes [ 0 ]");
                $aResult['succes'] = "false";
                $aResult['msg']  = "Desculpe mas Não há Registros a que satisfação os criterios";
                die(json_encode($aResult));
            }
            else {
                return False;
            }
        }
    }

    public function Monta_Where() {
        Log::Msg(2,"Class[ Clientes_Etiquetas ] Method[ Monta_Where ]");

        $where = " WHERE";

        // Tratamento Por Loja
        if ($this->loja_origem == 0){
            // Todas as Lojas
        }
        else {
            $where .= " a.fk_id_loja = {$this->loja_origem} AND";
        }

        // Tratamento Por Tipo Cliente
        if ($this->tipo_cliente){
            $where .= " a.tipo_cliente = {$this->tipo_cliente} AND";
        }


        // Tratamento Por Campo
        $where .= " a.{$this->campo}";

        if (($this->campo == 'dt_inclusao') OR ($this->campo == 'dt_alteracao')) {
            // Campos data - tratar para formato mysql
            $dt_inicial = Common::converte_data($this->valor_inicial);
            $dt_final   = Common::converte_data($this->valor_final);

            if ($dt_inicial and $dt_final){
                $where .= " BETWEEN '{$dt_inicial}' and '{$dt_final}'";
            }
            else {
                $where .= " >= '{$dt_inicial}'";
            }
        }
        else {
           if ($this->valor_inicial and $this->valor_final){
                $where .= " BETWEEN '{$this->valor_inicial}' and '{$this->valor_final}'";
            }
            else {
                $where .= " >= '{$this->valor_inicial}'";
            }
        }

        return $where;
    }


    public function Gerar_Etiquetas() {
        Log::Msg(2,"Class[ Clientes_Etiquetas ] Method[ Gerar_Etiquetas ]");

        // 1º Passo - Recuperar as informacoes da Etiqueta
        $etq = Etiquetas::getEtiquetaById($this->tipo_etiqueta);

        // 2º Passo - renomear as propriedades da etiqueta, so pra facilitar o trabalho
        $modelo = $etq->cod_modelo;               // Modelo Etiqueta
        $papel  = $etq->papel;                    // Tipo de Papel
        $pglar  = $etq->pag_largura;              // Largura da Pagina
        $pgalt  = $etq->pag_altura;               // Altura da Pagina
        $mesq   = $etq->pag_margem_esquerda;      // Margem Esquerda (mm)
        $msup   = $etq->pag_margem_superior;      // Margem Superior (mm)
        $leti   = $etq->etiqueta_largura;         // Largura da Etiqueta (mm)
        $aeti   = $etq->etiqueta_altura;          // Altura da Etiqueta (mm)
        $mHori  = $etq->margem_esquerda;          // Margem Horizontal dentro da etiqueta (esquerda)
        $mVert  = $etq->margem_superior;          // Margem Vertical dentro da etiqueta (superior)
        $cols   = $etq->colunas;                  // Numero maximo de Colunas
        $rows   = $etq->linhas ;                  // Numero maximo de Linhas
        $colVer = $etq->largura_coluna_vertical;  // Largura da coluna Vertical entre as etiquetas (mm)
        $colHor = $etq->largura_coluna_horizontal;// Largura da coluna Horizontal entre as etiquetas (mm)
        $maxchar= $etq->maximo_characteres;       // Maximo de caracteres por linha

        // 3º Passo - Saber o Total de Etiquetas a Ser Gerado
        $total_etiquetas = $this->Total_Clientes(false);
        Log::Msg(3, "Total Etiquetas a Serem Geradas [ $total_etiquetas ]");

        // 4º Passo - Dividir em Lotes de Etiqueta
        // o parametro "prm_paginas_por_lote" refere-se a quantidade de paginas de cada arquivo
        $qtd_etq_lote = (($cols * $rows) * $this->prm_paginas_por_lote);
        Log::Msg(3, "Etiquetas por Lote [ $qtd_etq_lote ]");

        $qtd_lotes = ceil($total_etiquetas / $qtd_etq_lote);
        Log::Msg(3, "Total Lotes a Serem Gerados [ {$qtd_lotes} ]");


        // 5º Passo - Verificar o Diretorio Destino, Criar um Diretorio
        $diretorio_destino = $this->verifica_diretorio_destino();

        // 6º Passo - Iniciar os Contadores de Start, Limit, e Total_Lotes
        $start = 0;
        $limit = $qtd_etq_lote;
        $total_lotes = 0;

        // 7º Passo - Loop para cada Lote criar um arquivo pdf
        while ($total_lotes < $qtd_lotes){
            Log::Msg(3, "Lote [ $total_lotes ] Start [ $start ] Limit [ $limit ]");

            $pdf=new FPDF('P','mm',$papel); //papel personalizado

            $pdf->Open();
            $pdf->AddPage();
            $pdf->SetMargins($mesq,$msup);   //seta as margens do documento

            $pdf->SetAuthor("");
            $pdf->SetFont('Arial',"", 7);
            $pdf->SetDisplayMode(100, 'continuous'); //define o nivel de zoom do documento PDF

            // Inicializando os contadores
            $coluna   = 0;
            $linha    = 0;
            $posicaoH = 0;
            $posicaoV = 0;


            // 8º Passo - Executar a Query e para cada resultado criar uma etiqueta
            $sql = "SELECT a.pk_id_cliente, a.nome, b.rua, b.numero, b.bairro, b.cidade, b.uf, b.cep, b.complemento FROM tb_clientes a INNER JOIN tb_endereco b on a.fk_id_endereco = b.id_endereco";
            $sql .= $this->Monta_Where();
            $sql .= " LIMIT $start, $limit";

            $record = new Repository();
            $record->setLog(0);
            $record->setCharset('latin1');

            $results = $record->load($sql);
            Log::Msg(5,$results);
            if ($results->count !=0) {

                //Percorrendo Cada Registro
                foreach ($results->rows as $result) {
                    $cod    = $result->pk_id_cliente;
                    $nome   = strtoupper(substr($result->nome, 0, $maxchar));
                    $ende   = strtoupper(substr($result->rua, 0, $maxchar-8));
                    $ende   = $ende . ", " . $result->numero . ", " . $result->complemento;
                    $ende   = strtoupper(substr($ende, 0, $maxchar));
                    $bairro = $result->bairro;
                    $cida   = $result->cidade;
                    $local  = $bairro . " - " . $cida . ", " . $result->uf;
                    $local  = strtoupper(substr($local, 0, $maxchar));
                    $cep    = "CEP: " . $result->cep;

                    //Para etiqueta com 12 por pagina
                    if($linha == "30") {
                        $pdf->AddPage();
                        $linha = 0;
                    }

                    if($coluna == $cols) { // Se for a segunda coluna
                        $coluna = 0; // $coluna volta para o valor inicial
                        $linha++;
                    }

                    if($linha == $rows) { // Se for a ultima linha da pagina
                        $pdf->AddPage(); // Adiciona uma nova pagina
                        $linha = 0; // $linha volta ao seu valor inicial
                    }

                    if ($coluna == 0) {
                        $posicaoX = ($coluna * $leti);
                        $somaH    = ($mesq + $posicaoX);
                    }
                    else {
                        $posicaoX = ($coluna * ($leti + $colVer));
                        $somaH    = $posicaoX + $colVer;
                    }

                    if ($linha == 0) {
                        $posicaoY = ($linha * $aeti);
                        $somaV    = ($msup + $posicaoY);
                    }
                    else {
                        $posicaoY = $msup + ($linha * $aeti);
                        $somaV    = $posicaoY + $colHor;
                    }

                    // Tratamento especifico para o GUIMA
                    // Etiquetas com informacoes diferentes pelo modelo
                    if ($modelo == 6181) {
                        $pdf->Rect($somaH, $somaV, $leti, $aeti);

                        $somaH = $somaH + $mHori; // Adiciono a margem Horizontal interna etiqueta
                        $somaV = $somaV + $mVert; // Adiciono a margem Vertical interna etiqueta

                        $pdf->Text($somaH,$somaV   ,$nome);
                        $pdf->Text($somaH,$somaV+4 ,$ende);
                        $pdf->Text($somaH,$somaV+8 ,$local);
                        $pdf->Text($somaH,$somaV+12,$cep);
                    }
                    elseif($modelo == 6280) {
                        $pdf->Rect($somaH, $somaV, $leti, $aeti);

                        $somaH = $somaH + $mHori; // Adiciono a margem Horizontal interna   etiqueta
                        $somaV = $somaV + $mVert; // Adiciono a margem Vertical interna etiqueta

                        $pdf->SetFont('Arial',"", 11);
                        $pdf->Text($somaH,$somaV   ,$cod );
                        $pdf->SetFont('Arial',"", 7);
                        $pdf->Text($somaH,$somaV+4 ,$nome);
                    }

                    $coluna++;

                    unset($somaH);
                    unset($somaV);
                    unset($posicaoX);
                    unset($posicaoY);

                }

            }


            $arquivo = "$diretorio_destino"."Etiquetas_"."{$total_lotes}".".pdf";
            //imprime a saida
            $pdf->Output($arquivo, F);

            // Contadores
            $start = ($start + $qtd_etq_lote);
            $total_lotes++;
        }

        // 9º Passo Verificar se o Arquivo foi criado e compactar / fazer backup
        $zip_file = "{$this->file_path}{$this->file_name}.zip";

        $comando = "zip -rj9 $zip_file $diretorio_destino";
        exec($comando, $verbose);
        Log::Msg(3,"Compactando Arquivos. Comando[ $comando ].");

        if ($this->prm_backup == "on") {
            $data = date("d-m-Y_H-i-s");
            $backup_file = "{$this->backup_path}{$this->file_name}_$data.zip";
            exec("cp -r $zip_file $backup_file");
        }

        if (file_exists($zip_file)){
            $aResult['success'] = "true";
            $aResult['file'] = "{$this->file_name}.zip";
            $aResult['path'] = "{$this->file_path}";
            echo json_encode($aResult);
        }

    }




    public function verifica_diretorio_destino(){
        Log::Msg(2,"Class[ Clientes_Etiquetas ] Method[ verifica_diretorio_destino ]");

        // <Diretorio_Work>/<nome_arquivo>
        $diretorio_destino = $this->file_path . $this->file_name ."/";
        Log::Msg(3, "Diretorio Destino [ $diretorio_destino ]");

        if (is_dir($diretorio_destino)){
            Log::Msg(3,"O diretório já existe.");
            $deletados = 0;
            foreach($arqs = scandir($diretorio_destino) as $deletar) {
                $comando = "rm -rf $diretorio_destino/$deletar";
                exec ($comando, $verbose);
                $deletados++;
                Log::Msg(3,"Apagando Arquivos. Comando[ $comando ].");
            }
            Log::Msg(3,"Arquivos Apagados [ $deletados ].");

            $comando = "rm -rf {$this->file_path}{$this->file_name}.zip";
            exec($comando,$verbose);
            Log::Msg(3,"Apagando Arquivo zip. Comando[ $comando ].");
        }
        else {
            Log::Msg(3,"O diretório não existe ainda.");
            mkdir($diretorio_destino, 0777);
            Log::Msg(3,"Criado diretório [ {$diretorio_destino} ].");
        }

        return $diretorio_destino;
    }

}
?>
