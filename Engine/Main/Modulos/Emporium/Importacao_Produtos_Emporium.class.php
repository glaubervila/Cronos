<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Stock
 * @name     :Importacao_Produtos_Emporium
 * @class    :Importacao_Produtos_Emporium.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :19/01/2013
 * @Diretorio:Main/Modulos/Emporium/
 * Classe Responsavel pela Importacao do Cadastro de Produtos e Estoque
 * do sistema de retaguarda Emporium.
 * Essa Classe le um arquivo no formato que o sistema emporium aceita,
 * a integracao vai ser feita da seguinte maneira.
 * exemplo retaguarda RMS gera o arquivo, cronos recebe esse arquivo processa, e manda uma copia para emporium, resumindo o sistema cronos vai entrar no meio da operacao normal.
 * @revision:
 * Obs: Esta Classe Somente Faz a leitura das informacoes no arquivo texto
        , e insere na base de dados
 * Nome do arquivo:
 *   llllddmm.CAD - loja com 4 digitos, dia com 2 digito e mes com 2 digitos
 *   ex: 00011601.CAD - loja 1 dia 16 de janeiro
 * Formato do Arquivo Texto:
 *      campos separados por | seguinto o manual de integração do emporim 
 *
 * Regras do arquivo de Origem:
 * Para cada produto existe um tipo de registro, que e identificado pelo começo de linha
 * 0|10| -  Cadastro de Produto
 * 0|11| - PLU/FILIAL
 * 0|12| - Preco/Filial
 * 0|01| - SKU
 */

class Importacao_Produtos_Emporium {

    public $arquivo;

    private $wrk;
    private $separador  = "|";
    private $blank      = ' ';
    private $null       = 'NULL';

    private $limpar_base = TRUE;


    private $total_importar = 0;
    private $total_importado= 0;
    private $total_erros    = 0;

    private $prm_backup = TRUE;
    private $bck_dir = '';

    // Parametro para enviar copia do arquivo para o emporium
    private $envia_emporium = FALSE;


    /** __construct($arquivo)
     * @param $arquivo: é um obj gerado pela classe Processa RCV
     *        com as informaçoes do arquivo a ser processado
     * Atribui os caminhos, Inicia o tratamento do arquivo
     */
    public function __construct($arquivo){
        Log::Msg(2,"Class[ Importacao_Produtos_Emporium ] Method[ __construct ]");


        // Carregar Arquivo de Configuracao do Emporium
        //$aConf = Common::PaseIniFile("/Conf/emporium_db.ini");

        //var_dump($aConf);
        $this->emporium_rcv = "/tmp/";

        //Log::Msg(2,"Configuracao Emporium. RCV [ {$this->emporium_rcv} ]");

        $this->arquivo = $arquivo;
        $this->wrk = Common::Verifica_Diretorio_Work();
        $this->bck_dir = Common::Verifica_Diretorio_BackUp();

        // Criando um Registro de Integracao do tipo Importacao
        $this->obj_integracao = new Integracao_Emporium();
        $this->id_integracao = $this->obj_integracao->criar_registro(1, 'Produtos');

        $this->lerArquivo();

    }

/**
 *  1º Passo - Mover o Arquivo Para Diretorio de Trabalho
 *  2º Passo - Abrir o Arquivo Para Leitura, Linha a Linha
 *  3º Passo - Para cada Linha, Instanciar um Objeto 
 *  4º Passo - Setar os Atributos e executar METODO cria_atualiza
 *  5° Passo - Apagar o Arquivo ou mover para pasta backup
*/
    public function lerArquivo(){
        Log::Msg(2,"Class[ Importacao_Produtos_Emporium ] Method[ __construct ]");


        Log::Msg(3,"Limpar a Base Antes da Importacao [ {$this->limpar_base} ]");
        if ($this->limpar_base){
            // Limpar a Base de Produtos, deleto somente os precos, estoque, eans;
            $limpar = Produtos::LimparBaseProdutos(false,true,true,true,false,false);
        }


        Log::Msg(3,"IMPORTAÇÃO DE PRODUTOS [ INICIADO ]");
        $this->obj_integracao->atualiza_status(1); // Iniciado

        $tempo_inical = date('Y-m-d H:i:s');

        // 1º Passo
        $origem = $this->arquivo->patch.$this->arquivo->name ;

        // Se tiver A Flag enviar para Emporium
        // Copiar o Arquivo Original para Pasta Compartilhada
        if ($this->envia_emporium){
            $dest_emporium  = $this->emporium_rcv.$this->arquivo->name;
            $comando = "cp $origem  $dest_emporium";
            Log::Msg(3,"Copiando Arquivo Original para Emporium");
            Log::Msg(3,"Comando [ $comando ]");
            exec($comando);
        }


        $destino = $this->wrk.$this->arquivo->name;
        $comando = "mv $origem  $destino";
        Log::Msg(3,"Comando [ $comando ]");
        exec($comando);
        $this->arquivo->patch = $destino;

        // 2º Passo
        $file =  $this->wrk . $this->arquivo->name;

        // abrindo o arquivo como leitura
        $arq = fopen($file,"r");

        // 3º Passo
        $this->obj_integracao->atualiza_status(2); // Em Andamento
        // enquanto nao chegar ao final do arquivo
        while (!feof ($arq)) {
            // carregue os dados temporariamente neste vetor
            $linha = fgets($arq);
            if (!feof($arq)) {
                $aLinha = explode($this->separador, $linha);

                $this->separar_registros($aLinha);
            }
        }
        fclose($arq);

        $this->obj_integracao->SetTotal($this->total_importar);
        $this->obj_integracao->SetTotalExportados($this->total_importado);

        // 5° Passo
        if ($this->prm_backup){
            Log::Msg(3,"Fazendo BackUP Arquivo [ $file ]");
            $date = date('Y-m-d_H-i');
            $bck_file = $this->bck_dir . $date . "_" .$this->arquivo->name;

            $comando = "zip -rj9 $bck_file.zip $file";
            Log::Msg(3,"Executando Comando [ $comando ]");
            exec($comando);

            $comando = "rm -rf $file";
            Log::Msg(3,"Executando Comando [ $comando ]");
            exec($comando);

            $this->obj_integracao->SetArquivo($bck_file);
        }
        else {
            //Common::Apagar_Arquivo($file);
            $comando = "rm -rf $file";
            exec($comando);
        }
        $tempo_final = date('Y-m-d H:i:s');

        Log::Msg(3,"IMPORTAÇÃO DE PRODUTOS [ STATUS ]");
        Log::Msg(3,"Total a Importar  [ {$this->total_importar} ]");
        Log::Msg(3,"Total Importado   [ {$this->total_importado} ]");
        Log::Msg(3,"Total de Error    [ {$this->total_erros} ]");
        Log::Msg(3,"Data Hora Inicio  [ {$tempo_inical} ]");
        Log::Msg(3,"Data Hora Termino [ {$tempo_final} ]");
        Log::Msg(3,"IMPORTAÇÃO DE PRODUTOS [ FINALIZADO ]");
        $this->obj_integracao->finaliza_integracao(); // Concluido

    }


    public function separar_registros($aLinha){
        Log::Msg(2,"Class[ Importacao_Produtos_Emporium ] Method[ separar_registros ]");

        $tipo_registro = $aLinha[1];
        switch ($tipo_registro) {

            case 10 :
                Log::Msg(3,"Registro Tipo 10 - PLU");
                $this->importar_produto($aLinha);

                $this->total_importar++;
            break;
            case 11 :
                Log::Msg(3,"Registro Tipo 11 - PLU/Filial");
                $this->importar_estoque($aLinha);
            break;
            case 12 :
                Log::Msg(3,"Registro Tipo 12 - Precos/Filial");
                $this->importar_preco($aLinha);
            break;
            case 1 :
                Log::Msg(3,"Registro Tipo 1 - SKU");
                $this->importar_ean($aLinha);
            break;
        }

    }


    public function importar_produto($aLinha){
        Log::Msg(2,"Class[ Importacao_Produtos_Emporium ] Method[ importar_produto ]");

        // Tratar os campos
        $aProduto = $this->tratar_campos($aLinha);

        if ($aProduto[0] != ""){
            Log::Msg(3,"Importando Produto, Cod [ $aProduto[2] ] Descricao_Curta [ $aProduto[7] ]");

            $obj_Produto = new Produtos();
            // Avisando que e uma importacao
            $obj_Produto->setImportacao(true);

            $obj_Produto->setPkIdProduto($aProduto[2]);
            $obj_Produto->setDescricaoCurta($aProduto[7]);
            $obj_Produto->setDescricaoLonga($aProduto[8]);
            $obj_Produto->setUnidade($aProduto[13]);
            $obj_Produto->setTributacao($aProduto[11]);
            $obj_Produto->setFkIdCategoria($aProduto[16]);

            ob_start();
            $obj_Produto->CriaAtualiza();
            $result = ob_get_contents();
            ob_end_clean();

            $obj_Produto = null;
            if((substr_count($result, "failure")) && (substr_count($result, "true"))){
                $this->total_erros++;
            }
            else{
                $this->total_importado++;
            }
        }
    }

    public function importar_estoque($aLinha){
        Log::Msg(2,"Class[ Importacao_Produtos_Emporium ] Method[ importar_estoque ]");

        // Tratar os campos
        $aProduto = $this->tratar_campos($aLinha);

        if ($aProduto[0] != ""){
            Log::Msg(3,"Importando Estoque, Cod [ $aProduto[3] ] Quantidade [ $aProduto[5] ]");

            $quantidade = number_format($aProduto[5], 3,'.','.');


            $obj_Produto = new Produtos();
            // Avisando que e uma importacao
            $obj_Produto->setImportacao(true);

            $obj_Produto->setPkIdProduto($aProduto[3]);
            $obj_Produto->setQuantidade($quantidade);

            ob_start();
            $obj_Produto->alterar_produto_estoque();
            $result = ob_get_contents();
            ob_end_clean();

            $obj_Produto = null;
            if((substr_count($result, "failure")) && (substr_count($result, "true"))){
                $this->total_erros++;
            }
        }
    }

    public function importar_preco($aLinha){
        Log::Msg(2,"Class[ Importacao_Produtos_Emporium ] Method[ importar_preco ]");

        // Tratar os campos
        $aProduto = $this->tratar_campos($aLinha);

        if ($aProduto[0] != ""){
            Log::Msg(3,"Importando Preco, Cod [ $aProduto[3] ] Preco [ $aProduto[4] ]");

            //$preco = number_format(, 3,'.','.');

            $decimal = substr($aProduto[4], -3);
            $inteiro =  substr($aProduto[4], 0, -3);

            $preco = (float)($inteiro.'.'.$decimal);

            Log::Msg(3,"Preco [ $preco ] Inteiro [ $inteiro ] Decimal [ $decimal ]");

            $obj_Produto = new Produtos();
            // Avisando que e uma importacao
            $obj_Produto->setImportacao(true);

            $obj_Produto->setPkIdProduto($aProduto[3]);
            $obj_Produto->setPreco($preco);

            ob_start();
            $obj_Produto->alterar_produto_preco();
            $result = ob_get_contents();
            ob_end_clean();

            $obj_Produto = null;
            if((substr_count($result, "failure")) && (substr_count($result, "true"))){
                $this->total_erros++;
            }
        }
    }

    public function importar_ean($aLinha){
        Log::Msg(2,"Class[ Importacao_Produtos_Emporium ] Method[ importar_ean ]");

        // Tratar os campos
        $aProduto = $this->tratar_campos($aLinha);

        if ($aProduto[0] != ""){
            Log::Msg(3,"Importando EAN, Cod [ $aProduto[3] ] EAN [ $aProduto[2] ]");

            $ean = $aProduto[2];



            $obj_Produto = new Produtos();
            // Avisando que e uma importacao
            $obj_Produto->setImportacao(true);

            $obj_Produto->setPkIdProduto($aProduto[3]);
            $obj_Produto->setEAN($ean);

            ob_start();
            $obj_Produto->alterar_produto_ean();
            $result = ob_get_contents();
            ob_end_clean();

            $obj_Produto = null;
            if((substr_count($result, "failure")) && (substr_count($result, "true"))){
                $this->total_erros++;
            }
        }
    }



    public function tratar_campos($a){
        $aCampos = array();
        foreach ($a as $campo){

            // tirar os espaços
            $campo_limpo = trim($campo);
            // tirar os Quebra de Linha
            $campo_limpo = rtrim($campo_limpo, " ");

            if (is_numeric($campo_limpo)){
                $campo_limpo = $campo_limpo;
            }
            else {
                // Codificar
                $campo_limpo = utf8_encode($campo_limpo);
                // Adicionar remover caracteres especias
                //$campo_limpo = Common::trata_string($campo_limpo);
            }

            array_push($aCampos, $campo_limpo);
        }

        return $aCampos;
    }
}


?>