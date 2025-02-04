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
    <script src="../js/carrosel-home.js" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        function updateQuantidade(id, quantidade) {
            const xhttp = new XMLHttpRequest();
            xhttp.open("POST", "../functions/update_quantidade.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(`id_produto=${id}&quantidade_produto=${quantidade}`);
        }
        
        function filterTable() {
            const input = document.getElementById("pesquisaInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("tabelaProdutos");
            const trs = table.getElementsByTagName("tr");
            
            for (let i = 1; i < trs.length; i++) {
                const tds = trs[i].getElementsByTagName("td");
                let show = false;
                
                for (let j = 0; j < tds.length; j++) {
                    if (tds[j].innerText.toLowerCase().indexOf(filter) > -1) {
                        show = true;
                        break;
                    }
                }
                trs[i].style.display = show ? "" : "none";
            }
        }
    </script>
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
                    <i class="bi bi-heart"></i>
                </div>
                <div class="noti">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="perfil" id="perfil">
                    <i class="bi bi-person"></i>
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
                <i class="bi bi-gear"></i>
            </div>
            <div class="btn">
                <i class="bi bi-people"></i>
            </div>
            <div class="btn">
                <a href="estoque.php"><i class="bi bi-box-seam"></i></a>
            </div>
            <div class="btn">
                <i class="bi bi-pencil-square"></i>
            </div>
        </label>
    </div>
    <div class="dados-estoque">
        <p><strong>Total de Peso do Estoque:</strong> <?php echo number_format($total_peso, 2, ',', '.'); ?> kg</p>
        <p><strong>Total de Quantidade:</strong> <?php echo $total_quantidade; ?> unidades</p>
        <p><strong>Total do Valor do Estoque:</strong> R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></p>
    </div>
    <div class="container-estoque">
            <input type="text" id="pesquisaInput" onkeyup="filterTable()" placeholder="Pesquisar por qualquer campo...">
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
</html>
