<?php
require 'Login.php'; // Certifique-se de que a sessão foi iniciada no Login.php

$id = $_POST['id'];
$idProfessor = $_POST['idProfessor'];

// Conecte-se ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Exclua o agendamento do banco de dados
$sql = "DELETE FROM schedules WHERE id = '$id' AND id_professor = '$idProfessor'";
if ($conn->query($sql) === TRUE) {
    echo 'success';
} else {
    echo 'Erro ao cancelar agendamento';
}

$conn->close();
