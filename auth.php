<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    http_response_code(401);
    header('Location: /Agendamento-Chromebook-main/Login.html');
    exit();
}
