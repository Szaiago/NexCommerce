<?php
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

// Mensagem de sucesso para debug (opcional - pode ser comentada em produção)
// echo "Conexão com o banco de dados realizada com sucesso!";
?>
