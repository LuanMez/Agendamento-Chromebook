<?php
session_start(); // Inicia a sessão

// Define o fuso horário para o Brasil
date_default_timezone_set('America/Sao_Paulo');

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    die('Você precisa estar logado para cancelar um agendamento.');
}

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "chromebook");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se o id_agendamento foi enviado
if (isset($_POST['id_agendamento'])) {
    $id_agendamento = trim($_POST['id_agendamento']);
    $id_professor_logado = $_SESSION['id']; // ID do professor logado

    // Consulta para verificar se o agendamento pertence ao professor logado
    $sql = "SELECT * FROM agendamento WHERE id = ? AND id_professor = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro na preparação da consulta SQL: ' . $conn->error);
    }
    $stmt->bind_param("ii", $id_agendamento, $id_professor_logado);

    // Executa a consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o agendamento foi encontrado
    if ($result->num_rows > 0) {
        // Realiza a exclusão do agendamento
        $delete_sql = "DELETE FROM agendamento WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id_agendamento);

        if ($delete_stmt->execute()) {
            $data_atual = date("d-m-Y");

            // Redireciona automaticamente para a agendaSemanal.php com JavaScript, passando id_professor e data
            echo "<script>
                    alert('Agendamento cancelado com sucesso.');
                    window.location.href = 'AgendaSemanal.php?id_professor=$id_professor_logado&data=$data_atual';
                  </script>";
        } else {
            echo "Erro ao cancelar o agendamento.";
        }
        $delete_stmt->close();
    } else {
        echo "Erro: Agendamento não encontrado ou você não tem permissão para cancelá-lo.";
    }
    // Fecha a consulta
    $stmt->close();
} else {
    echo "Erro: ID do agendamento não informado.";
}

// Fecha a conexão
$conn->close();
