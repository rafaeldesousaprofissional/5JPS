<?php
include 'bancoDados.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obter o ID do pedido via GET
$idPedido = $_GET['id_pedido'] ?? null;

if (!$idPedido) {
    echo "Pedido não encontrado.";
    exit();
}

try {
    // Buscar informações do pedido no banco de dados
    $stmt = $db->prepare("SELECT * FROM Pedido WHERE ID_Ped = ?");
    $stmt->execute([$idPedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        echo "Pedido não encontrado.";
        exit();
    }
} catch (PDOException $e) {
    echo "Erro ao buscar pedido: " . $e->getMessage();
    exit();
}

// Status do pedido
$status = $pedido['statusPagamento_Ped'];

// Estilos de status para os ícones
$statusStyle = [
    'Aguardando pagamento' => ['Preparando' => true, 'Sair para entrega' => false, 'Entregue' => false],
    'paraEntrega' => ['Preparando' => true, 'Sair para entrega' => true, 'Entregue' => false],
    'Entregue' => ['Preparando' => true, 'Sair para entrega' => true, 'Entregue' => true],
];
$currentStatus = $statusStyle[$status] ?? ['Preparando' => false, 'Sair para entrega' => false, 'Entregue' => false];

// Redirecionar para avaliar pedido ao ser entregue
if ($status === 'Entregue') {
    header("Location: avaliarPedido.php?id_pedido={$idPedido}");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="imagens/logo.png"/>
    <title>Acompanhar Pedido</title>
    <style>
        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }
        .status-container {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }
        .status-card {
            background-color: #ffd29c;
            padding: 20px;
            border-radius: 10px;
            width: 30%;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .status-card.active {
            background-color: #ffc07f;
            border: 2px solid #ff9900;
        }
        .status-card h3 {
            margin: 10px 0;
            font-size: 16px;
            color: #444;
        }
        .status-icon {
            font-size: 40px;
            color: #444;
        }
        .status-icon.active {
            color: #ff9900;
        }
        .continue-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .continue-btn:hover {
            background-color: #218838;
        }
        .info {
            margin-top: 20px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body class="body_index">
    <?php include'cabecalho/header.php' ?>
    <div class="container">
        <h1>Status do Pedido</h1>
        <p class="info">Tempo previsto: 30min</p>

        <div class="status-container">
            <div class="status-card <?= $currentStatus['Preparando'] ? 'active' : '' ?>">
                <div class="status-icon <?= $currentStatus['Preparando'] ? 'active' : '' ?>">✔️</div>
                <h3>Preparando</h3>
            </div>
            <div class="status-card <?= $currentStatus['Sair para entrega'] ? 'active' : '' ?>">
                <div class="status-icon <?= $currentStatus['Sair para entrega'] ? 'active' : '' ?>">🕒</div>
                <h3>Saiu para Entrega</h3>
            </div>
            <div class="status-card <?= $currentStatus['Entregue'] ? 'active' : '' ?>">
                <div class="status-icon <?= $currentStatus['Entregue'] ? 'active' : '' ?>">🕒</div>
                <h3>Entregue</h3>
            </div>
        </div>

        <?php if ($status !== 'Entregue'): ?>
            <button class="continue-btn" onclick="alert('Acompanhe até a entrega!')">Continuar</button>
        <?php endif; ?>
    </div>

    <?php include'cabecalho/footer.php' ?>
</body>
</html>
