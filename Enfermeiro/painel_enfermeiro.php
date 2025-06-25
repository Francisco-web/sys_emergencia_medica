<?php
require '../classes/Autenticacao.php';
$auth = new Autenticacao();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Enfermeiro(a)') {
    header("Location: ../index.php");
    exit;
}

?>

<h2>Painel do Enfermeiro</h2>
<p>Bem-vindo, <?= $auth->usuarioLogado(); ?>!</p>

