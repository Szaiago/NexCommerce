<?php
session_start();
if (!isset($_SESSION['nome_usuario'])) {
    echo "Acesso negado";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_produto']) && isset($_POST['quantidade_produto'])) {
    $id_produto = intval($_POST['id_produto']);
    $quantidade_produto = intval($_POST['quantidade_produto']);

    if ($id_produto <= 0 || $quantidade_produto < 0) {
        echo "Dados inválidos.";
        exit();
    }

    $conn = new mysqli("localhost", "root", "", "NextCommerce");

    if ($conn->connect_error) {
        echo "Erro na conexão com o banco.";
        exit();
    }

    $stmt = $conn->prepare("UPDATE produtos SET quantidade_produto = ? WHERE id_produto = ?");
    $stmt->bind_param("ii", $quantidade_produto, $id_produto);

    if ($stmt->execute()) {
        echo "Quantidade atualizada com sucesso!";
    } else {
        echo "Erro ao atualizar.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Requisição inválida.";
}
?>
