<?php
  
require_once 'Autenticacao.php';

$auth = new Autenticacao();

if (isset($_POST['btn_login'])) {
   
    $email = htmlspecialchars($_POST['email']);
    $senha = htmlspecialchars($_POST['senha']);

    if (empty($email) || empty($senha)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Preenche todos os campos.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        header("Location:../index.php");
    }else{

        if ($auth->login($_POST['email'], $_POST['senha'])) {
            // Redireciona para o painel conforme o tipo de usuário
            $tipo = $auth->tipoUsuario();
            
            switch ($tipo) {
                case 'Administrador':
                    header("Location: ../Admin/painel_admin.php");
                    break;
                case 'Medico(a)':
                    header("Location: ../Medico/painel_medico.php");
                    break;
                case 'Recepcionista':
                    header("Location: ../Recepcionista/painel_recepcionista.php");
                    break;
                case 'Enfermeiro(a)':
                header("Location: ../Enfermeiro/painel_enfermeiro.php");
                    break;
                default:
                 $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Tipo de usuário desconhecido.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                    header("Location:../index.php");
            }

        } else 
        {
            $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Credenciais inválidas.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            header("Location:../index.php");
        }
    }
}

?>