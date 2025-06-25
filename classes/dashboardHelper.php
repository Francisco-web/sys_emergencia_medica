<?php

class DashboardHelper {
    private $conexao;

    public function __construct() {
          $this->conexao = Database::Conexao();
    }

    // Para todos
    public function totalAtendimentosHoje(): int {
        $stmt = $this->conexao->prepare("SELECT COUNT(*) FROM atendimentos WHERE date(data_entrada) = CURDATE()");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // Admin
    public function contarUsuariosPorTipo(): array {
        $stmt = $this->conexao->query("SELECT tipo, COUNT(*) as total FROM usuarios GROUP BY tipo");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recepcionista
    public function contarPacientesHoje(): int {
        $stmt = $this->conexao->prepare("SELECT COUNT(*) FROM pacientes WHERE DATE(data_cadastro) = CURDATE()");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }


    // Enfermeiro
    public function contarPacientesAtendidos(): int {
        $stmt = $this->conexao->prepare("SELECT COUNT(*) FROM atendimentos WHERE DATE(data_entrada) = CURDATE() and atendido = 'Atendido'");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function listarTriagensPrioridadeAlta(): array {
        $stmt = $this->conexao->prepare("SELECT p.nome, t.prioridade, t.data FROM triagens t 
                                     JOIN pacientes p ON t.paciente_id = p.id 
                                     WHERE t.prioridade = 'Alta' AND DATE(t.data) = CURDATE()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Médico
    public function pacientesAguardandoAtendimento(): int {
        $stmt = $this->conexao->prepare("SELECT COUNT(*) FROM atendimentos WHERE atendido = 'Não atendido'");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function atendimentosDoDiaPorMedico($medico_id): int {
        $stmt = $this->conexao->prepare("SELECT COUNT(*) FROM atendimentos WHERE medico_id = ? AND DATE(data) = CURDATE()");
        $stmt->execute([$medico_id]);
        return (int) $stmt->fetchColumn();
    }

    public function listarUltimosAtendimentos($medico_id, $limite = 5): array {
        $stmt = $this->conexao->prepare("SELECT p.nome, a.diagnostico, a.data 
                                     FROM atendimentos a 
                                     JOIN pacientes p ON a.paciente_id = p.id 
                                     WHERE a.medico_id = ? 
                                     ORDER BY a.data DESC LIMIT ?");
        $stmt->bindValue(1, $medico_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
