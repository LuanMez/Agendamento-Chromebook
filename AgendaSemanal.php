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

    // horario da manha
    $hora = 7; //hora que comeca
    $minuto = 10; //minuto que comeca
    while ($hora < 13) {  //horario para acabar
        $hora_formatada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minuto, 2, '0', STR_PAD_LEFT); //formato hora
        $horarios[$hora_formatada] = [ //dias da semana
            'Segunda' => [],
            'Terça' => [],
            'Quarta' => [],
            'Quinta' => [],
            'Sexta' => []
        ];

        // verificando se chegou no intervalo de 9:40
        if ($hora == 9 && $minuto == 40) {
            // adiciona 09:50 e ajusta o incremento
            $minuto = 50;
            $hora_formatada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minuto, 2, '0', STR_PAD_LEFT);
            $horarios[$hora_formatada] = [
                'Segunda' => [],
                'Terça' => [],
                'Quarta' => [],
                'Quinta' => [],
                'Sexta' => []
            ];
            // ajusta para que o proximo incremento siga o padrao normal
            $minuto = 40;
            $hora++;
        } else {
            // aumenta 50 minutos
            $minuto += 50;
            if ($minuto >= 60) {
                $minuto -= 60;
                $hora++;
            }
        }
    }

    // horario da noite
    $hora = 18; //hora que comeca
    $minuto = 00; //minuto que comeca
    while ($hora < 22 || ($hora == 22 && $minuto == 10)) {  //horario para acabar
        $hora_formatada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minuto, 2, '0', STR_PAD_LEFT); //formato hora
        $horarios[$hora_formatada] = [ //dias da semana
            'Segunda' => [],
            'Terça' => [],
            'Quarta' => [],
            'Quinta' => [],
            'Sexta' => []
        ];

        // verificando se chegou no intervalo de 9:40
        if ($hora == 20 && $minuto == 40) {
            // adiciona 20:50 e ajusta o incremento
            $minuto = 50;
            $hora_formatada = str_pad($hora, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minuto, 2, '0', STR_PAD_LEFT);
            $horarios[$hora_formatada] = [
                'Segunda' => [],
                'Terça' => [],
                'Quarta' => [],
                'Quinta' => [],
                'Sexta' => []
            ];
            // ajusta para que o proximo incremento siga o padrao normal
            $minuto = 30;
            $hora++;
        } else {
            // aumenta 50 minutos
            $minuto += 40;
            if ($minuto >= 60) {
                $minuto -= 60;
                $hora++;
            }
        }
    }
    return $horarios;
}



// Verifica se o parâmetro id_salao foi passado na URL
if (isset($_GET['id_salao'])) {
    $id_salao = $_GET['id_salao'];

    // Conexão com o banco de dados
    $conn = new mysqli("localhost", "root", "", "ads");
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Obter a data da semana atual ou da semana passada através de parâmetros GET
    $hoje = isset($_GET['data']) ? $_GET['data'] : date('d-m-Y');
    $inicioDaSemana = obterInicioDaSemana($hoje);
    $fimDaSemana = date('d-m-Y', strtotime($inicioDaSemana . ' + 6 days'));
    $horarios = gerarHorarios();

    // Consulta SQL para obter os agendamentos da semana
    $sql = "SELECT agendamentos.*, 
                   cliente.nome AS cliente_nome, 
                   servicos.nome AS servico_nome,
                   servicos.tempo AS servico_tempo,
                   profissional.nome AS profissional_nome
            FROM agendamentos
            JOIN cliente ON agendamentos.id_cliente = cliente.id
            JOIN servicos ON agendamentos.id_servico = servicos.id
            JOIN profissional ON agendamentos.id_profissional = profissional.id
            WHERE agendamentos.id_salao = ? AND data BETWEEN ? AND ?";

    // Prepara a consulta SQL
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Erro na preparação da consulta SQL: ' . $conn->error);
    }

    // Vincula os parâmetros à consulta preparada
    $stmt->bind_param("iss", $id_salao, $inicioDaSemana, $fimDaSemana);

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
                case 'Saturday':
                    $diaSemana = 'Sábado';
                    break;
                case 'Sunday':
                    $diaSemana = 'Domingo';
                    break;
            }

            // Adiciona o agendamento ao array de horários
            if (isset($horarios[$hora_formatada][$diaSemana])) {
                $horarios[$hora_formatada][$diaSemana][] = [
                    'servico' => $agendamento['servico_nome'],
                    'cliente' => $agendamento['cliente_nome'],
                    'tempo' => $agendamento['servico_tempo'],
                    'profissional' => $agendamento['profissional_nome'],
                    'data_hora' => $agendamento['data'] . ' ' . $agendamento['horario']
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
            /*css*/
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
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .agendamento {
            background-color: #FFCCD5;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 4px;
            font-size: 14px;
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
    </style>
</head>

<body>
    <h1>Agendamento Semanal dos Chromebooks</h1>
    <h1>Data Atual: <?php echo strftime('%A, %d de %B de %Y', strtotime($hoje)); ?></h1>
    <div class="navegacao">
        <a href="?id_salao=<?php echo $id_salao; ?>&data=<?php echo date('d-m-Y', strtotime($inicioDaSemana . ' - 7 days')); ?>">Semana Anterior</a>
        <a href="?id_salao=<?php echo $id_salao; ?>&data=<?php echo date('d-m-Y', strtotime($inicioDaSemana . ' + 7 days')); ?>">Próxima Semana</a>
    </div>
    <table>
        <tr>
            <th>Hora</th>
            <th>Segunda</th>
            <th>Terça</th>
            <th>Quarta</th>
            <th>Quinta</th>
            <th>Sexta</th>
        </tr>
        <?php foreach ($horarios as $hora => $dias) : ?>
            <tr>
                <td><?php echo htmlspecialchars($hora); ?></td>
                <?php foreach (['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'] as $dia) : ?>
                    <td>
                        <?php if (isset($dias[$dia]) && !empty($dias[$dia])) : ?>
                            <?php foreach ($dias[$dia] as $agendamento) : ?>
                                <div class="agendamento">
                                    <strong><?php echo htmlspecialchars($agendamento['profissional']); ?></strong><br>
                                    <?php echo htmlspecialchars($agendamento['servico']); ?><br>
                                    <?php echo htmlspecialchars($agendamento['cliente']); ?><br>
                                    <?php echo htmlspecialchars($agendamento['tempo']); ?> minutos<br>
                                    <small><?php echo htmlspecialchars(date('H:i', strtotime($agendamento['data_hora']))); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>