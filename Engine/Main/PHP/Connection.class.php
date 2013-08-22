<?php
/**
 * @Class Connection
 * @author  :Glauber Costa Vila-Verde
 * @e-mail  :glauber.vila.verde@gmail.com
 * @date    :13/04/2010
 * @revision:13/04/2010
 * @description: gerencia conexões com bancos de dados,
 * através de arquivos de configuração.
 * classe baseada em exemplos do livro PhpOO de Pablo d'Oglio
 */

final class Connection {

    /**
     * Método __construct()
     */
    private function __construct(){}

    /**
     * Método open()
     * @param $name = Nome do Banco (igual ao nome de configuração do .ini)
     * Verifica se existe o arquivo (banco_db.ini) e instância um objeto PDO correspondente
     */

    public static function open($name) {

        $file = "Conf/{$name}_db.ini";

        // Verifica se Existe arquivo de configuração
        if (file_exists($file)) {
            // Lé o INI e retorna um array
            $db = parse_ini_file($file);
        }
        else {
            // Se não existir lança Excessão
            throw new Exception("Arquivo de Configuração ({$file})");
        }

        $host  = $db['host'];
        $name  = $db['name'];
        $user  = $db['user'];
        $pass  = $db['pass'];
        $port  = $db['port'];
        $type  = $db['type'];

        // descobre qual o tipo (driver) de banco a ser utilizado
        switch ($type){
            case 'pgsql':
                $conn = new PDO("pgsql:dbname={$name};user={$user}; password={$pass};host=$host");
                break;
            case 'mysql':
                $conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass);
                break;
            case 'sqlite':
                $conn = new PDO("sqlite:{$name}");
                break;
            case 'ibase':
                $conn = new PDO("firebird:dbname={$name}", $user, $pass);
                break;
            case 'oci8':
                $conn = new PDO("oci:dbname={$name}", $user, $pass);
                break;
            case 'mssql':
                $conn = new PDO("mssql:host={$host},1433;dbname={$name}", $user, $pass);
                break;
        }

        // define para que o PDO lance exceções na ocorrência de erros
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
//         $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET character_set_connection=utf8");
//         $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET character_set_client=utf8");
//         $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET character_set_results=utf8");

        // retorna o objeto instanciado.
        return $conn;
    }

}
?>