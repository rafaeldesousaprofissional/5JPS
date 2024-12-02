<?php
include 'session_check.php';

// Conexão com o banco de dados
try {
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ações de CRUD
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? '';
        $valor_antigo = $_POST['valor_antigo'] ?? null;
        $valor_novo = $_POST['valor_novo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $imagem = null;

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
        }

        if (isset($_POST['save'])) {
            if ($id) {
                // Atualização
                if ($imagem) {
                    $stmt = $db->prepare("
                        UPDATE Produto 
                        SET nome_Prod = :nome, valor_Antigo_Prod = :valor_antigo, valor_Prod = :valor_novo, descricao_Prod = :descricao, imagem_Prod = :imagem
                        WHERE ID_Prod = :id
                    ");
                    $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
                } else {
                    $stmt = $db->prepare("
                        UPDATE Produto 
                        SET nome_Prod = :nome, valor_Antigo_Prod = :valor_antigo, valor_Prod = :valor_novo, descricao_Prod = :descricao
                        WHERE ID_Prod = :id
                    ");
                }
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            } else {
                // Criação
                $stmt = $db->prepare("
                    INSERT INTO Produto (nome_Prod, valor_Antigo_Prod, valor_Prod, descricao_Prod, imagem_Prod)
                    VALUES (:nome, :valor_antigo, :valor_novo, :descricao, :imagem)
                ");
                $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
            }
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':valor_antigo', $valor_antigo);
            $stmt->bindParam(':valor_novo', $valor_novo);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->execute();
        }

        if (isset($_POST['delete'])) {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $stmt = $db->prepare("DELETE FROM Produto WHERE ID_Prod = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    $produtos = $db->query("SELECT * FROM Produto")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="imagens/logo.png"/>
    <style>
        /* Estilização simplificada */
        .container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
        }
        .produto {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .produto img {
            width: 100px;
            height: auto;
            border-radius: 8px;
        }
        .produto-info {
            flex: 1;
            margin-left: 20px;
        }
        .produto-info h3 {
            margin: 0;
            font-size: 18px;
        }
        .produto-info p {
            margin: 5px 0;
            color: #666;
        }
        .produto-info .preco {
            color: green;
            font-weight: bold;
        }
        .acoes {
            display: flex;
            gap: 10px;
        }
        .acoes button {
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
        }
        .acoes .editar {
            background-color: #4CAF50;
            color: white;
        }
        .acoes .excluir {
            background-color: #f44336;
            color: white;
        }
        .btn-adicionar {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 20px 0;
            display: block;
        }
        .form_modal {
            background-color: #fff;
            padding: 40px;
        }
        #modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        #modal .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
        }
        #modal .modal-content input,
        #modal .modal-content textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body class="body_index">
    <?php include 'cabecalho/header.php'; ?>
    <div class="container">
        <h1>Produtos</h1>
        <?php foreach ($produtos as $produto): ?>
            <div class="produto">
                <?php if (!empty($produto['imagem_Prod'])): ?>
                    <?php 
                        // Converter blob para base64
                        $imagemBase64 = 'data:image/jpeg;base64,' . base64_encode($produto['imagem_Prod']); 
                    ?>
                    <img src="<?= $imagemBase64 ?>" alt="Imagem do Produto">
                <?php else: ?>
                    <img src="imagem_padrao.jpg" alt="Imagem não disponível">
                <?php endif; ?>
                <div class="produto-info">
                    <h3><?= htmlspecialchars($produto['nome_Prod']) ?></h3>
                    <p><?= htmlspecialchars($produto['descricao_Prod']) ?></p>
                    <p>De: R$<?= number_format($produto['valor_Antigo_Prod'], 2, ',', '.') ?> <br>Por: R$<?= number_format($produto['valor_Prod'], 2, ',', '.') ?></p>
                </div>
                <div class="acoes">
                    <button 
                        class="editar" 
                        data-id="<?= htmlspecialchars($produto['ID_Prod']) ?>"
                        data-nome="<?= htmlspecialchars($produto['nome_Prod']) ?>"
                        data-valor-antigo="<?= htmlspecialchars($produto['valor_Antigo_Prod']) ?>"
                        data-valor-novo="<?= htmlspecialchars($produto['valor_Prod']) ?>"
                        data-descricao="<?= htmlspecialchars($produto['descricao_Prod']) ?>"
                        onclick="abrirModal(this)"
                    >
                        Editar
                    </button>
                    <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $produto['ID_Prod'] ?>">
                        <button type="submit" name="delete" class="excluir">Excluir</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        <button class="btn-adicionar" onclick="abrirModal()">Adicionar Produto</button>
    </div>

    <div id="modal">
        <form class="modal-content" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" required>
            <label for="valor_antigo">Valor Antigo</label>
            <input type="text" name="valor_antigo" id="valor_antigo">
            <label for="valor_novo">Valor Novo</label>
            <input type="text" name="valor_novo" id="valor_novo" required>
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" required></textarea>
            <label for="imagem">Imagem</label>
            <input type="file" name="imagem" id="imagem">
            <button type="submit" name="save">Salvar</button>
            <button type="button" onclick="document.getElementById('modal').style.display='none';">Cancelar</button>
        </form>
    </div>

    <?php include 'cabecalho/footer.php'; ?>

    <script>
        function abrirModal(botao) {
            const modal = document.getElementById('modal');

            if (botao) {
                document.getElementById('id').value = botao.getAttribute('data-id') || '';
                document.getElementById('nome').value = botao.getAttribute('data-nome') || '';
                document.getElementById('valor_antigo').value = botao.getAttribute('data-valor-antigo') || '';
                document.getElementById('valor_novo').value = botao.getAttribute('data-valor-novo') || '';
                document.getElementById('descricao').value = botao.getAttribute('data-descricao') || '';
            } else {
                document.getElementById('id').value = '';
                document.getElementById('nome').value = '';
                document.getElementById('valor_antigo').value = '';
                document.getElementById('valor_novo').value = '';
                document.getElementById('descricao').value = '';
            }

            modal.style.display = 'inline-flex';
        }
    </script>
</body>
</html>
