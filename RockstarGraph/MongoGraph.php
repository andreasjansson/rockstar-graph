<?php

class RockstarGraph_MongoGraph
{
  const DB_HOST = '127.0.0.1';
  const DB_PORT = '27017';
  const DB_NAME = 'rockstarMiner';

  private $mongo;
  private $db;
  
  public function __construct()
  {
    $this->mongo = new Mongo(self::DB_HOST . ':' . self::DB_PORT);
    $this->db = $this->mongo->selectDB(self::DB_NAME);
  }

  public function getBandCount()
  {
    
  }

  public function filterExistingRockstars(array $rockstars)
  {

  }

  public function addRockstars(array $rockstars)
  {

  }

  public function filterExistingBands(array $bands)
  {

  }

  public function addBands(array $bands)
  {

  }
}

class RockstarGraph_MongoGraphException extends Exception
{
  public function __construct($message)
  {
    parent::__construct($message);
    file_put_contents('mongo_graph_exceptions.log', $message . "\n",
                      FILE_APPEND);
  }
}