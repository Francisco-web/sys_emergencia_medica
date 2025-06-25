<?php
require '../classes/Autenticacao.php';
$auth = new Autenticacao();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Medico(a)') {
    header("Location: ../index.php");
    exit;
}

?>

<h2>Painel do Médico(a)</h2>
<p>Bem-vindo, <?= $auth->usuarioLogado(); ?>!</p>

