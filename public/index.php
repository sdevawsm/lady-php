<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Carrega a aplicação
$app = require_once __DIR__ . '/../app/bootstrap.php';

// Processa a requisição
$app->run(); 