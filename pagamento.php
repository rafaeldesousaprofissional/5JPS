<?php
include 'bancoDados.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Obter o ID do usuário logado
$userId = $_SESSION['user_id'];

try {
    // Buscar informações do usuário logado no banco de dados
    $stmt = $db->prepare("SELECT * FROM pessoa WHERE ID_pessoa = ?");
    $stmt->execute([$userId]);
    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pessoa) {
        echo "Erro: Usuário não encontrado.";
        exit();
    }
} catch (PDOException $e) {
    echo "Erro ao buscar informações do usuário: " . $e->getMessage();
    exit();
}

// Obter o carrinho da sessão
$carrinho = $_SESSION['carrinho'] ?? [];

// Verificar se o carrinho está vazio
if (empty($carrinho)) {
    header('Location: carrinho.php');
    exit();
}

// Inicializar variáveis
$produtos = [];
$valorTotal = 0;

try {
    // Buscar informações dos produtos no banco de dados
    $placeholders = implode(',', array_fill(0, count($carrinho), '?'));
    $stmt = $db->prepare("SELECT * FROM Produto WHERE ID_Prod IN ($placeholders)");
    $stmt->execute(array_keys($carrinho));
    $produtosDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular total e montar lista de produtos
    foreach ($produtosDb as $produto) {
        $idProduto = $produto['ID_Prod'];
        $quantidade = $carrinho[$idProduto] ?? 0;

        if ($quantidade > 0) {
            $produto['quantidade'] = $quantidade;
            $produto['subtotal'] = $produto['valor_Prod'] * $quantidade;
            $produtos[$idProduto] = $produto;
            $valorTotal += $produto['subtotal'];
        }
    }
} catch (PDOException $e) {
    echo "Erro ao buscar produtos: " . $e->getMessage();
    exit();
}

// Processar o formulário de pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formaPagamento = $_POST['forma_pagamento'] ?? null;

    if ($formaPagamento) {
        // Montar a descrição do pedido
        $descricaoPedido = '';
        foreach ($produtos as $produto) {
            $descricaoPedido .= "{$produto['quantidade']}*{$produto['nome_Prod']};";
        }

        // Usar os dados do usuário logado
        $nomeCliente = $pessoa['nome_pessoa'];
        $telefoneCliente = $pessoa['tel_pessoa'];
        $enderecoCliente = "{$pessoa['rua_pessoa']}, {$pessoa['bairro_pessoa']}";
        $observacao = 'Sem observações';

        try {
            // Inserir o pedido no banco de dados
            $stmt = $db->prepare("
                INSERT INTO Pedido (
                    valor_Ped, nomeCliente_Ped, telefoneCliente_Ped, descricao_Ped,
                    statusPagamento_Ped, obs_Ped, endereco_Ped
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $valorTotal,
                $nomeCliente,
                $telefoneCliente,
                $descricaoPedido,
                'Aguardando Pagamento',
                $observacao,
                $enderecoCliente
            ]);

            // Obter o ID do pedido recém-criado
            $idPedido = $db->lastInsertId();

            // Limpar o carrinho
            $_SESSION['carrinho'] = [];

            // Redirecionar para a página de acompanhamento do pedido
            header("Location: acompanharPedido.php?id_pedido={$idPedido}");
            exit();
        } catch (PDOException $e) {
            echo "Erro ao salvar o pedido: " . $e->getMessage();
            exit();
        }
    } else {
        $erro = 'Por favor, selecione uma forma de pagamento.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="imagens/logo.png"/>
    <title>Pagamento - Quentinhas TASI</title>
    <style>
        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }
        .titulo {
            color: #000;
            font-size: 24px;
        }
        .forma-pagamento {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .forma-pagamento label {
            cursor: pointer;
            font-size: 18px;
        }
        .btn-continuar {
            padding: 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .btn-continuar:hover {
            background-color: #218838;
        }
        .erro {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="body_index">
    <div class="container">
        <h1 class="titulo">Pagamento</h1>
        <p>Olá, <strong><?= htmlspecialchars($pessoa['nome_pessoa']) ?></strong>!</p>
        <p>Valor Total: R$ <?= number_format($valorTotal, 2, ',', '.') ?></p>

        <p>Endereço: <?= htmlspecialchars("{$pessoa['rua_pessoa']}, {$pessoa['bairro_pessoa']}") ?></p>

        <?php if (isset($erro)): ?>
            <p class="erro"><?= $erro ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="forma-pagamento">
                <label>
                    <input type="radio" name="forma_pagamento" value="Cartão"> Cartão
                </label>
                <label>
                    <input type="radio" name="forma_pagamento" value="Pix"> Pix
                </label>
                <label>
                    <input type="radio" name="forma_pagamento" value="Dinheiro"> Dinheiro
                </label>
            </div>
            <button type="submit" class="btn-continuar">Finalizar Pedido</button>
        </form>
    </div>
</body>
</html>
