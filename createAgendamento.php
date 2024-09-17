<?php
date_default_timezone_set("America/Sao_Paulo");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chromebook";

$data = $_POST["data"];
$hora = $_POST["hora"];
$cores = array("1", "2", "3", "4", "5");

$conn = new mysqli($servername, $username, $password, $dbname);

function searchCor($idC, $data, $hora)
{
  return "SELECT * FROM agendamento WHERE idCor='$idC' AND data='$data' AND horario='$hora'";
}

function insertAgendamento($idC, $data, $hora)
{
  return "INSERT INTO agendamento (id_professor, data, horario, idCor) VALUES (1, '$data', '$hora', '$idC')";
}


foreach ($cores as $cor) {

  $result = $conn->query(searchCor($cor, $data, $hora));

  if (mysqli_num_rows($result) == 0) {

    $conn->query(insertAgendamento($cor, $data, $hora));
    break;
  }
}

$conn->close();
