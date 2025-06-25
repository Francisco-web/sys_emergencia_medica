<?php
require_once 'Autenticacao.php'; // ou o nome correto do arquivo da classe

$auth = new Autenticacao();
$auth->logout();

// Redireciona para a tela de login após logout
header("Location: ../index.php");
exit;
