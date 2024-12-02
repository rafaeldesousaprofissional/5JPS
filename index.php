<?php 
// Conexão com o banco de dados SQLite usando PDO
include 'bancoDados.php';
include 'session_check.php'; 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css"/>
  <link rel="shortcut icon" href="imagens/logo.png"/>
  <title>Home - Quentinhas TASI</title>
</head>
<body class="body_index">
  <?php
  include 'cabecalho/header.php';
  ?>

  <div class="container">
    <h1>Produtos em Destaque</h1>
    <form id="formCarrinho" method="POST" action="carrinho.php">
      <div class="carousel">
        <?php
        // Consulta os produtos no banco de dados
        try {
            $stmt = $db->query("SELECT * FROM Produto");
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($produtos) > 0) {
                foreach ($produtos as $produto) {
                    echo '
                    <div class="carousel-item">
                      <img src="exibir_imagem.php?id='.$produto['ID_Prod'].'" alt="'.$produto['nome_Prod'].'">
                      <h3>'.$produto['nome_Prod'].'</h3>
                      <p class="description">'.$produto['descricao_Prod'].'</p>
                      <p class="price">DE: R$ '.number_format($produto['valor_Antigo_Prod'], 2, ',', '.').'</p>
                      <p class="price" style="color: green;">POR: R$ '.number_format($produto['valor_Prod'], 2, ',', '.').'</p>
                      <label for="quantidade_'.$produto['ID_Prod'].'">Quantidade:</label>
                      <input 
                        type="number" 
                        id="quantidade_'.$produto['ID_Prod'].'" 
                        name="produtos['.$produto['ID_Prod'].']" 
                        min="0" 
                        max="99" 
                        value="0" 
                        class="produto-input">
                    </div>
                    ';
                }
            } else {
                echo '<p>Nenhum produto disponível.</p>';
            }
        } catch (PDOException $e) {
            echo "Erro ao buscar produtos: " . $e->getMessage();
        }
        ?>
      </div>
         <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === '1') {

          echo '
            <button type="submit" class="btn-carrinho">Adicionar ao Carrinho</button>
            ';
          }
        ?>
    </form>
  </div>

  <?php include 'cabecalho/footer.php'; ?>

  <script>
    $(document).ready(function() {
      // Inicializa o carrossel
      $('.carousel').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 1
            }
          }
        ]
      });

      // Remove os campos com valor 0 antes de enviar o formulário
      $('#formCarrinho').on('submit', function(e) {
        $('.produto-input').each(function() {
          if ($(this).val() === "0" || $(this).val() === "") {
            $(this).prop('disabled', true); // Desabilita o campo para que não seja enviado
          }
        });
      });
    });
  </script>
</body>
</html>
