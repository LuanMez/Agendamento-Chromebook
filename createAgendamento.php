<?php
require 'Login.php'; // Certifique-se de que a sessão foi iniciada no Login.php
date_default_timezone_set("America/Sao_Paulo");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chromebook";

$data = $_POST["data"];
$hora = $_POST["hora"];
$preferencia = intval($_POST['preferencia']); //pegando a quantidade de caixas
$cores = array("1", "2", "3", "4", "5");
$id_professor = $_SESSION['id'];

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
foreach ($cores as $cor) {
  if ($agendados >= $preferencia) {
    break; // Para o loop quando atingir a quantidade de agendamentos escolhidos
  }

  $result = $conn->query(searchCor($cor, $data, $hora));

  if (mysqli_num_rows($result) == 0) {
    // Faz o agendamento se a cor não estiver ocupada no horário e data
    $conn->query(insertAgendamento($id_professor, $cor, $data, $hora));
    $agendados++; // Incrementa o contador de agendamentos
  }
}

if ($agendados == 0) {
  echo "Não foi possível fazer nenhum agendamento, todos os horários estão ocupados.";
} else {
  echo "$agendados agendamento(s) realizado(s) com sucesso.";
}

$conn->close();
