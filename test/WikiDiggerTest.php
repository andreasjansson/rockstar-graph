<?php

require_once 'PHPUnit/Framework.php';
require_once '../RockstarGraph/WikiDigger.php';

class WikiDiggerTest extends PHPUnit_Framework_TestCase
{
  public function testFindAssociatedActs()
  {
    $digger = new RockstarGraph_WikiDigger();
    $acts = $digger->findAssociatedActs('Nirvana_(band)');
    sort($acts);

    $this->assertEquals(
      array('Fecal_Matter_(band)', 'Foo_Fighters', 'Red_Hot_Chili_Peppers'),
       $acts);
  }

  /**
   * @expectedException RockstarGraph_WikiException
   */
  public function testFindNonExistingAssociatedActs()
  {
    $digger = new RockstarGraph_WikiDigger();
    $members = $digger->findAssociatedActs('sdfjk1ashfj2ksdahf3jaskfhsd5kfh');
  }

  public function testGetCanonicalName()
  {
    $digger = new RockstarGraph_WikiDigger();
    $this->assertEquals('Foo_Fighters',
                        $digger->getCanonicalName('The_Foo_Fighters'));
  }
}