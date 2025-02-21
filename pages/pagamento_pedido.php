<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$nome_usuario = $_SESSION['nome_usuario'];

// Limitar o nome a duas palavras
$nome_partes = explode(" ", $nome_usuario);
$nome_exibido = $nome_partes[0];
if (isset($nome_partes[1])) {
    $nome_exibido .= " " . $nome_partes[1];}
$user_id = $_SESSION['id_usuario'];

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

// Consulta o valor do pedido (o pedido mais recente do usuário)
$sql = "SELECT valor_pedido FROM pedidos WHERE id_usuario = ? ORDER BY id_pedido DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($valor_pedido);
$stmt->fetch();
if ($valor_pedido === null) {
    $valor_pedido = 0;
}
$stmt->close();
$conn->close();

// Dados para Pix:
$pixKey = "94cdb2a3-ca8e-4738-8537-957359223f90"; // Chave Pix fornecida
$numero_conta = "25381250-0";
$agencia = "0001";
$bancoPix = "077"; // Banco 077 (Banco Inter)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home/NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/fixo.css">
    <link rel="stylesheet" href="../css/pagamento.css">
    <link rel="stylesheet" href="../css/home.css">
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
<div class="container-pagamento">
    <div class="titulo-pagamento">
        <h2>PAGAMENTO</h2>
    </div>
    <div class="orientar-container-pagamento">
    <div class="valor-total">
        <h1>VALOR TOTAL:</h1>
        <p>R$ <?php echo number_format($valor_pedido, 2, ',', '.'); ?></p>
    </div>
        <div class="dados-pix">
            <h3>INFORMAÇÕES PARA PAGAMENTO VIA PIX:</h3>
                <input type="hidden" value="<?php echo $pixKey; ?>" id="chavePix" readonly>
                <button onclick="copiarChavePix()" class="copiar-chave">COPIAR CHAVE PIX</button>
            </p>
        </div>
    </div>
        <div class="instrucoes-pagamento">
        <p>Para realizar o pagamento do seu pedido, siga as instruções abaixo:</p> 
        <ul class="ul-instrucao"> 
            <li>Abra o aplicativo bancário de sua preferência.</li> 
            <li>Acesse a opção de pagamento via Pix.</li> 
            <li>Copie a chave Pix fornecida em nossa plataforma e cole no campo indicado pelo seu banco.</li> 
            <li>Verifique os detalhes da transação, como o valor e a chave Pix, para garantir que tudo está correto.</li> 
            <li>Finalize a operação e aguarde a confirmação do pagamento.</li> 
        </ul> 
        <p>Assim que o pagamento for confirmado, o seu pedido será processado e encaminhado para a etapa de preparação e envio. Caso tenha algum problema ou dúvida, nossa equipe de atendimento estará à disposição para ajudar.</p>
        </div>
    </div>
    <div class="container-finalizar">
        <a href="perfil.php"><button class="finalizar-pedido">FINALIZAR</button></a>
    </div>
</body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script>
        function copiarChavePix() {
            var chavePixInput = document.getElementById("chavePix");
            chavePixInput.select();
            chavePixInput.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Chave Pix copiada! Use essa chave para realizar o pagamento.");
        }
    </script>
</html>
