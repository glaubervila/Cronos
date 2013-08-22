<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :PHP
 * @name     :Configuracoes
 * @class    :Configuracoes.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :15/03/2011
 * @Diretorio:Main/PHP/
 * Classe Responsavel pela Manutencao das Configuracoes do sistema
 * @revision:
 * @Obs:
 *
 */

class Configuracoes {



    function __construct(){
        Log::Msg(2,"Class[ Configuracoes ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);
    }


    /**
     * Metodo: recupera_configuracoes
     * Tabela: Configuracao
     * Recupera as configuracoes das paginas html,
     * ex: icones, titulo, banners, tema.
     * retorna um json com o status do servidor
     * @return: json.
     */
    public function recupera_configuracoes(){

        $sql = "SELECT * FROM configuracao";
        $record = new Repository();
        $results = $record->load($sql);

        if ($results->count != 0){

            $result = array();
            $result['success'] = 'true';
            foreach ($results->rows as $row) {
                $result[$row->parametro] = $row->valor;
            }
            echo json_encode($result);
        }
        else {
            Log::Msg(3,"Falha na recuperacao das configuracoes");
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas não foi possivel recuperar as configuracoes.";
            $aResult['code'] = "Erro: [0000002]";
            die(json_encode($aResult));
        }
    }

}

?>