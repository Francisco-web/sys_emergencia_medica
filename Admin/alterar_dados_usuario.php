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
    $nome =  htmlspecialchars($_POST['nome_completo']);
    $telefone = htmlspecialchars($_POST['telefone']);
    $email = htmlspecialchars($_POST['email']);
    $perfil = htmlspecialchars($_POST['perfil']); 
    $id_usuario = htmlspecialchars($_POST['id']);

    // Validação 
    if (empty($nome)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>O nome é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($perfil)) {
       $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Perfil do Usuário é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($email)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Email é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (!preg_match('/^\+?[0-9]{7,15}$/', $telefone)) {
       $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Número de telefone inválido.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        if ($usuario->existeUsuario($email, $id_usuario)) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Usuario já cadastrado.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {
             
            $resultado = $usuario->atualizar($id_usuario, $nome, $email, $telefone, $senha=null, $perfil);
            if ($resultado) {
                $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Dados do Usuário actualizado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            } else {
                $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao Actualizar os dados do Usuário.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
                            <input type="text" name="id" id="" value="<?php echo $verPaciente['id'] ?>">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Nome Completo</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nome_completo" id="basic-default-name" placeholder="John Doe" value="<?php echo $verPaciente['nome']?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Telefone</label>
                            <div class="col-sm-10">
                                <input type="number" id="basic-default-phone" name="telefone" class="form-control phone-mask" value="<?php echo $verPaciente['telefone'] ?>" placeholder="958 799 8941" aria-label="658 799 8941" aria-describedby="basic-default-phone" minlength="9" maxlength="12"/>
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Perfil</label>
                            <div class="col-sm-10">
                                <select name="perfil" id="" class="form-control phone-mask">
                                    <option value="">Seleciona</option>
                                    <option value="Administrador" <?php  if($verPaciente['perfil'] == "Administrador"){echo "SELECTED";} ?>>Administrador</option>
                                    <option value="Enfermeiro(a)"  <?php  if($verPaciente['perfil'] == "Enfermeiro(a)"){echo "SELECTED";} ?>>Enfermeiro(a)</option>
                                    <option value="Medico(a)" <?php  if($verPaciente['perfil'] == "Medico(a)"){echo "SELECTED";} ?>>Médico(a)</option>
                                    <option value="Recepcionista" <?php  if($verPaciente['perfil'] == "Recepcionista"){echo "SELECTED";} ?>>Recepcionista</option>
                                </select>
                            </div>
                            </div>
                             <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Dados de Acesso</label>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Email</label>
                            <div class="col-sm-10">
                                <input type="text" id="basic-default-phone" name="email" value="<?php echo $verPaciente['email'] ?>" class="form-control phone-mask" placeholder="@" aria-describedby="basic-default-phone"/>
                            </div>
                            </div>

                            <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" name="btn_actualizar" class="btn btn-primary">Salvar</button>
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
