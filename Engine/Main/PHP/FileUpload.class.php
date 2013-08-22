<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

class FileUpload {

    private $file     = null;
    private $work_dir = '';
    private $tmp_image= '';
    private $max_size = 2097152; // 2MB

    public function __construct(){
        Log::Msg(2,"Class[ FileUpload ] Method[ __construct ]");
        Log::Msg(4, $_REQUEST);


        $this->file = $_FILES['foto'];
        $this->work_dir =  Common::Verifica_Diretorio_Work();
        $this->tmp_image = $this->work_dir . $this->file['name'];


        //echo "({success:true, data:{\"arquivo\":\"Main/Data/Imagens_Produtos/370387_150x110.png\"}})";

    }
    public function upload_imagem(){

        Log::Msg(2,"Class[ FileUpload ] Method[ upload_imagem ]");

        if ($this->verifica_imagem()){
            // Imagem validada ok
            if ($this->mover_imagem_work()){
                Log::Msg(3,"Upload Finalizado Com Sucesso.");
                echo "({success:true, data:{\"url_image\":\"{$this->tmp_image}\", \"name_image\":\"{$this->file['name']}\"}})";
            }
        }
    }

    public function mover_imagem_work(){
        Log::Msg(2,"Class[ FileUpload ] Method[ mover_imagem_work ]");

        Log::Msg(3,"Movendo Uploaded_File Tmp_Name[ {$this->file["tmp_name"]} ] Destino[ {$this->tmp_image} ]");
        if (move_uploaded_file($this->file['tmp_name'], $this->tmp_image)) {
            return TRUE;
        }
        else {
            die("({failure:true, data:{\"msg\":\"Falha no Envio do Arquivo!</br> Por favor tente novamente mais tarde.</b>\"}})");
        }
    }

    public function verifica_imagem(){
        Log::Msg(2,"Class[ FileUpload ] Method[ verifica_imagem ]");

        // Verifica se o mime-type do arquivo é de imagem
        Log::Msg(3,"Verificando Formato do arquivo Type[{$this->file["type"]}]");

        if(!eregi("^image\/(pjpeg|jpeg|png|gif|bmp)$", $this->file["type"])){
            Log::Msg(3,"Formato Invalido - Abortado");
            die("({failure:true, data:{\"msg\":\"Arquivo em formato inválido.</br>A imagem deve ser <b>jpg, jpeg, bmp, gif ou png</b>.\"}})");
        }
        // Formato ok
        else {
            // Verifica tamanho do arquivo
            if($this->file["size"] > $this->max_size){
                Log::Msg(3,"Arquivo Muito Grande - Abortado");

                die("({failure:true, data:{\"msg\":\"Arquivo em tamanho muito grande!</br> A imagem deve ser de no máximo <b>{$this->max_size} bytes..</b>\"}})");
            }
            else {
                // Imagem Ok
                return true;
            }
        }
    }
}


?>