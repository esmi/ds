<?php
namespace Esmi\DS;

class CircularQueue extends \SplQueue {
  function fill($a) {
    foreach($a as $d) {
        $this->enqueue($d);
    }
    $this->rewind();
  }
  function next() {
    if ($this->isEmpty()) {
      return;
    }
    parent::next();
    if (!$this->valid()) {
      $this->rewind();
    }
  }
  function prev() {
    if ($this->isEmpty()) {
      return;
    }
    parent::prev();
    if (!$this->valid())
    {
      for ($i = 0; $i < $this->count() -1; $i++) {
        $this->next();
      }
    }
  }
  function first() {
    if (!$this->valid()) {
      $this->rewind();
    }
  }
}

 ?>
