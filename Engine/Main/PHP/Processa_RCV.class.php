<?php
//Desliga o notice e warning do PHP.INI
ini_set('error_reporting','E_ALL & ~E_NOTICE');
session_start();

/**
 * função __autoload()
 *  Carrega uma classe quando ela é instânciada pela primeira vez.
 */
function __autoload($classe) {
    $pastas = array('Main/PHP');
    $modulos = scandir("Main/Modulos");
    foreach ($modulos as $pasta) {
        if ($pasta != "." && $pasta != ".."){
            array_push($pastas, "Main/Modulos/".$pasta);
        }
    }
    foreach ($pastas as $pasta) {
        if (file_exists("{$pasta}/{$classe}.class.php")){
            include_once "{$pasta}/{$classe}.class.php";
            Log::Msg(7, "AUTOLOAD Class[ $classe ] File [ {$pasta}/{$classe}.class.php ]");
        }
    }
}

/**
 * @package  :PHP
 * @name     :Processa_RCV
 * @class    :Processa_RCV.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :21/03/2010
 * @Diretorio:Main/PHP/
 * Classe Responsavel
 *
*/
class Processa_RCV {

    public $rcv_dir = "";
    public $log_file= "";
    public $arquivos= array();

    public $prm_copia_ftp = TRUE;

    public function __construct(){


    }

    public function SetRcvDir($rcv_dir){
        $this->rcv_dir = $rcv_dir;
    }

    public function SetLogFile($log_file){
        $this->log_file = $log_file;
    }

    public function Verifica_diretorio(){

        $dir = scandir($this->rcv_dir);

        foreach ($dir as $arquivo) {
            if ($arquivo != "." && $arquivo != ".." && $arquivo != ".svn"){

                $file_info = stat($arquivo);
                $file = new StdClass();
                $file->name  = $arquivo;
                $file->patch = $this->rcv_dir;
                $file->size  = $file_info['size'];  //tamanho
                $file->atime = $file_info['atime']; //acessado
                $file->mtime = $file_info['mtime']; //modificado //usar essa data pra comparar mais recente
                $file->ctime = $file_info['ctime']; //criado

                array_push($this->arquivos, $file);
            }
        }
        // var_dump($this->arquivos);

        // Ordenar por Chegada


        // Tratar os arquivos com suas respectivas classes
        foreach ($this->arquivos as $arquivo){

	    Log::Msg(3,"Arquivo [ {$arquivo->name} ] Extensao [ $extensao ] ");
	    $extensao = Common::getExtensaoArquivo($arquivo->name);

            $extensao = strtolower($extensao);

	    switch ($extensao) {

	      case 'jpg':
		  $this->Tratar_Arquivos_JPG($arquivo);
		  break;
	      case 'txt':
		  $this->Tratar_Arquivos_TXT($arquivo);
		  break;
              case 'cad':
                  $this->Tratar_Arquivos_CAD($arquivo);
                  break;

		default:
		    // Caso Nao Seja nenhum arquivo Conhecido
		    $this->tratar_arquivo_indefinido($arquivo);
		    break;

	    }

        }
    }


    public function Tratar_Arquivos_TXT($arquivo){

        // Aqui Separo pra onde cada arquivo vai
        switch ($arquivo->name) {

            case 'GRUPOS.TXT':
                // Integração com o sistema STOCK, cadastro de categorias / setores de loja
                new Importacao_Categorias_Stock($arquivo);
                break;

            case 'CADPROD.TXT':
                // Integração com o sistema STOCK, cadastro de produtos
                new Importacao_Produtos_Stock($arquivo);
                break;

            default:
                // Caso Nao Seja nenhum arquivo Conhecido
                $this->tratar_arquivo_indefinido($arquivo);
                break;
        }
    }

    public function Tratar_Arquivos_JPG($arquivo){
	Log::Msg(2,"Class[ Processa_RCV ] Method[ Tratar_Arquivos_JPG ]");
	// Imagems Produtos
        // Copiar a Imagem Para Imagem Destino
	$origem  = $arquivo->patch.$arquivo->name ;
        // Padronizando Saida Para Extensao em Maiusculo
        $arquivo_destino = strtoupper($arquivo->name);
	$destino = "Main/Data/Imagens_Produtos/" . $arquivo_destino;
	$comando = "mv $origem $destino";

	Log::Msg(3,"Movendo Imagem, Origem [ $origem ] Destino [ $destino ] Comando [ $comando ]");

	exec($comando, $verbose);

	if (!$verbose){
	    Log::Msg(3,"Imagem Copiada Com Sucesso");
	}
	else {
	    Log::Msg(3,"Falha ao Copiar Imagem");
	}

        // Mandar Copia de Imagem Para Pasta de FTP Para Atualiar os Clients
        // Se o Parametro prm_copia_ftp estiver true
        if ($this->prm_copia_ftp){

            $origem  = $destino;
            // Padronizando Saida Para Extensao em Maiusculo
            $destino_ftp = "/home/ftp_atacado/Imagens_Produtos/";
            $comando = "cp $origem $destino_ftp";

            Log::Msg(3,"Copiando Arquivo Para Diretorio FTP, Origem [ $origem ] Destino [ $destino_ftp ] Comando [ $comando ]");

            exec($comando, $verbose);

            if (!$verbose){
                Log::Msg(3,"Imagem Copiada Com Sucesso");
            }
            else {
                Log::Msg(3,"Falha ao Copiar Imagem");
            }

        }
    }


    public function Tratar_Arquivos_CAD($arquivo){

        // Arquivo CAD arquivo no formato emporium vindos do RMS

        // Integração com o sistema RMS, integracao de Produtos
        new Importacao_Produtos_Emporium($arquivo);

    }


    public function tratar_arquivo_indefinido($arquivo){
        Log::Msg(2,"Class[ Processa_RCV ] Method[ tratar_arquivo_indefinido ]");

        if(unlink($arquivo->patch)){
            $result = ' OK ';
        }
        else{
            $result = 'FALHA';
        }

        Log::Msg(3,"Excluir Arquivo Indefinido. File[ {$arquivo->name} ] Path[ {$arquivo->patch} ] Result[ $result ]");
    }
}



// Recuperando Parametros do Shell
if (!empty($argc)){
    $parametros = Common::parseArgs($argv);
}



$processa_rcv = new Processa_RCV();
$processa_rcv->SetRcvDir($parametros['rcv_dir']);
$processa_rcv->SetLogFile($parametros['log_file']);
$processa_rcv->Verifica_diretorio();

?>