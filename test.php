<?php

require __DIR__.'/vendor/autoload.php';

use App\Http\Middleware\RoleMiddleware;

$middleware = new RoleMiddleware();

echo "Radi! Klasa postoji.";
