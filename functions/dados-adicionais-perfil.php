<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['nome_usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = (int) $_SESSION['id_usuario'];

// Configurações do banco de dados
$host   = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "NextCommerce";

// Criar conexão com o banco de dados
$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Recebe os dados do formulário, incluindo o novo campo "rua"
$cpf         = $_POST['cpf']         ?? '';
$cep         = $_POST['cep']         ?? '';
$rua         = $_POST['rua']         ?? '';
$estado      = $_POST['estado']      ?? '';
$cidade      = $_POST['cidade']      ?? '';
$bairro      = $_POST['bairro']      ?? '';
$complemento = $_POST['complemento'] ?? '';

// Verifica se já existem dados adicionais para este usuário
$stmt = $conn->prepare("SELECT id FROM dados_adicionais WHERE id_usuario = ?");
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Atualiza os dados existentes
    $stmt->close();
    $stmt_update = $conn->prepare("UPDATE dados_adicionais SET cpf = ?, cep = ?, rua = ?, estado = ?, cidade = ?, bairro = ?, complemento = ? WHERE id_usuario = ?");
    if (!$stmt_update) {
        die("Erro na preparação da atualização: " . $conn->error);
    }
    $stmt_update->bind_param("sssssssi", $cpf, $cep, $rua, $estado, $cidade, $bairro, $complemento, $user_id);
    $stmt_update->execute();
    $stmt_update->close();
} else {
    // Insere os novos dados
    $stmt->close();
    $stmt_insert = $conn->prepare("INSERT INTO dados_adicionais (id_usuario, cpf, cep, rua, estado, cidade, bairro, complemento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt_insert) {
        die("Erro na preparação da inserção: " . $conn->error);
    }
    $stmt_insert->bind_param("isssssss", $user_id, $cpf, $cep, $rua, $estado, $cidade, $bairro, $complemento);
    $stmt_insert->execute();
    $stmt_insert->close();
}

$conn->close();

// Redireciona de volta para a página de dados adicionais com uma mensagem de sucesso
header("Location: ../pages/perfil.php?message=Dados+salvos+com+sucesso");
exit();
?>
