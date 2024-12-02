<?php

include 'session_check.php';

try {
    // Conexão com o banco de dados
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebendo dados do formulário
        $nome = $_POST['nome'] ?? '';
        $valor = $_POST['valor'] ?? '';
        $descricao = $_POST['descricao'] ?? '';

        // Validando se o arquivo foi enviado
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            // Lendo a imagem como binário
            $imagem = file_get_contents($_FILES['imagem']['tmp_name']);

            // Inserindo no banco
            $stmt = $db->prepare("
                INSERT INTO Produto (nome_Prod, valor_Prod, descricao_Prod, imagem_Prod) 
                VALUES (:nome, :valor, :descricao, :imagem)
            ");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
            $stmt->execute();

            echo "Produto adicionado com sucesso!";
        } else {
            echo "Erro ao enviar a imagem.";
        }
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
  

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto - ADM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="imagens/logo.png"/>
</head>
<body>
    
   <?php include 'cabecalho/header.php';?>
    
    <div class="container">
        <h1>Adicionar Produto</h1>
        <form action="adicionar_produto_ADM.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome do Produto:</label>
                <input type="text" id="nome" name="nome" placeholder="(ex): Feijoada" required>
            </div>
            <div class="form-group">
                <label for="valor">Valor do Produto (R$):</label>
                <input type="text" id="valor" name="valor" placeholder="(ex): 2.99" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição do Produto:</label>
                <textarea id="descricao" name="descricao" rows="4" placeholder="(ex): Feijoada com couve, acompanha farofa" required></textarea>
            </div>
            <div class="form-group">
                <label for="imagem">Imagem do Produto:</label>
                <input type="file" id="imagem" name="imagem" accept="image/*" required>
            </div>
            <button type="submit">Adicionar Produto</button>
        </form>
    </div>
    
     <?php include 'cabecalho/footer.php'; ?>
    
</body>
</html>

