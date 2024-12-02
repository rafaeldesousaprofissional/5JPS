<?php

// sumir com o menu para o entregador

// sessão
include 'session_check.php';

// Conexão com o banco de dados
try {
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Atualizar status do pedido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_pedido = $_POST['id_pedido'];
        $novo_status = $_POST['status_pagamento'];
        $motivo = $_POST['motivo'] ?? null;

        // Atualiza o status e, opcionalmente, o motivo
        $stmt = $db->prepare("UPDATE Pedido SET statusPagamento_Ped = :status, obs_Ped = :motivo WHERE ID_Ped = :id");
        $stmt->bindParam(':status', $novo_status);
        $stmt->bindParam(':motivo', $motivo);
        $stmt->bindParam(':id', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Consultar pedidos com status "paraEntrega"
    $pedidos = $db->query("SELECT * FROM Pedido WHERE statusPagamento_Ped = 'paraEntrega' ORDER BY ID_Ped DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Entregas</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="imagens/logo.png"/>
    <style>
        .container {
            padding: 20px;
            max-width: 900px;
            margin: 30px auto 30px;
        }

        .pedido-card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background-color: #f0f0f0;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .pedido-info {
            width: 60%;
        }

        .pedido-info h3 {
            margin: 0;
        }

        .pedido-info ul {
            padding-left: 20px;
            margin: 0;
            list-style: disc;
        }

        .pedido-status {
            width: 35%;
            text-align: center;
        }

        .pedido-status h4 {
            margin: 10px 0;
        }

        .pedido-status button {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-green {
            background-color: #4CAF50;
            color: white;
        }

        .btn-red {
            background-color: #f44336;
            color: white;
        }

        .btn-red:hover, .btn-green:hover {
            opacity: 0.9;
        }

        .motivo-input {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body class="body_index">
    <?php include 'cabecalho/header.php'; ?>
    <main class="container">
        <h2>Pedidos para Entrega:</h2>
        <?php if (!empty($pedidos)): ?>
            <?php foreach ($pedidos as $pedido): ?>
                <div class="pedido-card">
                    <div class="pedido-info">
                        <h3>Endereço:</h3>
                        <p><?= htmlspecialchars($pedido['endereco_Ped']) ?></p>
                        <h4>Cliente: <?= htmlspecialchars($pedido['nomeCliente_Ped']) ?> (<?= htmlspecialchars($pedido['telefoneCliente_Ped']) ?>)</h4>
                        <h4>Pedido #<?= $pedido['ID_Ped'] ?></h4>
                        <p>Produtos:</p>
                        <ul>
                            <?php
                            $produtos = explode(';', $pedido['descricao_Ped']);
                            foreach ($produtos as $produto) {
                                if (trim($produto)) {
                                    [$quantidade, $nome] = explode('*', $produto);
                                    echo "<li>{$quantidade}x {$nome}</li>";
                                }
                            }
                            ?>
                        </ul>
                        <p>Valor Total: R$<?= number_format($pedido['valor_Ped'], 2, ',', '.') ?></p>
                    </div>
                    <div class="pedido-status">
                        <h4>Atualizar Status:</h4>
                        <form action="" method="POST">
                            <input type="hidden" name="id_pedido" value="<?= $pedido['ID_Ped'] ?>">
                            <button type="submit" name="status_pagamento" value="Entregue" class="btn-green">Entregue</button>
                            <button type="submit" name="status_pagamento" value="Pago" class="btn-red">Cancelado</button>
                            <input type="text" name="motivo" class="motivo-input" placeholder= <?= $pedido['obs_Ped'] ? $pedido['obs_Ped'] : 'Motivo (opcional)' ?>  >
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Não há pedidos para entrega no momento.</p>
        <?php endif; ?>
    </main>
    <?php include 'cabecalho/footer.php'; ?>
</body>
</html>
