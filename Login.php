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

            // Verificar se a senha é "1234" (primeiro login)
            if ($senha === "1234") {
                // Redirecionar para a página de mudança de senha
                header("Location: /Agendamento_Chrome/mudarSenha.php?id_professor=$id");
                exit();
            } else {
                // Redirecionar para a página de agenda
                header("Location: /Agendamento_Chrome/AgendaSemanal.php?id_professor=$id");
                exit();
            }
        } else {
            // Senha incorreta
            echo "<script type='text/javascript'>
                    alert('Senha incorreta!');
                    window.location.href = 'login.html';
                  </script>";
        }
    } else {
        // Email incorreto
        echo "<script type='text/javascript'>
                alert('Email incorreto!');
                window.location.href = 'login.html';
              </script>";
    }

    $stmt->close();
}

// Fechar conexão
$conn->close();
