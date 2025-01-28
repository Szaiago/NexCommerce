<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextCommerce</title>
    <link rel="icon" href="css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/index.css">
    <script src="js/carrosel-index.js" defer></script>
    <script src="js/visualizarsenha.js" defer></script>
    <script src="js/logoempresa.js" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="logoempresa">
                <img id="logo" src="css/images/next1.png" alt="Logo Empresa">
            </div>
            <div class="titulo">
                <h2>LOGIN</h2>
            </div>
            <div class="alert-erro">
                <?php
                if (isset($_SESSION['LOGIN-ERRO'])) {  // Verificar se há erro
                    echo "<p>" . $_SESSION['LOGIN-ERRO'] . "</p>";  // Exibir erro
                    unset($_SESSION['LOGIN-ERRO']);  // Limpar a mensagem após exibição
                }
                ?>
            </div>
            <form action="functions/processar_login.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="button" class="toggle-password" id="togglePassword">
                    <ion-icon name="eye-outline" id="icon"></ion-icon>
                    </button>
                </div>
                <button type="submit">ENTRAR</button>
                <a href="cadastro.php">Não tem conta? Cadastre-se agora!</a>
            </form>
        </div>
        <div class="carousel-container">
            <div class="carousel">
                <img src="css/images/carroselindex1.jpeg" alt="Slide 1">
                <img src="css/images/carroselindex2.jpeg" alt="Slide 2">
                <img src="css/images/carroselindex3.jpeg" alt="Slide 3">
            </div>
            <div class="carousel-indicators">
                <div class="indicator active" data-slide="0"></div>
                <div class="indicator" data-slide="1"></div>
                <div class="indicator" data-slide="2"></div>
            </div>
        </div>
    </div>
</body>
</html>
