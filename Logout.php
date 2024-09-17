<?php
session_start();
session_destroy(); // Destrói todas as variáveis de sessão

// Redireciona para a página de login após logout
header("Location: login.html");
exit();
?>
