<?php
namespace Esmi\DS;
use Exception;

class ShmMemory {
  private $size = 3096;

  function __construct($size = 3096) {
    $this->size = $size;
    $this->q = new ShmopQueue($this->size);
  }
  function append($d) {

    $now = time();

    $its = $this->q->items();
    $it = [];
    if ($its) {
      $it = end($its);
      //var_dump($its);
    }
    else {
      $it = [
        "today" => 0,
        "month" => 0,
        "time" => 0
      ];
    }
    //var_dump($its);
    $isSave = false;

    //$it['luxer'] = ($d['luxer']);
    foreach ($d as $key => $value) {
        $it[$key] = $value;
    }
    //echo "it: .... \r\n";
    //var_dump($it);
    $this->q->enqueue($it);
    $its= $this->q->items();
    //var_dump($its);
    $count = count($its);
    //echo "count: $count\r\n";
    if ($count > 1) {
      for ($i = 1; $i < $count; $i++) {
        $this->q->dequeue();
      }
    }
    return ["status" => true];
  }
  function read() {
    $its = $this->q->items();

    return ($its) ? end($its) : null;
  }
}
 ?>
