<?php
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Nome do usuário logado
$nome_usuario = $_SESSION['nome_usuario'];
$nome_partes = explode(" ", $nome_usuario);
$nome_exibido = $nome_partes[0];
if (isset($nome_partes[1])) {
    $nome_exibido .= " " . $nome_partes[1];
}

// Verificando se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_produto = $_POST['nome_produto'];
    $marca_produto = $_POST['marca_produto'];
    $sku_produto = $_POST['sku_produto'];
    $cor_produto = $_POST['cor_produto'];
    $valor_produto = $_POST['valor_produto'];
    $categoria_produto = $_POST['categoria_produto'];
    $peso_produto = $_POST['peso_produto'];
    $avaliacao_produto = $_POST['avaliacao_produto'];
    $quantidade_produto = $_POST['quantidade_produto'];
    $descricao_produto = $_POST['descricao_produto'];
    
    // Processando imagens
    $imagens = [];
    for ($i = 1; $i <= 5; $i++) {
        if (isset($_FILES["img{$i}_produto"]) && $_FILES["img{$i}_produto"]["error"] == 0) {
            $extensao = pathinfo($_FILES["img{$i}_produto"]["name"], PATHINFO_EXTENSION);
            $nome_imagem = "img{$i}_" . time() . "." . $extensao;
            $caminho_imagem = "../images/" . $nome_imagem;
            move_uploaded_file($_FILES["img{$i}_produto"]["tmp_name"], $caminho_imagem);
            $imagens["img{$i}_produto"] = $caminho_imagem;
        } else {
            $imagens["img{$i}_produto"] = '';  // Caso não tenha imagem
        }
    }

    // Salvar os dados no banco (exemplo básico)
    $conn = new mysqli('localhost', 'usuario', 'senha', 'nextcommerce');
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    $sql = "INSERT INTO produtos (nome_produto, marca_produto, sku_produto, cor_produto, valor_produto, categoria_produto, peso_produto, avaliacao_produto, quantidade_produto, descricao_produto, img1_produto, img2_produto, img3_produto, img4_produto, img5_produto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdsdssssssss", $nome_produto, $marca_produto, $sku_produto, $cor_produto, $valor_produto, $categoria_produto, $peso_produto, $avaliacao_produto, $quantidade_produto, $descricao_produto, $imagens['img1_produto'], $imagens['img2_produto'], $imagens['img3_produto'], $imagens['img4_produto'], $imagens['img5_produto']);

    if ($stmt->execute()) {
        echo "Produto adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar produto: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Produto / NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/add_produto.css">
    <link rel="stylesheet" href="../css/fixo.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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

    <form action="" method="POST" enctype="multipart/form-data" class="inputs-cadastrar-produto">
    <div class="carrosel-produtos-img">
        <?php
        $imagens = [
            'img1_produto' => isset($_SESSION['img1_produto']) ? $_SESSION['img1_produto'] : '',
            'img2_produto' => isset($_SESSION['img2_produto']) ? $_SESSION['img2_produto'] : '',
            'img3_produto' => isset($_SESSION['img3_produto']) ? $_SESSION['img3_produto'] : '',
            'img4_produto' => isset($_SESSION['img4_produto']) ? $_SESSION['img4_produto'] : '',
            'img5_produto' => isset($_SESSION['img5_produto']) ? $_SESSION['img5_produto'] : ''
        ];

        foreach ($imagens as $key => $img) {
            echo '<div class="carrosel-container" id="' . $key . '" style="background-image: url(' . ($img ? htmlspecialchars($img) : '../css/images/placeholder.jpg') . ');">';
            echo '    <input type="file" name="' . $key . '" accept="image/*" class="input-file" id="file-' . $key . '" data-target="' . $key . '" hidden>';
            echo '    <button class="upload-btn" onclick="document.getElementById(\'file-' . $key . '\').click();"><i class="bi bi-plus"></i></button>';
            echo '</div>';
        }
        ?>
    </div>
    <div class="ajustar-forms">  
        <div class="titulo-forms">
            <p>CADASTRAR PRODUTO</p>
        </div>
    <label for="nome_produto">NOME DO PRODUTO:</label>
    <input type="text" name="nome_produto"  class="inputs-produtos" required>

    <label for="marca_produto">Marca:</label>
    <input type="text" name="marca_produto" class="inputs-produtos" required>

    <label for="sku_produto">SKU:</label>
    <input type="text" name="sku_produto" class="inputs-produtos" required>

    <label for="cor_produto">Cor:</label>
    <input type="text" name="cor_produto" class="inputs-produtos">

    <label for="valor_produto">Valor:</label>
    <input type="number" step="0.01" name="valor_produto" class="inputs-produtos" required>

    <label for="categoria_produto">Categoria:</label>
    <input type="text" name="categoria_produto" class="inputs-produtos" required>

    <label for="peso_produto">Peso:</label>
    <input type="number" step="0.01" name="peso_produto" class="inputs-produtos">

    <label for="avaliacao_produto">Avaliação:</label>
    <input type="number" step="0.01" name="avaliacao_produto" class="inputs-produtos">

    <label for="quantidade_produto">Quantidade:</label>
    <input type="number" name="quantidade_produto" class="inputs-produtos" required>

    <label for="descricao_produto">Descrição:</label>
    <textarea name="descricao_produto"></textarea>
    <button type="submit">Adicionar Produto</button>
    </div>  
</form>
<div class="menu">
  <input type="checkbox" id="toggle" />
  <label id="show-menu" for="toggle">
    <div class="btn">
      <i class="material-icons md-36 toggleBtn menuBtn">menu</i>
      <i class="material-icons md-36 toggleBtn closeBtn">close</i>
    </div>
    <div class="btn">
        <a href="adicionar-produto.php"><i class="bi bi-plus-circle"></i></a>
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
        <i class="bi bi-gear"></i>
    </div>
    <div class="btn">
        <i class="bi bi-people"></i>
    </div>
    <div class="btn">
        <i class="bi bi-box-seam"></i>
    </div>
    <div class="btn">               
        <i class="bi bi-pencil-square"></i>
    </div>
  </label>
</div>

</body>
<script>
document.querySelectorAll('.input-file').forEach(input => {
    input.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById(event.target.dataset.target);
                container.style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
</html>
