<?php

//INICIALIZAMOS A SESSÃO
session_start();
//DESTRUIMOS AS VARIÁVEIS
unset($_SESSION["isLogado"]);
unset($_SESSION["id_Usuario"]);
unset($_SESSION["id_Grupo"]);
unset($_SESSION["nome_Usuario"]);
// Redundancia
session_destroy();
session_unset();

//REDIRECIONAMOS PARA PÁGINA DE LOGIN
Header("Location: ../../index.html");


?>


