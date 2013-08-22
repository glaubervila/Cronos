<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Clientes
 * @name     :Etiquetas
 * @class    :Etiquetas.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :31/01/2011
 * @Diretorio:Main/Modulos/Clientes/
 * Classe Responsavel pela Manutencao das Etiquetas
 * @revision:
 * @Obs:
 *
 */

class Etiquetas {



    function __construct(){
        Log::Msg(2,"Class[ Etiquetas ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

    }



    public function getEtiquetaById($id) {
        Log::Msg(2,"Class[ Etiquetas ] Method[ get_etiquetas_cmb ] Param[ $id ]");

        $sql = "SELECT * FROM tb_modelos_etiquetas WHERE pk_id_etiqueta = $id";

        $record = new Repository();
        $results = $record->load($sql);
        Log::Msg(5,$results);

        if ($results->count != 0) {
            return $results->rows[0];
        }
    }

    public function get_etiquetas_cmb(){
        Log::Msg(2,"Class[ Etiquetas ] Method[ get_etiquetas_cmb ]");

        $sql = "SELECT pk_id_etiqueta, modelo FROM tb_modelos_etiquetas";

        $record = new Repository();
        $results = $record->load($sql);
        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }

    }


}
?>