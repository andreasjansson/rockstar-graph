<?php

require_once 'RockstarGraph/Miner.php';

$options = getopt('l::s::v::', array('limit::', 'start::', 'verbose::'));
$limit = (int)(isset($options['l']) ? $options['l'] :
               (isset($options['limit']) ? $options['limit'] : 10));
$start = isset($options['s']) ? $options['s'] :
  (isset($options['start']) ? $options['start'] : 'Nirvana_(band)');
$verbose = isset($options['v']) || isset($options['verbose']);

$miner = new RockstarGraph_Miner();
$links = $miner->mine($limit, $start, $verbose);

echo json_encode($links);

exit;