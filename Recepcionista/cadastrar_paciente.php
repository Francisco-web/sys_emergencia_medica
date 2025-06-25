<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Paciente.php';

$auth = new Autenticacao();

$dashboard = new DashboardHelper();

$paciente = new Paciente();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Recepcionista') {
    header("Location: ../index.php");
    exit;
}

$titulo = "Painel Recepcionista";

require_once '../core/header.php';

//menu
require_once '../core/menu.php';

//cadastrar paciente
if (isset($_POST['btn_salvar']) && $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $nome =  htmlspecialchars($_POST['nome_completo']);
    $genero = htmlspecialchars($_POST['genero']);
    $tipo_documento = htmlspecialchars($_POST['tipo_documento']);
    $documento_numero = htmlspecialchars($_POST['documento_numero']); 
    $data_nascimento = htmlspecialchars($_POST['data_nascimento']); 
    $telefone = htmlspecialchars($_POST['telefone']);

    //atendimento
    $prioridade = htmlspecialchars($_POST['prioridade']); 
    $atendido = htmlspecialchars($_POST['atendido']);

    // Validação 
    if (empty($nome)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>O nome é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($tipo_documento)) {
       $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Tipo de documento é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($documento_numero)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Número do documento é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_nascimento)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Data de nascimento inválida. Use o formato YYYY-MM-DD.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (!preg_match('/^\+?[0-9]{7,15}$/', $telefone)) {
       $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Número de telefone inválido.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($prioridade)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Prioridade de Atendimento não selecionada.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($atendido)) {
       $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Seleciona o estado de atendiemnto do paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{


        if ($paciente->existePaciente($nome, $documento_numero,isset($id_paciente))) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Paciente já cadastrado.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {
            $resultado = $paciente->criar($nome, $genero, $tipo_documento, $documento_numero, $data_nascimento, $telefone,$prioridade,$atendido);

            if ($resultado) {
                $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Paciente cadastrado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            } else {
                $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao cadastrar o paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
              <h4 class="fw-bold py-3 mb-4">Paciente</h4>
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
                      <h5 class="mb-0">Novo Atendimento de Paciente</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">		

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Dados do Paciente</label>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Nome Completo</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nome_completo" id="basic-default-name" placeholder="John Doe" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-company">Data de nascimento</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" name="data_nascimento" id="basic-default-company" min="1900-01-01" max="<?= date('Y-m-d') ?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Genero</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select name="genero" id="basic-default-email" class="form-control" id="">
                                            <option value="">Seleciona</option>
                                            <option value="Femenino">Femenino</option>
                                            <option value="Masculino">Masculino</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Telefone</label>
                            <div class="col-sm-10">
                                <input type="number" id="basic-default-phone" name="telefone" class="form-control phone-mask" placeholder="958 799 8941" aria-label="658 799 8941" aria-describedby="basic-default-phone" minlength="9" maxlength="12"/>
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Documento de Identificação</label>
                            <div class="col-sm-10">
                                <select name="tipo_documento" id="" class="form-control phone-mask">
                                    <option value="">Seleciona</option>
                                    <option value="Bilhete de Identidade">B.I</option>
                                    <option value="Passaporte">Passaporte</option>
                                    <option value="Cédula">Cédula</option>
                                </select>
                            </div>
                            </div>
                             <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Documento Nº</label>
                            <div class="col-sm-10">
                                <input type="text" id="basic-default-phone" name="documento_numero" class="form-control phone-mask" placeholder="00799894LA076" aria-label="00799894LA076" aria-describedby="basic-default-phone" minlength="9" maxlength="14"/>
                            </div>
                            </div>

                            <hr class="my-5">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Dados de Atendimento</label>
                            </div>
                            
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Prioridade de Atendimento</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select name="prioridade" class="form-control" id="">
                                            <option value="">Seleciona</option>
                                            <option value="Alta">Alta</option>
                                            <option value="Baixa">Baixa</option>
                                            <option value="Média">Média</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                            
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Atendido?</label>
                            <div class="col-sm-10">
                                <select name="atendido" id="" class="form-control phone-mask">
                                    <option value="">Seleciona</option>
                                    <option value="Não atendido">Ainda Não</option>
                                    <option value="Atendido">Sim, Atendido</option>
                                </select>
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
