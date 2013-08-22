<?php
session_start();

$action = $_REQUEST['action'];

if(!isset($action)) {
    die();
}
else {
    $action();
}


function verifica_login(){

    $_SESSION["isLogado"]     = NULL;
    $_SESSION["id_Usuario"]   = NULL;
    $_SESSION["nome_Usuario"] = NULL;

    // Dados Vindo do Formulï¿½rio
    $login = isset($_POST['login']) ? $_POST['login'] : "";
    $senha = isset($_POST['senha']) ? $_POST['senha'] : "";

    // Recuperando Usuï¿½rios na base
    require_once('../Ajax/Util.php');
    require_once('../Class/Usuario.php');

    $ObjUsuario = new Usuario();
    $rs = $ObjUsuario->verifica_login($login, $senha);
    $usuarios = mysql_fetch_object($rs);

    // var_dump($usuarios);

    if ($usuarios->login && $usuarios->senha) {

        $_SESSION["isLogado"]     = TRUE;
        $_SESSION["id_Usuario"]   = $usuarios->id;
        $_SESSION["nome_Usuario"] = $usuarios->nome;

        echo json_encode(array(
          success   => TRUE
          //, redirect  => 'principal.html'
        ));
    }
    else {
        $_SESSION["isLogado"] = NULL;
        echo json_encode(array(
            success=> FALSE
            , message=> utf8_encode("Verifique usuï¿½rio e senha.")
        ));
    }

}

function verifica_sessao(){
    if ($_SESSION["isLogado"] == TRUE) {
        echo json_encode(array(
            success   => TRUE
        ));
    }
    else {
        echo json_encode(array(
            success   => FALSE
        ));
    }
}

?>