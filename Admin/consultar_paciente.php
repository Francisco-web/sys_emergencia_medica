<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Paciente.php';

$auth = new Autenticacao();

$dashboard = new DashboardHelper();

$paciente = new Paciente();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Administrador') {
    header("Location: ../index.php");
    exit;
}

$titulo = "Painel Recepcionista";

require_once '../core/header.php';

//menu
require_once '../core/menu.php';

//cadastrar paciente
if (isset($_POST['excluirPaciente']) && $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $id =  htmlspecialchars($_POST['id']);

    // Validação 
    if (empty($id)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>O ID é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        if (!$paciente->buscar($id)) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Este ID não corresponde a nenhum Paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {
            $resultado = $paciente->deletar($id);

            if ($resultado) {
                $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Paciente Excluído com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            } else {
                $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao Excluir o paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
              <h4 class="fw-bold py-3 mb-4">Lista de Pacientes</h4>
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
                            <h5 class="card-header">Todos os Pacientes Cadastrados</h5>
                            <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Data de nascimento</th>
                                    <th>Genero</th>
                                    <th>Telefone</th>
                                    <th>Documento</th>
                                    <th>Documento Nº</th>
                                    <th>Data Cadastro</th>
                                    <th>Acção</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php
                                        $dados_pacientes = $paciente->listar();
                                        foreach ($dados_pacientes as $cada_paciente) {
                                    ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $cada_paciente['nome']?></strong></td>
                                        <td><?php echo $cada_paciente['data_nascimento']?></td>
                                        <td><?php echo $cada_paciente['genero']?></td>
                                        <td><span class="badge bg-label-primary me-1"><?php echo $cada_paciente['telefone']?></span></td>
                                        <td><?php echo $cada_paciente['tipo_documento']?></td>
                                        <td><?php echo $cada_paciente['id_documento']?></td>
                                        <td><?php echo $cada_paciente['data_cadastro']?></td>
                                        <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                            <a class="dropdown-item" href="alterar_dados_paciente.php?id=<?php echo $cada_paciente['id']?>"
                                                ><i class="bx bx-edit-alt me-1"></i> Alterar</a
                                            >
                                            <form action="" method="post">
                                                <input type="hidden" name="id" value="<?php echo $cada_paciente['id']?>">
                                                <button name="excluirPaciente" class="dropdown-item" onclick="return confirm('Tens certeza que desejas excluir?')"
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
