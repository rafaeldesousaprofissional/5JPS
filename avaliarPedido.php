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

// Verificar submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nota = $_POST['nota'] ?? null;
    $comentario = $_POST['comentario'] ?? '';

    if ($nota !== null) {
        try {
            // Atualizar avaliação e comentário no banco de dados
            $stmt = $db->prepare("UPDATE Pedido SET avaliacao_Ped = ?, obs_Ped = ? WHERE ID_Ped = ?");
            $stmt->execute([$nota, $comentario, $idPedido]);

            // Mensagem de sucesso e redirecionamento
            echo "<script>alert('Obrigado pela sua avaliação!'); window.location.href='index.php';</script>";
            exit();
        } catch (PDOException $e) {
            echo "Erro ao salvar avaliação: " . $e->getMessage();
            exit();
        }
    } else {
        echo "<script>alert('Por favor, selecione uma nota antes de enviar.');</script>";
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
    <title>Avaliar Pedido</title>
    <style>
        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }
        h1 {
            color: #444;
        }
        .rating {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .star {
            font-size: 40px;
            color: #ddd;
            cursor: pointer;
        }
        .star.selected {
            color: #ffcc00;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-top: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .submit-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
    <script>
        // Script para controle das estrelas de avaliação
        function selecionarNota(nota) {
            const estrelas = document.querySelectorAll('.star');
            estrelas.forEach((estrela, index) => {
                estrela.classList.toggle('selected', index < nota);
            });
            document.getElementById('nota').value = nota;
        }
    </script>
</head>
<body class="body_index">
    <?php include'cabecalho/header.php' ?>
    <div class="container">
        <h1>Avalie seu Pedido</h1>
        <form method="POST">
            <p>De sua nota de 0 a 5:</p>
            <div class="rating">
                <span class="star" onclick="selecionarNota(1)">★</span>
                <span class="star" onclick="selecionarNota(2)">★</span>
                <span class="star" onclick="selecionarNota(3)">★</span>
                <span class="star" onclick="selecionarNota(4)">★</span>
                <span class="star" onclick="selecionarNota(5)">★</span>
            </div>
            <input type="hidden" name="nota" id="nota" value="">

            <p>Nos conte o que achou:</p>
            <textarea name="comentario" placeholder="Comida saborosa..."></textarea>

            <button type="submit" class="submit-btn">Enviar Avaliação</button>
        </form>
    </div>

    <?php include'cabecalho/footer.php' ?>
</body>
</html>
