<?php
namespace Esmi\DS;
use Exception;

class RandomReader {
  public $simReader = true;
  function __construct($min = 0, $max = 500) {
    $this->min = $min;
    $this->max = $max;
  }
  public function read()
  {
    return \rand($this->min, $this->max);
  }
}
