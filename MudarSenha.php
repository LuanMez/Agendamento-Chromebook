<?php
// Habilitar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chromebook";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o ID do professor está sendo passado corretamente
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['id_professor'])) {
        die("ID do professor não fornecido.");
    }
    $id_professor = $_GET['id_professor'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['id_professor'])) {
        die("ID do professor não fornecido.");
    }
    $id_professor = $_POST['id_professor'];
}

$error = ''; // Variável para mensagens de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verificar se as senhas coincidem
    if ($nova_senha !== $confirmar_senha) {
        $error = "As senhas não coincidem!";
    } elseif ($nova_senha === '1234') {
        // Impedir que o usuário escolha "1234" como nova senha
        $error = "A nova senha não pode ser '1234'. Escolha outra senha.";
    } else {
        // Atualizar a senha no banco de dados
        $stmt = $conn->prepare("UPDATE professor SET senha=? WHERE id=?");
        if ($stmt === false) {
            die("Erro ao preparar a consulta: " . $conn->error);
        }

        $stmt->bind_param("si", $nova_senha, $id_professor);

        if ($stmt->execute()) {
            // Se a atualização for bem-sucedida, redirecionar o usuário
            header("Location: /Agendamento_Chrome/AgendaSemanal.php?id_professor=$id_professor");
            exit();
        } else {
            $error = "Erro ao atualizar a senha: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Mudar Senha</title>
    <link rel="stylesheet" href="css/StyleLogin.css">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #1B98E0;
            font-family: 'Roboto', sans-serif;
            text-align: center; 
        }

        .posi-caixa {
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-left: 39%;
            margin-top: 16%;
        }

        .caixa_alt_senha {
            border-radius: 8px;
            background-color: #fff;
            width: 70%;
            max-width: 450px;
            height: auto;
            box-shadow: rgba(164, 232, 255, 0.4) 5px 5px;
            padding: 20px;
        }

        form {
            max-width: 400px;
            margin: auto;
            text-align: center;
            margin: 15px;
        }

        label,
        input {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #1B98E0;
            color: white;
            border: none;
            cursor: pointer;
        }

        #error-message {
            color: white;
            background-color: red;
            padding: 10px;
            border-radius: 5px;
            position: fixed;
            top: 20px; /* Distância do topo */
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000; /* Para aparecer sobre outros elementos */
            display: none; /* Inicialmente escondido */
            transition: opacity 0.5s ease; /* Transição suave */
            opacity: 0; /* Começa invisível */
        }

        #error-message.show {
            display: block; /* Exibe quando necessário */
            opacity: 1; /* Torna visível */
        }

    </style>
    <script>
        window.onload = function() {
            const errorMessage = '<?php echo addslashes($error); ?>'; // Captura a mensagem de erro do PHP
            const erroDiv = document.querySelector('#error-message');

            if (errorMessage) {
                erroDiv.textContent = errorMessage; // Define a mensagem de erro
                erroDiv.classList.add('show'); // Adiciona classe para exibir
                setTimeout(() => {
                    erroDiv.classList.remove('show'); // Remove a classe após 3 segundos
                }, 3000); // Tempo que o pop-up fica visível
            }
        };
    </script>
</head>

<body>
    <div class="posi-caixa">
        <section class="caixa_alt_senha">
            <h1 class="login5">Alterar Senha</h1>
            <div class="login-card-cont">
                <form method="post" action="MudarSenha.php">
                    <input type="hidden" name="id_professor" value="<?php echo htmlspecialchars($id_professor); ?>">
                    <label for="nova_senha">Nova Senha:</label>
                    <input type="password" id="nova_senha" name="nova_senha" required>

                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                    <br>
                    <input type="submit" value="Alterar Senha">
                </form>
            </div>
        </section>
    </div>
    <div id="error-message"></div> <!-- Div para a mensagem de erro -->
</body>

</html>
