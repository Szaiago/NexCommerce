<?php
// Conexão com o banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";

// Criar a conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Configurar o charset
$conn->set_charset("utf8");

// Receber os dados do formulário
$nome_usuario = trim($_POST['nome_usuario']);
$email_usuario = trim($_POST['email_usuario']);
$senha_usuario = trim($_POST['senha_usuario']);
$senha_confirm = trim($_POST['confirm_senha']);

// Validar os dados recebidos
if (empty($nome_usuario) || empty($email_usuario) || empty($senha_usuario) || empty($senha_confirm)) {
    die("Por favor, preencha todos os campos.");
}

// Verificar se as senhas coincidem
if ($senha_usuario !== $senha_confirm) {
    die("As senhas não coincidem. Tente novamente.");
}

// Hash da senha para maior segurança
$senha_hashed = password_hash($senha_usuario, PASSWORD_BCRYPT);

// Verificar se o email é empresarial
$email_empresarial_usuario = null;
if (str_ends_with($email_usuario, "@nextcommerce.com")) {
    $email_empresarial_usuario = $email_usuario;
}

// Inserir os dados no banco de dados
$stmt = $conn->prepare("INSERT INTO usuario (nome_usuario, email_usuario, senha_usuario, email_empresarial_usuario) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome_usuario, $email_usuario, $senha_hashed, $email_empresarial_usuario);

if ($stmt->execute()) {
    echo "Cadastro realizado com sucesso!";

    // Redirecionar o usuário para a página correspondente
    if ($email_empresarial_usuario) {
        header("Location: ../pages/sistema_empresarial.php");
    } else {
        header("Location: ../pages/sistema_pessoal.php");
    }
    exit(); // Certificar-se de que o script termina após o redirecionamento
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>
