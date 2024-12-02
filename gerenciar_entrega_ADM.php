<?php
// sessão
include 'session_check.php';

// Conexão com o banco de dados
try {
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Atualizar status do pagamento
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_pedido = $_POST['id_pedido'];
        $novo_status = $_POST['status_pagamento'];

        $stmt = $db->prepare("UPDATE Pedido SET statusPagamento_Ped = :status WHERE ID_Ped = :id");
        $stmt->bindParam(':status', $novo_status);
        $stmt->bindParam(':id', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Consultar pedidos
    $pedidos = $db->query("SELECT * FROM Pedido ORDER BY ID_Ped DESC")->fetchAll(PDO::FETCH_ASSOC);
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

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
        
    </style>
</head>
<body>
    <?php include 'cabecalho/header.php'; ?>
    <main class="container">
        <h2>Pedidos:</h2>
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
                    <h4>Status:</h4>
                    <form action="" method="POST">
                        <input type="hidden" name="id_pedido" value="<?= $pedido['ID_Ped'] ?>">
                        <select name="status_pagamento">
                            <option value="Aguardando pagamento" <?= $pedido['statusPagamento_Ped'] === 'Aguardando pagamento' ? 'selected' : '' ?>>Aguardando pagamento</option>
                            <option value="Pago" <?= $pedido['statusPagamento_Ped'] === 'Pago' ? 'selected' : '' ?>>Pago</option>
                            <option value="Entregue" <?= $pedido['statusPagamento_Ped'] === 'Entregue' ? 'selected' : '' ?>>Entregue</option>
                             <option value="paraEntrega" <?= $pedido['statusPagamento_Ped'] === 'paraEntrega' ? 'selected' : '' ?>>paraEntrega</option>
                        </select>
                        <button type="submit">Atualizar</button>
                    </form>
                    <p>Avaliação: <?= $pedido['avaliacao_Ped'] ? $pedido['avaliacao_Ped'] . '/5' : 'Não avaliado' ?></p>
                    <p>
                        Observações: <?= $pedido['obs_Ped'] ? $pedido['obs_Ped'] : 'Não há' ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </main>
     <?php include 'cabecalho/footer.php'; ?>
</body>
</html>
