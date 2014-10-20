<?php

class FeedTable
{
  private $dbh;

  public function __construct($dbh)
  {
    $this->dbh = $dbh;
  }

  public function insert($feed)
  {
    $stmt = $this->dbh->prepare('INSERT INTO feed(xml) VALUES(:xml);');
    return $stmt->execute(array('xml' => $feed));
  }

  public function select()
  {
    $stmt = $this->dbh->prepare('SELECt * FROM feed');
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Feed');
    $stmt->execute();
    return $stmt->fetch();
  }
}

class Feed
{
  public $xml;
}
