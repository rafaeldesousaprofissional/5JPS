<?php
try {
    $msg = "";
    // Conexão com o banco de dados SQLite
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $endereco = $_POST['endereco'];
        $bairro = $_POST['bairro'];
        $tipo_pessoa = $_POST['tipo_pessoa'];
        $senha = $_POST['senha'];

        // Valida os campos
        if (!empty($nome) && !empty($email) && !empty($telefone) && !empty($endereco) && !empty($bairro) && !empty($senha) && isset($tipo_pessoa)) {
            // Gera o hash da senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Prepara a consulta SQL para inserir os dados no banco
            $sql = "INSERT INTO pessoa (nome_pessoa, email_pessoa, senha_pessoa, tel_pessoa, rua_pessoa, bairro_pessoa, tipo_pessoa) 
                    VALUES (:nome, :email, :senha, :telefone, :endereco, :bairro, :tipo_pessoa)";

            $stmt = $db->prepare($sql);

            // Associa os parâmetros à consulta
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':bairro', $bairro);
            $stmt->bindParam(':tipo_pessoa', $tipo_pessoa, PDO::PARAM_INT);

            // Executa a consulta
            if ($stmt->execute()) {
                $msg = "Cadastro realizado com sucesso!";
            } else {
                $msg = "Erro ao cadastrar.";
            }
        } else {
            $msg = "Por favor, preencha todos os campos.";
        }
    }
} catch (PDOException $e) {
    $msg = "Erro no banco de dados: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <link rel="shortcut icon" href="imagens/logo.png"/>
    <title>Cadastro - Quentinhas TASI</title>
    <style>
      .body_cadastro {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #fdd4d1;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
      }
  
      .container {
        display: flex;
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      }
  
      .images {
        flex: 1;
        display: grid;
        grid-template-rows: 1fr 1fr;
        grid-gap: 10px;
        padding: 10px;
        background-color: #f8a3a0;
      }
  
      .images img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
      }
  
      .form-container {
        flex: 1;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
      }
  
      .form-container h1 {
        margin: 0 0 10px;
        font-size: 1.8rem;
        color: #000;
        display: flex;
        justify-content: center;
      }
  
      .form-container p {
        margin: 0 0 30px;
        color: #666;
        display: flex;
        justify-content: center;
      }
  
      .form-container a {
        color: #f54254;
        text-decoration: none;
      }
  
      .form-container label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.9rem;
        color: #444;
      }
  
      .form-container input {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
      }
  
      .form-container input:focus {
        border-color: #f54254;
        outline: none;
      }
  
      .form-container button {
        background-color: #000;
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
      }
  
      .form-container button:hover {
        background-color: #333;
      }
  
      .form-container .terms {
        font-size: 0.8rem;
        color: #666;
        text-align: center;
      }
      .form-container form{
        display: flex;
        flex-direction: column;
      }
      .logo{
        display: flex;
        align-items: center;
        color: black;
        text-decoration: none;  /* Remova sublinhado de links */
        flex-direction: column;
      }
    </style>
</head>
<body class="body_cadastro">
    <div class="container">
        <div class="images">
            <img src="imagens/prato1.png" alt="Prato 1">
            <img src="imagens/prato2.png" alt="Prato 2">
        </div>
        <div class="form-container">
            <a class="logo" href="index.php">
                <img src="imagens/logo.png" alt="Logo" />
                <p>TASI Quentinhas</p>
            </a>
            <h1>Cria sua conta</h1>
            <p>e tenha acesso ao nosso delivery de quentinhas</p>
            <form action="cadastro.php" method="POST">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" placeholder="(xx) 98765-4321" required>

                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco" placeholder="ex: Rua clarimundo de melo, 123" required>

                <label for="bairro">Bairro</label>
                <input type="text" id="bairro" name="bairro" required>

                <label for="tipo_pessoa">Tipo de Usuário</label>
                <select id="tipo_pessoa" name="tipo_pessoa" required>
                    <option value="1">Cliente</option>
                    <option value="2">Administrador</option>
                    <option value="3">Entregador</option>
                </select>

                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" minlength="6" required>

                <button type="submit">Criar</button>
            </form>
            <?php if (!empty($msg)) { echo '<p>' . $msg . '</p>'; } ?>
            <p class="terms">Ao criar a conta você concorda com os <a href="#">Termos de Serviço</a></p>
            <p>Já tem uma conta? <a href="login.php">Faça Login aqui</a></p>
        </div>
        <div class="images">
            <img src="imagens/prato3.png" alt="Prato 3">
            <img src="imagens/prato4.png" alt="Prato 4">
        </div>
    </div>
</body>
</html>
