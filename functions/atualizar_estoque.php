<?php
session_start();

// Configurações do banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";

// Criar conexão com o banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die(json_encode(["error" => "Falha na conexão com o banco de dados."]));
}

// Configurar charset para evitar problemas com caracteres especiais
$conn->set_charset("utf8");

// Consulta SQL para obter os produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

// Inicializar variáveis para armazenar os totais
$total_peso_kg = 0;
$total_quantidade = 0;
$total_valor = 0;

// Calcular os totais do estoque
while ($row = $result->fetch_assoc()) {
    $total_peso_kg += ($row['peso_produto'] * $row['quantidade_produto']) / 1000; // Convertendo de gramas para kg
    $total_quantidade += $row['quantidade_produto'];
    $total_valor += $row['valor_produto'] * $row['quantidade_produto'];
}

// Retornar os dados como JSON
echo json_encode([
    "total_peso_kg" => number_format($total_peso_kg, 2, ',', '.'),
    "total_quantidade" => $total_quantidade,
    "total_valor" => number_format($total_valor, 2, ',', '.')
]);

$conn->close();
?>
