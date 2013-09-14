<?php
//Desliga o notice e warning do PHP.INI
ini_set('error_reporting','E_ALL & ~E_NOTICE');
ini_set('memory_limit', '32M');
session_start();

header('Content-Type: text/javascript; charset=UTF-8');
//header('Content-Type: text/html; charset=UTF-8');
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


class Application {

    static public function run(){

        if ($_REQUEST) {

             $class  = $_REQUEST['classe'];
             $action = $_REQUEST['action'];

            if (class_exists($class)){
                $pagina = new $class;
                ob_start();
                $pagina-> $action();
                $content = ob_get_contents();
                ob_end_clean();
            }
            echo $content;
        }

    }
}

Application::run();

?>