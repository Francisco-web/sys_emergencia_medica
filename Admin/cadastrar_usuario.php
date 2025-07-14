<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Usuario.php';

$auth = new Autenticacao();

$dashboard = new DashboardHelper();

$usuario = new Usuario();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Administrador') {
    header("Location: ../index.php");
    exit;
}

$titulo = "Painel Administrador";

require_once '../core/header.php';

//menu
require_once '../core/menu.php';

//cadastrar paciente
if (isset($_POST['btn_salvar']) && $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $nome =  htmlspecialchars($_POST['nome_completo']);
    $telefone = htmlspecialchars($_POST['telefone']);
    $email = htmlspecialchars($_POST['email']);
    $senha = htmlspecialchars($_POST['senha']); 
    $perfil = htmlspecialchars($_POST['perfil']); 

    // Validação 
    if (empty($nome)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>O nome é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($perfil)) {
       $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Perfil do Usuário é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($senha)) {
       $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Senha é obrigatória.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($email)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Email é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (!preg_match('/^\+?[0-9]{7,15}$/', $telefone)) {
       $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Número de telefone inválido.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{


        if ($usuario->existeUsuario($email, null)) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Usuário já cadastrado.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {
            $resultado = $usuario->criar($nome, $telefone, $email, password_hash($senha, PASSWORD_DEFAULT), $perfil);

            if ($resultado) {
                $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Usuário cadastrado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            } else {
                $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao cadastrar Usuário.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            }    
        }
    }

}

?>
        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

            <?php require_once '../core/navbar.php';?>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">Cadastrar Usuário</h4>
                <?php
                    if (isset($_SESSION["sucesso"])) {
                        echo $_SESSION["sucesso"];
                        unset ($_SESSION["sucesso"]); 
                    }elseif (isset($_SESSION["erro"])) {
                        echo $_SESSION["erro"];
                        unset ($_SESSION["erro"]);
                    }
                ?>
              <!-- Basic Layout & Basic with Icons -->
              <div class="row">
                <!-- Basic Layout -->
                <div class="col-xxl">
                  <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="mb-0">Novo usuário</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">		

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Dados Pessoais</label>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Nome Completo</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nome_completo" id="basic-default-name" placeholder="John Doe" />
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Telefone</label>
                            <div class="col-sm-10">
                                <input type="number" id="basic-default-phone" name="telefone" class="form-control phone-mask" placeholder="958 799 8941" aria-label="658 799 8941" aria-describedby="basic-default-phone" minlength="9" maxlength="12"/>
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Perfil</label>
                            <div class="col-sm-10">
                                <select name="perfil" id="" class="form-control phone-mask">
                                    <option value="">Seleciona</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Enfermeiro(a)">Enfermeiro(a)</option>
                                    <option value="Medico(a)">Médico(a)</option>
                                    <option value="Recepcionista">Recepcionista</option>
                                </select>
                            </div>
                            </div>
                             <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Dados de Acesso</label>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Email</label>
                            <div class="col-sm-10">
                                <input type="text" id="basic-default-phone" name="email" class="form-control phone-mask" placeholder="@" aria-describedby="basic-default-phone"/>
                            </div>
                            </div>

                             <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Senha</label>
                            <div class="col-sm-10">
                                <input type="password" id="basic-default-phone" name="senha" class="form-control" placeholder="********" aria-describedby="basic-default-phone" minlength="6" />
                            </div>
                            </div>

                            <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" name="btn_salvar" class="btn btn-primary">Salvar</button>
                            </div>
                            </div>
                            
                        </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

<?php require_once '../core/footer.php';?>
