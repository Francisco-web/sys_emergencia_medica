<?php
require '../classes/Autenticacao.php';
$auth = new Autenticacao();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Administrador') {
    header("Location: ../index.php");
    exit;
}

?>

<h2>Painel do Administrador</h2>
<p>Bem-vindo, <?= $auth->usuarioLogado(); ?>!</p>

