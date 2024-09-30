<?php
// Função para obter o início da semana (segunda-feira) para uma determinada data
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

function obterInicioDaSemana($data)
{
    $diaSemana = date('w', strtotime($data));
    $diasParaSegunda = ($diaSemana == 0) ? 6 : $diaSemana - 1;
    return date('d-m-Y', strtotime($data . " - $diasParaSegunda days"));
}

// Função para gerar os horários do agendamento
function gerarHorarios()
{
    $horarios = [];

    // horário da manhã
    $hora = 7; // hora que começa
    $minuto = 10; // minuto que começa
    while ($hora < 12) { // horário para acabar
        // Formata o horário
        $hora_formatada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minuto, 2, '0', STR_PAD_LEFT);

        // Adiciona ao array de horários
        if ($hora == 9 && $minuto == 40) { // faz com que 9:40 não entre no array
            $minuto = 50;
        } else {
            $horarios[$hora_formatada] = [
                'Segunda' => [],
                'Terça' => [],
                'Quarta' => [],
                'Quinta' => [],
                'Sexta' => []
            ];
            // Aumenta 50 minutos
            $minuto += 50;
            if ($minuto >= 60) { // se passar de 60 aumenta a hora
                $minuto -= 60;
                $hora++;
            }
        }
    }

    // horário da noite
    $hora = 18; // hora que começa
    $minuto = 0; // minuto que começa
    while ($hora < 21 || ($hora == 21 && $minuto == 30)) { // horário para acabar
        // Formata o horário
        $hora_formatada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minuto, 2, '0', STR_PAD_LEFT);

        // Adiciona ao array de horários
        if ($hora == 20 && $minuto == 40) { // faz com que 20:40 não entre no array
            $minuto = 50;
        } else {
            $horarios[$hora_formatada] = [
                'Segunda' => [],
                'Terça' => [],
                'Quarta' => [],
                'Quinta' => [],
                'Sexta' => []
            ];
            // Aumenta 40 minutos
            $minuto += 40;
            if ($minuto >= 60) { // se passar de 60 aumenta a hora
                $minuto -= 60;
                $hora++;
            }
        }
    }

    return $horarios;
}




// Verifica se o parâmetro id_professor foi passado na URL
if (isset($_GET['id_professor'])) {
    $id_professor = $_GET['id_professor'];

    // Conexão com o banco de dados
    $conn = new mysqli("localhost", "root", "", "chromebook");
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Obter a data da semana atual ou da semana passada através de parâmetros GET
    $hoje = isset($_GET['data']) ? $_GET['data'] : date('d-m-Y');
    $inicioDaSemana = obterInicioDaSemana($hoje);
    $fimDaSemana = date('d-m-Y', strtotime($inicioDaSemana . ' + 6 days'));
    $horarios = gerarHorarios();

    // Consulta SQL para obter os agendamentos da semana
    $sql = "SELECT agendamento.*, professor.nome AS professor_nome, agendamento.idCor
        FROM agendamento
        JOIN professor ON agendamento.id_professor = professor.id
        WHERE STR_TO_DATE(agendamento.data, '%d-%m-%Y') BETWEEN STR_TO_DATE(?, '%d-%m-%Y') 
        AND STR_TO_DATE(?, '%d-%m-%Y')";

    // Prepara a consulta SQL
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro na preparação da consulta SQL: ' . $conn->error);
    }

    // Vincula os parâmetros à consulta preparada
    $stmt->bind_param("ss", $inicioDaSemana, $fimDaSemana);


    // Executa a consulta
    $stmt->execute();

    // Obtém o resultado da consulta
    $result = $stmt->get_result();


    // Processa os resultados da consulta
    if ($result->num_rows > 0) {
        while ($agendamento = $result->fetch_assoc()) {
            $inicio = strtotime($agendamento['data'] . ' ' . $agendamento['horario']);
            $hora_formatada = date('H:i', $inicio);
            $diaSemana = date('l', $inicio); // Obtém o dia da semana completo (ex: Monday, Tuesday, etc.)

            // Converte o dia da semana para o nome em português
            switch ($diaSemana) {
                case 'Monday':
                    $diaSemana = 'Segunda';
                    break;
                case 'Tuesday':
                    $diaSemana = 'Terça';
                    break;
                case 'Wednesday':
                    $diaSemana = 'Quarta';
                    break;
                case 'Thursday':
                    $diaSemana = 'Quinta';
                    break;
                case 'Friday':
                    $diaSemana = 'Sexta';
                    break;
            }

            // Adiciona o agendamento ao array de horários exibe pro usuario
            if (isset($horarios[$hora_formatada][$diaSemana])) {
                $horarios[$hora_formatada][$diaSemana][] = [
                    'professor_nome' => $agendamento['professor_nome'],  // Exibe o nome do professor
                    'data_hora' => $agendamento['data'] . ' ' . $agendamento['horario'],
                    'idCor' => $agendamento['idCor'],
                    "idProf" => $agendamento['id_professor'],
                    'id' => $agendamento['id']
                ];
            } else {
                $horarios[$hora_formatada][$diaSemana] = [];
            }
        }
    }

    // Fecha a conexão e a consulta
    $stmt->close();
    $conn->close();
}


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento Chromebook</title>
    <style>
        body {
            font-family: "Roboto", sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            /* Altere aqui a cor para preto */
            text-align: center;
            padding: 8px;
            width: 150px;
        }

        th {
            background-color: #f2f2f2;
        }

        td {
            text-align: center;
            vertical-align: top;
        }

        .navegacao {
            text-align: center;
            margin-bottom: 20px;
        }

        .navegacao a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1B98E0;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .navegacao a:hover {
            background-color: #006494;
        }

        .manha {
            background-color: #e0f7fa;
        }

        .noite {
            background-color: #ffe0b2;
        }

        .agendamento {
            border-radius: 4px;
            margin-bottom: 4px;
            margin-left: auto;
            margin-right: auto;
            font-size: 14px;
        }

        .agendamento {
            padding: 2px;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            max-width: 275px;
        }

        .agendamento strong {
            display: block;
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
        }

        .agendamento form {
            display: inline-block;
        }

        .agendamento button {
            background-color: #ff4c4c;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 3px;
        }

        .agendamento button:hover {
            background-color: #ff1f1f;
        }

        .agendamento button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 76, 76, 0.5);
        }


        /* Estilos gerais (desktop) */
        .buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .buttons a {
            background-color: #1B98E0;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .buttons a:hover {
            background-color: #247BA0;
        }

        /* Estilo responsivo para telas menores (mobile) */
        @media screen and (max-width: 768px) {
            .buttons {
                flex-direction: row;
                /* Muda de coluna para linha */
                align-items: center;
                /* Alinha os itens no centro verticalmente */
                justify-content: center;
                /* Centraliza os botões horizontalmente */
            }

            .buttons a {
                width: auto;
                /* Define a largura automática dos botões */
                margin: 0 5px;
                /* Adiciona margem lateral entre os botões */
            }

            .buttons a:last-child {
                margin-right: 0;
                /* Remove a margem direita do último botão */
            }
        }
    </style>

</head>

<body>
    <?php

    switch (date("l", strtotime($hoje))) {
        case "Monday":
            $nomeDia = "segunda-feira";
            break;
        case "Tuesday":
            $nomeDia = "terça-feira";
            break;
        case "Wednesday":
            $nomeDia = "quarta-feira";
            break;
        case "Thursday":
            $nomeDia = "quinta-feira";
            break;
        case "Friday":
            $nomeDia = "sexta-feira";
            break;
        case "Saturday";
            $nomeDia = "sábado";
            break;
        case "Sunday":
            $nomeDia = "domingo";
            break;
    }

    switch (date("F", strtotime($hoje))) {
        case "January":
            $nomeMes = "Janeiro";
            break;
        case "February":
            $nomeMes = "Fevereiro";
            break;
        case "March":
            $nomeMes = "Março";
            break;
        case "April":
            $nomeMes = "Abril";
            break;
        case "May":
            $nomeMes = "Maio";
            break;
        case "June";
            $nomeMes = "Junho";
            break;
        case "July":
            $nomeMes = "Julho";
            break;
        case "August":
            $nomeMes = "Agosto";
            break;
        case "September":
            $nomeMes = "Setembro";
            break;
        case "October":
            $nomeMes = "Outubro";
            break;
        case "November":
            $nomeMes = "Novembro";
            break;
        case "December":
            $nomeMes = "Dezembro";
            break;
    }

    ?>
    <h1>Agendamento Semanal dos Chromebooks</h1>
    <h1>Data Atual:
        <?php echo $nomeDia . ", " . date("d", strtotime($hoje)) . " de " . $nomeMes . " de " . date("Y", strtotime($hoje)); ?>
    </h1>

    <div class="navegacao">
        <a href="Agendamento.php" class="btn-agendar">Agendar</a>
        <div class="buttons">
            <a href="?id_professor=<?php echo $id_professor; ?>&data=<?php echo date('d-m-Y', strtotime($inicioDaSemana . ' - 7 days')); ?>">Semana Anterior</a>
            <a href="?id_professor=<?php echo $id_professor; ?>&data=<?php echo date('d-m-Y', strtotime($inicioDaSemana . ' + 7 days')); ?>">Próxima Semana</a>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>Horário</th>
                    <th>Segunda</th>
                    <th>Terça</th>
                    <th>Quarta</th>
                    <th>Quinta</th>
                    <th>Sexta</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($horarios) && !empty($horarios)): ?>
                    <?php foreach ($horarios as $hora => $dias): ?>
                        <?php
                        // Calcular o horário de término
                        list($horaInicio, $minutoInicio) = explode(':', explode(' / ', $hora)[0]);
                        if ($horaInicio < 18) {
                            $minutoTermino = $minutoInicio + 50;
                            $horaTermino = $horaInicio;
                        } else {
                            $minutoTermino = $minutoInicio + 40;
                            $horaTermino = $horaInicio;
                        }

                        if ($minutoTermino >= 60) {
                            $minutoTermino -= 60;
                            $horaTermino++;
                        }

                        $horaTerminoFormatada = str_pad($horaTermino, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minutoTermino, 2, '0', STR_PAD_LEFT);
                        $horaDisplay = htmlspecialchars($hora) . ' - ' . $horaTerminoFormatada; // Exibe "7:10 / 8:00"

                        $horaInt = (int) explode(':', $horaInicio)[0];
                        $classe = ($horaInt < 14) ? 'manha' : 'noite';
                        ?>
                        <tr class="<?php echo $classe; ?>">
                            <td><?php echo $horaDisplay; ?></td> <!-- Exibe "7:10 / 8:00" -->
                            <?php foreach (['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'] as $dia): ?>
                                <td>
                                    <?php if (isset($dias[$dia]) && !empty($dias[$dia])): ?>
                                        <?php foreach ($dias[$dia] as $agendamento): ?>
                                            <?php
                                            $color = '';
                                            if (isset($agendamento['idCor'])) {
                                                switch ($agendamento['idCor']) {
                                                    case 1:
                                                        $color = 'orange';
                                                        break;
                                                    case 2:
                                                        $color = '#03F132'; //verde
                                                        break;
                                                    case 3:
                                                        $color = 'yellow';
                                                        break;
                                                    case 4:
                                                        $color = '#00A8FF'; //azul
                                                        break;
                                                    case 5:
                                                        $color = 'red';
                                                        break;
                                                    default:
                                                        $color = 'gray';
                                                }
                                            }
                                            ?>
                                            <div class="agendamento" style="background-color: <?php echo $color; ?>;">
                                                <strong><?php echo htmlspecialchars($agendamento['professor_nome']); ?></strong>
                                                <?php if ($_GET["id_professor"] == $agendamento["idProf"]): ?>
                                                    <form action="cancelarAgendamento.php" method="post">
                                                        <input type="hidden" name="id_agendamento" value="<?php echo $agendamento["id"]; ?>" />
                                                        <button type="submit">Cancelar</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>


        </table>


</body>

</html>