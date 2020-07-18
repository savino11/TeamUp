<?php

require_once 'dbconnection.php';

class User implements JsonSerializable
{

  private $id;
  private $email;
  private $username;
  private $password;
  private $posizione;
  private $token;
  private $interessi;

  public static function new($email, $username, $password, $posizione){

    $user = new User();

    $user->email = $email;
    $user->username = $username;
    $user->password = $password;
    $user->posizione = $posizione;
    $user->token = md5(microtime());

    return $user;

  }

  public static function newFromRecord($record){

    $user = new User();

    $user->id = $record['id'];
    $user->email = $record['email'];
    $user->username = $record['username'];
    $user->password = $record['password'];
    $user->posizione = $record['posizione'];
    $user->interessi = User::getUserInterests($user->id);
    $user->token = $record['token'];

    return $user;

  }

  public static function writeOnDB($user){

    $conn = DbConnect::connect();

    $insert = "INSERT INTO utenti(email,username,password,posizione,token) VALUES (?,?,?,?,?)";
    $stm = $conn->prepare($insert);
    $stm->bind_param("sssss",$user->email,$user->username,$user->password,$user->posizione,$user->token);
    $stm->execute();

    $id = $conn->insert_id;

    DbConnect::disconnect();

    return $id;

  }

  public static function readUserById($id){

    $conn = DbConnect::connect();

    $select = "SELECT * FROM utenti WHERE id = ?";
    $stm = $conn->prepare($select);
    $stm->bind_param("i",$id);
    $stm->execute();
    $result = $stm->get_result();
    $record = $result->fetch_assoc();

    DbConnect::disconnect();

    return $record;

  }

  public static function readUserByEmail($email){

    $conn = DbConnect::connect();

    $select = "SELECT * FROM utenti WHERE email = ?";
    $stm = $conn->prepare($select);
    $stm->bind_param("s",$email);
    $stm->execute();
    $result = $stm->get_result();
    $record = $result->fetch_assoc();

    DbConnect::disconnect();

    return $record;

  }

  public static function readUserByUsername($username){

    $conn = DbConnect::connect();

    $select = "SELECT * FROM utenti WHERE username = ?";
    $stm = $conn->prepare($select);
    $stm->bind_param("s",$username);
    $stm->execute();
    $result = $stm->get_result();
    $record = $result->fetch_assoc();

    DbConnect::disconnect();

    return $record;

  }

  public static function insertUserInterests($idUtente,$idCategorie){

    if(isset($idCategorie)){

      $conn = DbConnect::connect();

      for($i = 0; $i < count($idCategorie); $i++){
        $categoria = $idCategorie[$i];
        $insert = "INSERT INTO interessi_utente(utente,categoria) VALUES ('$idUtente','$categoria')";
        $conn->query($insert);
      }

      DbConnect::disconnect();

    }

  }

  public static function getUserInterests($id){

    $conn = DbConnect::connect();

    $select = "SELECT categorie.id, categorie.nome FROM categorie JOIN interessi_utente ON categorie.id = interessi_utente.categoria WHERE interessi_utente.utente = ?";
    $stm = $conn->prepare($select);
    $stm->bind_param("i",$id);
    $stm->execute();
    $result = $stm->get_result();
    $record = array();

    while ($row = $result->fetch_assoc())
    {
      $record[] = $row;
    }

    DbConnect::disconnect();

    return $record;

  }

  public static function deleteInterests ($idUser)
  {
    $conn = DbConnect::connect();

    $sql = "DELETE FROM interessi_utente WHERE utente = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("i",$idUser);
    $conn->query($sql);

    DbConnect::disconnect();
  }


  public static function updateOnDB($user){

    $conn = DbConnect::connect();

    $insert = "UPDATE utenti SET email = ?,
                                 username = ?,
                                 password = ?,
                                 posizione = ?,
                                 token = ? WHERE id= ?";
    $stm = $conn->prepare($insert);
    $stm->bind_param("sssssi",$user->email, $user->username, $user->password, $user->posizione, $user->token, $user->id);
    $stm->execute();

    DbConnect::disconnect();

  }


  /*
  costruisce la WHERE clause per la query al DB. gestisce i casi:
  1. caricamento dati dell'homepage
  2. ricerca attività
  */
  public static function constructString($locationid, $categories, $typeOfAction)
  {
    //definisco un flag per identificare il tipo di query da fare al DB, distinguendo tra:
    //1. le attività per l'utente
    //2. la ricerca
    $flagIsSearch = NULL;

    if ($typeOfAction == "load")
    {
      $flagIsSearch = false;
    }

    else if ($typeOfAction == "search")
    {
      $flagIsSearch = true;
    }

    $condizioni = array();

    //rielaboro la condizione relativa alla posizione
    if ($locationid != NULL && strlen($locationid) > 0)
    {
      $condizioni[] = "attivita.posizione = '".$locationid."'";
    }
    else
    {
      $condizioni[] = "";
    }

    //rielaboro la condizione relativa agli interessi
    if ($categories != NULL)
    {
      for ($i=0; $i<count($categories); $i++)
      {
        $condizioni[] = "categorie_attivita.categoria = ".$categories[$i]["id"];
      }
    }

    //costruisco la stringa per la WHERE clause
    $sql = "";
    if (count($condizioni) == 1)
    {
      if (strlen($condizioni[0]) > 0)
      {
        $sql = $sql." AND (".$condizioni[0].")";
      }
    }

    else
    {
      $sql = $sql." AND (";

      $logicalOperator = NULL;
      //in base al flag, utilizzo l'operatore logico OR oppure AND
      if ($flagIsSearch)
      {
        $logicalOperator = " AND ";
      }
      else
      {
        $logicalOperator = " OR ";
      }

      if (strlen($condizioni[0]) > 0)
      {
        $sql = $sql.$condizioni[0].$logicalOperator;
      }

      for ($i=1; $i<count($condizioni); $i++)
      {
        $sql = $sql.$condizioni[$i];

        if ($i != count($condizioni)-1)
        {
          $sql = $sql.$logicalOperator;
        }
      }

      $sql = $sql.")";
    }

    return $sql;
  }



  public static function deleteFromDB ($id)
	{
		$conn = DbConnect::connect();

		$sql = "DELETE FROM utenti WHERE id=?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id);
		$stmt->execute();

		DbConnect::disconnect();
	}

  public function getId(){
    return $this->id;
  }

  public function getEmail(){
    return $this->email;
  }

  public function getUsername(){
    return $this->username;
  }

  public function getPassword(){
    return $this->password;
  }

  public function getPosizione(){
    return $this->posizione;
  }

  public function getToken(){
    return $this->token;
  }

  public function getInteressi(){
    return $this->interessi;
  }

  public function setId($id){
    $this->id = $id;
  }

  public function setEmail($email){
    $this->email = $email;
  }

  public function setUsername($username){
    $this->username = $username;
  }

  public function setPassword($password){
    $this->password = $password;
  }

  public function setPosizione($posizione){
    $this->posizione = $posizione;
  }

  public function setToken($token){
    $this->token = $token;
  }

  public function setInteressi($interessi){
    $this->interessi = $interessi;
  }

  public function jsonSerialize()
  {
      return
      [
          'id'   => $this->getId(),
          'email' => $this->getEmail(),
					'username' => $this->getUsername(),
					'password' => $this->getPassword(),
					'posizione' => $this->getPosizione(),
					'token' => $this->getToken(),
          'interessi' => $this->getInteressi()
      ];
  }
}
?>
