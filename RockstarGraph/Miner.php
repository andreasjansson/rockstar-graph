<?php

require_once 'WikiDigger.php';
require_once 'Link.php';

class RockstarGraph_Miner
{
  public function __construct()
  {

  }

  // TODO: Completely broken, doesn't understand links at all!
  // Redo MongoGraph as well (not a lot of work there though)

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
      $previousBands = array_slice($bands, 0, $bandsPointer + 1);
      $associatedActs = array_diff($associatedActs, $previousBands);
      foreach($associatedActs as $associatedAct) {
        $links[] = new RockstarGraph_Link($band, $associatedAct);
        if(!in_array($associatedAct, $bands)) {
          $bands[] = $associatedAct;
          if($debugging)
            file_put_contents('php://stderr',
                              sprintf("%s => %s\n", $band, $associatedAct));
        }
      }

      $bandsPointer ++;
    }

    return $links;
  }
}
