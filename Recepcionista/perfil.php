<?php
require '../classes/Autenticacao.php';
require_once '../classes/DashboardHelper.php';
require_once '../classes/Usuario.php';

$auth = new Autenticacao();

$dashboard = new DashboardHelper();

$usuario = new Usuario();

if (!$auth->estaAutenticado() || $auth->tipoUsuario() !== 'Recepcionista') {
    header("Location: ../index.php");
    exit;
}

$titulo = "Painel Recepcionista";

require_once '../core/header.php';

//menu
require_once '../core/menu.php';

//cadastrar paciente
if (isset($_POST['salvar_alteracao']) && $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $nome =  htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $telefone = htmlspecialchars($_POST['telefone']);
    $id_usuario = htmlspecialchars($_POST['id']);

    // Validação 
    if (empty($nome)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>O nome é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($email)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Email é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (!preg_match('/^\+?[0-9]{7,15}$/', $telefone)) {
       $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>Número de telefone inválido.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        if ($usuario->existeUsuario($email, $id_usuario)) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Usuário já cadastrado.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {
            $resultado = $usuario->atualizar($id_usuario,$nome, $email,$telefone,isset($senha),isset($perfil));

            if ($resultado) {
                $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Dados do Usuário Actualizado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            } else {
                $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao Actdalizar Dados do Usuário.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            }    
        }
    }

}else if (isset($_POST['salvar_alteracao_senha']) && $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $senha_antiga =  htmlspecialchars($_POST['senhaAntiga']);
    $nova_senha = htmlspecialchars($_POST['novaSenha']);
    $id_usuario = htmlspecialchars($_POST['id']);

    // Validação 
    if (empty($senha_antiga)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>A senha antiga é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }elseif (empty($nova_senha)) {
        $_SESSION['erro'] = "<div class='alert alert-danger alert-dismissible' role='alert'>A senha Nova é obrigatório.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    }else{

        if ($nova_senha == $senha_antiga) {
            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Você inseriu a senha antiga, cria uma nova senha. Tenta novamente!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        
        }else if(!$usuario->existeSenha($senha_antiga,$id_usuario)){

            $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Esta senha não corresponde a senha antiga.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }else {

          $senha = password_hash($nova_senha, PASSWORD_DEFAULT);

          $resultado = $usuario->atualizar($id_usuario,isset($nome), isset($email),isset($telefone),$senha,isset($perfil));

          if ($resultado) {
              $_SESSION['sucesso']= "<div class='alert alert-success alert-dismissible' role='alert'>Nova senha actualizada com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
          } else {
              $_SESSION['erro']= "<div class='alert alert-danger alert-dismissible' role='alert'>Erro ao actualizar nova senha.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
              <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Definição de Conta /</span> Conta</h4>
                <?php
                    if (isset($_SESSION["sucesso"])) {
                        echo $_SESSION["sucesso"];
                        unset ($_SESSION["sucesso"]); 
                    }elseif (isset($_SESSION["erro"])) {
                        echo $_SESSION["erro"];
                        unset ($_SESSION["erro"]);
                    }
                ?>
              <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Perfil</a>
                    </li>
                  </ul>
                  <div class="card mb-4">
                    <h5 class="card-header">Detalhes</h5>
                    <!-- Account -->
                    <div class="card-body">
                      <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                          src="../assets/img/avatars/1.png"
                          alt="user-avatar"
                          class="d-block rounded"
                          height="100"
                          width="100"
                          id="uploadedAvatar"
                        />
                        <div class="button-wrapper">
                          <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Actualizar Foto de Perfil</span>
                            <i class="bx bx-upload d-block d-sm-none"></i>
                            <input
                              type="file"
                              id="upload"
                              class="account-file-input"
                              hidden
                              accept="image/png, image/jpeg"
                            />
                          </label>
                          <button type="reset" class="btn btn-outline-secondary account-image-reset mb-4">
                            <i class="bx bx-reset d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                          </button>

                          <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                        </div>
                      </div>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body">
                        <?php
                            $id = $auth->UsuarioID();
                            $dados_usuario = $usuario->buscar($id);
                        ?>
                      <form id="formAccountSettings" method="POST" onsubmit="return true">
                        <div class="row">
                            <input type="hidden" name="id" value="<?php echo $id;?>">
                            <input type="hidden" name="perfil" value="<?php echo $dados_usuario['perfil']?>">
                          <div class="mb-3 col-md-6">
                            <label for="firstName" class="form-label">Nome</label>
                            <input
                              class="form-control"
                              type="text"
                              id="firstName"
                              name="nome"
                              autofocus
                              value="<?php echo $dados_usuario['nome']?>"
                            />
                          </div>
                          <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input
                              class="form-control"
                              type="text"
                              id="email"
                              name="email"
                              placeholder="john.doe@gmail.com"
                              value="<?php echo $dados_usuario['email']?>"
                            />
                          </div>
                          <div class="mb-3 col-md-6">
                            <label class="form-label" for="phoneNumber">Telefone</label>
                            <div class="input-group input-group-merge">
                              <span class="input-group-text">AO (+244)</span>
                              <input
                                type="text"
                                id="phoneNumber"
                                name="telefone"
                                class="form-control"
                                placeholder="202 555 0111"
                                value="<?php echo $dados_usuario['telefone']?>"
                              />
                            </div>
                          </div>
                        <div class="mt-2">
                          <button type="submit" name="salvar_alteracao" class="btn btn-primary me-2">Salvar Alteração</button>
                          <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
                        </div>
                      </form>
                    </div>
                    <!-- /Account -->
                  </div>
                </div>

                 <div class="col-md-12">
                  <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-lock me-1"></i> Mudar Senha</a>
                    </li>
                  </ul>
                  <div class="card mb-4">
                   
                    <!-- senha -->
                    
                    <div class="card-body">
                        <?php
                          $id = $auth->UsuarioID();
                          $dados_usuario = $usuario->buscar($id);
                        ?>
                      <form id="formAccountSettings" method="POST" onsubmit="return true">
                        <div class="row">
                            <input type="hidden" name="id" value="<?php echo $id;?>">

                            <div class="mb-3 col-md-6">
                            <label for="firstName" class="form-label">Senha Actual</label>
                            <input
                              class="form-control"
                              type="password"
                              id=""
                              name="senhaAntiga"
                              autofocus
                              placeholder="***************"
                            />
                          </div>
                          <div class="mb-3 col-md-6">
                            <label for="novaSenha" class="form-label">Nova Senha</label>
                            <input
                              class="form-control"
                              type="password"
                              id="novaSenha"
                              name="novaSenha"
                              placeholder="***************"
                            />
                          </div>
                          </div>
                        <div class="mt-2">
                          <button type="submit" name="salvar_alteracao_senha" class="btn btn-primary me-2">Salvar Alteração</button>
                          <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
                        </div>
                      </form>
                    </div>
                    <!-- /Account -->
                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

<?php require_once '../core/footer.php';?>
