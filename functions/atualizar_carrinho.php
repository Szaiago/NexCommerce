<?php
session_start();

// Verificando se o usuário está logado
if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Nome do usuário logado
$nome_usuario = $_SESSION['nome_usuario'];

// Limitar o nome a duas palavras
$nome_partes = explode(" ", $nome_usuario);
$nome_exibido = $nome_partes[0];
if (isset($nome_partes[1])) {
    $nome_exibido .= " " . $nome_partes[1];
}

// Configurações do banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";

// Criar conexão com o banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Configurar charset para evitar problemas com caracteres especiais
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_carrinho = intval($_POST["id"]);
    $operacao = $_POST["operacao"];

    // Busca a quantidade atual
    $sql = "SELECT quantidade FROM carrinho WHERE id_carrinho = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_carrinho);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) {
        echo json_encode(["success" => false]);
        exit();
    }

    $nova_quantidade = ($operacao == "aumentar") ? $row["quantidade"] + 1 : max(1, $row["quantidade"] - 1);

    // Atualiza o banco de dados
    $sql_update = "UPDATE carrinho SET quantidade = ? WHERE id_carrinho = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $nova_quantidade, $id_carrinho);
    $stmt_update->execute();

    echo json_encode(["success" => true, "nova_quantidade" => $nova_quantidade]);
}
?>
