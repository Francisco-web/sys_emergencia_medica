
Sistema de Gestão de Emergências Médicas – Pronto-Socorro

Visão Geral

Este projeto tem como objetivo desenvolver um sistema web simples e eficiente para o registro rápido de pacientes em situações de emergência médica, permitindo o gerenciamento por diferentes perfis de usuários, com controle de atendimentos, cadastros, relatórios e dashboards personalizados.

Tecnologias Utilizadas:

- PHP 8 (Programação Orientada a Objetos)
- MySQL
- FPDF (Geração de relatórios em PDF)
- Bootstrap 5 template (Interface responsiva e amigável)
- HTML/CSS/JavaScript (Interatividade básica)

Perfis de Usuários:

1. Recepcionista
2. Profissional de Saúde (Médico(a)/Enfermeiro(a))
3. Administrador

Regras de Negócio:

- Um paciente só pode ser atendido se tiver sido registrado.
- Cada atendimento é vinculado a um profissional específico.
- Pacientes podem ter múltiplos atendimentos registrados.
- O administrador pode ativar/desativar usuários do sistema.
- O logout destrói a sessão de forma segura.
- Dashboards exibem dados específicos por perfil.

Funcionalidades:

Autenticação
Gestão de Pacientes
Atendimento Médico
Dashboard por Perfil
Relatórios (PDF)

Modelo de Dados (Tabelas):

`usuarios`
`pacientes`
`atendimentos`
`diagnosticos`
`ficha_de_atendimento`

Instalação Local:

1. Clone o repositório
2. Configure o banco de dados (`sys_emergencia_medica.sql`) e atualize `/config/Database.php`
3. Inicie um servidor local
4. Acesse `http://localhost/sys_emergencia_medica/`

✅ Requisitos:

- PHP 8.0+
- MySQL 5.7+
- Extensões: `PDO`, `session`

Testes Manuais:

- Login com diferentes perfis
- Cadastro de paciente
- Fluxo de atendimento médico

Autor
**Francisco André**  
Estudante de Engenharia de Sistemas de Informação  
Desenvolvedor Back-End | PHP | MySQL  
[https://linkedin.com/in/francisco-andre-1a679a252]
