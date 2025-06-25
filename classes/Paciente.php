<?php
class Paciente {
    private $conexao;

    public function __construct() {
        $this->conexao = Database::Conexao();
    }

    // Regra de negócio: Verificar duplicação por nome + data de nascimento
    public function existePaciente($nome, $documento_numero, $id_paciente){
        
        if($id_paciente)
        {
            // Verifica se o número do documento já está cadastrado
            $check = $this->conexao->prepare("SELECT COUNT(*) FROM pacientes WHERE id_documento = :documento_numero AND nome = :nome AND id != :id_paciente");
            $check->bindParam(':documento_numero', $documento_numero);
            $check->bindParam(':nome', $nome);
            $check->bindParam(':id_paciente', $id_paciente);

        }else {
            // Verifica se o número do documento já está cadastrado
            $check = $this->conexao->prepare("SELECT COUNT(*) FROM pacientes WHERE id_documento = :documento_numero AND nome = :nome");
            $check->bindParam(':documento_numero', $documento_numero);
            $check->bindParam(':nome', $nome);
        }
        
        $check->execute();

        if ($check->fetchColumn() > 0) {
            // Documento já cadastrado
            return $_SESSION['erro']="<div class='alert alert-danger alert-dismissible' role='alert'>Documento já está cadastrado.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        }
    }

    // Criar novo paciente
    public function criar($nome, $genero, $tipo_documento, $documento_numero, $data_nascimento, $telefone,$prioridade,$atendido):bool {
        

        $stmt = $this->conexao->prepare("INSERT INTO `pacientes`(`nome`, `data_nascimento`, `genero`, `telefone`, `tipo_documento`, `id_documento`) 
                                     VALUES (:nome, :data_nascimento, :genero, :telefone, :tipo_documento,:id_documento)");
        $stmt->bindParam(':nome',$nome, PDO::PARAM_STR);
        $stmt->bindParam(':data_nascimento',$data_nascimento, PDO::PARAM_STR);
        $stmt->bindParam(':genero',$genero, PDO::PARAM_STR);
        $stmt->bindParam(':telefone',$telefone, PDO::PARAM_STR);
        $stmt->bindParam(':tipo_documento',$tipo_documento, PDO::PARAM_STR);
        $stmt->bindParam(':id_documento',$documento_numero, PDO::PARAM_STR);
        $stmt->execute();

        $ultimo_id_paciente= $this->conexao->lastInsertId();

        //registrar atendidmento
        $stmt = $this->conexao->prepare("INSERT INTO `atendimentos`(`paciente_id`, `data_entrada`, `prioridade`, `atendido`) 
                                     VALUES (:id_paciente, Now(),:prioridade,:atendido)");
        $stmt->bindParam(':id_paciente',$ultimo_id_paciente, PDO::PARAM_INT);
        $stmt->bindParam(':prioridade',$prioridade, PDO::PARAM_STR);
        $stmt->bindParam(':atendido',$atendido, PDO::PARAM_STR);

        return $stmt->execute();

    }

    // Atualizar paciente
    public function atualizar($id_paciente, $nome, $genero, $tipo_documento, $documento_numero, $data_nascimento, $telefone) {
        
        $stmt = $this->conexao->prepare("UPDATE `pacientes` SET `nome`=:nome,`data_nascimento`=:data_nascimento,
        `genero`=:genero,`telefone`=:telefone,`tipo_documento`=:tipo_documento,`id_documento`=:id_documento,`actualizado_em`=Now()
        WHERE id = :id");
        $stmt->bindParam(':nome',$nome, PDO::PARAM_STR);
        $stmt->bindParam(':data_nascimento',$data_nascimento, PDO::PARAM_STR);
        $stmt->bindParam(':genero',$genero, PDO::PARAM_STR);
        $stmt->bindParam(':telefone',$telefone, PDO::PARAM_STR);
        $stmt->bindParam(':tipo_documento',$tipo_documento, PDO::PARAM_STR);
        $stmt->bindParam(':id_documento',$documento_numero, PDO::PARAM_STR);
        //$stmt->bindParam(':data_actual', $data_actual, PDO::PARAM_STR);
        $stmt->bindParam(':id',$id_paciente, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Buscar paciente por ID
    public function buscar($id) {
        $stmt = $this->conexao->prepare("SELECT * FROM pacientes WHERE id = :id");
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

