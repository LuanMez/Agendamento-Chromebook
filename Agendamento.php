<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/StyleAgendamento.css">
    <script type="text/javascript" src="ScriptAgentamento.js"></script>
    <title>Agendamento</title>
</head>

<body>

    <section class="conteudo">

        <div class="calendario">

            <header>

                <a class="btn-ant" id="btn_prev"><img src="https://i.imgur.com/yDpGRK8.png" /></a>

                <h2 id="mes">Maio</h2>
                <h2 id="ano">2024</h2>

                <a class="btn-pro" id="btn_next"><img src="https://i.imgur.com/PHg8KgG.png" /></a>
            </header>

            <hr>

            <table>

                <thead>

                    <tr>
                        <td>Dom</td>
                        <td>Seg</td>
                        <td>Ter</td>
                        <td>Qua</td>
                        <td>Qui</td>
                        <td>Sex</td>
                        <td>Sáb</td>
                    </tr>
                </thead>

                <tbody id="dias">

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                </tbody>
            </table>

        </div>

    </section>

    <h2>Escolha seu Horário!</h2>


    <section class="secao-turno">

        <div id="turno">

            <p id="manha">Manhã</p>
            <p id="tarde">Tarde</p>
            <p id="noite">Noite</p>

        </div>

    </section>

    <section class="horas">
    </section>

    <section class="informacoes">

        <h3 style="margin-left:20px;" id="servico">Funcionário:</h3>

        <select id="preferencia">
        </select><br>

        <hr>

        <h3 style="margin-left:20px;" id="servico">Serviço:</h3>

        <select id="servicos2">
        </select><br>

        <hr>

        <h3 style="margin-left:20px;" id="valor_final">Valor Final:</h3>


    </section>
    <br>
    <input type="submit" id="submit" value="Reservar">
</body>

</html>