<?php
try {
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Busca a imagem no banco de dados
        $stmt = $db->prepare("SELECT imagem_Prod FROM Produto WHERE ID_Prod = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto && $produto['imagem_Prod']) {
            // Define o cabeçalho para o tipo da imagem
            header("Content-Type: image/jpeg");
            echo $produto['imagem_Prod'];
        } else {
            // Imagem não encontrada, retorna uma imagem padrão
            header("Content-Type: image/jpeg");
            readfile('imagens/default.jpg'); // Substitua pelo caminho da imagem padrão
        }
    } else {
        echo "ID do produto não especificado.";
    }
} catch (PDOException $e) {
    echo "Erro ao buscar a imagem: " . $e->getMessage();
}
?>
