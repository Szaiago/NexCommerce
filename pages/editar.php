<?php
session_start();
if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$nome_usuario = $_SESSION['nome_usuario'];
$nome_partes = explode(" ", trim($nome_usuario));
$nome_exibido = isset($nome_partes[1]) ? $nome_partes[0] . " " . $nome_partes[1] : $nome_partes[0];

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produto_delete'])) {
    $id_produto = intval($_POST['id_produto_delete']);
    $sql_delete = "DELETE FROM produtos WHERE id_produto = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id_produto);
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Produto deletado com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao deletar produto.";
    }
    $stmt->close();
    header("Location: editar.php");
    exit();
}

$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque | NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/fixo.css">
    <link rel="stylesheet" href="../css/editar.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../js/logoempresamain.js" defer></script>
    <script src="../js/modalperfil.js" defer></script>
    <script src="../js/carrosel-home.js" defer></script>
    <script src="../js/consultar.js" defer></script>
    <script src="../js/modal-editar.js" defer></script>
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
                <a href="estoque.php"><i class="bi bi-box-seam" style="color:black;"></i></a>
            </div>
            <div class="btn">
                <a href="adicionar-produto.php"><i class="bi bi-plus-circle" style="color:black;"></i></a>
            </div>
        </label>
    </div>
    <div class="situacao">
        <?php
            if (isset($_SESSION['mensagem'])) {
            echo $_SESSION['mensagem'];
            unset($_SESSION['mensagem']); // Remove a mensagem após exibir
            }
         ?>
    </div>
    <div class="container-editar">
        <input type="text" id="pesquisaInput" onkeyup="filterTable()" placeholder="CONSULTAR">
        <table id="tabelaProdutos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>SKU</th>
                    <th>Peso</th>
                    <th>Valor</th>
                    <th>Editar</th>
                    <th>deletar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_produto']) ?></td>
                        <td><?= htmlspecialchars($row['nome_produto']) ?></td>
                        <td><?= htmlspecialchars($row['sku_produto']) ?></td>
                        <td><?= htmlspecialchars($row['peso_produto']) ?></td>
                        <td>R$ <?= number_format($row['valor_produto'], 2, ',', '.') ?></td>
                        <td>
                            <button type="button" class="abrir-modal-editar" 
                                data-id="<?= $row['id_produto'] ?>"
                                data-nome="<?= htmlspecialchars($row['nome_produto']) ?>"
                                data-marca="<?= htmlspecialchars($row['marca_produto'] ?? '') ?>"
                                data-cor="<?= htmlspecialchars($row['cor_produto'] ?? '') ?>"
                                data-categoria="<?= htmlspecialchars($row['categoria_produto'] ?? '') ?>"
                                data-peso="<?= htmlspecialchars($row['peso_produto'] ?? '') ?>"
                                data-material="<?= htmlspecialchars($row['material_produto'] ?? '') ?>"
                                data-valor="<?= htmlspecialchars($row['valor_produto'] ?? '') ?>"
                                data-descricao="<?= htmlspecialchars($row['descricao_produto'] ?? '') ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="id_produto_delete" value="<?= $row['id_produto'] ?>">
                                <button type="submit" class="delete-produto"><i class="bi bi-x-square"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="modal-editar-item" style="display:none;">
        <div class="modal-editar">
            <form action="../functions/salvar_edicao.php" method="POST">
                <div class="titulo">
                    <button type="button" onclick="fecharModal()" class="fechar-modal"><i class="bi bi-x" style="font-size:30px;"></i></button>
                    <div class="tiutlo-p">
                        <p>EDITAR PRODUTO</p>
                    </div>
                </div>
                <input type="hidden" id="id_produto" name="id_produto">
                <div class="nome">
                    <label>NOME:</label>
                    <input type="text" id="nome_produto" name="nome_produto" required>
                </div>
                <div class="info-organizar">  
                    <div class="marca">
                        <label>Marca:</label>
                        <input type="text" id="marca_produto" name="marca_produto" required>
                    </div>
                    <div class="cor">
                        <label>Cor:</label>
                        <input type="text" id="cor_produto" name="cor_produto" required>
                    </div>
                </div>
                <div class="info-organizar">
                    <div class="categoria">
                        <label>Categoria:</label>
                        <input type="text" id="categoria_produto" name="categoria_produto" required>
                    </div>
                    <div class="peso">
                        <label>Peso:</label>
                        <input type="number" step="0.01" id="peso_produto" name="peso_produto" required>
                    </div>
                </div>
                    <label>Material:</label>
                <input type="text" id="material_produto" name="material_produto" required>
                <label>Valor:</label>
                <input type="number" step="0.01" id="valor_produto" name="valor_produto" required>
                <div class="descricao">
                    <label>Descrição:</label>
                    <textarea id="descricao_produto" name="descricao_produto" class="descricao_produto" required></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="salvar_produto">SALVAR</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
<script> document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".abrir-modal-editar").forEach(button => {
        button.addEventListener("click", function () {
            let idProduto = this.dataset.id;
            document.getElementById("id_produto").value = idProduto; // Para edição
            document.getElementById("id_produto_delete").value = idProduto; // Para exclusão
        });
    });
</script>
</html>
