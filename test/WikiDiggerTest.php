<?php

require_once 'PHPUnit/Framework.php';
require_once '../RockstarGraph/WikiDigger.php';

class WikiDiggerTest extends PHPUnit_Framework_TestCase
{
  public function testFindBandMembers()
  {
    $digger = new RockstarGraph_WikiDigger();
    $members = $digger->findBandMembers('Nirvana_(band)');
    sort($members);
    $this->assertEquals(array(
      'Aaron_Burckhard', 'Chad_Channing', 'Dale_Crover',
      'Dan_Peters', 'Dave_Foster', 'Dave_Grohl',
      'Jason_Everman', 'Krist_Novoselic', 'Kurt_Cobain'), $members);
  }

  public function testFindAssociatedActs()
  {
    $digger = new RockstarGraph_WikiDigger();
    $acts = $digger->findAssociatedActs('Kurt_Cobain');
    sort($acts);

    $this->assertEquals(
      array('Fecal_Matter_(band)', 'Nirvana_(band)'), $acts);
  }

  /**
   * @expectedException RockstarGraph_WikiException
   */
  public function testFindNonExistingBandMembers()
  {
    $digger = new RockstarGraph_WikiDigger();
    $members = $digger->findBandMembers('sdfjk1ashfj2ksdahf3jaskfhsd5kfh');
  }
}