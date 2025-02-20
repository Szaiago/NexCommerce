<?php
session_start();

// Verificando se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['id_usuario'];
$nome_usuario = $_SESSION['nome_usuario'];

// Configurações do banco de dados
$host = "localhost";
$db_usuario = "root";
$db_senha = "";
$banco = "NextCommerce";

// Criar conexão com o banco de dados
$conn = new mysqli($host, $db_usuario, $db_senha, $banco);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Pegando os dados do carrinho: seleciona nome, valor e quantidade de cada produto
$sql_itens_carrinho = "SELECT c.id_carrinho, p.nome_produto, p.valor_produto, c.quantidade 
                       FROM carrinho c
                       INNER JOIN produtos p ON c.id_produto = p.id_produto
                       WHERE c.id_usuario = ?";
$stmt_itens_carrinho = $conn->prepare($sql_itens_carrinho);
$stmt_itens_carrinho->bind_param("i", $user_id);
$stmt_itens_carrinho->execute();
$result_itens_carrinho = $stmt_itens_carrinho->get_result();

$itens_pedido = [];
$total_compra = 0;
while ($row = $result_itens_carrinho->fetch_assoc()) {
    // Armazena o nome do produto seguido pela quantidade (ex.: "Tênis Dunk (2)")
    $itens_pedido[] = $row['nome_produto'] . " (" . $row['quantidade'] . ")";
    $total_compra += $row['valor_produto'] * $row['quantidade'];
}
// Junta os itens em uma única string separados por vírgula
$itens_pedido_str = implode(", ", $itens_pedido);

// Consultando os dados de entrega
$sql_dados_entrega = "SELECT * FROM dados_adicionais WHERE id_usuario = ?";
$stmt_dados_entrega = $conn->prepare($sql_dados_entrega);
$stmt_dados_entrega->bind_param("i", $user_id);
$stmt_dados_entrega->execute();
$result_dados_entrega = $stmt_dados_entrega->get_result();
$dados_entrega = $result_dados_entrega->fetch_assoc();

// Definindo o valor do frete com base no estado
$frete = 0;
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
if ($dados_entrega && isset($valoresFrete[$dados_entrega['estado']])) {
    $frete = $valoresFrete[$dados_entrega['estado']];
}

// Calculando o valor total do pedido
$total_final = $total_compra + $frete;

// Definindo o status do pedido
$status_pedido = 'Aguardando Pagamento';

// Inserindo o pedido no banco de dados  
// (A tabela "pedidos" deve possuir os campos: id_usuario, itens_pedido, valor_pedido, status_pedido, cpf, cep, cidade, bairro, rua, complemento)
$sql_insert_pedido = "INSERT INTO pedidos (id_usuario, itens_pedido, valor_pedido, status_pedido, cpf, cep, cidade, bairro, rua, complemento) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_insert_pedido = $conn->prepare($sql_insert_pedido);
$stmt_insert_pedido->bind_param(
    "isdsssssss", 
    $user_id, 
    $itens_pedido_str, 
    $total_final, 
    $status_pedido, 
    $dados_entrega['cpf'], 
    $dados_entrega['cep'], 
    $dados_entrega['cidade'], 
    $dados_entrega['bairro'], 
    $dados_entrega['rua'], 
    $dados_entrega['complemento']
);

if ($stmt_insert_pedido->execute()) {
    // Limpar o carrinho após finalizar o pedido
    $sql_limpar_carrinho = "DELETE FROM carrinho WHERE id_usuario = ?";
    $stmt_limpar_carrinho = $conn->prepare($sql_limpar_carrinho);
    $stmt_limpar_carrinho->bind_param("i", $user_id);
    $stmt_limpar_carrinho->execute();

    echo "Pedido finalizado com sucesso!";
    header("Location: ../pages/pagamento_pedido.php");
    exit();
} else {
    echo "Erro ao finalizar o pedido: " . $conn->error;
}

$stmt_insert_pedido->close();
$stmt_dados_entrega->close();
$stmt_itens_carrinho->close();
$conn->close();
?>
