<?php

class Callback
{
  private $verify_token = null;
  private $secret       = null;
  private $has          = false;
  private $feed         = null;

  public function __construct($options = array())
  {
    $this->verify_token = $options['verify_token'];
    $this->secret       = $options['secret'];
  }

  // ハブからのリクエストを受け取る
  public function handle()
  {
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        try {
          $this->mode();
          $this->hub_verify_token();
          header('HTTP/1.0 200 OK');
          echo $this->params('hub_challenge');
        } catch (Exception $e) {
          header('HTTP/1.0 404 Not Found');
        }

        break;
      case 'POST':
        $feed          = file_get_contents('php://input');
        $digest        = hash_hmac('sha1', $feed, $this->secret);
        $hub_signature = explode('=', $this->headers('X-Hub-Signature'))[1];

        $logger = new Logger('debug.log');
        $logger->log('$digest: '. $digest);
        $logger->log('$hub_signature' . $hub_signature);
        if ($digest === $hub_signature) {
          $this->has = true;
          $this->feed = $feed;
        } else {
          header('HTTP/1.0 404 Not Found');
        }

        break;
      default :
        header('HTTP/1.0 404 Not Found');
    }
  }

  public function has_feed_update()
  {
    return $this->has;
  }

  public function get_feed_update()
  {
    return $this->feed;
  }

  private function mode()
  {
    switch ($this->params('hub_mode')) {
      case 'subscribe':
        return true;
        break;
      case 'unsubscribe':
        return true;
        break;
      default:
        // 例外放出
        Throw new Exception('Missing hub mode.');
    }
  }

  private function hub_verify_token()
  {
    $hub_verify_token = $this->params('hub_verify_token');
    if ($hub_verify_token === $this->verify_token) {
      return true;
    } else {
      // 例外放出
      Throw new Exception('Invalid verification.');
    }
  }

  // 指定された名前のGETパラメータの値を返す
  // 存在しない場合はnull
  private function params($key)
  {
    return array_key_exists($key, $_GET) ? $_GET[$key] : null;
  }

  // 指定された名前のヘッダーの値を返す
  // 存在しない場合はnull
  private function headers($key)
  {
    $headers = getallheaders();
    return array_key_exists($key, $headers) ? $headers[$key] : null;
  }
}

class Logger
{
  private $name;
  private $mode;

  public function __construct($name, $mode = 'a')
  {
    $this->name = $name;
    $this->mode = $mode;
    $this->fp = fopen($name, $mode);
  }

  public function __destruct()
  {
    fclose($this->fp);
  }

  public function log($message = null)
  {
    if (!is_null($message)) {
      $message = $message . PHP_EOL;
      fwrite($this->fp, $message);
    }
  }
}
