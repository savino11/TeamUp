<?php

require_once 'dbconnection.php';

class Category
{
	private $id;
	private $nome;

	public static function newFromRecord($record)
	{
		$cat = new Category();

    $cat->id = $record["id"];
    $cat->nome = $record["nome"];

    return $cat;
  }

	public static function new($nome)
	{
	  $cat = new Category();

    $cat->nome = $nome;

    return $cat;
	}

	public static function readFromDB($id)
	{
		$conn = DbConnect::connect();

		$sql = "SELECT nome FROM categorie WHERE id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$record = $result->fetch_assoc();

		DbConnect::disconnect();

		return $record;
	}

  public static function readAllFromDB()
  {
    $conn = DbConnect::connect();
    $sql = "SELECT id,nome FROM categorie";
    $resultSet = $conn->query($sql);

    while ($row = $resultSet->fetch_assoc())
    {
      $records[] = $row;
    }

    DbConnect::disconnect();

    return $records;
  }


	public static function getNumFromDB(){

		$conn = DbConnect::connect();

		$select = "SELECT count(id) FROM categorie";
		$result = $conn->query($select);
		$record = $result->fetch_array();

		DbConnect::disconnect();

		return $record[0];

	}

	public static function getAllFromDB(){

		$conn = DbConnect::connect();

		$select = "SELECT * FROM categorie";
		$result = $conn->query($select);
		$i = 0;
		while($row[$i] = $result->fetch_array()){
			$i++;
		}

		DbConnect::disconnect();

		return $row;
	}

	public function getId() {

		return $this->id;
	}

	public function getNome() {

		return $this->nome;
	}

	public function setNome($nome) {

		$this->nome = $nome;
	}
}

?>
