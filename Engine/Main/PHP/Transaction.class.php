<?php
/**
 * @Class Transaction
 * @author  :Glauber Costa Vila-Verde
 * @e-mail  :glauber.vila.verde@gmail.com
 * @date    :13/04/2010
 * @revision:13/04/2010
 * @description: Esta classe fornece os métodos
 * necessarios para manipular transações
 * classe baseada em exemplos do livro PhpOO de Pablo d'Oglio
 * @dependencias:
 *    Connection.class.php
 */

final class Transaction {

    private static $conn; // conexão ativa

    /**
     * Método __construct()
     * Abre uma transação como private
     * para impedir varias instâncias de Transaction
     */
    private function __construct(){}

    /**
     * Método open()
     * @param $database = nome do banco de dados
     * Abre uma transação e uma conexão ao BD
     */
    public static function open($database){
        // abre uma conexão e armazena
        // na propriedade estática $conn
        if (empty(self::$conn)){

            self::$conn = Connection::open($database);
            // inicia a transação
            self::$conn->beginTransaction();
            Log::Msg(6,"Transaction [ Begin: $database ]");

        }
    }

    /**
     * Método get()
     * Retorna a conexão ativa da transação
     */
    public static function get(){
        // retorna a conexão ativa
        return self::$conn;
    }

    /**
     * Método rollback()
     * Desfaz todas operações realizadas na transação
     */
    public static function rollback(){
        if (self::$conn)
        {
            // desfaz as operações realizadas
            // durante a transação
            self::$conn->rollback();
            self::$conn = NULL;
            Log::Msg(6,"Transaction [ RollBack ]");
        }
    }

   /**
    * Método close()
    * Aplica todas operações realizadas e fecha a transação
    */
    public static function close(){
        if (self::$conn)
        {
            // aplica as operações realizadas
            // durante a transações
            self::$conn->commit();
            self::$conn = NULL;
            Log::Msg(6,"Transaction [ Commit ]");
        }
    }
}
?>