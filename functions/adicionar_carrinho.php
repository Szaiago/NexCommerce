<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['nome_usuario'])) {
    echo json_encode(["success" => false, "message" => "Usuário não logado"]);
    exit();
}

// Configurações do banco de dados
$host   = "localhost";
$usuario = "root";
$senha  = "";
$banco  = "NextCommerce";

// Conectar com o banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Falha na conexão com o banco de dados"]);
    exit();
}
$conn->set_charset("utf8");

if (isset($_POST['id_produto'], $_POST['quantidade'])) {
    $id_produto = $_POST['id_produto'];
    $quantidade = $_POST['quantidade'];
    $id_usuario = $_SESSION['id_usuario']; // Certifique-se de que o id_usuario está na sessão
    $preco = 0; // Inicializa o preço
    $img_produto = ""; // Inicializa a variável para a imagem

    // Consulta para obter o preço e a imagem do produto
    $sql = "SELECT valor_produto, img1_produto FROM produtos WHERE id_produto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_produto);
    $stmt->execute();
    $stmt->bind_result($preco, $img_produto);
    $stmt->fetch();
    $stmt->close();

    // Verificar se o usuário já possui o produto no carrinho
    $sql_check = "SELECT id_carrinho FROM carrinho WHERE id_usuario = ? AND id_produto = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $id_usuario, $id_produto);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        // O usuário já tem o produto no carrinho, então atualiza a quantidade
        $sql_update = "UPDATE carrinho SET quantidade = quantidade + ? WHERE id_usuario = ? AND id_produto = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iii", $quantidade, $id_usuario, $id_produto);
        $stmt_update->execute();
        $stmt_update->close();
        
        echo json_encode(["success" => true, "message" => "Produto atualizado no carrinho!"]);
    } else {
        // O usuário não tem o produto no carrinho, então cria um novo registro
        $sql_insert = "INSERT INTO carrinho (id_usuario, id_produto, quantidade, preco, img_produto) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiids", $id_usuario, $id_produto, $quantidade, $preco, $img_produto);
        $stmt_insert->execute();
        $stmt_insert->close();
        
        echo json_encode(["success" => true, "message" => "Produto adicionado ao carrinho!"]);
    }
    
    $stmt_check->close();
} else {
    echo json_encode(["success" => false, "message" => "Dados do produto não fornecidos"]);
}

$conn->close();
?>
