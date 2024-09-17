document.addEventListener('DOMContentLoaded', function () {


    const monthsBR = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    const tableDays = document.getElementById('dias');

    function formatMonth(mes) {
        return mes < 10 ? '0' + mes.toString() : mes.toString();
    }

    function GetDaysCalendar(mes, ano) {
        document.getElementById('mes').innerHTML = monthsBR[mes];
        document.getElementById('ano').innerHTML = ano;

        let firstDayOfWeek = new Date(ano, mes, 1).getDay() - 1;
        if (firstDayOfWeek < 0) firstDayOfWeek = 6; // Corrige o caso quando firstDayOfWeek é -1
        let getLastDayThisMonth = new Date(ano, mes, 0).getDate();

        const dayCells = tableDays.getElementsByTagName('td');

        for (let i = -firstDayOfWeek, index = 0; index < dayCells.length; i++, index++) {
            let dt = new Date(ano, mes, i);
            let dtNow = new Date();
            let dayTable = dayCells[index];

            // Resetando classes e conteúdo
            dayTable.classList.remove("mes-anterior");
            dayTable.classList.remove("proximo-mes");
            dayTable.classList.remove("dia-atual");
            dayTable.classList.remove("dia-selecionado");

            dayTable.innerHTML = '';

            if (i >= 1 && i <= getLastDayThisMonth) {
                dayTable.innerHTML = i;
                dayTable.addEventListener('click', function () {
                    let selected = tableDays.querySelector('.dia-selecionado');
                    if (selected) {
                        selected.classList.remove('dia-selecionado');
                        selected.removeAttribute("value");
                    }
                    this.classList.add('dia-selecionado');
                    this.setAttribute("value", formatMonth(i) + "-" + formatMonth(mes + 1) + "-" + ano);
                    console.log(document.getElementsByClassName("dia-selecionado")[0].getAttribute("value"));
                });

                if (dt.getFullYear() == dtNow.getFullYear() && dt.getMonth() == dtNow.getMonth() && dt.getDate() == dtNow.getDate()) {
                    dayTable.classList.add('dia-atual');
                }

                if (i < 1) {
                    dayTable.classList.add('mes-anterior');
                }
                if (i > getLastDayThisMonth) {
                    dayTable.classList.add('proximo-mes');
                }
            }
        }
    }

    let now = new Date();
    let mes = now.getMonth();
    let ano = now.getFullYear();
    GetDaysCalendar(mes, ano);

    const botao_proximo = document.getElementById('btn_next');
    const botao_anterior = document.getElementById('btn_prev');

    botao_proximo.addEventListener('click', function () {
        mes++;
        if (mes > 11) {
            mes = 0;
            ano++;
        }
        GetDaysCalendar(mes, ano);
    });

    botao_anterior.addEventListener('click', function () {
        mes--;
        if (mes < 0) {
            mes = 11;
            ano--;
        }
        GetDaysCalendar(mes, ano);
    });

    const horariosManha = ['7:10', '8:00', '8:50', '9:40', '9:50', '10:40', '11:30', '12:20'];
    const horariosNoite = ['18:00', '18:40', '19:20', '20:00', '20:40', '20:50', '21:30', '22:10'];

    console.log(document.getElementById('turno'));
    console.log(turno.getElementsByTagName('p'));

    function makeSelectable() {
        const turno = document.getElementById('turno');
        const pElements = turno.getElementsByTagName('p');


        for (let i = 0; i < pElements.length; i++) {
            pElements[i].className = 'turno';
            pElements[i].addEventListener('click', function () {
                for (let j = 0; j < pElements.length; j++) {
                    pElements[j].classList.remove('selected');
                }
                this.classList.add('selected');
                mudarHorarios(this.id);
            });
        }
    }



    function mudarHorarios(turno) {
        let horarios;
        if (turno == 'manha') {
            horarios = horariosManha;
        } else if (turno == 'tarde') {
            horarios = horariosTarde;
        } else if (turno == 'noite') {
            horarios = horariosNoite;
        }

        const horas = document.querySelector('.horas');
        while (horas.firstChild) {
            horas.removeChild(horas.firstChild);
        }

        horarios.forEach(function (horario) {
            const div = document.createElement('div');
            div.textContent = horario;
            div.className = 'horario';
            div.addEventListener('click', function () {
                const selected = horas.querySelector('.selected');
                if (selected) {
                    selected.classList.remove('selected');
                    selected.removeAttribute("value");
                }
                this.classList.add('selected');
                this.setAttribute("value", horario)
            });
            horas.appendChild(div);
        });
    }

    window.onload = function () {
        makeSelectable();
        mudarHorarios('manha');
        carregarProfissionais();
        carregarServicos();
    };

    function checkHoraArray() {
        if (document.getElementsByClassName("selected")[0].getAttribute("value") == null) {
            return document.getElementsByClassName("selected")[1].getAttribute("value");
        } else {
            return document.getElementsByClassName("selected")[0].getAttribute("value");
        }
    }

    document.getElementById('submit').addEventListener('click', (submit) => {
        submit.preventDefault();
        const redirectLink = document.getElementById("redirect").getAttribute("value")
        var data = document.getElementsByClassName("dia-selecionado")[0].getAttribute("value");
        var hora = checkHoraArray();

        const postData = `data=${encodeURIComponent(data)}&hora=${encodeURIComponent(hora)}`;


        var httpc = new XMLHttpRequest();
        var url = "createAgendamento.php";
        httpc.open("POST", url, true);

        httpc.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        httpc.onreadystatechange = function () { //Call a function when the state changes.
            alert(httpc.responseText);
            window.location.replace("AgendaSemanal.php" + redirectLink);

        };
        httpc.send(postData);



        /* const profissional = document.getElementById('preferencia').value;
        const dataSelecionadaElement = document.querySelector('.dia-selecionado');
        const dataSelecionada = `${dataSelecionada}-${formatMonth(mes + 1)}-${ano}`;


        const turnoSelecionado = document.querySelector('.turno.selected');
        const horarioSelecionado = document.querySelector('.horas .selected').textContent;

        const form = document.createElement('form');
        form.method = 'post';
        form.action = 'reservar.php';

        const inputProfissional = document.createElement('input');
        inputProfissional.type = 'hidden';
        inputProfissional.name = 'preferencia';
        inputProfissional.value = profissional;
        form.appendChild(inputProfissional);


        const inputData = document.createElement('input');
        inputData.type = 'hidden';
        inputData.name = 'dataSelecionada';
        inputData.value = dataSelecionada;
        form.appendChild(inputData);

        const inputTurno = document.createElement('input');
        inputTurno.type = 'hidden';
        inputTurno.name = 'turnoSelecionado';
        inputTurno.value = turnoSelecionado;
        form.appendChild(inputTurno);

        const inputHorario = document.createElement('input');
        inputHorario.type = 'hidden';
        inputHorario.name = 'horarioSelecionado';
        inputHorario.value = horarioSelecionado;
        form.appendChild(inputHorario);

        const inputCliente = document.createElement('input');
        inputCliente.type = 'hidden';
        inputCliente.name = 'id_cliente';
        inputCliente.value = id_cliente;
        form.appendChild(inputCliente);

        const inputSalao = document.createElement('input');
        inputSalao.type = 'hidden';
        inputSalao.name = 'id_salao';
        inputSalao.value = id_salao;
        form.appendChild(inputSalao);

        document.body.appendChild(form); 
        form.submit(); */
    });

});


