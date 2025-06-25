<?php
require_once '../config/Database.php';


class Autenticacao {
    private $conexao;

    public function __construct() {
        session_start(); // Inicia a sessão
        $this->conexao = Database::Conexao();
    }

    public function login($email, $senha) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];
            
            return true;
        }

        return false;
    }

    public function tipoUsuario() {
        return $_SESSION['usuario_perfil'] ?? null;
    }

    public function estaAutenticado() {
        return isset($_SESSION['usuario_id']);
    }

    public function logout() {
        session_destroy();
    }

    public function usuarioLogado() {
        return $_SESSION['usuario_nome'] ?? null;
    }

    public function usuarioPerfil() {
        return $_SESSION['usuario_perfil'] ?? null;
    }
      public function UsuarioID() {
        $id = $_SESSION['usuario_id'];
        return $id;
    }

}
