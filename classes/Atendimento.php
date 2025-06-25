<?php
class Atendimento {
    private $conexao;

    public function __construct() {
        $this->conexao = Database::Conexao();
    }

    // Regra de negócio: Verificar duplicação por nome + data de nascimento
    public function pacienteAtendido($nome, $documento_numero, $id_paciente){
        
        if(isset($_POST['id_paciente']))
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
    public function criar($nome, $genero, $tipo_documento, $documento_numero, $data_nascimento, $telefone):bool {

        $stmt = $this->conexao->prepare("INSERT INTO `pacientes`(`nome`, `data_nascimento`, `genero`, `telefone`, `tipo_documento`, `id_documento`) 
                                     VALUES (:nome, :data_nascimento, :genero, :telefone, :tipo_documento,:id_documento)");
        $stmt->bindParam(':nome',$nome, PDO::PARAM_STR);
        $stmt->bindParam(':data_nascimento',$data_nascimento, PDO::PARAM_STR);
        $stmt->bindParam(':genero',$genero, PDO::PARAM_STR);
        $stmt->bindParam(':telefone',$telefone, PDO::PARAM_STR);
        $stmt->bindParam(':tipo_documento',$tipo_documento, PDO::PARAM_STR);
        $stmt->bindParam(':id_documento',$documento_numero, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Atualizar paciente
    public function atualizar($id_atendimento, $prioridade, $atendido, $sinais_vitais, $sintomas){
        
        $stmt = $this->conexao->prepare("UPDATE `atendimentos` SET `sintomas`=:sintomas,`sinais_vitais`=:sinais_vitais,`data_saida`=NOW(),`prioridade`=:prioridade,`atendido`=:atendido
        WHERE id = :id");
        $stmt->bindParam(':prioridade',$prioridade, PDO::PARAM_STR);
        $stmt->bindParam(':atendido',$atendido, PDO::PARAM_STR);
        $stmt->bindParam(':sinais_vitais',$sinais_vitais, PDO::PARAM_STR);
        $stmt->bindParam(':sintomas',$sintomas, PDO::PARAM_STR);
        $stmt->bindParam(':id',$id_atendimento, PDO::PARAM_STR);
        return $stmt->execute();
    }

      public function criarFicha($fichaNumero, $id_atendimento, $id_enfermeiro):bool {

        $stmt = $this->conexao->prepare("INSERT INTO `ficha_de_atendimento`(`ficha`, `id_atendimento`, `id_enfermeiro`) 
                                     VALUES (:fichaNumero, :id_atendimeto, :id_enfermeiro)");
        $stmt->bindParam(':fichaNumero',$fichaNumero, PDO::PARAM_STR);
        $stmt->bindParam(':id_enfermeiro',$id_enfermeiro, PDO::PARAM_INT);
        $stmt->bindParam(':id_atendimeto',$id_atendimento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Buscar paciente por ID
    public function buscar($id) {
        $stmt = $this->conexao->prepare("SELECT * FROM atendimentos WHERE id = :id");
        $stmt->bindParam(':id',$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar todos os pacientes
    public function listar() {
        $stmt = $this->conexao->query("SELECT atm.id,atm.prioridade,atm.sintomas,atm.fichaNumero,atm.sinais_vitais,atm.data_entrada,atm.data_saida,atm.atendido,pc.nome,pc.data_nascimento,pc.genero FROM atendimentos atm join pacientes pc ON atm.paciente_id = pc.id ORDER BY prioridade ,data_entrada DESC");
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

