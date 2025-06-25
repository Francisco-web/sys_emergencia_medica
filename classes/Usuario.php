<?php
class Usuario {
    private $conexao;

    public function __construct() {
        $this->conexao = Database::Conexao();
    }

    // Regra de negócio: Verificar duplicação por nome + data de nascimento
    public function existeUsuario($email, $id_usuario){
        
        if($id_usuario)
        {
            // Verifica se o número do documento já está cadastrado
            $check = $this->conexao->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id_usuario");
            $check->bindParam(':email', $email);
            $check->bindParam(':id_usuario', $id_usuario);

        }else {
            // Verifica se o número do documento já está cadastrado
            $check = $this->conexao->prepare("SELECT COUNT(*) FROM pacientes WHERE email = :email");
            
            $check->bindParam(':email', $email);
        }
        
        $check->execute();

        if ($check->fetchColumn() > 0) {
            // Documento já cadastrado
            return $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Este Usuário já está cadastrado.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }
    }

    public function existeSenha($senha_antiga,$id_usuario){
        
        // Verifica se o número do documento já está cadastrado
        $check = $this->conexao->prepare("SELECT * FROM usuarios WHERE id = :id_usuario");
        $check->bindParam(':id_usuario', $id_usuario);
        $check->execute();
        $dado_senha = $check->fetch(PDO::FETCH_ASSOC);

        if (password_verify($senha_antiga,$dado_senha['senha'])) {
            return true;
        }else {
            return false;
        }
    }

    // Criar novo paciente
    public function criar($nome, $telefone,$email,$senha,$perfil):bool {
        
        $stmt = $this->conexao->prepare("INSERT INTO `pacientes`(`nome`, `data_nascimento`, `genero`, `telefone`, `tipo_documento`, `id_documento`) 
                                     VALUES (:nome, :data_nascimento, :genero, :telefone, :tipo_documento,:id_documento)");
        $stmt->bindParam(':nome',$nome, PDO::PARAM_STR);
        $stmt->bindParam(':senha',$senha, PDO::PARAM_STR);
        $stmt->bindParam(':perfil',$perfil, PDO::PARAM_STR);
        $stmt->bindParam(':telefone',$telefone, PDO::PARAM_STR);
        $stmt->bindParam(':email',$email, PDO::PARAM_STR);
        return $stmt->execute();

    }

    // Atualizar paciente
    public function atualizar($id_usuario,$nome, $email,$telefone,$senha,$perfil) {
        
        if ($id_usuario && $telefone && $nome && $email) {

            $stmt = $this->conexao->prepare("UPDATE `usuarios` SET `nome`=:nome,`email`=:email,`telefone`=:telefone WHERE id = :id");
            $stmt->bindParam(':nome',$nome, PDO::PARAM_STR);
            $stmt->bindParam(':telefone',$telefone, PDO::PARAM_STR);
            $stmt->bindParam(':email',$email, PDO::PARAM_STR);
            $stmt->bindParam(':id',$id_usuario, PDO::PARAM_INT);
            return $stmt->execute();

        }else if ($id_usuario && $senha) {
            // Atualiza apenas a senha
            $stmt = $this->conexao->prepare("UPDATE `usuarios` SET `senha`=:senha WHERE id = :id");
            $stmt->bindParam(':senha',$senha, PDO::PARAM_STR);
            $stmt->bindParam(':id',$id_usuario, PDO::PARAM_INT);
            return $stmt->execute();
        }else {

            $stmt = $this->conexao->prepare("UPDATE `usuarios` SET `nome`=:nome,`email`=:email,`telefone`=:telefone,`senha`=:senha,`perfil`=:perfil
            WHERE id = :id");
            $stmt->bindParam(':nome',$nome, PDO::PARAM_STR);
            $stmt->bindParam(':senha',$senha, PDO::PARAM_STR);
            $stmt->bindParam(':perfil',$perfil, PDO::PARAM_STR);
            $stmt->bindParam(':telefone',$telefone, PDO::PARAM_STR);
            $stmt->bindParam(':email',$email, PDO::PARAM_STR);
            $stmt->bindParam(':id',$id_usuario, PDO::PARAM_INT);
            return $stmt->execute();
        }
      
        
    }

    // Buscar paciente por ID
    public function buscar($id) {
        $stmt = $this->conexao->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id',$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar todos os pacientes
    public function listar() {
        $stmt = $this->conexao->query("SELECT * FROM pacientes ORDER BY data_cadastro DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Deletar paciente
    public function deletar($id) {
        $stmt = $this->conexao->prepare("DELETE FROM pacientes WHERE id =:id");
        $stmt->bindParam(':id',$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Marcar paciente como atendido
    public function marcarAtendido($id) {
        $stmt = $this->conexao->prepare("UPDATE pacientes SET atendido = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Contar pacientes não atendidos
    public function contarAguardando() {
        $stmt = $this->conexao->query("SELECT COUNT(*) FROM pacientes WHERE atendido = 0");
        return $stmt->fetchColumn();
    }

    // Listar pacientes por prioridade (Alta)
    public function listarAltaPrioridade() {
        $stmt = $this->conexao->query("SELECT * FROM pacientes WHERE prioridade = 'Alta' AND atendido = 0 ORDER BY data_cadastro ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

