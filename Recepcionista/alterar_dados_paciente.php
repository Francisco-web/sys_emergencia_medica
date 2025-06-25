<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Atendimento.php';
require_once '../classes/Paciente.php';

$paciente = new Paciente();

$auth = new Autenticacao();

$dashboard = new DashboardHelper();

$Atendimento = new Atendimento();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Recepcionista') {
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
    $genero = htmlspecialchars($_POST['genero']);
    $tipo_documento = htmlspecialchars($_POST['tipo_documento']);
    $documento_numero = htmlspecialchars($_POST['documento_numero']); 
    $data_nascimento = htmlspecialchars($_POST['data_nascimento']); 
    $telefone = htmlspecialchars($_POST['telefone']);
    $id_paciente = htmlspecialchars( $_POST['id_paciente']); 

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
    }else{

        if ($paciente->existePaciente($nome, $documento_numero, $id_paciente)) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Paciente já cadastrado.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {
             
            $resultado = $paciente->atualizar($id_paciente,$nome, $genero, $tipo_documento, $documento_numero, $data_nascimento, $telefone);

            if ($resultado) {
                $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Dados do Paciente actualizado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            } else {
                $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao Actualizar os dados do paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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

                if ($fila_paciente) {
                    
             ?>
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">Alterar dados do Atendimento</h4>
                <?php
                    if (isset($_SESSION["sucesso"])) {
                        echo $_SESSION["sucesso"];
                        unset ($_SESSION["sucesso"]); 
                    }elseif (isset($_SESSION["erro"])) {
                        echo $_SESSION["erro"];
                        unset ($_SESSION["erro"]);
                    }
                    
                    $id = htmlspecialchars( $_GET["id"]);
                    
                    $verAtendimento = $Atendimento->buscar($id);
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
                            <input type="hidden" name= "id" value="<?php echo $verAtendimento['id'] ?>">
                             <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Prioridade de Atendimento</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select name="prioridade" class="form-control" id="">
                                            <option value="">Seleciona</option>
                                            <option value="Alta" <?php if($verAtendimento['prioridade'] == 'Alta'){echo "SELECTED";} ?>>Alta</option>
                                            <option value="Baixa" <?php if($verAtendimento['prioridade'] == 'Baixa'){echo "SELECTED";} ?>>Baixa</option>
                                            <option value="Média" <?php if($verAtendimento['prioridade'] == 'Média'){echo "SELECTED";} ?>>Média</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Atendido?</label>
                            <div class="col-sm-10">
                                <select name="atendido" id="" class="form-control phone-mask">
                                    <option value="">Seleciona</option>
                                    <option value="Não atendido" <?php if($verAtendimento['atendido'] == 'Não atendido'){echo "SELECTED";} ?>>Ainda Não</option>
                                    <option value="Atendido" <?php if($verAtendimento['atendido'] == 'Atendido'){echo "SELECTED";} ?>>Sim, Atendido</option>
                                    <option value="Em atendimento" <?php if($verAtendimento['atendido'] == 'Em atendimento'){echo "SELECTED";} ?>>Em Atendimento</option>
                                </select>
                            </div>
                            </div>
                            <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" name="btn_actualizar_atendimento" class="btn btn-primary">Actualizar</button>
                                <a class="btn btn-secondary" href="fila_de_paciente.php">Voltar</a>
                            </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->
            <?php
                }else{
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
                    $verPaciente = $paciente->buscar($id);
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
                            <input type="hidden" name= "id_paciente" value="<?php echo $verPaciente['id'] ?>">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Nome Completo</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nome_completo" id="basic-default-name" placeholder="John Doe" value="<?php echo $verPaciente['nome'] ?>"/>
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-company">Data de nascimento</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" name="data_nascimento" id="basic-default-company" min="1900-01-01" value="<?php echo $verPaciente['data_nascimento'] ?>" max="<?= date('Y-m-d') ?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Genero</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select name="genero" id="basic-default-email" class="form-control" id="">
                                            <option value="Femenino" <?php if($verPaciente['genero'] == "Femenino" ){echo "SELECTED";}?>>Femenino</option>
                                            <option value="Masculino" <?php if($verPaciente['genero'] == "Masculino" ){echo "SELECTED";}?>>Masculino</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Telefone</label>
                            <div class="col-sm-10">
                                <input type="number" id="basic-default-phone" name="telefone" class="form-control phone-mask" value="<?php echo $verPaciente['telefone'];?>" placeholder="958 799 8941" aria-label="658 799 8941" aria-describedby="basic-default-phone" minlength="9"/>
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Documento de Identificação</label>
                            <div class="col-sm-10">
                                <select name="tipo_documento" id="" class="form-control phone-mask">
                                    <option value="Bilhete de Identidade" <?php if($verPaciente['tipo_documento'] == "Bilhete de Identidade" ){echo "SELECTED";}?>>B.I</option>
                                    <option value="Passaporte" <?php if($verPaciente['tipo_documento'] == "Passaporte" ){echo "SELECTED";}?>>Passaporte</option>
                                    <option value="Cédula" <?php if($verPaciente['tipo_documento'] == "Cédula" ){echo "SELECTED";}?>>Cédula</option>
                                </select>
                            </div>
                            </div>
                             <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Documento Nº</label>
                            <div class="col-sm-10">
                                <input type="text" id="basic-default-phone" name="documento_numero" class="form-control phone-mask" value="<?php echo $verPaciente['id_documento'];?>" placeholder="00799894LA076" aria-label="00799894LA076" aria-describedby="basic-default-phone" minlength="9"/>
                            </div>
                            </div>
                            <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" name="btn_actualizar" class="btn btn-primary">Actualizar</button>
                                <a class="btn btn-secondary" href="consultar_paciente.php">Voltar</a>
                            </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
                </div>
            </div>
            <!-- / Content -->
            <?php
                }
            ?>            
<?php require_once '../core/footer.php';?>
