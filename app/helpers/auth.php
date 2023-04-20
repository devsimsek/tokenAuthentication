<?php

global $users;
global $sessions;
$users = new Sdb("repo");
$sessions = new Sdb("repo");

if(!file_exists(SDF_ROOT . "/repo/sessions.sdb")) $sessions->create("sessions.sdb");
if(!file_exists(SDF_ROOT . "/repo/users.sdb")) $users->create("users.sdb");
$users->load("users.sdb");
$sessions->load("sessions.sdb");

function getSessions() {
  global $sessions;
  return $sessions->map();
}

function getSession(string $token): array|false {
  foreach (getSessions() as $s) {
    if (isset($s["token"]) && $s["token"] === $token)
      return $s;
  }
  return false;
}

function createSession(string $user, string $token, int $expire) {
  global $sessions;
  $sessions->set($user, ["username" => $user, "token" => $token, "expire" => $expire]);
  $sessions->save();
}

function registerUser(string $username, string $password) {
  /** @var Sdb */
  global $users;
  $users->set($username, json_encode(["username" => $username, "password" => $password]));
  $users->save();
}

function getUser(string $username) {
  global $users;
  return $users->has($username) ? $users->fetch($username) : false;
}

// Generate token with 6 hours of validity
function generateToken(): array {

  $expire = strtotime('+6 day', time());

  $token = "SAU_" . bin2hex(random_bytes(16)); // 32 chars long

  return ["token" => $token, "expire" => $expire];
}
