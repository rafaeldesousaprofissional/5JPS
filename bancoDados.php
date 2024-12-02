<?php 

try {
  $db = new PDO('sqlite:database.sqlite');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $res = $db->exec(
    "CREATE TABLE IF NOT EXISTS messages (
      id INTEGER PRIMARY KEY AUTOINCREMENT, 
      title TEXT, 
      message TEXT, 
      time INTEGER
    );
    
    CREATE TABLE IF NOT EXISTS Produto (
    ID_Prod INTEGER PRIMARY KEY AUTOINCREMENT,
    nome_Prod VARCHAR(100),
    valor_Antigo_Prod DECIMAL(10,2),
    valor_Prod DECIMAL(10, 2),
    descricao_Prod TEXT,
    imagem_Prod BLOB
    );
    
    CREATE TABLE IF NOT EXISTS pessoa (
    ID_pessoa INTEGER PRIMARY KEY AUTOINCREMENT,
    nome_pessoa VARCHAR(100),
    email_pessoa VARCHAR(100),
    senha_pessoa VARCHAR(255),
    tel_pessoa VARCHAR(15),            
    rua_pessoa VARCHAR(100),       
    bairro_pessoa VARCHAR(50),
    tipo_pessoa INTEGER
    ); 

    CREATE TABLE IF NOT EXISTS Pedido (
      ID_Ped INTEGER PRIMARY KEY AUTOINCREMENT,
      valor_Ped DECIMAL(10, 2),
      nomeCliente_Ped VARCHAR(100),
      telefoneCliente_Ped VARCHAR(15),
      descricao_Ped TEXT,
      avaliacao_Ped INT CHECK(avaliacao_Ped BETWEEN 1 AND 5),
      statusPagamento_Ped VARCHAR(20),
      obs_Ped TEXT,
      endereco_Ped VARCHAR(255)
    );

    "
  );


  
  $stmt = $db->prepare(
    "INSERT INTO messages (title, message, time) 
      VALUES (:title, :message, :time)"
  );
  
  // Bind values directly to statement variables
  $stmt->bindValue(':title', 'message title', SQLITE3_TEXT);
  $stmt->bindValue(':message', 'message body', SQLITE3_TEXT);
  
  // Format unix time to timestamp
  $formatted_time = date('Y-m-d H:i:s');
  $stmt->bindValue(':time', $formatted_time, SQLITE3_TEXT);
   
  // Execute statement
  $stmt->execute();
  
  $messages = $db->query("SELECT * FROM messages");
    
  // Garbage collect db
  //$db = null;
} catch (PDOException $ex) {
  echo $ex->getMessage();
}

?>