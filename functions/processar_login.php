<?php
session_start();  // Iniciar a sessão

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
$email_usuario = trim($_POST['email']);
$senha_usuario = trim($_POST['password']);

// Validar os dados recebidos
if (empty($email_usuario) || empty($senha_usuario)) {
    $_SESSION['LOGIN-ERRO'] = "Por favor, preencha todos os campos.";  // Definir mensagem de erro
    header("Location: ../index.php");
    exit();
}

// Buscar o usuário no banco de dados
$stmt = $conn->prepare("SELECT id, nome_usuario, senha_usuario, email_usuario FROM usuario WHERE email_usuario = ?");
$stmt->bind_param("s", $email_usuario);
$stmt->execute();
$stmt->store_result();

// Verificar se o e-mail existe no banco de dados
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id_usuario, $nome_usuario, $senha_hash, $email_usuario);
    $stmt->fetch();
    
    // Verificar se a senha está correta
    if (password_verify($senha_usuario, $senha_hash)) {
        // Armazenar o ID do usuário na sessão
        $_SESSION['id_usuario'] = $id_usuario;
        $_SESSION['nome_usuario'] = $nome_usuario;
        
        // Verificar se o email é empresarial
        if (str_ends_with($email_usuario, "@nextcommerce.com")) {
            // Redirecionar para o sistema empresarial
            header("Location: ../pages/sistema_empresarial.php");
        } else {
            // Redirecionar para o sistema pessoal
            header("Location: ../pages/sistema_pessoal.php");
        }
        exit();
    } else {
        $_SESSION['LOGIN-ERRO'] = "Senha incorreta.";  // Definir erro de senha
        header("Location: ../index.php");
        exit();
    }
} else {
    $_SESSION['LOGIN-ERRO'] = "E-mail não encontrado.";  // Definir erro de email não encontrado
    header("Location: ../index.php");
    exit();
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>
