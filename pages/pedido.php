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

// Contando os itens no carrinho
$user_id = $_SESSION['id_usuario']; // ID do usuário logado
$sql_carrinho = "SELECT SUM(quantidade) as total_itens FROM carrinho WHERE id_usuario = ?";
$stmt_carrinho = $conn->prepare($sql_carrinho);
if ($stmt_carrinho === false) {
    die("Erro na preparação da consulta do carrinho: " . $conn->error);
}
$stmt_carrinho->bind_param("i", $user_id);
$stmt_carrinho->execute();
$result_carrinho = $stmt_carrinho->get_result();
$row_carrinho = $result_carrinho->fetch_assoc();
$total_itens_carrinho = $row_carrinho['total_itens'];

// Consulta SQL para obter os produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);


if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Pedido não encontrado.</p>";
    exit;
}

$id_pedido = intval($_GET['id']); // Converte para inteiro para evitar SQL Injection

// Consulta o pedido específico no banco de dados
$sql = "SELECT * FROM pedidos WHERE id_pedido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se o pedido foi encontrado
if ($result->num_rows == 0) {
    echo "<p>Pedido não encontrado.</p>";
    exit;
}

$pedido = $result->fetch_assoc();
$itens_pedido = explode(',', $pedido['itens_pedido']); // Transforma a string em um array

$produtos_info = [];

// Processa os itens removendo a quantidade para busca, mas mantendo a original para exibição
foreach ($itens_pedido as $item) {
    $nome_original = trim($item); // Nome com quantidade (ex: "Produto X (2)")
    $nome_produto = trim(preg_replace('/\s*\(\d+\)$/', '', $item)); // Nome sem quantidade para consulta

    if (!empty($nome_produto)) {
        // Consulta na tabela produtos para obter id_produto e img1_produto
        $sql_produto = "SELECT id_produto, img1_produto FROM produtos WHERE nome_produto = ?";
        $stmt_produto = $conn->prepare($sql_produto);
        $stmt_produto->bind_param("s", $nome_produto);
        $stmt_produto->execute();
        $result_produto = $stmt_produto->get_result();

        if ($result_produto->num_rows > 0) {
            $produto = $result_produto->fetch_assoc();
            $produtos_info[] = [
                'nome_original' => $nome_original, // Nome com quantidade para exibição
                'id' => $produto['id_produto'],
                'imagem' => $produto['img1_produto']
            ];
        }
        $stmt_produto->close();
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home/NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/fixo.css">
    <link rel="stylesheet" href="../css/pedidos.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../js/logoempresamain.js" defer></script>
    <script src="../js/modalperfil.js" defer></script>
    <script src="../js/carrosel-home.js" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <header>
        <div class="main">
            <div class="logo-main">
                <div class="logoempresa">
                    <img id="logo" src="../css/images/next1.png" alt="Logo Empresa">
                </div>
            </div>
            <div class="options">
                <div class="favoritos">
                    <a href="carrinho.php" class="favoritos"><i class="bi bi-bag" style="font-size:22px;"></i>
                    <?php if ($total_itens_carrinho > 0): ?>
                        <span class="badge"><?php echo $total_itens_carrinho; ?></span> <!-- Exibe o número de itens -->
                    <?php endif; ?>
                    </a>
                </div>
                <div class="noti">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="perfil" id="perfil">
                    <a href="perfil.php" class="perfil"><i class="bi bi-person"></i></a>
                </div>
            </div>
        </div>
    </header>
    <div class="modal" id="modal-perfil">
    <div class="modal-content">
        <p><?php echo htmlspecialchars($nome_exibido); ?></p>
        <form action="../functions/logout.php" method="POST" class="logout">
            <button type="submit" class="logout-btn">LOGOUT</button>
        </form>
    </div>
</div>
<div class="menu">
  <input type="checkbox" id="toggle" />
  <label id="show-menu" for="toggle">
    <div class="btn">
      <i class="material-icons md-36 toggleBtn menuBtn">menu</i>
      <i class="material-icons md-36 toggleBtn closeBtn">close</i>
    </div>
    <div class="btn">
        <a href="adicionar-produto.php"><i class="bi bi-plus-circle" style="color:black;"></i></a>
    </div>
    <div class="btn"  style="display:none;">
      <i class="material-icons md-36">photo</i>
    </div>
    <div class="btn" style="display:none;">
      <i class="material-icons md-36">music_note</i>
    </div>
    <div class="btn"  style="display:none;">
      <i class="material-icons md-36">chat_bubble</i>
    </div>
    <div class="btn" >
        <i class="bi bi-gear" style="color:black;"></i></i>
    </div>
    <div class="btn">
        <i class="bi bi-people" style="color:black;"></i></i>
    </div>
    <div class="btn">
        <a href="estoque.php"><i class="bi bi-box-seam" style="color:black;"></i></a>
    </div>
    <div class="btn">               
        <a href="editar.php"><i class="bi bi-pencil-square" style="color:black;"></i></a>
    </div>
  </label>
</div>
<div class="container-pedido">
    <div class="titulo-pedido">
        <p class="info"><strong>ID do Pedido:<?php echo htmlspecialchars($pedido['id_pedido']); ?></strong> </p>
    </div>
    <h3 class="titulo-itens-pedido">ITENS DO PEDIDO</h3>
    <div class="itens-container">
        <?php foreach ($produtos_info as $produto): ?>
            <div class="produto">
                <img src="../images/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome_original']); ?>">
                <p class="nome-item"><strong><?php echo htmlspecialchars($produto['nome_original']); ?></strong></p>
            </div>
        <?php endforeach; ?>
    </div>
    <h3 class="titulo-itens-pedido">DADOS PEDIDO</h3>
    <div class="dados-pedido">
        <div class="dados">
            <p class="info"><strong>Data do Pedido:</strong> <?php echo date("d/m/Y H:i", strtotime($pedido['data_pedido'])); ?></p>
            <p class="info"><strong>Status:</strong> <?php echo htmlspecialchars($pedido['status_pedido']); ?></p>
            <p class="info"><strong>Valor Total:</strong> R$ <?php echo number_format($pedido['valor_pedido'], 2, ',', '.'); ?></p>
        </div>
        <div class="dados2">
            <h3 class="titulo-endereco">ENDEREÇO DE ENTREGA</h3>
            <p class="info">
                <?php echo htmlspecialchars($pedido['rua']); ?>, 
                <?php echo htmlspecialchars($pedido['bairro']); ?>, 
                <?php echo htmlspecialchars($pedido['cidade']); ?> - 
                <?php echo htmlspecialchars($pedido['cep']); ?><br>
                Complemento: <?php echo htmlspecialchars($pedido['complemento']); ?>
                </p>
        </div>
    </div>
</div>
</body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
</html>
