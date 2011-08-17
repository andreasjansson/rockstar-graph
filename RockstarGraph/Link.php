<?php

class RockstarGraph_Link
{
  public $band1;
  public $band2;

  /**
   * @param string $band1
   * @param string $band2
   */
  public function __construct($band1, $band2)
  {
    $this->band1 = $band1;
    $this->band2 = $band2;
  }
}