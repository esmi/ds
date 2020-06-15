<?php
namespace Esmi\DS;

class UrlReader {
  function __construct($url)	{
    $this->url = $url;
  }
  // function read():
  // return value:
  // -1.0: "target is 404 not found!"
  // -2.0: "reader is empty, ip ok; 取不到值"
  // -3.0: "ip fail, timeout!"
  public function read()
  {
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //$output = curl_exec($ch);
    if( ! $output = curl_exec($ch))
    {
        curl_close($ch);
        $otype = gettype($output);
        if ( $otype == "boolean" && $output == false) { // ip fail, timeout
          $ret = -3.0;
        }
        else if ($otype == "string" && $output == "") {
          $ret = -2.0;  // ip ok , but reader is empty. 取不到值
        }
        return -4.0;
    }

    curl_close($ch);

    echo $output;
    if (strpos($output, '404 Not Found') !== false) {
      return -1.0;
    }

    return floatval($output);
  }
  public function raw()
  {
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);


    $output = curl_exec($ch);
    curl_close($ch);

    //echo $output;
    if (strpos($output, '404 Not Found') !== false) {
      return null;
    }

    return $output;
  }
  function alive($timeout = 2) {
    $ch = curl_init();
    curl_setopt ( $ch, CURLOPT_URL, $this->url );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
    $http_respond = curl_exec($ch);
    $http_respond = trim( strip_tags( $http_respond ) );
    $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    curl_close( $ch );

    if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {
      return true;
    } else {
      // return $http_code;, possible too
      return false;
    }
  }
}

// $urlReader = new UrlReader("http://localhost:8090/daelux/modbus/luxReader.php");
// echo $urlReader->read();
?>
