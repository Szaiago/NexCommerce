<?php
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";

// Conexão com o banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_produto = $_POST['id_produto'] ?? '';

    if (empty($id_produto)) {
        $_SESSION['mensagem'] = "<span style='color: red;'>Erro: ID do produto não fornecido.</span>";
        header("Location: ../pages/editar.php");
        exit();
    }

    // Verifica se o produto existe no banco de dados antes de deletar
    $sql_verifica = "SELECT id_produto FROM produtos WHERE id_produto = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    $stmt_verifica->bind_param("i", $id_produto);
    $stmt_verifica->execute();
    $result_verifica = $stmt_verifica->get_result();

    if ($result_verifica->num_rows === 0) {
        $_SESSION['mensagem'] = "<span style='color: red;'>Erro: Produto não encontrado.</span>";
        header("Location: ../pages/editar.php");
        exit();
    }

    $stmt_verifica->close();

    // Se existir, procede com a exclusão
    $sql = "DELETE FROM produtos WHERE id_produto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_produto);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "<span style='color: green;'>Produto deletado com sucesso!</span>";
    } else {
        $_SESSION['mensagem'] = "<span style='color: red;'>Erro ao deletar produto: " . $stmt->error . "</span>";
    }

    $stmt->close();
} else {
    $_SESSION['mensagem'] = "<span style='color: red;'>Método inválido!</span>";
}

$conn->close();
header("Location: ../pages/editar.php");
exit();
?>
