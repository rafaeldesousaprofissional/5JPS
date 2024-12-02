<head>
  <script>
    function menu() {

      if(document.getElementById("menu").style.display === "flex"){
        document.getElementById("menu").style.display = "none";
      }
      else{
        document.getElementById("menu").style.display = "flex";
      }

    }
  </script>
</head>
<header>
  <?php 
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] != '3') {
    echo '
      <button class="botao_menu" onclick="menu()" >
         <img src="imagens/menu.png" alt="Menu" />
      </button>
      '; 
    }
  ?>
      <a href="index.php">
        <img src="imagens/logo.png" alt="Logo" />
        <p>
          TASI Quentinhas
        </p>
      </a>
  <div class="usuario" >
    <p>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</p>
    <p>Você está logado como <?php echo htmlspecialchars($_SESSION['user_role']); ?>.</p>
    <a href="logout.php">Sair</a>
  </div>
  
</header>
<div id="menu">
      <?php 
      if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === '1') { // cliente
          echo '
          <a href="carrinho.php">
            <button class="botao-cta">
              <img src="imagens/carrinho_icon.png" alt="carrinho" width="15%"/>
              Carrinho
            </button>
          </a>
          <a href="login.php">
            <button class="botao-cta">
              <img src="imagens/user_icon.png" alt="login" width="15%"/>
              Login
            </button>
          </a>
          <a href="cadastro.php">
            <button class="botao-cta">
              <img src="imagens/user_icon.png" alt="cadastro" width="15%"/>
              Cadastro
            </button>
          </a>
          ';
      }
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === '2') { //admin 
            echo '
            <a href="gerenciar_produto_ADM.php">
              <button class="botao-cta">
                <img src="imagens/user_icon.png" alt="Gereciar Produto" width="15%"/>
                Gerenciar Produto
              </button>
            </a>
            <a href="adicionar_produto_ADM.php">
              <button class="botao-cta">
                <img src="imagens/user_icon.png" alt="adicionar Produto" width="15%"/>
                Adicionar Produto
              </button>
            </a>
            <a href="gerenciar_entrega_ADM.php">
              <button class="botao-cta">
                <img src="imagens/user_icon.png" alt="Gerenciar Entregas" width="15%"/>
                Gerenciar Entregas
              </button>
            </a>
            ';
        }
      ?>
</div>