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

// Configurações do banco de dados
$host   = "localhost";
$usuario = "root";
$senha  = "";
$banco  = "NextCommerce";

// Criar conexão com o banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Consulta SQL para obter os produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);
$produtos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
         $produtos[] = $row;
    }
}

// Define qual produto exibir no container-produto
$produto = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    foreach ($produtos as $p) {
         if ($p['id_produto'] == $id) {
             $produto = $p;
             break;
         }
    }
}
// Se nenhum produto foi selecionado ou encontrado, usa o primeiro (caso haja)
if ($produto === null && count($produtos) > 0) {
    $produto = $produtos[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | NextCommerce</title>
    <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/fixo.css">
    <link rel="stylesheet" href="../css/home.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css"/>
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
            <i class="bi bi-gear" style="color:black;"></i>
        </div>
        <div class="btn">
            <i class="bi bi-people" style="color:black;"></i>
        </div>
        <div class="btn">
            <a href="estoque.php"><i class="bi bi-box-seam" style="color:black;"></i></a>
        </div>
        <div class="btn">               
            <a href="editar.php"><i class="bi bi-pencil-square" style="color:black;"></i></a>
        </div>
      </label>
    </div>

    <!-- Container do Produto (detalhes do produto selecionado) -->
    <div class="container-produto">
    <?php if ($produto): ?>
        <div class="alocar-imagens">
            <?php
            for ($i = 1; $i <= 5; $i++) {
                $img = $produto["img{$i}_produto"];
                if (!empty($img)) {
                    echo "<img src=\"$img\" alt=\"Imagem do Produto\" class=\"img-produto\">";
                }
            }
            ?>
        </div>
        <div class="info-produto">
        <p class="id-produto">ID PRODUTO: <?php echo $produto['id_produto']; ?></p>
        <h1><?php echo htmlspecialchars($produto['nome_produto']); ?></h1>
        <div class="avaliacao">
            <?php
            $avaliacao = round($produto['avaliacao_produto']);
            for ($i = 1; $i <= 5; $i++) {
                echo $i <= $avaliacao ? '<span class="estrela cheia">★</span>' : '<span class="estrela vazia">☆</span>';
            }
            ?>
        </div>
        <p class="preco">
            <span class="valor">R$ <?php echo number_format($produto['valor_produto'], 2, ',', '.'); ?></span> 
            <span class="pix">no Pix</span>
        </p>
        <p class="descricao"><?php echo htmlspecialchars($produto['descricao_produto']); ?></p>
        <button class="botao-carrinho">ADICIONAR AO CARRINHO</button>
    </div>
    <?php else: ?>
        <p>Nenhum produto selecionado.</p>
    <?php endif; ?>
</div>

    </div>
    

    <!-- Container dos Cards (lista de produtos) -->
    <div class="container-cards">
        <?php
        if (count($produtos) > 0) {
            foreach ($produtos as $row) {
        ?>
            <a href="produto.php?id=<?php echo $row['id_produto']; ?>" class="card-link">
                <div class="card-anuncio">
                    <div class="img-anuncio">
                        <div class="carousel-container">
                            <div class="carousel-anuncio">
                                <?php
                                $images = [];
                                for ($i = 1; $i <= 5; $i++) {
                                    $img = $row["img{$i}_produto"];
                                    if (!empty($img)) {
                                        $images[] = $img;
                                        echo "<div class='slide-anuncio' style='background-image: url(\"$img\");'></div>";
                                    }
                                }
                                ?>
                            </div>
                            <div class="carousel-indicators-anuncio">
                                <?php
                                foreach ($images as $index => $img) {
                                    echo "<div class='indicator-card' data-slide='$index'></div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="conteudo-anuncio">
                        <p class="id_produto">ID: <?php echo $row["id_produto"]; ?></p>
                        <p class="nome_produto"><?php echo htmlspecialchars($row["nome_produto"]); ?></p>
                        <p class="valor_produto">R$ <?php echo number_format($row["valor_produto"], 2, ',', '.'); ?></p>
                        <p class="descricao_produto"><?php echo htmlspecialchars($row["descricao_produto"]); ?></p>
                        <p class="avaliacao_produto">⭐ <?php echo number_format($row["avaliacao_produto"], 1); ?></p>
                    </div>
                </div>
            </a>
        <?php
            }
        } else {
            echo "<p>Nenhum produto cadastrado.</p>";
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.card-anuncio').forEach(card => {
            const indicators = card.querySelectorAll('.indicator-card');
            const carousel = card.querySelector('.carousel-anuncio');
            const slides = card.querySelectorAll('.slide-anuncio');
            let currentIndex = 0;
            const slideCount = slides.length;

            function updateCarousel(index) {
                const width = slides[0].clientWidth;
                carousel.style.transition = 'transform 0.5s ease-in-out';
                carousel.style.transform = `translateX(-${index * width}px)`;
                
                indicators.forEach((indicator, i) => {
                    indicator.classList.toggle('active', i === index);
                });
            }

            function nextImage() {
                currentIndex = (currentIndex + 1) % slideCount;
                updateCarousel(currentIndex);
            }

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    currentIndex = index;
                    updateCarousel(currentIndex);
                });
            });

            setInterval(nextImage, 5000);
            updateCarousel(currentIndex);
        });
    });
    </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script>
$(document).ready(function(){
  $('.alocar-imagens').slick({
    dots: false,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    adaptiveHeight: false,
    arrows: false, // seta de navegação
    autoplay: true,
    autoplaySpeed: 2500
  });
});
</script>

</body>
</html>
