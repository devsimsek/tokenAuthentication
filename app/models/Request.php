<?php

class Request extends SDF\Model
{
  public string $client;
  public string $ip;
  public string $date;
  public string $path;
  public array $query;

  public function __construct() {
    $this->ip = $_SERVER["REMOTE_ADDR"];
    $this->path = $_SERVER["REQUEST_URI"];
    $this->date = date("Y-m-d H:i:s");
    $this->client = $_SERVER['HTTP_USER_AGENT'];
    $this->query = [];
  }

  public static function http_status(int $code): string
    {
        $http_codes = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Checkpoint',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'Im a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        ];

        return $http_codes[$code] ? $http_codes[$code] : $http_codes[500];
    }

    /**
     *
     * Set Header
     *
     * Sets header
     *
     * @param int $code
     * @param string $content_type
     * @param string $charset
     */
    public function set_header(int $code, string $content_type = "application/json", string $charset = "utf-8")
    {
        header('HTTP/1.1 ' . $code . ' ' . $this->http_status($code));
        header('Content-Type: ' . $content_type . '; charset=' . $charset);
    }

    public function get_input(string $field): array|false {
      if ($field === "post") {
        if (is_array($_POST) && !empty($_POST))
          return $_POST;
        return (array)json_decode(file_get_contents(('php://input')));
      }
      return false;
    }
}
