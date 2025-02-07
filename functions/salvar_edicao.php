<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Conexão com o banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Verifica se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_produto = $_POST['id_produto'] ?? '';
    $marca = $_POST['marca_produto'] ?? '';
    $cor = $_POST['cor_produto'] ?? '';
    $categoria = $_POST['categoria_produto'] ?? '';
    $peso = $_POST['peso_produto'] ?? '';
    $material = $_POST['material_produto'] ?? '';
    $valor = $_POST['valor_produto'] ?? '';
    $descricao = $_POST['descricao_produto'] ?? '';

    // Verifica se o ID do produto foi enviado
    if (empty($id_produto)) {
        $_SESSION['mensagem'] = "<span style='color: red;'>Erro: ID do produto não fornecido.</span>";
        header("Location: ../editar.php");
        exit();
    }

    // Prepara a query de atualização
    $sql = "UPDATE produtos SET 
                marca_produto = ?, 
                cor_produto = ?, 
                categoria_produto = ?, 
                peso_produto = ?, 
                material_produto = ?, 
                valor_produto = ?, 
                descricao_produto = ?
            WHERE id_produto = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $_SESSION['mensagem'] = "<span style='color: red;'>Erro na preparação da consulta: " . $conn->error . "</span>";
        header("Location: ../editar.php");
        exit();
    }

    // Converte valores para números decimais
    $peso = floatval($peso);
    $valor = floatval($valor);

    // Faz o bind dos parâmetros e executa a query
    $stmt->bind_param("sssssdsi", $marca, $cor, $categoria, $peso, $material, $valor, $descricao, $id_produto);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "<span style='color: green;'>Produto atualizado com sucesso!</span>";
    } else {
        $_SESSION['mensagem'] = "<span style='color: red;'>Erro ao atualizar produto: " . $stmt->error . "</span>";
    }

    $stmt->close();
} else {
    $_SESSION['mensagem'] = "<span style='color: red;'>Método inválido!</span>";
}



// Fecha conexão
$conn->close();

// Redireciona para editar.php
header("Location: ../pages/editar.php");
exit();
?>
