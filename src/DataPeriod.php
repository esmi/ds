<?php
namespace Esmi\DS;
use Exception;

class DataPeriod {
  //
  // 0.1 min => 6 s
  // 0.01 min => 0.6 s
  // 讀取間格, 平均時間, 資料長度
  // 2s     , 5m    ,  60/2 * 5 => 150
  // 3s     , 1m    ,  60/3 * 1 => 20
  // 3s     , 5m    ,  60/3 * 5 => 100
  // 5s     , 5m    ,  60/5 * 5 => 60
  private $data = [];
  private $period;
  protected $reader;
  private $debug = false;

  public function __construct($reader, $period )  {
      $this->reader = $reader;
      $this->period = $period;
      $this->failed = false;
      $this->failstat = 0;
  }
  public function setReader($reader) {
    $this->reader = $reader;
  }
  public function simReader() {
    return  property_exists($this->reader, "simReader") ? $this->reader->simReader : false;
  }
  public function setperiod($m) {
    $this->period = $m;
  }
  public function setdebug($debug=true) {
    $this->debug = $debug;
  }
  public function average() {
    $c = count($this->data);
    $t = 0;
    foreach ($this->data as $k => $v) {
      $t += $v['value'];
    }
    return $t/$c;
  }
  public function isfail() {
    return $this->failed;
  }
  public function stat() {
    return $this->failStat;
  }
  public function read()  {
    $v = $this->reader->read();

    $this->failed = !($v >= 0);

    if ($v >= 0) {
      $this->addValue($v);
      $this->failStat = 0;
    }
    else { // if less than or equal zero add zero to data.
      $this->failStat = $v;
      $this->addValue(0);
    }
    return $v;
  }
  public function getData() {
    return $this->data;
  }
  private function addValue($value) {
    $d = new \DateTime();
    array_push($this->data, ["value" => $value, 'datatime' => clone $d]);
    if ($this->debug) echo "push $value, " . ((array)$d)['date'] . "\r\n";
    $seconds = $this->period * 60;
    $d->modify("-{$seconds} seconds");
    if ($this->debug) echo "shift before: " . ((array)$d)['date'] . "\r\n";

    $this->shitdata($d);
  }

  private function shitdata($d) {
    $i = 0;
    while (true) {
      $els = $this->data[0];

      if ($els['datatime'] < $d) {
        if ($this->debug) echo "shift $i: ";
        if ($this->debug) echo '(datatime)'. ($els['datatime'])->date . ", ";
        if ($this->debug) echo '($d)' . ($d)->date . "\r\n";
        array_shift($this->data);
      }
      else {
        if ($this->debug) echo '$data length: ' . count($this->data)  ; //. "\r\n";
        break;
      }
      $i++;
    }
  }
  private function debugTIme()
  {
    if ($this->debug) {
      var_dump($this->data);
      echo "第一筆時間:" . $this->data[0]['datatime'] -> date . "\r\n";
      echo "最後一筆時間:" . $this->data[count($this->data)-1]['datatime'] -> date . "\r\n";
    }
    $iv = $this->data[count($this->data)-1]['datatime']->diff($this->data[0]['datatime']);
    if ($this->debug) print_r($iv);
    if ($this->debug) echo $iv->format("%s") . "\r\n";
    return;
  }
}
