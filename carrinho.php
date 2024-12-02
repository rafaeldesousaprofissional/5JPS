<?php
include 'bancoDados.php';
session_start();

// Inicializar o carrinho caso ele ainda não exista
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Processar o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['produtos'])) {
        // Atualizar o carrinho com os produtos enviados
        $produtosSelecionados = $_POST['produtos'];
        foreach ($produtosSelecionados as $idProduto => $quantidade) {
            $quantidade = (int)$quantidade; // Garantir que seja inteiro
            if ($quantidade > 0) {
                $_SESSION['carrinho'][$idProduto] = $quantidade; // Adicionar ou atualizar o carrinho
            } else {
                unset($_SESSION['carrinho'][$idProduto]); // Remover se a quantidade for 0
            }
        }
    }
}

// Obter os produtos do carrinho
$carrinho = $_SESSION['carrinho'];
$produtos = [];
if (!empty($carrinho)) {
    // Preparar consulta para buscar detalhes dos produtos
    $placeholders = implode(',', array_fill(0, count($carrinho), '?'));
    $stmt = $db->prepare("SELECT * FROM Produto WHERE ID_Prod IN ($placeholders)");
    $stmt->execute(array_keys($carrinho));
    $produtosDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Garantir que os produtos sejam indexados corretamente com base no ID
    foreach ($produtosDb as $produtoDb) {
        $idProduto = $produtoDb['ID_Prod'];
        if (isset($carrinho[$idProduto])) {
            $quantidade = $carrinho[$idProduto];
            $produtoDb['quantidade'] = $quantidade;
            $produtoDb['subtotal'] = $produtoDb['valor_Prod'] * $quantidade;
            $produtos[$idProduto] = $produtoDb; // Indexar pelo ID do produto
        }
    }
}

// Calcular o valor total do carrinho
$valorTotal = array_reduce($produtos, function($carry, $produto) {
    return $carry + $produto['subtotal'];
}, 0);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="imagens/logo.png"/>
    <title>Carrinho - Quentinhas TASI</title>
    <style>
        /* Adicionar seus estilos personalizados aqui */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 30px;
        }
        h1 {
            text-align: center;
            color: #000;
        }
        .produto-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f7f7f7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .produto-card img {
            width: 120px;
            height: auto;
            border-radius: 8px;
        }
        .produto-info {
            flex: 1;
            margin-left: 20px;
        }
        .produto-info h2 {
            margin: 0 0 10px;
            font-size: 18px;
        }
        .produto-info p {
            margin: 5px 0;
            color: #555;
        }
        .quantidade {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantidade input {
            width: 50px;
            text-align: center;
            margin: 0 10px;
        }
        .resumo {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .resumo h2 {
            text-align: center;
            color: #333;
        }
        .btn-continuar {
            display: block;
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: #fff;
            text-align: center;
            border-radius: 10px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .btn-continuar:hover {
            background: #218838;
        }
        .frete {
            color: #28a745;
            font-weight: bold;
        }
        .botoes{
            display: flex;
        }
        .valor{
            margin-right: 40px;
        }
    </style>
</head>
<body class="body_index">
    <?php include 'cabecalho/header.php'; ?>
    <div class="container">
        <h1>Quentinhas TASI</h1>

        <?php if (!empty($produtos)): ?>
            <div class="produtos">
                <?php foreach ($produtos as $produto): ?>
                    <div class="produto-card">
                        <?php
                            // Verificar se a imagem está presente no banco de dados
                            $imagem = base64_encode($produto['imagem_Prod']); // Codifica a imagem em base64
                        ?>
                        <img src="data:image/jpeg;base64,<?= $imagem ?>" alt="<?= htmlspecialchars($produto['nome_Prod']) ?>">
                        <div class="produto-info">
                            <h2><?= htmlspecialchars($produto['nome_Prod']) ?></h2>
                            <p>De: <s>R$ <?= number_format($produto['valor_Antigo_Prod'], 2, ',', '.') ?></s></p>
                            <p>Por: <strong style="color: #28a745;">R$ <?= number_format($produto['valor_Prod'], 2, ',', '.') ?></strong></p>
                        </div>
                        <div class="valor">
                            <p>valor: R$ <?= number_format($produto['valor_Prod'] * $produto['quantidade'], 2, ',', '.') ?></p>
                        </div>
                        
                        <div class="quantidade">
                            <form method="POST">
                                <input type="number" name="produtos[<?= $produto['ID_Prod'] ?>]" value="<?= $produto['quantidade'] ?>" min="0">
                                <div class="botoes">
                                    
                                 <button type="submit" name="produtos[<?= $produto['ID_Prod'] ?>]" value="<?= $produto['quantidade'] - 1 ?>" style="padding: 5px 15px;">-</button>
                                <button type="submit" name="produtos[<?= $produto['ID_Prod'] ?>]" value="<?= $produto['quantidade'] + 1 ?>" style="padding: 5px 15px;">+</button>
                                    
                                </div>
                                
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div>
                <a href="index.php" class="btn-continuar">Adicionar Produtos</a>
            </div>

            <div class="resumo">
                <h2>Resumo do Pedido</h2>
                <p>Subtotal: R$ <?= number_format($valorTotal, 2, ',', '.') ?></p>
                <p>Frete: <span class="frete">Grátis</span></p>
                <p>Total: R$ <?= number_format($valorTotal, 2, ',', '.') ?></p>
                <a href="pagamento.php" class="btn-continuar">Continuar</a>
            </div>
        <?php else: ?>
            <p>Seu carrinho está vazio.</p>
            <a href="index.php" class="btn-continuar">Adicionar Produtos</a>
        <?php endif; ?>
    </div>
     <?php include 'cabecalho/footer.php'; ?>
</body>
</html>
