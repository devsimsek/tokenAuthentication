<?php

// Copyright (C)Devsimsek. Researching token authentication
class Auth extends SDF\Controller {
  protected $request;
  
  public function __construct() {
    parent::__construct();
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
    if (!empty($this->request->get_input("post")["username"]) && !empty($this->request->get_input("post")["password"])) {
      $user = json_decode(getUser($this->request->get_input("post")["username"]));
      if ($user->password === md5($this->request->get_input("post")["password"])) {
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
    if (!empty($this->request->get_input("post")["username"]) && !empty($this->request->get_input("post")["password"])) {
      $s = registerUser($this->request->get_input("post")["username"], md5($this->request->get_input("post")["password"]));
      if ($s) {
        $this->request->set_header(200);
        print_r(json_encode(["status" => "success", "message" => "Account created."]));
      } else {
        $this->request->set_header(409);
        print_r(json_encode(["status" => "conflict", "message" => "Account already exists."]));
      }
    }
  }

  public function session() {
    if (!empty($this->request->get_input("post")["token"])) {
      $session = getSession($this->request->get_input("post")["token"]);
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
    if (!empty($this->request->get_input("post")["token"])) {
      $rs = removeSession($this->request->get_input("post")["token"]);
      if ($rs) {
        $this->request->set_header(200);
        print_r(json_encode(["status" => "success", "message" => "Session removed."]));
      } else {
        $this->request->set_header(404);
        print_r(json_encode(["status" => "error", "message" => "Session not found."]));
      }
    }
  }

  public function preflight() {
    $this->request->set_header(200);
  }
}

