<?php
require 'Login.php'; // Certifique-se de que a sessão foi iniciada no Login.php
date_default_timezone_set("America/Sao_Paulo");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chromebook";

// Captura os valores enviados via POST
$data = $_POST["data"];
$horarios = explode(",", $_POST["horarios"]); // Array de horários selecionados
$preferencia = intval($_POST['preferencia']); // Captura a quantidade de agendamentos escolhida pelo usuário
$cores = array("1", "2", "3", "4", "5");
$id_professor = $_SESSION['id'];

// Exibe os valores para verificar se estão corretos
echo "Data: $data<br>";
echo "Horários: " . implode(", ", $horarios) . "<br>";
echo "Preferência: $preferencia<br>";

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

// Loop pelos horários selecionados
foreach ($horarios as $hora) {
  echo "Tentando agendar para o horário: $hora<br>"; // Debug

  foreach ($cores as $cor) {

    // Executa a consulta para verificar se o horário já está ocupado
    $result = $conn->query(searchCor($cor, $data, $hora));
    if ($result === false) {
      echo "Erro na consulta: " . $conn->error . "<br>";
    }

    if (mysqli_num_rows($result) == 0) {
      // Faz o agendamento se a cor não estiver ocupada no horário e data
      $insertResult = $conn->query(insertAgendamento($id_professor, $cor, $data, $hora));

      if ($insertResult) {
        echo "Agendamento realizado para o horário $hora com a cor $cor<br>";
        $agendados++; // Incrementa o contador de agendamentos
      } else {
        echo "Erro ao inserir agendamento: " . $conn->error . "<br>";
      }
    } else {
      echo "Horário $hora com a cor $cor já está ocupado<br>";
    }

    if ($agendados >= $preferencia) {
      $agendados = 0; //ERA SÓ ISSO MERMAO
      break; // Para o loop quando atingir a quantidade de agendamentos escolhidos
    }
  }
}

if ($agendados == 0) {
  echo "Não foi possível fazer nenhum agendamento, todos os horários estão ocupados.";
} else {
  echo "$agendados agendamento(s) realizado(s) com sucesso.";
}

$conn->close();
