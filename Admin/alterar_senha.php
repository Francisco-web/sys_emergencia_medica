<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Atendimento.php';
require_once '../classes/Usuario.php';

$usuario = new Usuario();

$auth = new Autenticacao();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Administrador') {
    header("Location: ../index.php");
    exit;
}

$titulo = "Painel Recepcionista";

require_once '../core/header.php';

//menu
require_once '../core/menu.php';

//cadastrar paciente
if (isset($_POST['btn_actualizar']) && $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $senha =  htmlspecialchars($_POST['senha']);
    $id_usuario = htmlspecialchars($_POST['id']);

    // Validação 
    if (empty($senha)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>A senha é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        if ($usuario->existeUsuario($email, $id_usuario)) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Usuario não encontrado.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {
             
            $resultado = $usuario->atualizar($id_usuario, isset($nome), isset($email), isset($telefone), password_hash($senha, PASSWORD_DEFAULT), isset($perfil));
            if ($resultado) {
                $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Senha do Usuário actualizado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            } else {
                $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao Actualizar a senha do usuário.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            }    
        }
    }

}elseif (isset($_POST['btn_actualizar_atendimento']) && $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $prioridade =  htmlspecialchars($_POST['prioridade']);
    $atendido = htmlspecialchars($_POST['atendido']);
    $id_atendimento = htmlspecialchars($_POST['id']);
    

    // Validação 
    if (empty($prioridade)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Prioridade de Atendimento não selecionada.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($atendido)) {
       $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Seleciona o estado de atendiemnto do paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{
             
        $resultado = $Atendimento->atualizar($id_atendimento, $prioridade, $atendido, isset($sinais_vitais), isset($sintomas));

        if ($resultado) {
            $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Dados do Paciente actualizado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        } else {
            $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao Actualizar os dados do paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
             <?php
                $fila_paciente = htmlspecialchars(isset($_GET['fila']));

             ?>
            <div class="container-xxl flex-grow-1 container-p-y">
                <h4 class="fw-bold py-3 mb-4">Alterar dados do Paciente</h4>
                <?php
                    if (isset($_SESSION["sucesso"])) {
                        echo $_SESSION["sucesso"];
                        unset ($_SESSION["sucesso"]); 
                    }elseif (isset($_SESSION["erro"])) {
                        echo $_SESSION["erro"];
                        unset ($_SESSION["erro"]);
                    }
                    
                    $id = htmlspecialchars($_GET["id"]);
                    $verPaciente = $usuario->buscar($id);
                ?>
                <!-- Basic Layout & Basic with Icons -->
                <div class="row">
                <!-- Basic Layout -->
                <div class="col-xxl">
                  <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="mb-0"></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">		

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Dados Pessoais</label>
                            </div>
                            <input type="hidden" name="id" id="" value="<?php echo $verPaciente['id'] ?>">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Senha</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="senha" id="basic-default-name" placeholder="******"/>
                            </div>
                            </div>

                            <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" name="btn_actualizar" class="btn btn-primary">Actualizar</button>
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
