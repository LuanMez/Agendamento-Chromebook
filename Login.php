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

        // Verificar se a senha fornecida é exatamente "1234"
        if ($senha === "1234") {
            // Login com senha padrão "1234", redireciona para mudança de senha
            $_SESSION['id'] = $id;
            $_SESSION['nome'] = $nome;

            header("Location: /Agendamento_Chrome/MudarSenha.php?id_professor=$id");
            exit();
        }
        // Se não for "1234", verificar o hash da senha armazenada no banco
        elseif (password_verify($senha, $senha_bd)) {
            // Login bem-sucedido com senha hash
            $_SESSION['id'] = $id;
            $_SESSION['nome'] = $nome;

            // Redirecionar para a página de agenda
            header("Location: /Agendamento_Chrome/AgendaSemanal.php?id_professor=$id");
            exit();
        } else {
            // Senha incorreta
            echo "<script type='text/javascript'>
                    alert('Senha incorreta!');
                    window.location.href = 'Login.html#popup1';
                  </script>";
        }
    } else {
        // Email incorreto
        echo "<script type='text/javascript'>
                alert('Email incorreto!');
                window.location.href = 'Login.html#popup1';
              </script>";
    }

    $stmt->close();
}

// Fechar conexão
$conn->close();
