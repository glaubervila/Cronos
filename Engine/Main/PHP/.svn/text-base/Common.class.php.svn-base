<?php
/**
 * @class   :Common.class.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :16/06/2010
 * Classe prove metodos, comuns ao sistema, retorna informacoes para o Common.js
 * @revision:
 */
class Common {


    function __construct(){
        Log::Msg(2,"Class[Common] Method[__construct]");
        Log::Msg(4, $_REQUEST);
    }

    /**
     * Metodo: verifica_servidor_mysql
     * Tabela: Configuracao
     * Tenta Abrir conexao com servidor mysql
     * retorna um json com o status do servidor
     * @return: json.
     */
    public function verifica_servidor_mysql(){

        $sql = "SELECT * FROM configuracao WHERE 1 LIMIT 1";
        $record = new Repository();
        $results = $record->load($sql);

        if ($results->count != 0){
            Log::Msg(3,"Teste Conexao Servidor Mysql [ TRUE ]");
            echo "{success: true}";
        }
        else {
            Log::Msg(3,"Teste Conexao Servidor Mysql [ FALSE ]");
            // SEM CONEXAO COM Mysql RETORNAR MESAGEM DE ERRO e ABORTAR
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas o Servidor está temporariamente indisponivel.";
            $aResult['code'] = "Erro: [0000001]";
            die(json_encode($aResult));
        }
    }

    /**
     * Metodo: mergeObject
     * Tabela:
     * Recebe um array de Objetos e agrupa em um unico
     * obj da Standart Class com todas as suas propriedades
     * obs: se existirem mais de uma propriedade com o mesmo nome será sobrescrita
     * @return: $obj = StdClass
     */
    public function mergeObject($objs){
        $result = new StdClass();
        if(count($objs) >  0 ){
            foreach($objs as $ob){
                //$props = get_class_vars(get_class($ob));
                $props = get_object_vars($ob);
                foreach($props as $ind=>$val){
                    $result->$ind = $ob->$ind;
                }
            }
        }
        return $result;
    }

    /**
     *
     */
    function converte_data($date, $formato = 'yy-mm-dd'){

        if ($date) {
            // Formato para Mysql
            if ($formato == 'yy-mm-dd') {
                $array = explode('/', $date);
                $result = $array[2] . '-' . $array[1] . '-' . $array[0];
            }
            elseif ($formato == 'dd-mm-yy'){
                $array = explode('/', $date);
                $result = $array[0] . '-' . $array[1] . '-' . $array[2];
            }
            elseif ($formato == 'dd-mm-yyyy'){
                $array = explode('-', $date);
                $result = $array[2] . '-' . $array[1] . '-' . $array[0];
            }
            elseif ($formato == 'ddmmyyyy'){
                $array = explode('-', $date);
                $result = $array[2] . $array[1] . $array[0];
            }
            return $result;
        }
        else {
            return false;
        }
    }

    /**
     * METHOD: get_Status
     * Retorna um Json com os status
     * Usado para Stores de Combos e Grids
     * tabela: Status
     */
    public function getStatus(){
        Log::Msg(2,"Class[ Common ] Method[ getStatus ]");

        $record = new Repository();

        $sql = "SELECT pk_id_status, status FROM Status ";

        $results = $record->load($sql);

        Log::Msg(5,$results);

        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }

   /**
    * Este Metodo Verifica se o Diretorio Main/Work Existe
    */
   public function Verifica_Diretorio_Work(){
        $dir = "Main/Work/";
        if(is_dir($dir)){
            return "$dir";
        }
        else {
            return FALSE;
        }
   }
   /**
    * Este Metodo Verifica se o Diretorio Main/Data Existe
    */
   public function Verifica_Diretorio_Data(){
        $dir = "Main/Data/";
        if(is_dir($dir)){
            return "$dir";
        }
        else {
            return FALSE;
        }
   }

   /**
    * Este Metodo Verifica se o Diretorio Main/Data/BackUp Existe
    */
   public function Verifica_Diretorio_BackUp(){
        $dir = "Main/Data/BackUp/";
        if(is_dir($dir)){
            return "$dir";
        }
        else {
            return FALSE;
        }
   }

    public function parseArgs($argv){

        array_shift($argv);
        $out = array();
        foreach ($argv as $arg){
            if (substr($arg,0,2) == '--'){
                $eqPos = strpos($arg,'=');
                if ($eqPos === false){
                    $key = substr($arg,2);
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                } else {
                    $key = substr($arg,2,$eqPos-2);
                    $out[$key] = substr($arg,$eqPos+1);
                }
            } else if (substr($arg,0,1) == '-'){
                if (substr($arg,2,1) == '='){
                    $key = substr($arg,1,1);
                    $out[$key] = substr($arg,3);
                } else {
                    $chars = str_split(substr($arg,1));
                    foreach ($chars as $char){
                        $key = $char;
                        $out[$key] = isset($out[$key]) ? $out[$key] : true;
                    }
                }
            } else {
                $out[] = $arg;
            }
        }
        return $out;
    }

    // Nao Funciona Mais no PHP 5.3x
    public function trata_string($str){
        if (!is_numeric($str)) {
            $str = get_magic_quotes_gpc() ? stripslashes($str) : $str;
            $str = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($str) : mysql_escape_string($str);
        }
        return $str;
    }

    /**
     * __format
     * Função de formatação de strings.
     * @name format_string
     * @version 1.0
     * @param string $campo String a ser formatada
     * @param string $mascara Regra de formatção da string (ex. ##.###.###/####-##)
     * @return string Retorna o campo formatado
     */
    public function format_string($campo='',$mascara=''){
        //remove qualquer formatação que ainda exista
        $sLimpo = ereg_replace("/[' '-./ t]/",'',$campo);
        // pega o tamanho da string e da mascara
        $tCampo = strlen($sLimpo);
        $tMask = strlen($mascara);
        if ( $tCampo > $tMask ) {
            $tMaior = $tCampo;
        } else {
            $tMaior = $tMask;
        }
        //contar o numero de cerquilhas da marcara
        $aMask = str_split($mascara);
        $z=0;
        $flag=FALSE;
        foreach ( $aMask as $letra ){
            if ($letra == '#'){
                $z++;
            }
        }
        if ( $z > $tCampo ) {
                //o campo é menor que esperado
                $flag=TRUE;
        }
        //cria uma variável grande o suficiente para conter os dados
        $sRetorno = '';
        $sRetorno = str_pad($sRetorno, $tCampo+$tMask, " ",STR_PAD_LEFT);
        //pega o tamanho da string de retorno
        $tRetorno = strlen($sRetorno);
        //se houve entrada de dados
        if( $sLimpo != '' && $mascara !='' ) {
            //inicia com a posição do ultimo digito da mascara
            $x = $tMask;
            $y = $tCampo;
            $cI = 0;
            for ( $i = $tMaior-1; $i >= 0; $i-- ) {
                if ($cI < $z){
                    // e o digito da mascara é # trocar pelo digito do campo
                    // se o inicio da string da mascara for atingido antes de terminar
                    // o campo considerar #
                    if ( $x > 0 ) {
                        $digMask = $mascara[--$x];
                    } else {
                        $digMask = '#';
                    }
                    //se o fim do campo for atingido antes do fim da mascara
                    //verificar se é ( se não for não use
                    if ( $digMask=='#' ) {
                        $cI++;
                        if ( $y > 0 ) {
                            $sRetorno[--$tRetorno] = $sLimpo[--$y];
                        } else {
                            //$sRetorno[--$tRetorno] = '';
                        }
                    } else {
                        if ( $y > 0 ) {
                            $sRetorno[--$tRetorno] = $mascara[$x];
                        } else {
                            if ($mascara[$x] =='('){
                                $sRetorno[--$tRetorno] = $mascara[$x];
                            }
                        }
                        $i++;
                    }
                }
            }
            if (!$flag){
                if ($mascara[0]!='#'){
                    $sRetorno = '(' . trim($sRetorno);
                }
            }
            return trim($sRetorno);
        } else {
            return '';
        }
    } //fim __format


    public function getExtensaoArquivo($arquivo){

        $tam = strlen($arquivo);

        //ext de 3 chars
        if( $arquivo[($tam)-4] == '.' ){
        $extensao = substr($arquivo,-3);
        }

        //ext de 4 chars
        elseif( $arquivo[($tam)-5] == '.' ){
        $extensao = substr($arquivo,-4);
        }

        //ext de 2 chars
        elseif( $arquivo[($tam)-3] == '.' ){
        $extensao = substr($arquivo,-2);
        }

        //Caso a extensão não tenha 2, 3 ou 4 chars ele não aceita e retorna Nulo.
        else{
        $extensao = NULL;
        }
        return strtolower($extensao);

    }

    /**Converte um Array de Objetos em Um Array
     */
    public function objectToArray ($object) {
        $arr = array();
        for ($i = 0; $i < count($object); $i++) {
            $arr[] = get_object_vars($object[$i]);
        }
        return $arr;
    }

    public function PaseIniFile($file){

        $c = getcwd();
        Log::Msg(3,"Lendo Arquivo de Conf [ $file ] Diretorio Atual [ $c ]");

        // Verifica se Existe arquivo de configuração
        if (file_exists($file)) {
            Log::Msg(3,"Arquivo Configuracao Existe");
            // Lé o INI e retorna um array
            $arr = parse_ini_file($file);
            return $arr;
        }
        else {
            // Se não existir lança Excessão
            return FALSE;
        }
    }

    public function ArrayToObject($array){

        $obj = new StdClass();
        foreach ($array as $key => $value){
            $obj->$key = $value;
        }

        return $obj;
    }

    public function Adiciona_Quebra_linha($text, $maxChar){

        $array_linhas = explode('\n', $text);
        foreach ($array_linhas as $linha){
            $string[] = wordwrap($text, $maxChar, "\n");
        }

        return implode('',$string);
    }

    /** Method getWebServiceConf
     * le o arquivo de configuracao de conexao com
     * Web Service ou Banco a Banco do Servidor
     * @Return: Array Com dados da conexao
     */
     public function getWebServiceConf(){

        $ini_file = "Conf/cronos_ws.ini";
        $arrIni = Common::PaseIniFile($ini_file);
        if($arrIni){
            return $arrIni;
        }
        else{
            Log::Msg(0,"[ ERRO ] Falha na leitura do arquivo de configuracao de conexao com servidor cronos. INI_FILE [ $ini_file ]");
        }
    }

    public function ReturnError($err){

        if (is_array($err)){
            Log::Msg(0,"[ ERRO ] Descricao");
            Log::Msg(0,$err);
        }
        else{
            Log::Msg(0,"[ ERRO ] Message [ $err ]");
        }

        $aResult['failure'] = "true";
        $aResult['msg']       = "Desculpe mas houve uma Falha... Tente Novamente, se o problema persistir entre em contato com o administrador.";
        $aResult['descricao'] = "<pre>$err</pre>";
        die(json_encode($aResult));
    }


    /**
     * Função para transformar Cor HTML (Hexadecimal) para o padrão RGB
     * print_r(hex2rgb ('FFEECC'));
     * Array ( [Red] => 255 [Green] => 238 [Blue] => 204 )
     */
    public function hex2rgb ($color) {

        return array (

            'Red'=>  hexdec (substr ($color, 0, 2)),

            'Green'=> hexdec (substr ($color, 2, 2)),

            'Blue'=> hexdec (substr ($color, 4, 2))

       );

    }

    // get remote file last modification date (returns unix timestamp)
    public function GetRemoteLastModified( $uri ) {
        // default
        $unixtime = 0;

        $fp = fopen( $uri, "r" );
        if( !$fp ) {
            return false;
        }

        $MetaData = stream_get_meta_data( $fp );

        foreach( $MetaData['wrapper_data'] as $response ) {
            // case: redirection
            if( substr( strtolower($response), 0, 10 ) == 'location: ' ) {
                $newUri = substr( $response, 10 );
                fclose( $fp );
                return Common::GetRemoteLastModified( $newUri );
            }
            // case: last-modified
            elseif( substr( strtolower($response), 0, 15 ) == 'last-modified: ' ) {
                $unixtime = strtotime( substr($response, 15) );
                break;
            }
        }
        fclose( $fp );
        return $unixtime;
    }


    public function Atualiza_Arquivo_Local ($file, $local_path, $remote_path) {
        //Log::Msg(2,"Atualiza Arquivo Local [ $file ], [ $local_path ], [ $remote_path ]");
        $url = $remote_path . $file;
        $local_file = $local_path . $file;

        // saber se o arquivo existe no servidor
        if (@fopen($url,"r")==true){
            //Log::Msg(3,"Arquivo Existe em [ $url ]");
            // saber se existe no host
            if (file_exists($local_path)){
                //Log::Msg(3,"Arquivo Existe no Host [ $local_file ]");
                //Testar a data de Modificação
                $mtimelocal  = filemtime($local_file);
                $mtimeremoto =  Common::GetRemoteLastModified( $url );

                //Log::Msg(3,"Data de Modificacao. Remoto [ ".date ("Y-m-d H:i:s", $mtimeremoto)." ] Local [ ".date ("Y-m-d H:i:s", $mtimelocal)." ]");

                // Se o Arquivo for mas recente no servidor
                if ($mtimeremoto > $mtimelocal){
                    // Arquivo Mais Atual no Servidor
                    //Log::Msg(3,"Arquivo Mais Atual no Servidor.");
                    if (copy($url, $local_path . urldecode(basename($url)))){
                        //Log::Msg(3,"Arquivo Atualizado. [ $file ]");
                        //chmod($local_file, 777);
                        return true;
                    }
                    else {
                        Log::Msg(0,"Falha ao Atualizar Arquivo. [ $file ]");
                        return false;
                    }
                }
                else {
                    //Log::Msg(3,"Arquivo Igual não faz nada.");
                    return true;
                }
            }
            else {
                //Log::Msg(3,"Arquivo Nao Existe no Host [ $local_path ]");

                if (copy($url, $local_path . urldecode(basename($url)))){
                    //Log::Msg(3,"Arquivo Copiado [ $file ]");
                    //chmod($local_file, 777);
                    return true;
                }
                else {
                    Log::Msg(0,"Falha ao Copiar Arquivo [ $file ]");
                    return false;
                }
            }
        }
        else {
            Log::Msg(0,"Arquivo Nao Existe ou Nao foi possivel acessar.");
            return false;
        }


    }


    public function getParametro($parametro){
        $record = new Repository();
 
        $sql = "SELECT valor FROM `configuracao` WHERE parametro = '$parametro';";
        $result = $record->load($sql);

        $value = $result->rows[0]->valor ? $result->rows[0]->valor : 0;

        Log::Msg(2,"Recuperando Parametro [ $parametro ] Value [ $value ].");
        return $value;

    }

}

?>