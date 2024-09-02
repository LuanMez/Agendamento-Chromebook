<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ads";
$id_salao = $_GET['id_salao']; // Supondo que você esteja passando o id_salao via GET

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Buscar serviços vinculados ao id_salao
$sql = "SELECT id, nome FROM servicos WHERE id_salao = $id_salao";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Saída de cada linha
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row["id"] . "'>" . $row["nome"] . "</option>";
    }
} else {
    echo "0 resultados";
}
$conn->close();
