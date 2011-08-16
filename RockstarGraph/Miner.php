<?php

require_once 'WikiDigger.php';

class RockstarGraph_Miner
{
  public function __construct()
  {

  }

  // TODO: Completely broken, doesn't understand links at all!
  // Redo MongoGraph as well (not a lot of work there though)

  /**
   * @param int $limit
   * @param string $startingPoint url
   * @return RockstarGraph_MongoGraph
   */
  public function mine($limit, $startingPoint)
  {
    $mongoGraph = new RockstarGraph_MongoGraph();
    $digger = new RockstarGraph_WikiDigger();
    $nodeCount = 0;
    if($initialStage == NULL)
      $initialStage = self::BAND_STAGE;
    $bands = array();
    $bands[] = $startingPoint;

    while($mongoGraph->getBandCount() < $limit) {
      $rockstars = array();
      foreach($bands as $band) {
        try {
          $bandMembers = $digger->findBandMembers($band);
          $rockstars = array_unique(array_merge($rockstars, $bandMembers));
        }
        catch(WikiException $e) {
          // don't die, just don't add to rockstars
        }
      }
      $rockstars = $mongoGraph->filterExistingRockstars($rockstars);
      $mongoGraph->addRockstars($rockstars);

      foreach($rockstars as $rockstar) {
        try {
          $associatedActs = $digger->findAssociatedActs($rockstar);
          $bands = array_unique(array_merge($bands, $associatedActs));
        }
        catch(WikiException $e) {
          // don't die, just don't add to bands
        }
      }
      $bands = $mongoGraph->filterExistingBands($bands);
      $mongoGraph->addBands();
    }

    return $mongoGraph;
  }
}
