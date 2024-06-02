<?php

require 'vendor/autoload.php';

use App\Router;
use App\Controllers\User;
use App\Controllers\Messages;
use App\Controllers\Message;
use App\Controllers\Bots;
use App\Controllers\Registration;
use App\Controllers\Login;
use App\Controllers\Logout;


new Router([
  'user/:id' => User::class,
  'message/:id' => Message::class,
  'messages' => Messages::class,
  'bots' => Bots::class,
  'registration' => Registration::class, // Ajout de la route pour l'inscription renommée
  'login' => Login::class,
  'logout' => Logout::class,
  'check-session' => Login::class . '::checkSession', // Ajout de la route pour vérifier la session
  'update-flatshare' => Registration::class . '::updateFlatshare' // Ajout de la route pour la mise à jour du flatshare_id

]);