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

    // Atualizar paciente
    public function atualizar($id_atendimento, $prioridade, $atendido, $sinais_vitais, $sintomas){
        
        $stmt = $this->conexao->prepare("UPDATE `atendimentos` SET `sintomas`=:sintomas,`sinais_vitais`=:sinais_vitais,`prioridade`=:prioridade,`atendido`=:atendido
        WHERE id = :id");
        $stmt->bindParam(':prioridade',$prioridade, PDO::PARAM_STR);
        $stmt->bindParam(':atendido',$atendido, PDO::PARAM_STR);
        $stmt->bindParam(':sinais_vitais',$sinais_vitais, PDO::PARAM_STR);
        $stmt->bindParam(':sintomas',$sintomas, PDO::PARAM_STR);
        $stmt->bindParam(':id',$id_atendimento, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function atender($id_atendimento, $id_enfermeiro,$prioridade, $atendido, $sinais_vitais, $sintomas) {

        $stmt = $this->conexao->prepare("UPDATE `atendimentos` SET `sintomas`=:sintomas,`sinais_vitais`=:sinais_vitais,`data_saida`=NOW(),`prioridade`=:prioridade,`atendido`=:atendido
        WHERE id = :id");
        $stmt->bindParam(':prioridade',$prioridade, PDO::PARAM_STR);
        $stmt->bindParam(':atendido',$atendido, PDO::PARAM_STR);
        $stmt->bindParam(':sinais_vitais',$sinais_vitais, PDO::PARAM_STR);
        $stmt->bindParam(':sintomas',$sintomas, PDO::PARAM_STR);
        $stmt->bindParam(':id',$id_atendimento, PDO::PARAM_STR);
        
        if($stmt->execute()){
         $stmt = $this->conexao->prepare("INSERT INTO `ficha_de_atendimento`(`id_atendimento`, `id_usuario`) 
                                        VALUES (:id_atendimeto, :id_usuario)");
        $stmt->bindParam(':id_usuario',$id_enfermeiro, PDO::PARAM_INT);
        $stmt->bindParam(':id_atendimeto',$id_atendimento, PDO::PARAM_INT);
        return $stmt->execute();
        }
        return false;
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
        $stmt = $this->conexao->query("SELECT atm.id,atm.prioridade,atm.sintomas,atm.fichaNumero,atm.sinais_vitais,atm.data_entrada,atm.data_saida,atm.atendido,pc.nome,pc.data_nascimento,pc.genero FROM atendimentos atm join pacientes pc ON atm.paciente_id = pc.id ORDER BY atm.data_entrada DESC, atm.prioridade DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPacientesNaoAtendidos() {
        $stmt = $this->conexao->query("SELECT atm.id,atm.prioridade,atm.sintomas,atm.fichaNumero,atm.sinais_vitais,atm.data_entrada,atm.data_saida,atm.atendido,pc.nome,pc.data_nascimento,pc.genero FROM atendimentos atm join pacientes pc ON atm.paciente_id = pc.id WHERE atendido ='Não Atendido' ORDER BY prioridade ,data_entrada DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function listarPacientesAtendidos() {
        $stmt = $this->conexao->query("SELECT atm.id,atm.prioridade,atm.sintomas,atm.fichaNumero,atm.sinais_vitais,atm.data_entrada,atm.data_saida,atm.atendido,pc.nome,pc.data_nascimento,pc.genero,us.nome as profissional FROM ficha_de_atendimento fa join atendimentos atm ON fa.id_atendimento = atm.id join pacientes pc ON atm.paciente_id = pc.id join usuarios us ON fa.id_usuario = us.id WHERE atendido !='Não Atendido' ORDER BY prioridade DESC ,data_entrada DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function MeusPacientes($id) {
        $sql = "SELECT atm.id, atm.prioridade, atm.sintomas, atm.fichaNumero, atm.sinais_vitais, atm.data_entrada, atm.data_saida, atm.atendido, pc.nome, pc.data_nascimento, pc.genero 
            FROM ficha_de_atendimento fa 
            JOIN atendimentos atm ON fa.id_atendimento = atm.id 
            JOIN pacientes pc ON atm.paciente_id = pc.id 
            WHERE id_usuario = :id_enfermeiro 
            ORDER BY prioridade DESC, data_entrada DESC";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id_enfermeiro', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Deletar paciente
    public function deletar($id) {
        $stmt = $this->conexao->prepare("DELETE FROM pacientes WHERE id =:id");
        $stmt->bindParam(':id',$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

   public function darAuta($id_atendimento, $atendido) {
        $stmt = $this->conexao->prepare("UPDATE `atendimentos` SET `data_saida`=NOW(), `atendido`=:atendido  WHERE id = :id");
        $stmt->bindParam(':atendido',$atendido, PDO::PARAM_STR);
        $stmt->bindParam(':id',$id_atendimento, PDO::PARAM_INT);
        return $stmt->execute();
    }

}

