<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Stock
 * @name     :Importacao_Categorias_Stock
 * @class    :Importacao_Categorias_Stock.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :22/03/2011
 * @Diretorio:Main/Modulos/Stock/
 * Classe Responsavel pela Importacao do Cadastro de Produtos e Estoque
 * do sistema de retaguarda STOCK.
 * @revision:
 * @Obs: Esta Classe Somente Faz a leitura das informacoes no arquivo texto
        , e insere na base de dados
 * @Nome do arquivo: GRUPOS.TXT
 * @Separador: |
 * @Formato do Arquivo Texto
 *     Cod |nome da categoria/setor |
 * @Exemplo:
 *     0001|cosmético           |
 *
 *  1º Passo - Mover o Arquivo Para Diretorio de Trabalho
 *  2º Passo - Abrir o Arquivo Para Leitura, Linha a Linha
 *  3º Passo - Para cada Linha, Instanciar um Objeto Categoria
 *  4º Passo - Setar os Atributos e executar METODO cria_atualiza
 *  5° Passo - Apagar o Arquivo ou mover para pasta backup
 */

class Importacao_Categorias_Stock {

    public $arquivo;

    private $wrk;
    private $separador  = "|";
    private $blank      = ' ';
    private $null       = 'NULL';


    private $total_importar = 0;
    private $total_importado= 0;
    private $total_erros    = 0;

    private $prm_backup = TRUE;
    private $bck_dir = '';


    /** __construct($arquivo)
     * @param $arquivo: é um obj gerado pela classe Processa RCV
     *        com as informaçoes do arquivo a ser processado
     * Atribui os caminhos, Inicia o tratamento do arquivo
     */
    public function __construct($arquivo){
        Log::Msg(2,"Class[ Importacao_Categorias_Stock ] Method[ __construct ]");

        $this->arquivo = $arquivo;
        $this->wrk = Common::Verifica_Diretorio_Work();
        $this->bck_dir = Common::Verifica_Diretorio_BackUp();


        $this->importar_categorias();
    }


    public function importar_categorias(){
        Log::Msg(2,"Class[ Importacao_Categorias_Stock ] Method[ importar_categorias ]");

        Log::Msg(3,"IMPORTAÇÃO DE CATEGORIAS [ INICIADO ]");


        // 1º Passo
        //$this->arquivo->patch = Common::Move_Arquivo($this->arquivo->patch.$this->arquivo->name, $this->wrk.$this->arquivo->name);
        Log::Msg(3,"Comando [ $comando ]");
        $origem = $this->arquivo->patch.$this->arquivo->name ;
        $destino = $this->wrk.$this->arquivo->name;
        $comando = "mv $origem  $destino";
        exec($comando);
        $this->arquivo->patch = $destino;


        // 2º Passo
        $file =  $this->wrk . $this->arquivo->name;

        // abrindo o arquivo como leitura
        $arq = fopen($file,"r");

        // 3º Passo
        // enquanto nao chegar ao final do arquivo
        while (!feof ($arq)) {
            // carregue os dados temporariamente neste vetor
            $linha = fgets($arq);
            if (!feof($arq)) {
                $aLinha = explode($this->separador, $linha);

                $this->total_importar++;

                $this->criar_categoria($aLinha);
            }
        }
        fclose($arq);

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
        }
        else {
            //Common::Apagar_Arquivo($file);
            $comando = "rm -rf $file";
            exec($comando);
        }

        Log::Msg(3,"IMPORTAÇÃO DE CATEGORIAS [ STATUS ]");
        Log::Msg(3,"Total a Importar  [ {$this->total_importar} ]");
        Log::Msg(3,"Total Importado   [ {$this->total_importado} ]");
        Log::Msg(3,"Total de Error    [ {$this->total_erros} ]");
        Log::Msg(3,"IMPORTAÇÃO DE CATEGORIAS [ FINALIZADO ]");
    }

    public function criar_categoria($aLinha){
        Log::Msg(2,"Class[ Importacao_Categorias_Stock ] Method[ criar_categoria ]");

        // Tratar os campos

        $aCategoria = array();
        foreach ($aLinha as $campo){
            // tirar os espaços
            $campo_limpo = trim($campo);
            // tirar os Quebra de Linha
            $campo_limpo = rtrim($campo_limpo, " ");
            //
            $campo_limpo = utf8_encode($campo_limpo);
            array_push($aCategoria, $campo_limpo);
        }


        // 4º Passo
        // Tratar se a linha não está vazia
        if ($aCategoria[0] != ""){
            // Atribuir os campos
            $obj_categoria = new Categoria();
            $obj_categoria->SetImportacao(true);

            // Codigo
            $obj_categoria->Set_id_categoria($aCategoria[0]);
            // Categoria
            $obj_categoria->Set_categoria($aCategoria[1]);


            ob_start();
            $obj_categoria->criaAtualiza();
            $result = ob_get_contents();
            ob_end_clean();

            if((substr_count($result, "success")) && (substr_count($result, "true"))){
                $this->total_importado++;
            }
            else{
                $this->total_erros++;
            }

        }

    }

}
