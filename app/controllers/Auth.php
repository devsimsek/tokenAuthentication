<?php

// Copyright (C)Devsimsek. Researching token authentication
class Auth extends SDF\Controller {
  protected $request;

  protected array $users = [
    [
      "username" => "devsimsek",
      "password" => "86318e52f5ed4801abe1d13d509443de" // ali
    ]
  ];

  protected array $sessions = [];
  
  public function __construct() {
    parent::__construct();
    $_POST = is_array($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type');
    $this->load->file("Sdb", SDF_APP_LIB);
    $this->load->file("Request", SDF_APP_MODL);
    $this->load->helper("auth");
    $this->request = new Request();
  }

  public function index() {
    $this->request->set_header(200);
    print_r(json_encode(["message" => "You've reached to the authentication api"])); 
  }

  public function signin() {
    if (!empty($_POST) and !empty($_POST["username"]) && !empty($_POST["password"])) {
      $user = json_decode(getUser($_POST["username"]));
      if ($user->password === md5($_POST["password"])) {
        if (!isset(getSessions()[$user->username])) {
          $this->request->set_header(200);
          $token = generateToken();
          createSession($user->username, $token["token"], $token["expire"]);
          print_r(json_encode(["status" => "authorized", "token" => $token["token"]]));
        } else {
          $this->request->set_header(200);
          print_r(json_encode(["status" => "notice", "message" => "Already authenticated"]));
        }
      } else {
        $this->request->set_header(401);
        print_r(json_encode(["status" => "unauthorized"]));
      }
    }
  }

  public function signup() {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
      registerUser($_POST["username"], md5($_POST["password"]));
      $this->request->set_header(200);
      print_r(json_encode(["status" => "success", "message" => "Account created."]));
    }
  }

  public function session() {
    if (!empty($_POST["token"])) {
      $session = getSession($_POST["token"]);
      if ($session) {
        if ($session["expire"] > time()) {
          $this->request->set_header(200);
          print_r(json_encode(["status" => "authorized"]));
        } else {
          $this->request->set_header(401);
          print_r(json_encode(["status" => "unauthorized", "message" => "Token expired."]));
        }
      } else {
        $this->request->set_header(401);
        print_r(json_encode(["status" => "unauthorized"]));
      }
    } else {
      $this->request->set_header(400);
      print_r(json_encode(["status" => "error", "message" => "Bad request."]));
    }
  }

  public function signout() {
    if (!empty($_POST["token"])) {
      $rs = removeSession($_POST["token"]);
      if ($rs) {
        $this->request->set_header(200);
        print_r(json_encode(["status" => "success", "message" => "Session removed."]));
      } else {
        $this->request->set_header(404);
        print_r(json_encode(["status" => "error", "message" => "Session not found."]));
      }
    }
  }
}

