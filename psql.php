<?php

class FeedTable
{
  const INSERT = 'INSERT INTO feed(xml) VALUES(:xml);';
  const SELECT = 'SELECT * FROM feed;';
  private $dbh;

  public function __construct($dbh)
  {
    $this->dbh = $dbh
  }

  public functionn insert($feed)
  {
    $stmt = $this->dbh->prepare(INSERT);
    return $stmt->execute(array('xml' => $feed));
  }

  public function select()
  {
    $stmt = $this->dbh->prepare(SELECT);
    $stmt->setFechMode(PDO::FETCH_CLASS, );
    return $stmt->fetch();
  }
}

class Feed
{
  public $xml;
}
