<?php
ob_start(); // Inicia o buffer de saída
@require 'Login.php'; // Certifique-se de que a sessão foi iniciada no Login.php
ob_end_clean(); // Limpa o buffer de saída

date_default_timezone_set("America/Sao_Paulo");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chromebook";

// Captura os valores enviados via POST
$data = $_POST["data"];
$horarios = explode(",", $_POST["horarios"]); // Array de horários selecionados
$preferencia = intval($_POST['preferencia']); // Captura a quantidade de agendamentos escolhida pelo usuário
$cores = array(1, 2, 3, 4, 5); // Usando inteiros para facilitar o switch
$id_professor = $_SESSION['id'];

// Exibe os valores para verificar se estão corretos
echo "Data: $data\n";
echo "Horários: " . implode(", ", $horarios) . "\n";
echo "Número de caixas: $preferencia\n";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

function searchCor($idC, $data, $hora)
{
    return "SELECT * FROM agendamento WHERE idCor='$idC' AND data='$data' AND horario='$hora'";
}

function insertAgendamento($id_professor, $idC, $data, $hora)
{
    return "INSERT INTO agendamento (id_professor, data, horario, idCor) VALUES ('$id_professor', '$data', '$hora', '$idC')";
}

$agendados = 0; // Contador de agendamentos feitos
$quantidade = 0; // Permite exibir se não fez nenhum agendamento

// Loop pelos horários selecionados
foreach ($horarios as $hora) {
    foreach ($cores as $cor) {
        // Executa a consulta para verificar se o horário já está ocupado
        $result = $conn->query(searchCor($cor, $data, $hora));
        if ($result === false) {
            echo "Erro na consulta: " . $conn->error . "\n";
        }

        if (mysqli_num_rows($result) == 0) {
            // Faz o agendamento se a cor não estiver ocupada no horário e data
            $insertResult = $conn->query(insertAgendamento($id_professor, $cor, $data, $hora));

            if ($insertResult) {
                // Switch para exibir a cor corretamente
                switch ($cor) {
                    case 1:
                        $colorName = 'laranja';
                        break;
                    case 2:
                        $colorName = 'verde';
                        break;
                    case 3:
                        $colorName = 'amarelo';
                        break;
                    case 4:
                        $colorName = 'azul';
                        break;
                    case 5:
                        $colorName = 'vermelho';
                        break;
                    default:
                        $colorName = 'cor inválida';
                }
                echo "Agendamento realizado para o horário $hora com a cor $colorName\n";
                $agendados++; // Incrementa o contador de agendamentos
                $quantidade++; //incrementa a quantidade de agentamentos
            } else {
                echo "Erro ao inserir agendamento: " . $conn->error . "\n";
            }
        }

        if ($agendados >= $preferencia) {
            $agendados = 0; // Resetando o contador de agendamentos
            break; // Para o loop quando atingir a quantidade de agendamentos escolhidos
        }
    }
}

if ($quantidade == 0) {
    echo "Horário $hora já está ocupado\n";
    echo "Não foi possível fazer nenhum agendamento, todos os horários estão ocupados.";
} else {
    echo "$quantidade agendamento(s) realizado(s) com sucesso.";
}

$conn->close();
