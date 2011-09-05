<?php

// TODO: figure out where it goes wrong. Test Digger by
// calling findAssociatedActs("The_Zombies"). If that works,
// have a long good look at the logic in this file.
// Showstopper: Paul_McCartney doesn't link to the Beatles!

require_once 'WikiDigger.php';
require_once 'Link.php';

class RockstarGraph_Miner
{
  public function __construct()
  {

  }

  /**
   * Breadth-first crawl of Wikipedia bands.
   * 
   * @param int $limit
   * @param string $startingPoint url
   * @return RockstarGraph_Link[]
   */
  public function mine($limit, $startingPoint, $debugging = FALSE)
  {
    $digger = new RockstarGraph_WikiDigger();
    $nodeCount = 0;
    $bands = array();
    $bands[] = $startingPoint;
    $bandsPointer = 0;

    while(count($bands) < $limit && $bandsPointer < count($bands)) {
      $band = $bands[$bandsPointer];
      try {
        $associatedActs = $digger->findAssociatedActs($band);
      }
      catch(RockstarGraph_WikiException $e) {
        // don't do anything, just stay alive
      }

      $associatedActs = array_unique($associatedActs);
      foreach($associatedActs as $associatedAct) {
        $links[] = new RockstarGraph_Link($band, $associatedAct);
        if($debugging)
          file_put_contents('php://stderr',
                            sprintf("%s => %s\n", $band, $associatedAct));

        if(!in_array($associatedAct, $bands)) {
          $bands[] = $associatedAct;
        }
      }

      $bandsPointer ++;
    }

    return $links;
  }
}
