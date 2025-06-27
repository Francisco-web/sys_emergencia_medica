<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';

$auth = new Autenticacao();

$dashboard = new DashboardHelper();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Medico(a)') {
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
              <div class="row">
                <div class="col-lg-8 mb-4 order-0">
                  <div class="card">
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-7">
                        <div class="card-body">
                          <h5 class="card-title text-primary">Seja Bem Vindo(a) sr.(a) <?= $auth->usuarioLogado(); ?>!</h5>
                          <p class="mb-4">
                           
                          </p>

                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                          <img
                            src="../assets/img/illustrations/man-with-laptop-light.png"
                            height="140"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 order-1">
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt6"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                <a class="dropdown-item" href="javascript:void(0);">Ver Mais</a>
                              </div>
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Pacientes Cadastrados</span>
                          <h3 class="card-title text-nowrap mb-1"><?=$dashboard->contarPacientesHoje();?></h3>
                        </div>
                      </div>
                    </div>
                     <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt6"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                              </div>
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Total de Atendimento</span>
                          <h3 class="card-title text-nowrap mb-1"><?=$dashboard->totalAtendimentosHoje();?></h3>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt3"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                              </div>
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Pacientes Atendidos</span>
                          <h3 class="card-title mb-2"><?=$dashboard->contarPacientesAtendidos();?></h3>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt6"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                              >
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                              </div>
                            </div>
                          </div>
                          <span class="fw-semibold d-block mb-1">Pacientes em espera</span>
                          <h3 class="card-title text-nowrap mb-1"><?=$dashboard->pacientesAguardandoAtendimento();?></h3>
                        </div>
                      </div>
                    </div>
                   
                    
                  </div>
                </div>
               
              </div>
            
            </div>
            <!-- / Content -->

<?php require_once '../core/footer.php';?>
