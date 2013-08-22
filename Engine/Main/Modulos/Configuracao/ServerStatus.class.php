<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @class   :ServerStatus.class.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :06/05/2010
 * @revision:17/04/2010
 * Disponibiliza metodos para monitoração dos serviços do servidor
 * utiliza função exec() e comando de ShellScript
 * Necessita de Sistema Operacional Linux
 * Homologado com Slackware 13.0
 */
class ServerStatus {

    function __construct(){

    }
    public function getFileSystem(){
        $results = $this->Filesystems();
        $k = 0;

        foreach ($results as $result){
           $json_itens[$k] = array(
                id         => "$k"
                , particao => $result->disk
                , montagem => $result->mount
                , tipo     => $result->fstype
                , percent  => $result->percent
                , utilizada=> $this->format_bytesize($result->used)
                , livre    => $this->format_bytesize($result->free)
                , total    => $this->format_bytesize($result->size)
            );
            $k++;
        }
        echo "{rows: ".json_encode($json_itens)."}";
    }
    public function getUtilizacaoMemoria(){
        $results = $this->Memory();
        $k = 0;
        //var_dump($results);
        // Memoria Fisica
        if ($results['ram']){
            // Ram
            $json_itens[$k] = array(
                id         => "$k"
                , memoria  => 'Ram'
                , percent  => $results['ram']->percent
                , livre    => $this->format_bytesize($results['ram']->t_free)
                , utilizada=> $this->format_bytesize($results['ram']->t_used)
                , total    => $this->format_bytesize($results['ram']->total)
            );
            $k++;
            // Kernel + applications
            $json_itens[$k] = array(
                id         => "$k"
                , memoria  => '- Kernel + Aplica&ccedil;&otilde;es'
                , percent  => $results['ram']->app_percent
                , livre    => ''
                , utilizada=> $this->format_bytesize($results['ram']->app)
                , total    => ''
            );
            $k++;
            // Buffer
            $json_itens[$k] = array(
                id         => "$k"
                , memoria  => '- Buffer'
                , percent  => $results['ram']->buffers_percent
                , livre    => ''
                , utilizada=> $this->format_bytesize($results['ram']->buffers)
                , total    => ''
            );
            $k++;
            // Cache
            $json_itens[$k] = array(
                id         => "$k"
                , memoria  => '- Cache'
                , percent  => $results['ram']->cached_percent
                , livre    => ''
                , utilizada=> $this->format_bytesize($results['ram']->cached)
                , total    => ''
            );
            $k++;
        }
        if ($results['swap']){
            $json_itens[$k] = array(
                id         => "$k"
                , memoria  => 'Swap'
                , percent  => $results['swap']->percent
                , livre    => $this->format_bytesize($results['swap']->t_free)
                , utilizada=> $this->format_bytesize($results['swap']->t_used)
                , total    => $this->format_bytesize($results['swap']->total)
            );
            $k++;
        }
        if ($results['devswap']){
                foreach ($results['devswap'] as $res) {
                    $json_itens[$k] = array(
                        id         => "$k"
                        , memoria  => "- {$res->dev}"
                        , percent  => $res->percent
                        , livre    => $this->format_bytesize($res->free)
                        , utilizada=> $this->format_bytesize($res->used)
                        , total    => $this->format_bytesize($res->total)
                    );
                    $k++;
                }
            }
        echo "{rows: ".json_encode($json_itens)."}";
    }

    public function getInterfacesRede(){
        $results = $this->Network();
        $k = 0;
        foreach ($results as $result) {
            $json_itens[$k] = array(
                    id         => "$k"
                    , dev_name => $result->dev_name
                    , transfer => $result->transfer
                    , receive  => $result->receives
                    , erros    => $result->errs
                );
                $k++;
        }
        echo "{rows: ".json_encode($json_itens)."}";
    }

    public function getInfSistema(){
        Log::Msg(2,"Class[ServerStatus] Method[getInfSistema]");
        // Apache HostName
        $rows = array();

        $rows[0]->nome  = 'Servidor';
        $rows[0]->valor = $this->apacheHostname();
        // Ip Adress
        $rows[1]->nome  = 'N&uacute;mero IP';
        $rows[1]->valor = $this->ipAddress();
        // Uptime
        $uptime = $this->Uptime();
        $uptime->dd = $uptime->dd != 0 ? $uptime->dd : 0;
        $rows[2]->nome  = 'Tempo Ativo';
        $rows[2]->valor = "{$uptime->dd} dias {$uptime->hh} hrs {$uptime->mm} min";

        // Load Average
        $loadavg = $this->LoadAverage();
        $string_loadavg = "{$loadavg->I}, {$loadavg->V}, {$loadavg->XV}";
        $rows[3]->nome  = 'Load Average';
        $rows[3]->valor = $string_loadavg;

        // Sessoes de Usuarios
        $rows[4]->nome  = 'Usu&aacute;rios Ativos';
        $rows[4]->valor = $this->Users();

        $k = 0;
        foreach ($rows as $row){
            $json_itens[$k] = array(
                id       => "$k"
                , nome   => $row->nome
                , valor  => $row->valor
            );
            $k++;
        }
        echo "{rows: ".json_encode($json_itens)."}";
    }

    public function getUptime(){
        $uptime = $this->Uptime();
        $uptime->dd = $uptime->dd != 0 ? $uptime->dd : 0;
        $data = "{$uptime->dd} Dias {$uptime->hh} Horas {$uptime->mm} Minutos";
        echo "{\"success\":true, \"uptime\":\"$data\"}";
    }

    public function getLoadAverage(){
        $loadavg = $this->LoadAverage();
        $data = "{$loadavg->I}, {$loadavg->V}, {$loadavg->XV}";
        echo "{\"success\":true, \"uptime\":\"$data\"}";
    }

    public function getIpAddress(){
        $ipAddress = $this->ipAddress();
        echo "{\"success\":true, \"ip_adress\":\"$ipAddress\"}";
    }

    public function getApacheHostname(){
        $apacheHostname = $this->apacheHostname();
        echo "{\"success\":true, \"apache_Hostname\":\"$apacheHostname\"}";
    }



//===============================================================================

    public function apacheHostname() {
        if (! ($result = getenv('SERVER_NAME'))) {
            $result = 'N.A.';
        }
        return $result;
    }

    public function ipAddress(){
        if (!($result = getenv('SERVER_ADDR'))) {
            $result = gethostbyname($this->chostname());
        }
        return $result;
    }

    public function Users(){
        if ($strBuf = self::executeProgram('who', '-q')) {
            $arrWho = preg_split('/=/', $strBuf);
            return $arrWho[1];
        }
    }

    function Filesystems($df_param = 'P') {
        // Mostrar os pontos de montagem
        $show_bind = false;
        // Mostrar used inodes em porcentagem
        $show_inodes = true;

        $j = 0;

        $df = self::executeProgram('df', '-k' . $df_param );
        $df = preg_split("/\n/", $df, -1, PREG_SPLIT_NO_EMPTY);

        if( $show_inodes ) {
            $df2 = self::executeProgram('df', '-i' . $df_param );
            $df2 = preg_split("/\n/", $df2, -1, PREG_SPLIT_NO_EMPTY);
        }

        $mount = self::executeProgram('mount',null);
        $mount = preg_split("/\n/", $mount, -1, PREG_SPLIT_NO_EMPTY);

        foreach( $df as $df_line) {
            $df_buf1  = preg_split("/(\%\s)/", $df_line, 2);
            if( count($df_buf1) != 2) {
                continue;
            }

            preg_match("/(.*)(\s+)(([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)$)/", $df_buf1[0], $df_buf2);
            $df_buf = array($df_buf2[1], $df_buf2[4], $df_buf2[6], $df_buf2[8], $df_buf2[10], $df_buf1[1]);

            if( $show_inodes ) {
                preg_match_all("/([0-9]+)%/", $df2[$j + 1], $inode_buf, PREG_SET_ORDER);
            }

            if( count($df_buf) == 6 ) {

                $df_buf[0] = trim( str_replace("\$", "\\$", $df_buf[0] ) );
                $df_buf[5] = trim( $df_buf[5] );

                $current = 0;
                foreach( $mount as $mount_line ) {
                    $current++;

                    if ( preg_match("#" . $df_buf[0] . " on " . $df_buf[5] . " type (.*) \((.*)\)#", $mount_line, $mount_buf) ) {
                    $mount_buf[1] .= "," . $mount_buf[2];
                    } elseif ( !preg_match("#" . $df_buf[0] . "(.*) on " . $df_buf[5] . " \((.*)\)#", $mount_line, $mount_buf) ) {
                        continue;
                    }

                    if ( $show_bind || !stristr($mount_buf[2], "bind")) {
                        $results[$j] = new StdClass();
                        $results[$j]->disk    = str_replace( "\\$", "\$", $df_buf[0] );
                        $results[$j]->size    = $df_buf[1];
                        $results[$j]->used    = $df_buf[2];
                        $results[$j]->free    = $df_buf[3];
                        $results[$j]->percent = round(($results[$j]->used * 100) / $results[$j]->size);
                        $results[$j]->mount   = $df_buf[5];
                        $results[$j]->fstype  = substr( $mount_buf[1], 0, strpos( $mount_buf[1], "," ) );
                        $results[$j]->options = substr( $mount_buf[1], strpos( $mount_buf[1], "," ) + 1, strlen( $mount_buf[1] ) );
                        if( $show_inodes && isset($inode_buf[ count( $inode_buf ) - 1][1]) ) {
                            $results[$j]->inodes = $inode_buf[ count( $inode_buf ) - 1][1];
                        }
                        $j++;
                        unset( $mount[$current - 1] );
                        sort( $mount );
                        break;
                    }
                }
            }
        }
        return $results;
    }

    public function Memory() {
        $results['ram']     = new StdClass();
        $results['swap']    = new StdClass();
        $results['devswap'] = array();

        $bufr = $this->read_file( '/proc/meminfo' );
        if ( $bufr ) {
            $bufe = explode("\n", $bufr);
            foreach( $bufe as $buf ) {
                $results['ram']->name     = 'Ram';
                $results['swap']->name    = 'Swap';
                if (preg_match('/^MemTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                $results['ram']->total = $ar_buf[1];
                } else if (preg_match('/^MemFree:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                $results['ram']->t_free = $ar_buf[1];
                } else if (preg_match('/^Cached:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                $results['ram']->cached = $ar_buf[1];
                } else if (preg_match('/^Buffers:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                $results['ram']->buffers = $ar_buf[1];
                } else if (preg_match('/^SwapTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                $results['swap']->total = $ar_buf[1];
                } else if (preg_match('/^SwapFree:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                $results['swap']->t_free = $ar_buf[1];
                }
            }

            $results['ram']->t_used   = $results['ram']->total - $results['ram']->t_free;
            $results['ram']->percent  = round(($results['ram']->t_used * 100) / $results['ram']->total);
            $results['swap']->t_used  = $results['swap']->total - $results['swap']->t_free;
            $results['swap']->percent = round(($results['swap']->t_used * 100) / $results['swap']->total);

            // values for splitting memory usage
            if (isset($results['ram']->cached) && isset($results['ram']->buffers)) {
                $results['ram']->app = $results['ram']->t_used - $results['ram']->cached - $results['ram']->buffers;
                $results['ram']->app_percent = round(($results['ram']->app * 100) / $results['ram']->total);
                $results['ram']->buffers_percent = round(($results['ram']->buffers * 100) / $results['ram']->total);
                $results['ram']->cached_percent = round(($results['ram']->cached * 100) / $results['ram']->total);
            }

            $bufr = $this->read_file( '/proc/swaps' );
            if ( $bufr ) {
                $swaps = explode("\n", $bufr);
                for ($i = 1; $i < (sizeof($swaps)); $i++) {
                if( trim( $swaps[$i] ) != "" ) {
                    $ar_buf = preg_split('/\s+/', $swaps[$i], 6);
                    $results['devswap'][$i - 1] = new StdClass;
                    $results['devswap'][$i - 1]->dev   = $ar_buf[0];
                    $results['devswap'][$i - 1]->total = $ar_buf[2];
                    $results['devswap'][$i - 1]->used  = $ar_buf[3];
                    $results['devswap'][$i - 1]->free  = ($results['devswap'][$i - 1]->total - $results['devswap'][$i - 1]->used);
                    $results['devswap'][$i - 1]->percent = round(($ar_buf[3] * 100) / $ar_buf[2]);
                }
                }
            }
        }
        return $results;
    }

    public function Network() {
        $results = array();
        $bufr = $this->read_file('/proc/net/dev');
        if ( $bufr ) {
            $bufe = explode("\n", $bufr);
            foreach( $bufe as $buf ) {
                if (preg_match('/:/', $buf)) {
                    list($dev_name, $stats_list) = preg_split('/:/', $buf, 2);
                    $stats = preg_split('/\s+/', trim($stats_list));

                    $dev = new StdClass();
                    $dev->dev_name = trim($dev_name);
                    // Recebidos
                    $dev->rx_bytes   = trim($stats[0]);
                    $dev->rx_packets = trim($stats[1]);
                    $dev->rx_errs    = trim($stats[2]);
                    $dev->rx_drop    = trim($stats[3]);
                    // Enviados
                    $dev->tx_bytes   = trim($stats[8]);
                    $dev->tx_packets = trim($stats[9]);
                    $dev->tx_errs    = trim($stats[10]);
                    $dev->tx_drop    = trim($stats[11]);
                    // Totais
                    $dev->receives = $this->format_bytesize($stats[0]);
                    $dev->transfer = $this->format_bytesize($stats[8]);
                    $dev->errs     = $stats[2] + $stats[10];
                    $dev->drop     = $stats[3] + $stats[11];

                    $results[] = $dev;
                }
            }
        }
        return $results;
    }

    public function Uptime(){
        $string = $this->read_file('/proc/uptime', 1);
        $a_string = split(' ', $string);
        $timestamp = trim($a_string[0]);
        $time = $this->timestamp_to_time($timestamp);
        $uptime = new StdClass();
        $uptime->dd = $time['dd'];
        $uptime->hh = $time['hh'];
        $uptime->mm = $time['mm'];
        return $uptime;
    }
    public function LoadAverage(){
        $string = $this->read_file('/proc/loadavg', 1);
        $a_string = split(' ', $string);
        $loadavg = new StdClass();
        $loadavg->I  = trim($a_string[0]);
        $loadavg->V  = trim($a_string[1]);
        $loadavg->XV = trim($a_string[2]);
        return $loadavg;
    }


    public function read_file($fileName, $line = 0, $bytes = 4096) {
        $result = '';
        $k = 1;
        if (file_exists($fileName)) {
            if ($handler = fopen($fileName, 'r')) {
                while (!feof($handler)) {
                    $result .= fgets($handler, $bytes);
                    if ($line <= $k && $line != 0 ){
                        break;
                    }
                    else {
                        $k++;
                    }
                }
                fclose($handler);
            }
            else {
                Log::Msg(0,"EXCEPTION [Falha na leitura do arquivo!  \"$fileName\"]");
                return false;
            }
        }
        else {
            Log::Msg(0,"EXCEPTION [Arquivo não encontrado! \"$fileName\"]");
            return false;
        }
        return $result;
    }

    public function timestamp_to_time($timestamp) {

        $time = '';

        $min = $timestamp / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));

        if ($days != 0) {
            $time['dd'] = $days;
        }
        if ($hours != 0) {
            $time['hh'] = $hours;
        }

        $time['mm'] = $min;
        return $time;
    }

    /**
     * Formata um valor em Kbytes
     * @param integer $kbytes = valor a ser convertido
     */
    public function format_bytesize ($kbytes, $dec_places = 2) {

        if ($kbytes > 1048576) {
            $result = sprintf('%.' . $dec_places . 'f', $kbytes / 1048576);
            $result .= ' Gb';
        } elseif ($kbytes > 1024) {
            $result = sprintf('%.' . $dec_places . 'f', $kbytes / 1024);
            $result .= ' Mb';
        } else {
            $result = sprintf('%.' . $dec_places . 'f', $kbytes);
            $result .= ' Kb';
        }
        return $result;
    }


    /**
     * Recupera o conteudo de stdout/stderr com a opção de timeout para leitura
     * @param array   $pipes array com ponteiros para  stdin, stdout, stderr (proc_open())
     * @param string  &$out  string que vai receber as mensagens de saida (referencia)
     * @param string  &$err  string que vai receber as mensagens de erro (referencia)
     * @param integer $sek   timeout valor em segundos
     * @return void
     */
    public function timeoutfgets($pipes, &$out, &$err, $sek = 10) {
        // fill output string
        $time = $sek;
        $w = null;
        $e = null;

        while ($time >= 0) {
            $read = array($pipes[1]);
            while (!feof($read[0]) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0 && strlen($c = fgetc($read[0])) > 0) {
                $out .= $c;
            }
            --$time;
        }
        // fill error string
        $time = $sek;
        while ($time >= 0) {
            $read = array($pipes[2]);
            while (!feof($read[0]) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0 && strlen($c = fgetc($read[0])) > 0) {
                $err .= $c;
            }
            --$time;
        }
    }


    /**
     * Localiza um programa, verifica se ele nao esta rodando no WINNT,
     * no WINNT somente retorna o nome do programa com a extensao .exe
     * no Linux varre um array de diretorios, e retorna o caminho completo.
     * @param string $strProgram nome do programa
     * @return string path completo do programa
     */
    public function findProgram($strProgram){

        $arrPath = array();
        $strProgrammpath = '';

        if (PHP_OS == 'WINNT') {
            $strProgram .= '.exe';
            $arrPath = preg_split('/;/', getenv("Path"), -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $arrPath = preg_split('/:/', getenv("PATH"), -1, PREG_SPLIT_NO_EMPTY);
        }
        // Adiciono os caminhos padroes quando nao for windows
        if ( empty($arrPath) && PHP_OS != 'WINNT') {
            array_push($arrPath, '/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin', '/opt/bin','/opt/sbin');
        }
        foreach ($arrPath as $strPath) {
            // To avoid "open_basedir restriction in effect" error when testing paths if restriction is enabled
            if (!$strPath || !is_dir($strPath)) {
                continue;
            }
            $strProgrammpath = $strPath."/".$strProgram;
            if (is_executable($strProgrammpath)) {
                return $strProgrammpath;
            }
        }
    }

    /**
     * Executa um programa, retorna string
     * verifica a existencia de | para executar outro programa antes
     * ex: CORRETO $program = executeProgram('netstat', '-anp | grep LIST');
     * ERRADO $program = executeProgram('netstat', '-anp|grep LIST');
     * @param string  $strProgramname nome do programa
     * @param string  $strArgs argumentos do programa
     * @return  string
     */
    public static function executeProgram($strProgramname, $strArgs) {
        $strBuffer = '';
        $strError = '';
        $pipes = array();
        $strProgram = self::findProgram($strProgramname);
        if (!$strProgram) {
            Log::Msg(0,"ERROR: Programa nao Encontrado! Programa[$strProgram] Return[False]");
            return false;
        }
        // Se na String de Argumentos tiver | deve procurar o programa
        if ($strArgs) {
            $arrArgs = preg_split('/ /', $strArgs, -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0, $cnt_args = count($arrArgs); $i < $cnt_args; $i++) {
                if ($arrArgs[$i] == '|') {
                    $strCmd = $arrArgs[$i + 1];
                    $strNewcmd = self::_findProgram($strCmd);
                    $strArgs = preg_replace("/\| ".$strCmd.'/', "| ".$strNewcmd, $strArgs);
                }
            }
        }
        $descriptorspec = array(0=>array("pipe", "r"), 1=>array("pipe", "w"), 2=>array("pipe", "w"));
        $process = proc_open($strProgram." ".$strArgs, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $strBuffer .= self::timeoutfgets($pipes, $strBuffer, $strError);
            $return_value = proc_close($process);
        }
        $strError = trim($strError);
        $strBuffer = trim($strBuffer);
        if (! empty($strError) && $return_value <> 0) {
            Log::Msg(0,"ERROR: Execucao do Programa[$strProgram] nao retornou nada. Return[False] Message[$strError]");
            return false;
        }
        if (! empty($strError)) {
            if ($booErrorRep) {
                Log::Msg(0,"ERROR: Execucao do Programa[$strProgram] Return[true] Message[$strError]");
            }
            return true;
        }
        return $strBuffer;
    }

}

?>