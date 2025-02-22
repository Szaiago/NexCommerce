<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['nome_usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Nome do usuário logado
$nome_usuario = $_SESSION['nome_usuario'];

// Extrair as duas primeiras palavras do nome para exibição
$nome_partes = explode(" ", $nome_usuario);
$nome_exibido = $nome_partes[0];
if (isset($nome_partes[1])) {
    $nome_exibido .= " " . $nome_partes[1];
}

// Configuração do banco de dados
$host   = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "NextCommerce";

// Criar conexão
$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Obter ID do usuário
$user_id = isset($_SESSION['id_usuario']) ? (int) $_SESSION['id_usuario'] : 0;
if ($user_id === 0) {
    die("Erro: ID do usuário inválido.");
}

// Consultar dados do usuário
$sql_usuario = "SELECT id_usuario, nome_usuario, email_usuario, senha_usuario FROM usuario WHERE id_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
if ($stmt_usuario === false) {
    die("Erro na preparação da consulta do usuário: " . $conn->error);
}
$stmt_usuario->bind_param("i", $user_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();
$stmt_usuario->close();

// Consultar dados adicionais (se existirem) para pré-preencher os inputs
$sql_dados = "SELECT cpf, cep, rua, estado, cidade, bairro, complemento FROM dados_adicionais WHERE id_usuario = ?";
$stmt_dados = $conn->prepare($sql_dados);
if ($stmt_dados === false) {
    die("Erro na preparação da consulta dos dados adicionais: " . $conn->error);
}
$stmt_dados->bind_param("i", $user_id);
$stmt_dados->execute();
$result_dados = $stmt_dados->get_result();
if ($result_dados->num_rows > 0) {
    $dados = $result_dados->fetch_assoc();
} else {
    $dados = array(
        'cpf'         => '',
        'cep'         => '',
        'rua'         => '',
        'estado'      => '',
        'cidade'      => '',
        'bairro'      => '',
        'complemento' => ''
    );
}
$stmt_dados->close();

// Gerar as iniciais do usuário para exibição
$iniciais = strtoupper(substr($nome_partes[0], 0, 1));
if (isset($nome_partes[1])) {
    $iniciais .= strtoupper(substr($nome_partes[1], 0, 1));
}

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

// Consulta SQL para obter os pedidos do usuário logado
$sql_pedidos = "SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY data_pedido DESC";
$stmt_pedidos = $conn->prepare($sql_pedidos);
if ($stmt_pedidos === false) {
    die("Erro na preparação da consulta de pedidos: " . $conn->error);
}

$stmt_pedidos->bind_param("i", $user_id);
$stmt_pedidos->execute();
$result_pedidos = $stmt_pedidos->get_result();
$stmt_pedidos->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home/NextCommerce</title>
  <link rel="icon" href="../css/images/next1.png" type="image/x-icon">
  <link rel="stylesheet" href="../css/fixo.css">
  <link rel="stylesheet" href="../css/perfil.css">
  <link rel="stylesheet" href="../css/home.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="../js/logoempresamain.js" defer></script>
  <script src="../js/modalperfil.js" defer></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  
  <!-- jQuery e jQuery Mask Plugin para as máscaras -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script>
    $(document).ready(function(){
      // Aplica a máscara de CPF e CEP
      $('#cpf').mask('000.000.000-00');
      $('#cep').mask('00000-000');
    });
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
          <a href="carrinho.php" class="favoritos"><i class="bi bi-bag" style="font-size:22px;"></i>
            <?php if ($total_itens_carrinho > 0): ?>
              <span class="badge"><?php echo $total_itens_carrinho; ?></span> <!-- Exibe o número de itens -->
            <?php endif; ?>
          </a>
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
  
  <div class="container-perfil">
    <div class="perfil-header">
      <div class="perfil-iniciais">
        <?php echo htmlspecialchars($iniciais); ?>
      </div>
    </div>
    <div class="dados-perfil">
      <p class="id-usuario"><strong>ID USUÁRIO:</strong> <?php echo htmlspecialchars($usuario['id_usuario']); ?></p>
      <h2 class="nome-usuario"><?php echo htmlspecialchars($usuario['nome_usuario']); ?></h2>
      <div class="dados">
        <p class="dados-usuario">DADOS</p>
      </div>
      <p class="email-usuario"><strong>EMAIL:</strong> <?php echo htmlspecialchars($usuario['email_usuario']); ?></p>
      
      <form class="dados-adicionais" method="POST" action="../functions/dados-adicionais-perfil.php">
        <div class="orientar-dados">
            <div class="cpf">
                <label for="cpf">CPF</label>
             <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" value="<?php echo htmlspecialchars($dados['cpf'] ?? ''); ?>" required>
            </div>
            <div class="cep">
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" placeholder="00000-000" maxlength="9" value="<?php echo htmlspecialchars($dados['cep'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="orientar-dados">
            <div class="estado">
                <label for="estado">Estado</label>
                <input type="text" id="estado" name="estado" placeholder="Estado" style="text-align: center;" value="<?php echo htmlspecialchars($dados['estado'] ?? ''); ?>" required>
            </div>
            <div class="cidade">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" placeholder="Cidade" style="text-transform: uppercase;" value="<?php echo htmlspecialchars($dados['cidade'] ?? ''); ?>" required>
            </div>
            <div class="bairro">
                <label for="bairro">Bairro</label>
                <input type="text" id="bairro" name="bairro" placeholder="Bairro" style="text-transform: uppercase;" value="<?php echo htmlspecialchars($dados['bairro'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="orientar-dados">
            <div class="rua">
                <label for="rua">Rua</label>
                <input type="text" id="rua" name="rua" placeholder="Rua" style="text-transform: uppercase;" value="<?php echo htmlspecialchars($dados['rua'] ?? ''); ?>">
            </div>
            <div class="complemento">
                <label for="complemento">Complemento</label>
                <input type="text" id="complemento" name="complemento" placeholder="Complemento"  style="text-transform: uppercase;" value="<?php echo htmlspecialchars($dados['complemento'] ?? ''); ?>">
            </div>
        </div>
        <button type="submit">SALVAR</button>
      </form>
    </div>
  </div>
  <div class="container-pedidos">
    <div class="titulo-pedido">  
      <h2>MEUS PEDIDOS</h2>
    </div>

  <?php if ($result_pedidos->num_rows > 0): ?>
  <ul>
    <?php while ($pedido = $result_pedidos->fetch_assoc()): ?>
      <div class="pedido" onclick="window.location.href='pedido.php?id=<?php echo $pedido['id_pedido']; ?>'">
        <div class="id-pedido">
          <p>ID: <?php echo htmlspecialchars($pedido['id_pedido']); ?></p>
        </div>
        <div class="itens-pedido">
          <div class="subtitulo-pedido">
            <p>ITENS PEDIDO:</p>
          </div>
          <p><?php echo htmlspecialchars($pedido['itens_pedido']); ?></p>
        </div>
        <div class="valor-pedido">
          <div class="subtitulo-pedido">
            <p>VALOR TOTAL:</p>
          </div>
          <p>R$ <?php echo number_format($pedido['valor_pedido'], 2, ',', '.'); ?></p>
        </div>
        <div class="status-pedido">
          <div class="subtitulo-pedido">
            <p>STATUS PEDIDO:</p>
          </div>
          <p><?php echo htmlspecialchars($pedido['status_pedido']); ?></p>
        </div>
        <div class="endereco-pedido">
          <div class="subtitulo-pedido">
            <p>ENDEREÇO:</p>
          </div>
          <p>
            <?php echo htmlspecialchars($pedido['rua']); ?>, 
            <?php echo htmlspecialchars($pedido['bairro']); ?>, 
            <?php echo htmlspecialchars($pedido['cidade']); ?> - 
            <?php echo htmlspecialchars($pedido['cep']); ?><br>
            <?php echo htmlspecialchars($pedido['complemento']); ?>
          </p>
        </div>
        <div class="data-pedido">
          <div class="subtitulo-pedido">
            <p>DATA PEDIDO:</p>
          </div>
          <p><?php echo date("d/m/Y H:i", strtotime($pedido['data_pedido'])); ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  </ul>
<?php else: ?>
  <p>Você ainda não fez nenhum pedido.</p>
<?php endif; ?>

</div>



  </div>
  <script>
    // Ao sair do campo CEP, busca os dados via API do ViaCEP e preenche os inputs
    document.getElementById("cep").addEventListener("blur", function(){
      let cep = this.value.replace(/\D/g, '');
      if (cep.length !== 8) {
        alert("Digite um CEP válido!");
        return;
      }
      fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
          if (data.erro) {
            alert("CEP inválido!");
            document.getElementById("rua").value = "";
            document.getElementById("estado").value = "";
            document.getElementById("cidade").value = "";
            document.getElementById("bairro").value = "";
          } else {
            // Preenche os campos com os dados retornados pela API (caso algum valor não venha, o usuário pode editá-lo)
            document.getElementById("rua").value    = data.logradouro || "";
            document.getElementById("estado").value = data.uf         || "";
            document.getElementById("cidade").value = data.localidade || "";
            document.getElementById("bairro").value = data.bairro     || "";
          }
        })
        .catch(error => {
          console.error("Erro ao buscar o CEP:", error);
        });
    });
  </script>
</body>
</html>
