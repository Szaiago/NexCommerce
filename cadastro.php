<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro/NextCommerce</title>
    <link rel="icon" href="css/images/next1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/cadastro.css">
    <script src="js/carrosel-index.js" defer></script>
    <script src="js/logoempresa.js" defer></script>
    <script src="js/visualizarsenha.js" defer></script>
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
                <h2>CADASTRO</h2>
            </div>
            <div class="alert-erro">
                <?php
                if (isset($_SESSION['erro'])) {
                    echo "<p>" . $_SESSION['erro'] . "</p>";
                    unset($_SESSION['erro']); // Limpar mensagem após exibição
                }
                ?>
            </div>
            <form action="functions/processar_cadastro.php" method="POST">
                <input type="text" name="nome_usuario" placeholder="Nome completo" required>
                <input type="email" name="email_usuario" placeholder="Email" required>
                <div class="password-container">
                    <input type="password" name="senha_usuario" id="password" placeholder="Senha" required>
                    <button type="button" class="toggle-password" id="togglePassword">
                        <ion-icon name="eye-outline" id="icon"></ion-icon>
                    </button>
                </div>
                <div class="password-container">
                    <input type="password" name="confirm_senha" id="confirmPassword" placeholder="Confirme a senha" required>
                    <button type="button" class="toggle-password" id="toggleConfirmPassword">
                        <ion-icon name="eye-outline" id="iconConfirm"></ion-icon>
                    </button>
                </div>
                <button type="submit">CADASTRAR</button>
                <a href="index.php">Já tem uma conta? Faça login aqui!</a>
            </form>
        </div>
        <div class="carousel-container">
            <div class="carousel">
                <img src="css/images/carroselindex4.jpeg" alt="Slide 1">
                <img src="css/images/carroselindex5.jpeg" alt="Slide 2">
                <img src="css/images/carroselindex6.jpeg" alt="Slide 3">
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
