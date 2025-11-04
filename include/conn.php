<?php
include 'global.php';

Class Database{

  private $server = DB_HOSTNAMEDNS;
  private $username = DB_USERNAME;
  private $password = DB_PASSWORD;
  private $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,);
  protected $conn;

  public function open(){
    try{
      $this->conn = new PDO($this->server, $this->username, $this->password, $this->options);
      return $this->conn;
    }
    catch (PDOException $e){
      echo "There is some probles in connecting to the Database " . $e->getMessage();
    }

  }
  public function close(){
    $this->conn = null;
  }

}
$pdo = new Database();
 ?>
