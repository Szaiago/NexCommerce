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
if (!$stmt_carrinho) {
    die("Erro na preparação da consulta do carrinho: " . $conn->error);
}

$stmt_carrinho->bind_param("i", $user_id);
$stmt_carrinho->execute();
$result_carrinho = $stmt_carrinho->get_result();

// Buscar os produtos no carrinho
$sql = "SELECT c.id_carrinho, p.nome_produto, p.descricao_produto, p.img1_produto, c.quantidade 
        FROM carrinho c
        INNER JOIN produtos p ON c.id_produto = p.id_produto
        WHERE c.id_usuario = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da consulta de produtos no carrinho: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result_carrinho) {
    $row_carrinho = $result_carrinho->fetch_assoc();
    $total_itens_carrinho = $row_carrinho['total_itens'] ?? 0; // Se for NULL, define como 0
} else {
    $total_itens_carrinho = 0;
}

$stmt_carrinho->close();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home/NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/fixo.css">
    <link rel="stylesheet" href="../css/carrinho.css">
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
<div class="titulo-carrinho">
    <p>CARRINHO</p>
</div>
<div class="container-carrinho">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="item-carrinho" data-id="<?php echo $row['id_carrinho']; ?>">
                <div class="img-produto">
                    <img src="../images/<?php echo htmlspecialchars($row['img1_produto']); ?>" alt="Produto">
                </div>
                <div class="info-produto">
                    <h3><?php echo htmlspecialchars($row['nome_produto']); ?></h3>
                    <p><?php echo htmlspecialchars($row['descricao_produto']); ?></p>
                </div>
                <div class="controle-quantidade">
                    <button class="diminuir" data-id="<?php echo $row['id_carrinho']; ?>">-</button>
                    <span class="quantidade"><?php echo $row['quantidade']; ?></span>
                    <button class="aumentar" data-id="<?php echo $row['id_carrinho']; ?>">+</button>
                </div>
                <button class="remover-item" data-id="<?php echo $row['id_carrinho']; ?>">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Seu carrinho está vazio.</p>
    <?php endif; ?>
</div>
<div class="container-finalizar">
    <?php
    // Fetch cart products and calculate the total price
$total_compra = 0;
$sql_produtos_carrinho = "SELECT p.preco_produto, c.quantidade 
                          FROM carrinho c 
                          INNER JOIN produtos p ON c.id_produto = p.id_produto 
                          WHERE c.id_usuario = ?";
$stmt_produtos_carrinho = $conn->prepare($sql_produtos_carrinho);
$stmt_produtos_carrinho->bind_param("i", $user_id);
$stmt_produtos_carrinho->execute();
$result_produtos_carrinho = $stmt_produtos_carrinho->get_result();

// Calculate total value of the products in the cart
while ($row_produto = $result_produtos_carrinho->fetch_assoc()) {
    $total_compra += $row_produto['preco_produto'] * $row_produto['quantidade'];
}

// Calculate the freight (you can replace this with real logic, for now it's a placeholder)
$frete = 20.00; // Example value, can be based on location or other factors

// Calculate total final (product total + freight)
$total_final = $total_compra + $frete;

    ?>
    <h2>Finalizar Compra</h2>
    
    <div class="dados-entrega">
        <h3>Endereço de Entrega</h3>
        <?php if ($dados_entrega): ?>
            <p><strong>CEP:</strong> <?php echo htmlspecialchars($dados_entrega['cep']); ?></p>
            <p><strong>Estado:</strong> <?php echo htmlspecialchars($dados_entrega['estado']); ?></p>
            <p><strong>Cidade:</strong> <?php echo htmlspecialchars($dados_entrega['cidade']); ?></p>
            <p><strong>Bairro:</strong> <?php echo htmlspecialchars($dados_entrega['bairro']); ?></p>
            <p><strong>Rua:</strong> <?php echo htmlspecialchars($dados_entrega['rua']); ?></p>
            <p><strong>Complemento:</strong> <?php echo htmlspecialchars($dados_entrega['complemento']); ?></p>
        <?php else: ?>
            <p>Dados de entrega não cadastrados.</p>
        <?php endif; ?>
    </div>

    <div class="resumo-compra">
        <h3>Resumo da Compra</h3>
        <p><strong>Total dos produtos:</strong> R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></p>
        <p><strong>Frete:</strong> R$ <?php echo number_format($frete, 2, ',', '.'); ?></p>
        <hr>
        <p><strong>Total a pagar:</strong> R$ <?php echo number_format($total_final, 2, ',', '.'); ?></p>
    </div>

    <button class="finalizar-compra">Finalizar Compra</button>
</div>

</div>
</body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="../js/carrinho.js"></script>
</html>
