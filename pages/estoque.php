<?php
session_start();

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

// Consulta SQL para obter os produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

// Inicializar variáveis para armazenar os totais
$total_peso_kg = 0; // Convertido para kg
$total_quantidade = 0;
$total_valor = 0;

// Calcular os totais do estoque
while ($row = $result->fetch_assoc()) {
    $total_peso_kg += ($row['peso_produto'] * $row['quantidade_produto']) / 1000; // Convertendo de gramas para kg
    $total_quantidade += $row['quantidade_produto'];
    $total_valor += $row['valor_produto'] * $row['quantidade_produto'];
}

// Resetar ponteiro para listar produtos corretamente na tabela
$result->data_seek(0);

// Obtém o ID do usuário logado
$user_id = $_SESSION['id_usuario']; // Supondo que o ID do usuário está armazenado na sessão

// Consulta SQL para contar os itens no carrinho (calculando quantidade total de cada produto)
$sql_carrinho = "SELECT SUM(quantidade) as total_itens FROM carrinho WHERE id_usuario = ?";
$stmt = $conn->prepare($sql_carrinho);

if ($stmt === false) {
    // Exibe um erro caso a preparação da consulta falhe
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_carrinho = $stmt->get_result();
$row_carrinho = $result_carrinho->fetch_assoc();
$total_itens_carrinho = $row_carrinho['total_itens'];

// Fechar a conexão
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque | NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/fixo.css">
    <link rel="stylesheet" href="../css/estoque.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../js/logoempresamain.js" defer></script>
    <script src="../js/modalperfil.js" defer></script>
    <script src="../js/consultar.js" defer></script>
    <script src="../js/update-quantida-estoque.js" defer></script>
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
                <a href="sistema_empresarial.php"><i class="bi bi-arrow-left" style="color:black;"></i></a>
            </div>
            <div class="btn" style="display:none;">
                <i class="material-icons md-36">photo</i>
            </div>
            <div class="btn" style="display:none;">
                <i class="material-icons md-36">music_note</i>
            </div>
            <div class="btn" style="display:none;">
                <i class="material-icons md-36">chat_bubble</i>
            </div>
            <div class="btn">
                <i class="bi bi-gear" style="color:black;"></i></i>
            </div>
            <div class="btn">
                <i class="bi bi-people" style="color:black;"></i></i>
            </div>
            <div class="btn">
                <a href="editar.php"><i class="bi bi-pencil-square" style="color:black;"></i></a>
            </div>
            <div class="btn">
                <a href="adicionar-produto.php"><i class="bi bi-plus-circle" style="color:black;"></i></a>
            </div>
        </label>
    </div>
    <div class="dados-estoque">
        <div class="alinhar-dados" style="border-radius:5px 0 0 5px;">
            <h1>PESO</h1>
            <p><?php echo number_format($total_peso_kg, 2, ',', '.'); ?> kg</p>
        </div>
        <div class="alinhar-dados">
            <p><?php echo $total_quantidade; ?> unidades</p>
        </div>
        <div class="alinhar-dados" style="border-radius:0 5px 5px 0;">
            <p>R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></p>
        </div>
    </div>
    <div class="container-estoque">
            <input type="text" id="pesquisaInput" onkeyup="filterTable()" placeholder="CONSULTAR">
            <table id="tabelaProdutos">
                <thead>
                    <tr>
                        <th>ID Produto</th>
                        <th>Nome Produto</th>
                        <th>SKU Produto</th>
                        <th>Peso Produto</th>
                        <th>Valor Produto</th>
                        <th>Quantidade Produto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id_produto']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nome_produto']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sku_produto']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['peso_produto']) . "</td>";
                        echo "<td>R$ " . number_format($row['valor_produto'], 2, ',', '.') . "</td>";
                        echo "<td><input type='number' value='" . htmlspecialchars($row['quantidade_produto']) . "' onchange='updateQuantidade(" . $row['id_produto'] . ", this.value)'></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
</body>
<script>
    function atualizarEstoque() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "../functions/atualizar_estoque.php", true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    document.querySelector(".dados-estoque").innerHTML = `
                       <div class="alinhar-dados">
                            <h1>PESO</h1>
                             <p><?php echo number_format($total_peso_kg, 2, ',', '.'); ?> kg</p>
                        </div>
                        <div class="alinhar-dados">
                            <h1>QUANTIDADE</h1>
                            <p><?php echo $total_quantidade; ?> unidades</p>
                        </div>
                        <div class="alinhar-dados">
                            <h1>VALOR</h1>
                            <p>R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></p>
                        </div>
                    `;
                } catch (e) {
                    console.error("Erro ao processar os dados do estoque:", e);
                }
            }
        };
        xhr.send();
    }

    // Atualiza a cada 2 segundos (2000ms)
    setInterval(atualizarEstoque, 2000);

    // Atualiza imediatamente ao carregar a página
    atualizarEstoque();
</script>

</html>
