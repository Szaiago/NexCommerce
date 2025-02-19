<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['nome_usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Nome do usuário logado
$nome_usuario = $_SESSION['nome_usuario'];

// Extrair primeiras duas palavras do nome
$nome_partes = explode(" ", $nome_usuario);
$nome_exibido = $nome_partes[0];
if (isset($nome_partes[1])) {
    $nome_exibido .= " " . $nome_partes[1];
}

// Configuração do banco de dados
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";

// Criar conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Definir charset para evitar problemas com acentos
$conn->set_charset("utf8");

// Obter ID do usuário
$user_id = isset($_SESSION['id_usuario']) ? (int) $_SESSION['id_usuario'] : 0;

// Verificar se o ID do usuário foi recuperado corretamente
if ($user_id === 0) {
    die("Erro: ID do usuário inválido.");
}

// Contar itens no carrinho
$sql_carrinho = "SELECT SUM(quantidade) as total_itens FROM carrinho WHERE id_usuario = ?";
$stmt_carrinho = $conn->prepare($sql_carrinho);
if ($stmt_carrinho === false) {
    die("Erro na preparação da consulta do carrinho: " . $conn->error);
}

$stmt_carrinho->bind_param("i", $user_id);
$stmt_carrinho->execute();
$result_carrinho = $stmt_carrinho->get_result();
$row_carrinho = $result_carrinho->fetch_assoc();
$total_itens_carrinho = $row_carrinho['total_itens'] ?? 0;
$stmt_carrinho->close(); // Fechar o statement após o uso

// Consultar dados do usuário
$sql_usuario = "SELECT id_usuario, nome_usuario, email_usuario, senha_usuario FROM usuario WHERE id_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
if ($stmt_usuario === false) {
    die("Erro na preparação da consulta do usuário: " . $conn->error);
}

$stmt_usuario->bind_param("i", $user_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();
$stmt_usuario->close(); // Fechar o statement após o uso

// Gerar iniciais do nome do usuário
$iniciais = strtoupper(substr($nome_partes[0], 0, 1));
if (isset($nome_partes[1])) {
    $iniciais .= strtoupper(substr($nome_partes[1], 0, 1));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home/NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/fixo.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="stylesheet" href="../css/home.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../js/logoempresamain.js" defer></script>
    <script src="../js/modalperfil.js" defer></script>
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
                    <i class="bi bi-bag" style="font-size:22px;"></i>
                    <?php if ($total_itens_carrinho > 0): ?>
                        <span class="badge"><?php echo $total_itens_carrinho; ?></span> <!-- Exibe o número de itens -->
                    <?php endif; ?>
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
<div class="container-perfil">
    <div class="perfil-header">
        <div class="perfil-iniciais">
            <?php echo htmlspecialchars($iniciais); ?>
        </div>
    </div>
    <div class="dados-perfil">
        <p class="id-usuario"><strong>ID USUÁRIO:</strong> <?php echo htmlspecialchars($usuario['id_usuario']); ?></p>
        <h2 class="nome-usuario"><?php echo htmlspecialchars($usuario['nome_usuario']); ?></h2>
        <div class="dados">
            <p class="dados-usuario">DADOS</p>
        </div>
        <p class="email-usuario"><strong>EMAIL:</strong> <?php echo htmlspecialchars($usuario['email_usuario']); ?></p>
        <forms class="dados-adicionais">
            <p>CPF</p>
                <input type="text" placeholder="CPF">
            <p>CEP</p>
                <input type="text" placeholder="CEP">
            <p>ESTADO</p>
            <p>CIDADE</p>
            <p>BAIRRO</p>
        </forms>
    </div>
</div>
</body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    </script>
</html>
