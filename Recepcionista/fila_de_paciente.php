<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Atendimento.php';
require_once '../classes/Paciente.php';

$auth = new Autenticacao();

$dashboard = new DashboardHelper();



$atendimento = new Atendimento();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Recepcionista') {
    header("Location: ../index.php");
    exit;
}

$titulo = "Painel Recepcionista";

require_once '../core/header.php';

//menu
require_once '../core/menu.php';

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
              <h4 class="fw-bold py-3 mb-4">Lista de Pacientes em espera</h4>
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
                            <h5 class="card-header">Todos os Pacientes em espera</h5>
                            <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Data de nascimento</th>
                                    <th>Genero</th>
                                    <th>Prioridade</th>
                                    <th>Atendido?</th>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                    <th>Acção</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php
                                        $dados_pacientes_atendimento = $atendimento->listar();
                                        foreach ($dados_pacientes_atendimento as $cada_paciente_atendimento) {
                                    ?>
                                    <tr>
                                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $cada_paciente_atendimento['nome']?></strong></td>
                                        <td><?php echo $cada_paciente_atendimento['data_nascimento']?></td>
                                        <td><?php echo $cada_paciente_atendimento['genero']?></td>
                                        <td>
                                            <?php 
                                                if($cada_paciente_atendimento['prioridade'] == 'Alta')
                                                {
                                                    echo '<span class="badge bg-label-danger me-1">'. $cada_paciente_atendimento["prioridade"] .'</span>';
                                                }elseif ($cada_paciente_atendimento['prioridade'] == 'Média') {
                                                    echo '<span class="badge bg-label-warning me-1">'. $cada_paciente_atendimento["prioridade"] .'</span>';
                                                }elseif ($cada_paciente_atendimento['prioridade'] == 'Baixa') {
                                                    echo '<span class="badge bg-label-primary me-1">'. $cada_paciente_atendimento["prioridade"] .'</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if($cada_paciente_atendimento['atendido'] == 'Atendido')
                                            {
                                                echo '<span class="badge bg-label-primary me-1">'. $cada_paciente_atendimento["atendido"] .'</span>';
                                            }elseif ($cada_paciente_atendimento['atendido'] == 'Em atendimento') {
                                                echo '<span class="badge bg-label-success me-1">'. $cada_paciente_atendimento["atendido"] .'</span>';
                                            }elseif ($cada_paciente_atendimento['atendido'] == 'Não atendido') {
                                                echo '<span class="badge bg-label-danger me-1">'. $cada_paciente_atendimento["atendido"] .'</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $cada_paciente_atendimento['data_entrada']?></td>
                                        <td><?php echo $cada_paciente_atendimento['data_saida']?></td>
                                        <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                            <a class="dropdown-item" href="alterar_dados_paciente.php?id=<?php echo $cada_paciente_atendimento['id']?>&fila"
                                                ><i class="bx bx-edit-alt me-1"></i> Alterar</a
                                            >
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
