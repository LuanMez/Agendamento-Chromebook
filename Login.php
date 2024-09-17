<?php
session_start();

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chromebook";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Processar o formulário de login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Usar consultas preparadas para evitar injeção de SQL
    $stmt = $conn->prepare("SELECT id, nome, senha FROM professor WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome, $senha_bd);
        $stmt->fetch();

        // Verificar se a senha está correta (sem hash)
        if ($senha === $senha_bd) {
            // Login bem-sucedido
            $_SESSION['id'] = $id;
            $_SESSION['nome'] = $nome;

            header("Location: /Agendamento_Chrome/AgendaSemanal.php?id_professor=$id");
            exit();
        } else {
            // Login falhou
            echo "Senha incorreta!"; //MELHORAR ISSO AQUI
        }
    } else {
        // Login falhou
        echo "Email incorreto!"; //MELHORAR ISSO AQUI
    }

    $stmt->close();
}

// Fechar conexão
$conn->close();
