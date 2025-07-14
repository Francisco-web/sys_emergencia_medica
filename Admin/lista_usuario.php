<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Paciente.php';
require_once '../classes/Usuario.php';

$auth = new Autenticacao();

$dashboard = new DashboardHelper();

$usuario = new Usuario();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Administrador') {
    header("Location: ../index.php");
    exit;
}

$titulo = "Painel Recepcionista";

require_once '../core/header.php';

//menu
require_once '../core/menu.php';

//cadastrar paciente
if (isset($_POST['excluirUsuario']) && $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $id =  htmlspecialchars($_POST['id']);

    // Validação 
    if (empty($id)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>O ID é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        if (!$usuario->buscar($id)) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Este ID não corresponde a nenhum Paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {
            $resultado = $usuario->deletar($id);

            if ($resultado) {
                $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Usuário Excluído com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            } else {
                $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao Excluir o Usuário.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
              <h4 class="fw-bold py-3 mb-4">Lista de Usuários</h4>
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
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <!-- Basic Bootstrap Table -->
                        <div class="card">
                            <h5 class="card-header">Todos os Usuários Cadastrados</h5>
                            <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Perfil</th>
                                    <th>Estado</th>
                                    <th>Acção</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php
                                        $dados_pacientes = $usuario->listar();
                                        foreach ($dados_pacientes as $cada_paciente) {
                                    ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $cada_paciente['nome']?></strong></td>
                                        <td><?php echo $cada_paciente['email']?></td>
                                        <td><?php echo $cada_paciente['telefone']?></td>
                                        <td><span class="badge bg-label-primary me-1"><?php echo $cada_paciente['perfil']?></span></td>
                                        <td>
                                            <?php 
                                            if($cada_paciente['estado'] == 1)
                                            {
                                                echo '<span class="badge bg-label-primary me-1"> Activo </span>';
                                            }else {
                                                echo '<span class="badge bg-label-danger me-1">Inactivo</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                            <a class="dropdown-item" href="alterar_dados_usuario.php?id=<?php echo $cada_paciente['id']?>"
                                                ><i class="bx bx-edit-alt me-1"></i> Alterar</a
                                            >
                                            <a class="dropdown-item" href="alterar_senha.php?id=<?php echo $cada_paciente['id']?>"
                                                ><i class="bx bx-edit-alt me-1"></i> Alterar senha</a
                                            >
                                            <form action="" method="post">
                                                <input type="hidden" name="id" value="<?php echo $cada_paciente['id']?>">
                                                <button name="excluirUsuario" class="dropdown-item" onclick="return confirm('Tens certeza que desejas excluir?')"
                                                ><i class="bx bx-trash me-1"></i> Excluir</button>
                                            </form>
                                            </div>
                                        </div>
                                        </td>

                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                        <!--/ Basic Bootstrap Table -->
                    </div>
                </div>
            </div>
            <!-- / Content -->

<?php require_once '../core/footer.php';?>
