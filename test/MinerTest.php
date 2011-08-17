<?php

require_once 'PHPUnit/Framework.php';
require_once '../RockstarGraph/Miner.php';

class WikiDiggerTest extends PHPUnit_Framework_TestCase
{
  public function testMine()
  {
    $miner = new RockstarGraph_Miner();
    $links = $miner->mine(100, 'Nirvana_(band)', TRUE);
    $this->assertEquals('Fecal_Matter_(band)', $links[0]->band2);
    $this->assertEquals('Foo_Fighters', $links[1]->band2);
  }
}

