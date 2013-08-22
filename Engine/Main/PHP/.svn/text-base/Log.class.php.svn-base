<?php
/**
 * @Class Log
 * @author  :Glauber Costa Vila-Verde
 * @e-mail  :glauber.vila.verde@gmail.com
 * @date    :16/04/2010
 * @revision:26/04/2010
 * @description: Disponibiliza métodos para logar
 * mensagens, array e objetos
 */

class Log {

    public $_logFile;

    public function __construct(){}

    public function getLogFile(){
        if(PHP_OS == 'WINNT') {
            $path = "Log";
        }
        else {
            $path = "Log";
        }
        $fileName = "/cronos_".date("ymd").".log";

        return $path.$fileName;
    }

    public function setLogFile($logfile){

        $this->_logFile = $logfile;

    }

    /**
     * Método Msg()
     *  @param $nivel   = valor inteiro que distingue o tipo de mensagem
     *  @param $message = string a ser escrita no log
     *  escreve em um arquivo retonado pelo método getLogFile
     *  usa um array para identificar quais os tipos de mensagens serão escritas
     *  Niveis
     *  0 : Erros/Exceptions
     *  1 : Querys
     *  2 : Headers
     *  3 : Comentarios
     *  4 : Requests - array retornado pelo HTTP Requests logado em cada construct
     *  5 : Objetos - mostra o conteudo de um objeto em forma de array
     *  6 : Transaction Log ( Abertura e Fechamente de Transacoes)
     *  7 : AUTOLOAD - Loga cada Classe que e instanciada
     *  8 : Usuário - escreve a identificação única do usuário (opcional não aparece mesmo que nivel seja 9, para aparecer o valor 8 deve estar no array)

     *  9 : Loga todas as mensagens ('Debug Level')
     */
    public function Msg($nivel, $message) {
        $logFile = Log::getLogFile();

        // Parametros
        $aNivelLog = array(9);
        //$aNivelLog = array(0);
        // Tratamento de identificação de usuário
        $id_user = in_array(8, $aNivelLog) ? "[".$_SESSION["id_Usuario"]."]" : "";


        if ((in_array($nivel, $aNivelLog)) or (in_array(9, $aNivelLog))) {

            // Teste se a mensagem é um array
            if (is_array($message)){
                if ($nivel = 4) {
                    $text = "\nArray __REQUEST\n";
                }
                else {
                    $text = "\nLogArray\n";
                }
                $text .= Log::LogArray($message);
            }
            elseif (is_object($message)){
/*                $text  = "\nLogObject\n";
                $text .= Log::LogObject($message);*/
            }
            else {
                $time = Log::getTime();
                // monta a string
                $text = "$time :: $id_user $message\n";
            }

            $handler = fopen($logFile, a);
            fwrite($handler, $text);
        }

        fclose($handler);
    }


    public function LogArray($array) {
        reset($array);
        while (list($chave,$valor) = each($array)){
            //echo "Chave: $chave. Valor: $valor";
            $time = Log::getTime();
            if (is_array($valor)) {
                $valor = Log::LogArray($valor);
            }
            //$text .= "$time ::  [$chave] = \"$valor\"\n";
            $text .= "[$chave] = \"$valor\"\n";

        }
        return $text;
    }

    public function LogObject($object) {
/*        $array = get_object_vars($object);
        //var_dump($array);
        $text .= Log::LogArray($array);
        var_dump($text);*/
        //return $text;
    }

    public function getTime(){
        // Tempo
        $time = date("Y-m-d H:i:s");
        $mt = microtime();
        $mt = substr ($mt, 0, 5);
        $time .= " ".$mt;
        return $time;
    }
}

?>
