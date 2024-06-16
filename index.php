<?php

require 'vendor/autoload.php';

use App\Router;
use App\Controllers\Messages;
use App\Controllers\Message;
use App\Controllers\Registration;
use App\Controllers\Login;
use App\Controllers\Logout;
use App\Controllers\Flatshare;
use App\Controllers\Profil;
use App\Controllers\Task;
use App\Controllers\User;


new Router([
  'message/:id' => Message::class,
  'messages' => Messages::class,
  'registration' => Registration::class, // Ajout de la route pour l'inscription renommée
  'login' => Login::class,
  'logout' => Logout::class,
  'check-session' => Login::class . '::checkSession',
  'flatshares' => Flatshare::class,
  'flatshare/join' => Flatshare::class,
  'profil/:id' => Profil::class,
  'tasks' => Task::class,
  'users/:flatshare_id' => User::class,
  'createMessage'=> Message::class
]);