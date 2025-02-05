<?php
session_start();
if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$nome_usuario = $_SESSION['nome_usuario'];
$nome_partes = explode(" ", $nome_usuario);
$nome_exibido = $nome_partes[0];
if (isset($nome_partes[1])) {
    $nome_exibido .= " " . $nome_partes[1];
}

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "NextCommerce";

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8");

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
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
            function abrirModal(id, nome, marca, cor, valor, categoria, peso, material, descricao) {
        document.getElementById('id_produto').value = id;
        document.getElementById('nome_produto').value = nome;
        document.getElementById('marca_produto').value = marca;
        document.getElementById('cor_produto').value = cor;
        document.getElementById('valor_produto').value = valor;
        document.getElementById('categoria_produto').value = categoria;
        document.getElementById('peso_produto').value = peso;
        document.getElementById('material_produto').value = material;
        document.getElementById('descricao_produto').value = descricao;
        document.getElementById('modal-editar').style.display = 'block';
    }

    function fecharModal() {
        document.getElementById('modal-editar').style.display = 'none';
    }

    function deletarProduto() {
        let idProduto = document.getElementById('id_produto').value;
        if (confirm('Tem certeza que deseja deletar este produto?')) {
            window.location.href = '../functions/delete_produto.php?id=' + idProduto;
        }
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
    <div class="container-editar">
        <input type="text" id="pesquisaInput" onkeyup="filterTable()" placeholder="CONSULTAR">
        <table id="tabelaProdutos">
            <thead>
                <tr>
                    <th>ID Produto</th>
                    <th>Nome Produto</th>
                    <th>SKU Produto</th>
                    <th>Peso Produto</th>
                    <th>Valor Produto</th>
                    <th>Editar</th>
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
                            <button onclick="abrirModal(
                                '<?= $row['id_produto'] ?>',
                                '<?= $row['nome_produto'] ?>',
                                '<?= $row['sku_produto'] ?>',
                                '<?= $row['peso_produto'] ?>',
                                '<?= $row['valor_produto'] ?>',
                                '<?= $row['quantidade_produto'] ?>'
                            )">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- MODAL PARA EDIÇÃO -->
    <div class="modal-editar" id="modal-editar" style="display: none;">
    <div class="modal-content">
        <h2>Editar Produto</h2>
        <form action="../functions/update_produto.php" method="POST">
            <input type="hidden" id="id_produto" name="id_produto">
            
            <label>Nome:</label>
            <input type="text" id="nome_produto" name="nome_produto" required>

            <label>Marca:</label>
            <input type="text" id="marca_produto" name="marca_produto" required>

            <label>Cor:</label>
            <input type="text" id="cor_produto" name="cor_produto" required>

            <label>Valor:</label>
            <input type="number" step="0.01" id="valor_produto" name="valor_produto" required>

            <label>Categoria:</label>
            <input type="text" id="categoria_produto" name="categoria_produto" required>

            <label>Peso:</label>
            <input type="number" step="0.01" id="peso_produto" name="peso_produto" required>

            <label>Material:</label>
            <input type="text" id="material_produto" name="material_produto" required>

            <label>Descrição:</label>
            <textarea id="descricao_produto" name="descricao_produto" required></textarea>

            <div class="modal-buttons">
                <button type="submit">Salvar</button>
                <button type="button" onclick="fecharModal()">Cancelar</button>
                <button type="button" class="delete-button" onclick="deletarProduto()">Deletar</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
