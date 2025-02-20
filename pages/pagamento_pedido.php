<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../index.php");
    exit();
}

$nome_usuario = $_SESSION['nome_usuario'];
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

// Formatar o valor para Pix (usando ponto como separador decimal)
$valor_formatado = number_format($valor_pedido, 2, '.', '');

// Gerar o payload Pix (exemplo simplificado; para produção, calcular o CRC conforme o padrão EMV)
$payload = "00020101021226860014BR.GOV.BCB.PIX0114{$pixKey}520400005303986540{$valor_formatado}5802BR5913NextCommerce6009Itajai62070503***6304XXXX";

// Gerar a URL para o QR Code Pix usando um serviço público
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($payload) . "&size=200x200";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento - NextCommerce</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function copiarPayload() {
            var payloadInput = document.getElementById("pixPayload");
            payloadInput.select();
            payloadInput.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Payload Pix copiado! Use esse payload para realizar o pagamento.");
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
        </div>
    </header>
    
    <div class="container-pagamento">
        <h2>Pagamento do Pedido</h2>
        <p><strong>Valor total:</strong> R$ <?php echo number_format($valor_pedido, 2, ',', '.'); ?></p>

        <div class="dados-pix">
            <h3>Informações para pagamento via Pix</h3>
            <p><strong>Banco:</strong> Banco <?php echo $bancoPix; ?></p>
            <p><strong>Agência:</strong> <?php echo $agencia; ?></p>
            <p><strong>Conta:</strong> <?php echo $numero_conta; ?></p>
            <p><strong>Chave Pix:</strong> <?php echo $pixKey; ?></p>
            <p><strong>Payload Pix:</strong> 
                <input type="text" value="<?php echo $payload; ?>" id="pixPayload" readonly>
                <button onclick="copiarPayload()">Copiar Payload Pix</button>
            </p>
            <p><strong>QR Code Pix:</strong><br>
                <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code Pix">
            </p>
        </div>
        
        <div class="instrucoes-pagamento">
            <p>Para realizar o pagamento, copie o payload Pix ou escaneie o QR Code com seu aplicativo bancário.</p>
            <p>Após a confirmação do pagamento, o pedido será processado.</p>
        </div>
    </div>
</body>
</html>
