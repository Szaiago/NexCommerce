<?php
session_start();

// Verificando se o usuário está logado
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

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'NextCommerce');
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificando se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_produto = $_POST['nome_produto'];
    $marca_produto = $_POST['marca_produto'];
    $cor_produto = $_POST['cor_produto'];
    
    // Ajustando o formato do valor antes de salvar
    $valor_produto = str_replace(['R$', ','], ['', '.'], $_POST['valor_produto']);
    $valor_produto = floatval($valor_produto);

    $categoria_produto = $_POST['categoria_produto'];
    $peso_produto = $_POST['peso_produto'];
    $descricao_produto = $_POST['descricao_produto'];
    $material_produto = $_POST['material_produto'];

    // Verificando se o nome do produto já existe
    $sql_check = "SELECT * FROM produtos WHERE nome_produto = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('s', $nome_produto);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "Produto com esse nome já existe. Por favor, escolha outro nome.";
        exit();
    }

    // Gerar SKU automaticamente
    $sku_produto = strtoupper(uniqid('SKU_'));

    // Quantidade inicial definida como 0
    $quantidade_produto = 0;

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

    // SQL para inserir o produto
    $sql = "INSERT INTO produtos (nome_produto, marca_produto, sku_produto, cor_produto, valor_produto, categoria_produto, peso_produto, quantidade_produto, descricao_produto, material_produto, img1_produto, img2_produto, img3_produto, img4_produto, img5_produto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssdsdssssssss', 
        $nome_produto, 
        $marca_produto, 
        $sku_produto, 
        $cor_produto, 
        $valor_produto, 
        $categoria_produto, 
        $peso_produto, 
        $quantidade_produto, 
        $descricao_produto, 
        $material_produto,
        $imagens['img1_produto'], 
        $imagens['img2_produto'], 
        $imagens['img3_produto'], 
        $imagens['img4_produto'], 
        $imagens['img5_produto']
    );

    // Executando a consulta
    if ($stmt->execute()) {
        echo "Produto adicionado com sucesso!";
        header("Location: adicionar-produto.php?success=1");
        exit();
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
    <title>Adicionar Produto / NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/add_produto.css">
    <link rel="stylesheet" href="../css/fixo.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/logoempresamain.js" defer></script>
    <script src="../js/modalperfil.js" defer></script>
    <script src="../js/carrosel-home.js" defer></script>
    <script src="../js/mascarainputs.js" defer></script>
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
        <div class="nome_produto">
            <label for="nome_produto">TÍTULO</label>
            <input type="text" name="nome_produto" class="inputs-produtos" required>
        </div>
        <div class="info-organizar">
            <div class="marca_produto">
                <label for="marca_produto">MARCA</label>
                <input type="text" name="marca_produto" class="inputs-produtos" required>
            </div>
            <div class="cor_produto">
                <label for="cor_produto">COR</label>
                <select name="cor_produto" class="inputs-produtos">
                    <option value="" disabled selected>SELECIONE UMA COR</option>
                    <option value="vermelho">VERMELHO</option>
                    <option value="azul">AZUL</option>
                    <option value="verde">VERDE</option>
                    <option value="amarelo">AMARELO</option>
                    <option value="preto">PRETO</option>
                    <option value="branco">BRANCO</option>
                    <option value="rosa">ROSA</option>
                    <option value="roxo">ROXO</option>
                    <option value="laranja">LARANJA</option>
                    <option value="cinza">CINZA</option>
                    <option value="bege">BEGE</option>
                </select>
            </div>
        </div>
        <div class="info-organizar">
        <div class="categoria_produto">
            <label for="categoria_produto">CATEGORIA</label>
            <select name="categoria_produto" class="inputs-produtos" required>
                <option value="" disabled selected>SELECIONE UMA CATEGORIA</option>
                <option value="roupas">ROUPAS</option>
                <option value="eletronicos">ELETRÔNICOS</option>
                <option value="beleza">BELEZA</option>
                <option value="alimentos">ALIMENTOS</option>
                <option value="casa">CASA</option>
                <option value="decoracao">DECORAÇÃO</option>
                <option value="brinquedos">BRINQUEDOS</option>
                <option value="livros">LIVROS</option>
                <option value="esportes">ESPORTES E LAZER</option>
                <option value="saude">SAÚDE</option>
                <option value="tecnologia">TECNOLOGIA</option>
            </select>
        </div>
            <div class="material_produto">
                <label for="material_produto">MATERIAL</label>
                <input type="text" name="material_produto" class="inputs-produtos" required>
            </div>
        </div>
        <div class="info-organizar">
            <div class="peso_produto">
                <label for="peso_produto">PESO (G)</label>
                <input type="text" name="peso_produto" class="inputs-produtos" id="peso_produto" required>
            </div>
            <div class="valor_produto">
                <label for="valor_produto">VALOR</label>
                <input type="text" name="valor_produto" class="inputs-produtos" id="valor_produto" required>
            </div>
        </div>
        <div class="descricao_produto">
            <label for="descricao_produto">DESCRIÇÃO</label>
            <textarea name="descricao_produto"></textarea>
        </div>

        <button type="submit" class="salvar_produto">ADICIONAR PRODUTO</button>
    </div>  
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-footer">
            <button type="button" class="btn btn-success" data-bs-dismiss="modal"><i class="bi bi-x"></i></button>
        </div>
        <div class="confirmado">
            <i class="bi bi-patch-check"></i>
            <p>CADASTRADO!</p>
        </div>
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
        <a href="sistema_empresarial.php"><i class="bi bi-arrow-left" style="color:black;"></i></a>
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    }
});
</script>
<script>
    // Adiciona a classe "ativo" ao ícone após um pequeno delay
    setTimeout(() => {
        document.querySelector('.confirmado i').classList.add('ativo');
    }, 100); // Pequeno atraso para garantir que a transição ocorra
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>
