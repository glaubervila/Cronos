<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @package  :Clientes
 * @name     :Clientes_Exportar
 * @class    :Clientes_Exportar_Excel.class.php
 * @author   :Glauber Costa Vila-Verde
 * @date     :28/01/2011
 * @Diretorio:Main/Modulos/Clientes/
 * Classe Responsavel pela Exportacao do Cadastro de Clientes
 * para arquivo cvs compativel com Excel
 * @revision:
 * Mensagens de ERRO
 *      4001001 - Nao Ha Registros a Exportar
 *      4001002 - Falha na Geracao do Arquivo

 * Obs:
 * Tipo Exportacao
    ['1', 'Completa']
    ['2', 'Alterados']

 * Tipo de Pessoa
    ['1', 'Pessoa Física']
    ['2', 'Pessoa Jurídica']

 * Status
    [ 1, 'Ativo']
    [ 0, 'Inativo']
 */

class Clientes_Exportar_Excel {

    private $loja_origem    = "";
    private $tipo_cliente   = "";
    private $todos          = "";
    private $aCamposCliente = array();
    private $aCamposEndereco= array();

    private $prm_backup     = 'on';

    private $arquivo_result = "";
    private $arquivo_backup = "";

    private $total_a_exportar = 0;
    private $total_exportado  = 0;

    private $file_name      = "";
    private $file_path      = "";

    private $campos         = "";

    private $separador        = "; ";

    function __construct(){
        Log::Msg(2,"Class[ Clientes_Exportar_Excel ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->loja_origem  = $_REQUEST['loja'];
        $this->tipo_cliente = $_REQUEST['tipo_cliente'];

        $this->todos       = $_REQUEST['todos_campos'];

        foreach ($_REQUEST as $campo => $valor){

            if (substr_count($campo, "cliente-") != 0) {
                $this->aCamposCliente[] = $valor;
            }
            elseif (substr_count($campo, "endereco-") != 0) {
                $this->aCamposEndereco[] = $valor;
            }
        }

        // Diretorios e arquivos de trabalho
        $data = date("d-m-Y_H-i-s");

        $this->file_path =  Common::Verifica_Diretorio_Work();
        $this->file_name = "Cadastro_Clientes_excel.cvs";

        $this->arquivo_result = $this->file_path . $this->file_name;

        $dir_backup = Common::Verifica_Diretorio_BackUp();
        $this->arquivo_backup = $dir_backup."Cadastro_Clientes_excel_cvs_$data.zip";
    }

    public function Exportar_Excel() {
        Log::Msg(2,"Class[ Clientes_Exportar_Excel ] Method[ Exportar_Excel ]");


        // 1º Passo - Montar o Select (Saber se sao todos os campos ou nao)
        $sql = $this->Montar_Select();

        // 2º Passo - Montar a Clausula Where (tratar a loja, Tipo Cliente)
        $sql = $sql . $this->Monta_Where();

        // 3º Passo - Montar o Arquivo
        $this->Gerar_xls($sql);

        // 4º Passo - Fazer BackUp
        if ($this->prm_backup == 'on' ) {
            $this->BackUp();
        }
    }

    public function Montar_Select() {
        Log::Msg(2,"Class[ Clientes_Exportar_Excel ] Method[ Montar_Select ]");

        if ($this->todos == 'on'){
            // TODOS OS CAMPOS
            $select = "SELECT a.pk_id_cliente, a.fk_id_loja, a.tipo, a.status, a.tipo_cliente, a.nome, a.cpf, a.cnpj, a.rg, a.inscricao_estadual, a.dt_nascimento, a.profissao, a.sexo, a.estado_civil, a.telefone_fixo, a.telefone_movel, a.email,b.rua, b.numero, b.bairro, b.cidade, b.uf, b.cep, b.complemento, b.tipo_endereco, a.observacoes FROM tb_clientes a INNER JOIN tb_endereco b ON a.fk_id_endereco = b.id_endereco";

            $this->campos = "pk_id_cliente, fk_id_loja, tipo, status, tipo_cliente, nome, cpf, cnpj, rg, inscricao_estadual, dt_nascimento, profissao, sexo, estado_civil, telefone_fixo, telefone_movel, email, rua, numero, bairro, cidade, uf, cep, complemento, tipo_endereco, observacoes";
        }
        else {
            // SO OS CAMPOS SELECIONADOS
            $this->campos = implode(', ', $this->aCamposCliente);
            $this->campos .= ", " . implode(', ', $this->aCamposEndereco);

            foreach ($this->aCamposCliente as $campo_cliente){
                $acampos[] = "a." . $campo_cliente;
            }
            foreach ($this->aCamposEndereco as $campo_endereco){
                $acampos[] = "b." . $campo_endereco;
            }
            $campos = implode(', ', $acampos);

            $select = "SELECT $campos FROM tb_clientes a INNER JOIN tb_endereco b ON a.fk_id_endereco = b.id_endereco";

        }
        return $select;
    }

    public function Monta_Where() {
        Log::Msg(2,"Class[ Clientes_Exportar_Excel ] Method[ Monta_Where ]");

        $where = " WHERE ";

        // Loja
        if ($this->loja_origem == 0){
            // Todas as Lojas
        }
        else {
            $awhere[] = "a.fk_id_loja = {$this->loja_origem}";
        }

        // Tipo Cliente
        if ($this->tipo_cliente){
            $awhere[] = "a.tipo_cliente = {$this->tipo_cliente}";
        }

        $where .= implode (" AND ", $awhere);

        return $where;
    }

    public function Gerar_xls($sql){
        Log::Msg(2,"Class[ Clientes_Exportar_Excel ] Method[ Gerar_xls ]");

        $record = new Repository();
        $record->setLog(0);
        $record->setCharset('latin1');

        // Criar o Arquivo
        $arq_result = fopen($this->arquivo_result,"w");


        // Criar os Cabecalhos
        $acabecalho = $this->Criar_Cabecalhos();
        $cabecalho = implode($this->separador, $acabecalho);
        fputs($arq_result, "$cabecalho\n");

        // Recuperar Todos os Ids
        $aIds = $this->Recupera_Ids();

        if ($aIds) {
            // Carregar os Registros
            foreach ($aIds as $id) {

                // Fazendo Select Pelo Id
                $sql_id = $sql . " AND pk_id_cliente = {$id->pk_id_cliente}";

                $result = $record->load($sql_id);

                if ($result->count != 0) {

                    // Passando o Obj para um Array
                    $props = get_object_vars($result->rows[0]);

                    // Tratando Valores
                    $props['fk_id_loja'] = Lojas::getNomeById($props['fk_id_loja']);

                    if ($props['tipo']){
                        $props['tipo']   = $props['tipo'] == 1 ? 'Pessoa Fisíca' : 'Pessoa Jurídica';
                    }
                    if ($props['status']){
                        $props['status'] = $props['status'] == 1 ? 'Ativo' : 'Inativo';
                    }
                    if ($props['tipo_endereco']){
                        $props['tipo_endereco'] = $props['tipo_endereco'] == 1 ? 'Residencial' : 'Comercial';
                    }

                    // Gerando linha com campos separados pelo separador :P
                    $linha = implode($this->separador, $props);
                    $linha .= "\n";

                    // Escrever os Registros
                    fputs($arq_result, $linha);

                    // Totalizando Exportados
                    $this->total_exportado++;

                    $linha = null;
                }
                else {
                    $aResult['failure'] = "true";
                    $aResult['msg']  = "Desculpe mas houve uma Falha na Geração do Arquivo.";
                    $aResult['code'] = "Erro: [4001002]";
                    die(json_encode($aResult));

                }
            }

            // Fechar os Arquivo
            fclose($arq_result);


            // Retornar a Mesagem de ok + o arquivo
            if ($this->total_a_exportar == $this->total_exportado){
                //echo "{\"success\": \"true, \"total_exportado:\"{$this->total_exportado}, \"file:{$this->arquivo_result}}";
                $aResult['success'] = "true";
                $aResult['total_exportado'] = "{$this->total_exportado}";
                $aResult['file'] = "{$this->file_name}";
                $aResult['path'] = "{$this->file_path}";
                echo json_encode($aResult);
            }
        }
        else {
            Log::Msg(3,"Nenhum Registro satisfaz o filtro");
            $aResult['failure'] = "true";
            $aResult['msg']  = "Desculpe mas Nenhum Registro foi encontrado";
            $aResult['code'] = "Erro: [4001001]";
            die(json_encode($aResult));
        }
    }

    public function Criar_Cabecalhos() {
        Log::Msg(2,"Class[ Clientes_Exportar_Excel ] Method[ Criar_Cabecalhos ]");

        $labels = array();
        $labels['pk_id_cliente'] = "Codigo";
        $labels['fk_id_loja']   = "Loja";
        $labels['tipo']         = "Tipo";
        $labels['status']       = "Status";
        $labels['tipo_cliente'] = "Tipo Cliente";
        $labels['nome']         = "Nome";

        $labels['cpf']           = "CPF";
        $labels['cnpj']          = "CNPJ";
        $labels['rg']            = "RG";
        $labels['inscricao_estadual'] = "I. Estadual";
        $labels['dt_nascimento'] = "Data Nascimento";

        $labels['profissao']     = "Profissão";
        $labels['sexo']          = "Sexo";
        $labels['estado_civil']  = "Estado Civil";
        $labels['telefone_fixo'] = "Telefone Fixo";
        $labels['telefone_movel']= "Telefone Movel";

        $labels['email']  = "E-mail";
        $labels['rua']    = "Rua";
        $labels['numero'] = "Numero";
        $labels['bairro'] = "Bairro";
        $labels['cidade'] = "Cidade";

        $labels['uf']          = "UF";
        $labels['cep']         = "CEP";
        $labels['complemento'] = "Complemento";
        $labels['tipo_endereco'] = "Tipo Endereço";
        $labels['observacoes'] = "Observação";

        $acampos = explode(', ', $this->campos);

        foreach ($acampos as $campos) {
            $cabecalho[] = $labels[trim($campos)];
        }
        return $cabecalho;
    }

    public function Recupera_Ids() {
        Log::Msg(2,"Class[ Clientes_Exportar_Excel ] Method[ Recupera_Ids ]");

        $record = new Repository();

        $sql = "SELECT a.pk_id_cliente FROM tb_clientes a";
        $sql.= $this->Monta_Where();

        $results = $record->load($sql);

        if ($results->count != 0) {
            $this->total_a_exportar = $results->count;
            return $results->rows;
        }
        else {
            return FALSE;
        }
    }

    public function BackUp(){
        Log::Msg(2,"Class[ Clientes_Exportar_Excel ] Method[ BackUp ]");

        // compactar o arquivo result na Work e mandar pra backup
        exec("zip -r9 {$this->arquivo_backup} {$this->arquivo_result}", $verbose);

    }
}