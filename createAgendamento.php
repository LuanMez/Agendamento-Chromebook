<?php
ob_start();
require_once 'auth.php';
@require 'Login.php';
ob_end_clean();

date_default_timezone_set("America/Sao_Paulo");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chromebook";

$data = htmlspecialchars($_POST["data"], ENT_QUOTES, 'UTF-8');
if (isset($_POST['horarios']) && !empty($_POST['horarios']))
    $horarios = array_map('htmlspecialchars', explode(",", $_POST["horarios"]));
else
    $horarios = NULL;

$preferencia = intval($_POST['preferencia']);
$cores = [1, 2, 3, 4, 5];
$id_professor = $_SESSION['id'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$agendados = 0;
$quantidade = 0;

// Verificação de datas e horários inválidos
$hoje = new DateTime();
$dataSelecionada = DateTime::createFromFormat('d-m-Y', $data);
$horaAtual = $hoje->format('H:i');

// Verifica se a data selecionada já passou
if ($dataSelecionada < $hoje->setTime(0, 0)) {
    echo htmlspecialchars('A data selecionada já passou. Escolha uma data válida.', ENT_QUOTES, 'UTF-8');
    exit;
}

// Verifica se a data é hoje e se o horário desejado é anterior ao horário atual
if ($dataSelecionada->format('Y-m-d') === $hoje->format('Y-m-d')) {
    foreach ($horarios as $hora) {
        if (DateTime::createFromFormat('H:i', $hora) < DateTime::createFromFormat('H:i', $horaAtual)) {
            echo htmlspecialchars("O horário $hora já passou. Escolha um horário válido.", ENT_QUOTES, 'UTF-8');
            exit;
        }
    }
}


if (!empty($horarios) && !empty($data)) {
    foreach ($horarios as $hora) {
        // Cria um objeto DateTime para comparar o horário de agendamento
        $horaAgendamento = DateTime::createFromFormat('H:i', $hora);

        // Se o horário de agendamento for menor que a hora atual, exibe mensagem de erro
        if ($horaAgendamento <= $hoje) {
            echo htmlspecialchars('Não é possível agendar para um horário no passado.', ENT_QUOTES, 'UTF-8');
            continue;
        }

        foreach ($cores as $cor) {
            $check_sql = "SELECT * FROM agendamento WHERE idCor = ? AND data = ? AND horario = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("iss", $cor, $data, $hora);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $insert_sql = "INSERT INTO agendamento (id_professor, data, horario, idCor) VALUES (?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("issi", $id_professor, $data, $hora, $cor);

                if ($insert_stmt->execute()) {
                    $quantidade++;
                    $agendados++;

                    switch ($cor) {
                        case 1:
                            echo htmlspecialchars("Agendamento realizado para o horário $hora com a cor laranja\n", ENT_QUOTES, 'UTF-8');
                            break;
                        case 2:
                            echo htmlspecialchars("Agendamento realizado para o horário $hora com a cor verde\n", ENT_QUOTES, 'UTF-8');
                            break;
                        case 3:
                            echo htmlspecialchars("Agendamento realizado para o horário $hora com a cor amarelo\n", ENT_QUOTES, 'UTF-8');
                            break;
                        case 4:
                            echo htmlspecialchars("Agendamento realizado para o horário $hora com a cor azul\n", ENT_QUOTES, 'UTF-8');
                            break;
                        case 5:
                            echo htmlspecialchars("Agendamento realizado para o horário $hora com a cor vermelho\n", ENT_QUOTES, 'UTF-8');
                    }
                } else {
                    echo htmlspecialchars("Erro ao inserir agendamento: " . $conn->error . "\n", ENT_QUOTES, 'UTF-8');
                }
                $insert_stmt->close();
            }

            $stmt->close();

            if ($agendados >= $preferencia) {
                $agendados = 0;
                break;
            }
        }
    }

    if ($quantidade === 0) {
        echo htmlspecialchars('Não foi possível fazer nenhum agendamento. Todos os horários estão ocupados.', ENT_QUOTES, 'UTF-8');
    } else {
        echo htmlspecialchars("$quantidade agendamento(s) realizado(s) com sucesso.", ENT_QUOTES, 'UTF-8');
    }

    $conn->close();
} else {
    http_response_code(400);
}
