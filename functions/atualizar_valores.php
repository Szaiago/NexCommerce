<?php
session_start();

// Verifique se o usuário está logado
$user_id = $_SESSION['id_usuario']; 
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";
$conn = new mysqli($host, $usuario, $senha, $banco);
$conn->set_charset("utf8");

// Calcule os valores
$total_compra = 0;
$sql_produtos_carrinho = "SELECT p.valor_produto, c.quantidade 
                          FROM carrinho c 
                          INNER JOIN produtos p ON c.id_produto = p.id_produto 
                          WHERE c.id_usuario = ?";
$stmt_produtos_carrinho = $conn->prepare($sql_produtos_carrinho);
$stmt_produtos_carrinho->bind_param("i", $user_id);
$stmt_produtos_carrinho->execute();
$result_produtos_carrinho = $stmt_produtos_carrinho->get_result();
while ($row_produto = $result_produtos_carrinho->fetch_assoc()) {
    $total_compra += $row_produto['valor_produto'] * $row_produto['quantidade'];
}

// Tabela de valores do frete por estado
$valoresFrete = [
    "SC" => 10.00, "PR" => 12.00, "RS" => 15.00,
    "SP" => 18.00, "RJ" => 22.00, "MG" => 25.00,
    "ES" => 28.00, "BA" => 30.00, "PE" => 35.00,
    "CE" => 38.00, "GO" => 32.00, "DF" => 33.00,
    "MT" => 40.00, "MS" => 38.00, "PA" => 42.00,
    "AM" => 45.00, "MA" => 39.00, "PI" => 36.00,
    "RO" => 44.00, "AC" => 50.00, "RR" => 55.00,
    "AP" => 48.00, "AL" => 37.00, "SE" => 35.00,
    "PB" => 39.00, "RN" => 40.00, "TO" => 43.00
];

// Consultando os dados de entrega na tabela dados_adicionais
$sql_dados_entrega = "SELECT * FROM dados_adicionais WHERE id_usuario = ?";
$stmt_dados_entrega = $conn->prepare($sql_dados_entrega);
$stmt_dados_entrega->bind_param("i", $user_id);
$stmt_dados_entrega->execute();
$result_dados_entrega = $stmt_dados_entrega->get_result();

// Verificando se há dados de entrega cadastrados
$dados_entrega = $result_dados_entrega->fetch_assoc();

// Determinando o valor do frete com base no estado
$frete = 0;
if ($dados_entrega && isset($valoresFrete[$dados_entrega['estado']])) {
    $frete = $valoresFrete[$dados_entrega['estado']];
}

// Calculando o valor total final (produtos + frete)
$total_final = $total_compra + $frete;

// Retorne os valores em formato JSON
echo json_encode([
    'total_produtos' => number_format($total_compra, 2, ',', '.'),
    'frete' => number_format($frete, 2, ',', '.'),
    'total_pagar' => number_format($total_final, 2, ',', '.')
]);
?>
