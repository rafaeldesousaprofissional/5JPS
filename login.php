<?php
session_start(); // Inicia a sessão

try {
    $msg = "";
    // Conexão com o banco de dados SQLite
    $db = new PDO('sqlite:database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Consulta para verificar o usuário com base no email e tipo
        $sql = "SELECT * FROM pessoa WHERE email_pessoa = :email AND tipo_pessoa = :role";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role, PDO::PARAM_INT);
        $stmt->execute();

        // Busca o usuário no banco
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifica a senha
            if (password_verify($password, $user['senha_pessoa'])) {
                // Salva os dados do usuário na sessão
                $_SESSION['user_id'] = $user['ID_pessoa'];
                $_SESSION['user_email'] = $user['email_pessoa'];
                $_SESSION['user_role'] = $user['tipo_pessoa']; // Salva o tipo de usuário na sessão

                // Redireciona para a página inicial
                if ($role == 1) { // cliente
                    header("Location: index.php");
                  }
                else if ($role == 2){ // ADM
                    header("Location: index.php");
                }
                else{ // Entregador
                  header("Location: gerenciar_entrega_Entregador.php");
                }
                exit;
            } else {
                $msg = "Senha incorreta.";
            }
        } else {
            $msg = "Usuário ou tipo de conta não encontrado.";
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
  <title>Login - Quentinhas TASI</title>
  <link rel="shortcut icon" href="imagens/logo.png"/>
  <!-- <link rel="stylesheet" href="styles.css"> -->
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

    select {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
    }
    
    .dropdown {
      position: relative;
      display: flex;
      flex-direction: column;
    }

    .dropdown select {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background: #fff url('imagens/seta.png') no-repeat right 10px center;
      background-size: 12px;
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
        <p>
          TASI Quentinhas
        </p>
      </a>
      <h1>Faça Login</h1>
      <form action="login.php" method="POST">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Digite seu email" required>

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" placeholder="Digite sua senha" required>

        <div class="dropdown">
          <label for="role">Tipo de usuário</label>
          <select id="role" name="role" required>
            <option value="1">Cliente</option>
            <option value="2">Administrador</option>
            <option value="3">Entregador</option>
          </select>
        </div>

        <button type="submit">Entrar</button>
      </form>
      <div>
        <?php if (!empty($msg)) { echo '<p>'. $msg . '</p>';} ?>
      </div>
      <div class="actions">
        <p>
          <a href="forgot_password.php">Esqueceu sua senha?</a>
        </p>

        <p>
          <a href="cadastro.php">Não tem uma conta? Crie aqui</a>
        </p>
      </div>
    </div>
    <div class="images">
      <img src="imagens/prato3.png" alt="Prato 3">
      <img src="imagens/prato4.png" alt="Prato 4">
    </div>
  </div>
</body>
</html>
