<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Atendimento.php';
require_once '../classes/Paciente.php';

$paciente = new Paciente();

$auth = new Autenticacao();

$dashboard = new DashboardHelper();

$Atendimento = new Atendimento();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Medico(a)') {
    header("Location: ../index.php");
    exit;
}

$titulo = "Alterar dados do Paciente";

require_once '../core/header.php';

//menu
require_once '../core/menu.php';

if (isset($_POST['btn_actualizar_atendimento']) && $_SERVER["REQUEST_METHOD"] == "POST" ) 
{
    $prioridade =  htmlspecialchars($_POST['prioridade']);
    $atendido = htmlspecialchars($_POST['atendido']);
    $sinais_vitais = htmlspecialchars($_POST['sinais_vitais']);
    $sintomas = htmlspecialchars($_POST['sintomas']);

    $id_atendimento = htmlspecialchars($_POST['id']);
    $id_enfermeiro = $auth->UsuarioID();

    // Validação 
    if (empty($prioridade)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Prioridade de Atendimento não selecionada.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($atendido)) {
       $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Seleciona o estado de atendiemnto do paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        $resultado = $Atendimento->atualizar($id_atendimento, $prioridade, $atendido, $sinais_vitais, $sintomas);

        if ($resultado) {
            
            $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Dados do Paciente actualizado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        } else {
            $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao Actualizar os dados do paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }
    }

} else if (isset($_POST['btn_atender'])  && $_SERVER["REQUEST_METHOD"] == "POST" ) 
{
    $prioridade =  htmlspecialchars($_POST['prioridade']);
    $atendido = htmlspecialchars($_POST['atendido']);
    $sinais_vitais = htmlspecialchars($_POST['sinais_vitais']);
    $sintomas = htmlspecialchars($_POST['sintomas']);

    $id_atendimento = htmlspecialchars($_POST['id']);
    $id_enfermeiro = $auth->UsuarioID();

    // Validação 
    if (empty($prioridade)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Prioridade de Atendimento não selecionada.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($atendido)) {
       $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Seleciona o estado de atendiemnto do paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        $resultado = $Atendimento->atender($id_atendimento, $id_enfermeiro,$prioridade, $atendido, $sinais_vitais, $sintomas);

        if ($resultado) {
            
            $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Atendimento ao Paciente realizado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        } else {
            $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao registrar o Atendimento do Paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }
    }

}else if (isset($_POST['btn_dar_alta'])  && $_SERVER["REQUEST_METHOD"] == "POST" ) 
{
    $atendido = "Atendido";

    $id_atendimento = htmlspecialchars($_POST['id']);

    // Validação 
    if (empty($id_atendimento)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Ficha de atendimento inválido.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        $resultado = $Atendimento->darAuta($id_atendimento, $atendido);

        if ($resultado) {
            
            $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'> O Paciente recebeu alta médica com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        } else {
            $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao dar alta médica ao Paciente.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
            $fila_paciente = htmlspecialchars(isset($_GET['atender']));

            if ($fila_paciente) {
                
            ?>
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">A realizar Atendimento</h4>
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

                    $verPaciente = $paciente->buscar($verAtendimento['paciente_id']);
                ?>
              <!-- Basic Layout & Basic with Icons -->
              <div class="row">
                <!-- Basic Layout -->
                <div class="col-xxl">
                  <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="mb-0">Paciente: <?php echo $verPaciente['nome'] ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">		
                            <input type="hidden" name= "id" value="<?php echo $verAtendimento['id'] ?>">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-company">Ficha Número</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="fichaNumero" readonly id="basic-default-company" value="<?php echo $verAtendimento['fichaNumero'] ?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-company">Data de nascimento</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" name="data_nascimento" readonly id="basic-default-company" min="1900-01-01" value="<?php echo $verPaciente['data_nascimento'] ?>" max="<?= date('Y-m-d') ?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-company">Idade</label>
                            <div class="col-sm-10">
                                <?php
                                    $dataNascimento = new DateTime($verPaciente['data_nascimento']);
                                    $dataAtual = new DateTime(); // data de hoje

                                    $intervalo = $dataAtual->diff($dataNascimento);

                                    // Exibe no formato: "anos,meses"
                                    $idade = $intervalo->y . ',' . $intervalo->m;
                                ?>
                                <input type="text" class="form-control" name="idade" id="basic-default-company" readonly value="<?php echo $idade; ?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Genero</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select name="genero" id="basic-default-email" class="form-control" id="" disabled>
                                            <option value="Femenino" <?php if($verPaciente['genero'] == "Femenino" ){echo "SELECTED";}?>>Femenino</option>
                                            <option value="Masculino" <?php if($verPaciente['genero'] == "Masculino" ){echo "SELECTED";}?>>Masculino</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Telefone</label>
                            <div class="col-sm-10">
                                <input type="number" id="basic-default-phone" name="telefone" class="form-control phone-mask" value="<?php echo $verPaciente['telefone'];?>" placeholder="958 799 8941" aria-label="658 799 8941" aria-describedby="basic-default-phone" minlength="9" readonly/>
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Documento de Identificação</label>
                            <div class="col-sm-10">
                                <select name="tipo_documento" id="" class="form-control phone-mask" disabled>
                                    <option value="Bilhete de Identidade" <?php if($verPaciente['tipo_documento'] == "Bilhete de Identidade" ){echo "SELECTED";}?>>B.I</option>
                                    <option value="Passaporte" <?php if($verPaciente['tipo_documento'] == "Passaporte" ){echo "SELECTED";}?>>Passaporte</option>
                                    <option value="Cédula" <?php if($verPaciente['tipo_documento'] == "Cédula" ){echo "SELECTED";}?>>Cédula</option>
                                </select>
                            </div>
                            </div>
                              <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Documento Nº</label>
                            <div class="col-sm-10">
                                <input type="text" id="basic-default-phone" name="documento_numero" class="form-control phone-mask" value="<?php echo $verPaciente['id_documento'];?>" placeholder="00799894LA076" aria-label="00799894LA076" aria-describedby="basic-default-phone" minlength="9" readonly/>
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Dados de Consulta Médica</label>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">sintomas</label>
                                <div class="col-sm-10">
                                    <textarea name="sintomas" id="" class="form-control" placeholder="Dor de cabeça, Náuseas, Tontura, Fadiga, Ansiedade,etc..."><?php echo $verAtendimento['sintomas'];?></textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Sinais vitais</label>
                                <div class="col-sm-10">
                                    <textarea name="sinais_vitais" id="" class="form-control" placeholder="Temperatura corporal, Frequência cardíaca (batimentos por minuto),Frequência respiratória (respirações por minuto), Pressão arterial, Saturação de oxigênio,etc..."><?php echo $verAtendimento['sinais_vitais'];?></textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Prioridade de Atendimento</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select name="prioridade" class="form-control" id="">
                                            <option value="Por definir" <?php if($verAtendimento['prioridade'] == 'Por definir'){echo "SELECTED";} ?>>Não definido</option>
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
                                    <option value="Não Atendido" <?php if($verAtendimento['atendido'] == 'Não Atendido'){echo "SELECTED";} ?>>Ainda Não</option>
                                    <option value="Em atendimento" <?php if($verAtendimento['atendido'] == 'Em atendimento'){echo "SELECTED";} ?>>Em Atendimento</option>
                                </select>
                            </div>
                            </div>
                            <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" name="btn_atender" class="btn btn-primary">Atender</button>
                                <a class="btn btn-secondary" href="fila_de_paciente.php?meusPacientes">Voltar</a>
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
                }else {
            ?> 
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-4">Actualizar dados de atendimento</h4>
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

                    $verPaciente = $paciente->buscar($verAtendimento['paciente_id']);
                ?>
              <!-- Basic Layout & Basic with Icons -->
              <div class="row">
                <!-- Basic Layout -->
                <div class="col-xxl">
                  <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="mb-0">Paciente: <?php echo $verPaciente['nome'] ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">		
                            <input type="hidden" name= "id" value="<?php echo $verAtendimento['id'] ?>">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-company">Ficha Número</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="fichaNumero" readonly id="basic-default-company" value="<?php echo $verAtendimento['fichaNumero'] ?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-company">Data de nascimento</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" name="data_nascimento" readonly id="basic-default-company" min="1900-01-01" value="<?php echo $verPaciente['data_nascimento'] ?>" max="<?= date('Y-m-d') ?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-company">Idade</label>
                            <div class="col-sm-10">
                                <?php
                                    $dataNascimento = new DateTime($verPaciente['data_nascimento']);
                                    $dataAtual = new DateTime(); // data de hoje

                                    $intervalo = $dataAtual->diff($dataNascimento);

                                    // Exibe no formato: "anos,meses"
                                    $idade = $intervalo->y . ',' . $intervalo->m;
                                ?>
                                <input type="text" class="form-control" name="idade" id="basic-default-company" readonly value="<?php echo $idade; ?>" />
                            </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Genero</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select name="genero" id="basic-default-email" class="form-control" id="" disabled>
                                            <option value="Femenino" <?php if($verPaciente['genero'] == "Femenino" ){echo "SELECTED";}?>>Femenino</option>
                                            <option value="Masculino" <?php if($verPaciente['genero'] == "Masculino" ){echo "SELECTED";}?>>Masculino</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Telefone</label>
                            <div class="col-sm-10">
                                <input type="number" id="basic-default-phone" name="telefone" class="form-control phone-mask" value="<?php echo $verPaciente['telefone'];?>" placeholder="958 799 8941" aria-label="658 799 8941" aria-describedby="basic-default-phone" minlength="9" readonly/>
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Documento de Identificação</label>
                            <div class="col-sm-10">
                                <select name="tipo_documento" id="" class="form-control phone-mask" disabled>
                                    <option value="Bilhete de Identidade" <?php if($verPaciente['tipo_documento'] == "Bilhete de Identidade" ){echo "SELECTED";}?>>B.I</option>
                                    <option value="Passaporte" <?php if($verPaciente['tipo_documento'] == "Passaporte" ){echo "SELECTED";}?>>Passaporte</option>
                                    <option value="Cédula" <?php if($verPaciente['tipo_documento'] == "Cédula" ){echo "SELECTED";}?>>Cédula</option>
                                </select>
                            </div>
                            </div>
                              <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Documento Nº</label>
                            <div class="col-sm-10">
                                <input type="text" id="basic-default-phone" name="documento_numero" class="form-control phone-mask" value="<?php echo $verPaciente['id_documento'];?>" placeholder="00799894LA076" aria-label="00799894LA076" aria-describedby="basic-default-phone" minlength="9" readonly/>
                            </div>
                            </div>
                            <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="basic-default-phone">Dados de Consulta Médica</label>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">sintomas</label>
                                <div class="col-sm-10">
                                    <textarea name="sintomas" id="" class="form-control" placeholder="Dor de cabeça, Náuseas, Tontura, Fadiga, Ansiedade,etc..."><?php echo $verAtendimento['sintomas'];?></textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Sinais vitais</label>
                                <div class="col-sm-10">
                                    <textarea name="sinais_vitais" id="" class="form-control" placeholder="Temperatura corporal, Frequência cardíaca (batimentos por minuto),Frequência respiratória (respirações por minuto), Pressão arterial, Saturação de oxigênio,etc..."><?php echo $verAtendimento['sinais_vitais'];?></textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-default-email">Prioridade de Atendimento</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                        <select name="prioridade" class="form-control" id="">
                                            <option value="Por definir" <?php if($verAtendimento['prioridade'] == 'Por definir'){echo "SELECTED";} ?>>Não definido</option>
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
                                    <option value="Não Atendido" <?php if($verAtendimento['atendido'] == 'Não Atendido'){echo "SELECTED";} ?>>Ainda Não</option>
                                    <option value="Atendido" <?php if($verAtendimento['atendido'] == 'Atendido'){echo "SELECTED";} ?>>Sim, Atendido</option>
                                    <option value="Em atendimento" <?php if($verAtendimento['atendido'] == 'Em atendimento'){echo "SELECTED";} ?>>Em Atendimento</option>
                                </select>
                            </div>
                            </div>
                            <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" name="btn_actualizar_atendimento" class="btn btn-primary">Actualizar</button>
                                <?PHP
                                    if($verAtendimento['atendido'] == 'Em atendimento'){
                                        echo '<button type="submit" name="btn_dar_alta" class="btn btn-info">Dar alta Médica</button>';
                                    }
                                ?>
                                <a class="btn btn-secondary" href="fila_de_paciente.php">Voltar</a>
                            </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>    
            <?php
                }
            ?> 
                 
                     
<?php require_once '../core/footer.php';?>
