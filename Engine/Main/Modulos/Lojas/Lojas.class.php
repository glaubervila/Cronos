<?php
//header('Content-Type: text/javascript; charset=UTF-8');
session_start();

/**
 * @class   :Lojas.class.php
 * @author  :Glauber Costa Vila-Verde
 * @date    :23/06/2010
 * Classe Responsavel pela Manutencao do Cadastro de  Lojas
 * @revision:
 */
class Lojas {
    private $_entidade = 'tb_lojas';
    private $_record   = null;
    private $_fields   = array("id","nome", "grupo", "razao_social", "cnpj", "inscricao_estadual", "inscricao_municipal",  "telefone_fixo", "telefone_movel", "email", "rua", "numero", "bairro", "cidade", "uf", "complemento", "img_logotipo", "dt_inclusao", "dt_alteracao");
    
    private $_id            = "";  // Chave Primaria
    private $_Nome          = "";  // Nome da Loja
    private $_Grupo         = "";  // Chave Estrangeira com a tabela Grupos_Lojas
    private $_Razao_Social  = "";  // Razao Social
    private $_CNPJ          = "";  // CNPJ
    private $_I_Estadual    = "";  // Inscricao Estadual
    private $_I_Municipal   = "";  // Inscricao Municipal
    private $_Tel_Fixo      = "";  // Telefone Fixo
    private $_Tel_Movel     = "";  // Telefone Movel
    private $_Email         = "";  // E-mail
    private $_Rua           = "";  // Rua
    private $_Numero        = "";  // Numero
    private $_Bairro        = "";  // Bairro
    private $_Cidade        = "";  // Cidade
    private $_Uf            = "";  // Uf
    private $_Cep           = "";  // CEP
    private $_Complemento   = "";  // Complemento
    private $_ImgLogotipo   = "";  // Imagem para com o logotipo em Resources
    private $_Dt_Inclusao   = "";  // Data da Inclusao
    private $_Dt_Alteracao  = "";  // Data da Alteracao

    function __construct(){
        Log::Msg(2,"Class[ Lojas ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);

        $this->_record = new Repository();

        $this->_id            = $_REQUEST['id'];
        $this->_Nome          = $_REQUEST['nome'];
        $this->_Grupo         = $_REQUEST['grupo'];
        $this->_Razao_Social  = $_REQUEST['razao_social'];
        $this->_CNPJ          = $_REQUEST['cnpj'];
        $this->_I_Estadual    = $_REQUEST['inscricao_estadual'];
        $this->_I_Municipal   = $_REQUEST['inscricao_municipal'];
        $this->_Tel_Fixo      = $_REQUEST['telefone_fixo'];
        $this->_Tel_Movel     = $_REQUEST['telefone_movel'];
        $this->_Email         = $_REQUEST['email'];
        $this->_Rua           = $_REQUEST['rua'];
        $this->_Numero        = $_REQUEST['numero'];
        $this->_Bairro        = $_REQUEST['bairro'];
        $this->_Cidade        = $_REQUEST['cidade'];
        $this->_Uf            = $_REQUEST['uf'];
        $this->_Cep           = $_REQUEST['cep'];
        $this->_Complemento   = $_REQUEST['complemento'];
        $this->_ImgLogotipo   = $_REQUEST['img_logotipo'];
        $this->_Dt_Inclusao   = $_REQUEST['dt_inclusao'];
        $this->_Dt_Alteracao  = $_REQUEST['dt_alteracao'];
    }

    public function CriaAtualiza(){
        Log::Msg(2,"Class[ Lojas ] Method[ CriaAtualiza ]");
        // Desligando o auto commit
        $this->_record->setCommit(0);

        Log::Msg(3,"id[{$this->_id}]");
        if (($this->_id != -1 ) && ($this->_id != '' )) {
            Log::Msg(3,"Update [ Entidade: {$this->_entidade}]");

            $sql = "UPDATE {$this->_entidade} SET nome = '{$this->_Nome}', grupo = '{$this->_Grupo}' , razao_social = '{$this->_Razao_Social}', cnpj = '{$this->_CNPJ}', inscricao_estadual = '{$this->_I_Estadual}', inscricao_municipal = '{$this->_I_Municipal}', telefone_fixo = '{$this->_Tel_Fixo}', telefone_movel = '{$this->_Tel_Movel}', email = '{$this->_Email}', rua = '{$this->_Rua}', numero = '{$this->_Numero}', bairro = '{$this->_Bairro}', cidade = '{$this->_Cidade}', uf = '{$this->_Uf}', cep = '{$this->_Cep}', complemento = '{$this->_Complemento}', img_logotipo = '{$this->_ImgLogotipo}', dt_inclusao = '{$this->_Dt_Inclusao}', dt_alteracao = NOW() WHERE id = {$this->_id}";


            $this->_record->store($sql);
            // Encerrando operação e salvando as alteracoes
            $this->_record->commit();
        }
        else {

            Log::Msg(3,"Insert [ Entidade: {$this->_entidade}]");
            $sql = "INSERT INTO tb_lojas (id, nome, grupo, razao_social, cnpj, inscricao_estadual, inscricao_municipal, telefone_fixo, telefone_movel, email, rua, numero, bairro, cidade, uf, complemento, img_logotipo, dt_inclusao, dt_alteracao) VALUES ('', '{$this->_Nome}',  '{$this->_Grupo}', '{$this->_Razao_Social}', '{$this->_CNPJ}', '{$this->_I_Estadual}', '{$this->_I_Municipal}', '{$this->_Tel_Fixo}', '{$this->_Tel_Movel}', '{$this->_Email}', '{$this->_Rua}', '{$this->_Numero}', '{$this->_Bairro}', '{$this->_Cidade}', '{$this->_Uf}', '{$this->_Complemento}', '{$this->_ImgLogotipo}', NOW(), '' )";

            $this->_id = $this->_record->store($sql);

            $this->_record->commit();
        }

        $this->getLoja();
    }

    /**
     *@return {rows:[{"id":"","Nome":"","Grupo":"","Razao_Social":"","CNPJ":"","Inscricao_Estadual":"","Inscricao_Municipal":"","id_Endereco":"","Telefone_Fixo":"","Telefone_Movel":"","Email":"loja1@lojas.com.br","Dt_Inclusao":"","Dt_Alteracao":""}],totalCount:1}
     */
    public function getLoja(){
        Log::Msg(2,"Class[ Lojas ] Method[ getLoja ]");

        $sql = "SELECT * FROM {$this->_entidade} WHERE {$this->_fields[0]} = {$this->_id}";

        $results = $this->_record->load($sql);
        Log::Msg(5,$results);

        if ($results) {
            echo "{success: true,data:";
            echo json_encode($results->rows[0]);
            echo "}";
        }
    }



    public function getLojas(){
        Log::Msg(2,"Class[ Lojas ] Method[ getLojas ]");

        $sql = "SELECT * FROM {$this->_entidade}";

        $results = $this->_record->load($sql);
        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }

    public function getLojasCmb(){
        Log::Msg(2,"Class[ Lojas ] Method[ getLojas ]");

        $sql = "SELECT id, nome FROM {$this->_entidade}";

        $results = $this->_record->load($sql);
        if ($results->count != 0) {
            $rows = json_encode($results->rows);
            $result = "{rows:{$rows},totalCount:{$results->count}}";
            echo $result;
        }
    }

    public function deleteLojas(){
        Log::Msg(2,"Class[ Lojas ] Method[ deleteLojas ]");

        if (is_array($this->_id)) {
            $id = implode(',', $this->_id);
        }
        else {
            $id = $this->_id;
        }
        $sql = "DELETE FROM {$this->_entidade} WHERE {$this->_fields[0]} IN ({$id})";
        $this->_record->delete($sql);
    }

    public function getNomeById($id){
        //Log::Msg(2,"Class[ Lojas ] Method[ getNomeById ]");

        $record = new Repository();

        $sql = "SELECT nome FROM tb_lojas WHERE id = $id";

        $results = $record->load($sql);

        if ($results->count != 0) {

            return $results->rows[0]->Nome;
        }
    }
}

?>