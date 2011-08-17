<?php

class RockstarGraph_Link
{
  // shortened to save space when dumped as json
  public $b1;
  public $b2;

  /**
   * @param string $band1
   * @param string $band2
   */
  public function __construct($band1, $band2)
  {
    $this->b1 = $band1;
    $this->b2 = $band2;
  }
}