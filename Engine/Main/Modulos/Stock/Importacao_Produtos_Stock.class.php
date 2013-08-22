<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Stock
 * @name     :Importacao_Produtos_Stock
 * @class    :Importacao_Produtos_Stock.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :22/03/2011
 * @Diretorio:Main/Modulos/Stock/
 * Classe Responsavel pela Importacao do Cadastro de Produtos e Estoque
 * do sistema de retaguarda STOCK.
 * @revision:
 * Obs: Esta Classe Somente Faz a leitura das informacoes no arquivo texto
        , e insere na base de dados
 * Nome do arquivo:
 *   CADPROD.TXT
 *
 * Formato do Arquivo Texto:
 * cod | codbar | descRes | desc | grup | prv | pvAtac | qtd |
 *
 * Regras do arquivo de Origem:
 * existe produtos que nao tem codigo de barras
 * existe produto que tem mais de um codigo de barras para o mesmo codigo interno
 * Preco de Varejo quanto o de Atacado estao multiplicados por 100
 * e com zeros a esquerda, logo voce precisa dividir por 100.
 * A Quantidade disponivel em Estoque estao multiplicadas por 1000
 * e com zeros a esquerda, logo voce precisa dividir por 1000.
 * Pode haver item com Preco de Atacado Zero.
 * Pode haver item com Quantidade disponivel em Estoque Zero.
 */

class Importacao_Produtos_Stock {

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
        Log::Msg(2,"Class[ Importacao_Produtos_Stock ] Method[ __construct ]");

        $this->arquivo = $arquivo;
        $this->wrk = Common::Verifica_Diretorio_Work();
        $this->bck_dir = Common::Verifica_Diretorio_BackUp();

        $this->importar_produtos();
    }

/**
 *  1º Passo - Mover o Arquivo Para Diretorio de Trabalho
 *  2º Passo - Abrir o Arquivo Para Leitura, Linha a Linha
 *  3º Passo - Para cada Linha, Instanciar um Objeto Categoria
 *  4º Passo - Setar os Atributos e executar METODO cria_atualiza
 *  5° Passo - Apagar o Arquivo ou mover para pasta backup
*/
    public function importar_produtos(){
        Log::Msg(2,"Class[ Importacao_Produtos_Stock ] Method[ __construct ]");

        Log::Msg(3,"IMPORTAÇÃO DE PRODUTOS [ INICIADO ]");
        $tempo_inical = date('Y-m-d H:i:s');

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

                $this->criar_produto($aLinha);
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
        $tempo_final = date('Y-m-d H:i:s');

        Log::Msg(3,"IMPORTAÇÃO DE PRODUTOS [ STATUS ]");
        Log::Msg(3,"Total a Importar  [ {$this->total_importar} ]");
        Log::Msg(3,"Total Importado   [ {$this->total_importado} ]");
        Log::Msg(3,"Total de Error    [ {$this->total_erros} ]");
        Log::Msg(3,"Data Hora Inicio  [ {$tempo_inical} ]");
        Log::Msg(3,"Data Hora Termino [ {$tempo_final} ]");
        Log::Msg(3,"IMPORTAÇÃO DE PRODUTOS [ FINALIZADO ]");


    }

    public function criar_produto($aLinha){
        Log::Msg(2,"Class[ Importacao_Produtos_Stock ] Method[ criar_produto ]");

        // Tratar os campos
        $aProduto = array();
        foreach ($aLinha as $campo){
            // tirar os espaços
            $campo_limpo = trim($campo);
            // tirar os Quebra de Linha
            $campo_limpo = rtrim($campo_limpo, " ");
            // Adicionar remover caracteres especias
            //$campo_limpo = Common::trata_string($campo_limpo);
            // Codificar
            $campo_limpo = utf8_encode($campo_limpo);
            array_push($aProduto, $campo_limpo);
        }

        //$aProduto[0] = cod
        //$aProduto[1] = codbar
        //$aProduto[2] = descRes
        //$aProduto[3] = desc
        //$aProduto[4] = grup
        //$aProduto[5] = prv
        //$aProduto[6] = pvAtac
        //$aProduto[7] = qtd

        // Aplicar Regras do Arquivo de Origem
        //Codigo
        $aProduto[0] = (int)str_replace("-", '', $aProduto[0]);
        $aProduto[5] = (float)($aProduto[5] / 100);
        $aProduto[6] = (float)($aProduto[6] / 100);
        $aProduto[7] = (float)($aProduto[7] / 1000);

        // 4º Passo
        // Tratar se a linha não está vazia
        if ($aProduto[0] != ""){

            Log::Msg(3,"Importando Produto, Cod [ $aProduto[0] ] Descricao_Curta [ $aProduto[2] ]");

            $obj_Produto = new Produtos();
            // Avisando que e uma importacao
            $obj_Produto->setImportacao(true);

            $obj_Produto->setPkIdProduto($aProduto[0]);
            $obj_Produto->setDescricaoCurta($aProduto[2]);
            $obj_Produto->setDescricaoLonga($aProduto[3]);
            $obj_Produto->setFkIdCategoria($aProduto[4]);
            $obj_Produto->setPreco($aProduto[6]);
            $obj_Produto->setQuantidade($aProduto[7]);

//             // Colocar Nome de Imagem Padrao
//             $dir_images = $obj_Produto->getDirImages();
//             $name_image = $aProduto[0].'.jpg';
//             $obj_Produto->setUrlImage($dir_images.$name_image);
//             $obj_Produto->setNameImage($name_image);
            $obj_Produto->localizar_imagem();

            ob_start();
            $obj_Produto->CriaAtualiza();
            $result = ob_get_contents();
            ob_end_clean();

            if((substr_count($result, "failure")) && (substr_count($result, "true"))){
                $this->total_erros++;
            }
            else{
                $this->total_importado++;
            }
        }
    }
}


?>
