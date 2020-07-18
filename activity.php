<?php

require_once 'dbconnection.php';
require_once 'user.php';

class Activity implements JsonSerializable
{
	private $id;
	private $titolo;
	private $descrizione;
	private $dataCreazione;
	private $idPosizione;
	private $postiRimanenti;
	private $completata;
	private $categorie;
	private $partecipanti;

	public static function newFromRecord($record)
	{
		$activity = new Activity();

    $activity->id = $record["id"];
    $activity->titolo = $record["titolo"];
    $activity->descrizione = $record["descrizione"];

		$date = date_create($record["data_creazione"]);
		$activity->dataCreazione = date_format($date, "d/m/Y");

		$activity->idPosizione = $record["posizione"];
    $activity->postiRimanenti = $record["posti_rimanenti"];
    $activity->completata = $record["completata"];
		$activity->categorie = Activity::readFromCategoriesActivity($activity->id);
		$activity->partecipanti = Activity::readFromPartecipanti($activity->id);

    return $activity;
  }

	public static function new($titolo, $descrizione, $idPosizione, $postiRimanenti, $partecipanti)
	{
	  $activity = new Activity();

    $activity->titolo = $titolo;
    $activity->descrizione = $descrizione;
    $activity->dataCreazione = date("Y-m-d");
    $activity->idPosizione = $idPosizione;
    $activity->postiRimanenti = $postiRimanenti;
    $activity->completata = false;
		$activity->partecipanti = $partecipanti;

    return $activity;
	}

	public static function writeOnDB ($activity)
	{
			$conn = DbConnect::connect();

			$sql = "INSERT INTO attivita (titolo, descrizione, data_creazione, posizione, posti_rimanenti, completata)
							VALUES (?,?,?,?,?,?)";

			$stmt= $conn->prepare($sql);
			$stmt->bind_param("ssssis", $activity->titolo, $activity->descrizione, $activity->dataCreazione, $activity->idPosizione, $activity->postiRimanenti, $activity->completata);
			$stmt->execute();

			$id = $conn->insert_id;

			DbConnect::disconnect();
			return $id;
	}

	public static function readFromDB($id)
	{
		$conn = DbConnect::connect();

		$sql = "SELECT * FROM attivita WHERE id = ?";
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
    $sql = "SELECT * FROM attivita";
    $resultSet = $conn->query($sql);
		$records = array();
		$i = 0;
    while ($row = $resultSet->fetch_assoc())
    {
      $records[$i] = $row;
			$i++;
    }

    DbConnect::disconnect();

    return $records;
  }


	public static function readActivityOfUser($idUser)
	{
		$conn = DbConnect::connect();

		$sql = "SELECT attivita.id,attivita.titolo,attivita.descrizione,attivita.data_creazione,
									 attivita.posizione,attivita.posti_rimanenti,attivita.completata,partecipanti.is_leader
						FROM attivita
						JOIN partecipanti ON attivita.id=partecipanti.attivita
						JOIN utenti ON partecipanti.utente=utenti.id
						WHERE utenti.id=$idUser";

		$resultSet = $conn->query($sql) or die($conn->error);
		$records = array();
		$i = 0;
		while ($row = $resultSet->fetch_assoc())
		{
			$records[$i] = $row;
			$i++;
		}

		DbConnect::disconnect();

		return $records;
	}


	public static function readActivityForUser($userObj, $typeOfAction)
	{
		$conditions = User::constructString($userObj->getPosizione(), $userObj->getInteressi(), $typeOfAction);

		//------------------------ QUERY E LATO DB
		$conn = DbConnect::connect();

		$sql = "SELECT DISTINCT attivita.id,attivita.titolo,attivita.descrizione,attivita.data_creazione,attivita.posizione,attivita.posti_rimanenti,attivita.completata
						FROM attivita
						JOIN categorie_attivita ON attivita.id = categorie_attivita.attivita
						WHERE attivita.completata = 0 AND attivita.posti_rimanenti > 0 AND attivita.id NOT IN
							(SELECT DISTINCT attivita.id
						    FROM attivita
						    JOIN partecipanti ON attivita.id = partecipanti.attivita
						    WHERE partecipanti.utente =".$userObj->getId().")";

		if (strlen($conditions) > 0)
		{
			$sql = $sql.$conditions;
		}

		$resultSet = $conn->query($sql) or die($conn->error);
		$records = array();
		$i = 0;

		while ($row = $resultSet->fetch_assoc())
		{
			$records[$i] = $row;
			$i++;
		}

		DbConnect::disconnect();

		return $records;
	}



	public static function updateOnDB ($activity)
	{
		if (!isset($activity->id))
		{
			return false;
		}

		else
		{
			$conn = DbConnect::connect();

			$sql = "UPDATE attivita
							SET titolo = ?,
							descrizione = ?,
							posizione = ?,
							posti_rimanenti = ?,
							completata = ?
							WHERE id = ?";

			$stmt= $conn->prepare($sql);
			$stmt->bind_param("sssisi", $activity->titolo, $activity->descrizione, $activity->idPosizione, $activity->postiRimanenti, $activity->completata, $activity->id);
			$flag = $stmt->execute();

			DbConnect::disconnect();

			return $flag;
		}
	}

	public static function deleteFromDB ($idActivity)
	{
		if (!isset($idActivity))
		{
			return false;
		}

		else
		{
			$conn = DbConnect::connect();

			$sql = "DELETE FROM attivita WHERE id=?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $idActivity);
			$flag = $stmt->execute();

			DbConnect::disconnect();

			return $flag;
		}
	}

	public static function writeOnCategoriesActivity ($idActivity, $idCat)
	{
		$conn = DbConnect::connect();

		$sql = "INSERT INTO categorie_attivita (attivita, categoria) VALUES (?,?)";
		$stmt= $conn->prepare($sql);
		$stmt->bind_param("ii", $idActivity, $idCat);
		$flag = $stmt->execute();

		DbConnect::disconnect();

		return $flag;
	}

	public static function readFromCategoriesActivity ($idActivity)
	{
		$conn = DbConnect::connect();

		$sql = "SELECT categoria,nome
						FROM categorie_attivita
						JOIN categorie
						ON categorie_attivita.categoria = categorie.id WHERE attivita=$idActivity";

		$resultSet = $conn->query($sql);
		$record = array();
		$i = 0;
		while ($row = $resultSet->fetch_assoc())
		{
			$record[$i] = $row;
			$i++;
		}

		DbConnect::disconnect();

		return $record;

	}

	public static function deleteFromCategoriesActivity ($idActivity)
	{
		if (!isset($idActivity) || $idActivity <= 0)
		{
			return false;
		}

		else
		{
			$conn = DbConnect::connect();

			$sql = "DELETE
							FROM categorie_attivita
							WHERE attivita=?";

			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $idActivity);
			$flag = $stmt->execute();

			DbConnect::disconnect();

			return $flag;
		}
	}

	public static function writeOnPartecipanti ($idUser, $idActivity, $isLeader)
	{
		$conn = DbConnect::connect();

		$sql = "INSERT INTO partecipanti (utente, attivita, is_leader) VALUES (?,?,?)";
		$stmt= $conn->prepare($sql);
		$stmt->bind_param("iis", $idUser, $idActivity, $isLeader);
		$flag = $stmt->execute();

		DbConnect::disconnect();

		return $flag;
	}

	public static function readFromPartecipanti($idActivity)
	{
		$conn = DbConnect::connect();

		$sql = "SELECT id, utente,username,email,is_leader
						FROM partecipanti
						JOIN utenti
						ON partecipanti.utente = utenti.id
						WHERE attivita=$idActivity
						ORDER BY is_leader DESC";

		$resultSet = $conn->query($sql) or die($conn->error);
		$records = array();
		$i = 0;
		while ($row = $resultSet->fetch_assoc())
		{
			$records[$i] = $row;
			$i++;
		}

		DbConnect::disconnect();

		return $records;
	}

	public static function deleteFromPartecipanti($idUser, $idAct)
	{
		$conn = DbConnect::connect();

		$sql = "DELETE FROM partecipanti
						WHERE utente=? AND attivita=?";

		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ii", $idUser, $idAct);
		$stmt->execute();

		DbConnect::disconnect();
	}

	public static function upgradeOnLeader($utente, $attivita)
	{
			$conn = DbConnect::connect();
			$sql = "UPDATE partecipanti  SET is_leader = 1 WHERE utente = ? AND attivita = ?";
			$stmt= $conn->prepare($sql);
			$stmt->bind_param("ii", $utente, $attivita);
			$stmt->execute();

			DbConnect::disconnect();
	}

	public static function downgrade($idUtente)
	{
		$conn = DbConnect::connect();
		$sql = "UPDATE partecipanti  SET is_leader = 0 WHERE utente = ?";
		$stmt= $conn->prepare($sql);
		$stmt->bind_param("i", $idUtente);
		$stmt->execute();

		DbConnect::disconnect();
	}



	public static function readActivityNameFromRichiestePendenti ($idUser)
	{
		$conn = DbConnect::connect();

		$sql = "SELECT attivita.titolo FROM attivita
						JOIN richieste_pendenti_attivita ON attivita.id = richieste_pendenti_attivita.attivita
						WHERE attivita.completata = 0 AND utente=$idUser";

		$resultSet = $conn->query($sql) or die ($conn->error);
		$records = array();
		$i=0;

		while ($row = $resultSet->fetch_assoc())
		{
			$records[$i] = $row;
			$i++;
		}

		return $records;
	}


	public static function activitiesRequestedByUser ($idUser)
	{
		$conn = DbConnect::connect();

		$sql = "SELECT attivita FROM richieste_pendenti_attivita
						WHERE utente=$idUser";

		$resultSet = $conn->query($sql) or die ($conn->error);
		$records = array();
		$i=0;

		while ($row = $resultSet->fetch_assoc())
		{
			$records[$i] = $row;
			$i++;
		}

		return $records;
	}

	public static function writeOnRichiestePendenti($idActivity, $idUser, $date, $descr)
	{
		$conn = DbConnect::connect();

		$sql = "INSERT INTO richieste_pendenti_attivita (attivita, utente, data_richiesta, descrizione)
						VALUES (?,?,?,?)";

		$stmt= $conn->prepare($sql);
		$stmt->bind_param("iiss", $idActivity, $idUser, $date, $descr);
		$flag = $stmt->execute();

		DbConnect::disconnect();

		return $flag;
	}

	public static function deleteFromRichiestePendenti ($idUser, $idAct)
	{
		$conn = DbConnect::connect();

		$sql = "DELETE FROM richieste_pendenti_attivita
						WHERE utente=? AND attivita=?";

		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ii", $idUser, $idAct);
		$flag = $stmt->execute();

		DbConnect::disconnect();

		return $flag;
	}


	public static function getAttivitaIdsWhereOnlyLeaderIs($userId) {

		$conn = DbConnect::connect();

		$sql = "SELECT attivita
				FROM partecipanti
				WHERE utente = $userId AND is_leader = 1 AND attivita IN (	SELECT attivita
																			FROM partecipanti
																			GROUP BY attivita
																			HAVING SUM(is_leader) = 1 )";

		$resultSet = $conn->query($sql);
		$activitiesId = array();
		$i = 0;

		while ($row = $resultSet->fetch_assoc())
		{
			$activitiesId[$i] = $row['attivita'];
			$i++;
		}

		DbConnect::disconnect();

		return $activitiesId;
	}

	public static function getAttivitaIdsWhereLeaderIs($userId) {

		$conn = DbConnect::connect();

		$sql = "SELECT attivita
				FROM partecipanti
				WHERE utente = $userId AND is_leader = 1";

		$resultSet = $conn->query($sql);
		$activitiesId = array();
		$i = 0;

		while ($row = $resultSet->fetch_assoc())
		{
			$activitiesId[$i] = $row['attivita'];
			$i++;
		}

		DbConnect::disconnect();

		return $activitiesId;
	}


	public function jsonSerialize()
  {
      return
      [
          'id'   => $this->getId(),
          'titolo' => $this->getTitolo(),
					'descrizione' => $this->getDescrizione(),
					'data_creazione' => $this->getDataCreazione(),
					'idPosizione' => $this->getIdPosizione(),
					'posti_rimanenti' => $this->getPostiRimanenti(),
					'completata' => $this->isCompletata(),
					'categorie' => $this->getCategorie(),
					'partecipanti' => $this->getPartecipanti()
      ];
  }

	public function getId() {

		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getTitolo() {

		return $this->titolo;
	}

	public function setTitolo($titolo) {

		$this->titolo = $titolo;
	}

	public function getDescrizione() {

		return $this->descrizione;
	}

	public function setDescrizione($descrizione) {

		$this->descrizione = $descrizione;
	}

	public function getDataCreazione() {

		return $this->dataCreazione;
	}

	public function getIdPosizione() {

		return $this->idPosizione;
	}

	public function setPosizione($idPosizione) {

		$this->posizione = $idPosizione;
	}

	public function getPostiRimanenti() {

		return $this->postiRimanenti;
	}

	public function setPostiRimanenti($postiRimanenti) {

		$this->postiRimanenti = $postiRimanenti;
	}

	public function isCompletata() {

		return $this->completata;
	}

	public function switchCompletata() {

		$this->completata = !$this->completata;
	}

	public function getCategorie()
	{
		return $this->categorie;
	}

	public function setCategorie($categorie)
	{
		$this->categorie = $categorie;
	}

	public function getPartecipanti()
	{
		return $this->partecipanti;
	}


	public static function close($id) {

		$conn = DbConnect::connect();

		$sql = "UPDATE `attivita` SET completata=1 WHERE id=?";

		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id);
		$stmt->execute();

		DbConnect::disconnect();
	}

	public static function deleteAllRichiestePendenti($id) 	{

		$conn = DbConnect::connect();

		$sql = "DELETE FROM richieste_pendenti_attivita
						WHERE attivita=?";

		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id);
		$stmt->execute();

		DbConnect::disconnect();
	}

	public static function getRichiestePendenti ($activityId)
	{
		$conn = DbConnect::connect();

		$sql = "select id, username, email, descrizione
				from richieste_pendenti_attivita join utenti
				on richieste_pendenti_attivita.utente = utenti.id
				WHERE attivita = $activityId
				ORDER BY data_richiesta";

		$resultSet = $conn->query($sql) or die ($conn->error);
		$records = array();
		$i=0;

		while ($row = $resultSet->fetch_assoc())
		{
			$records[$i] = $row;
			$i++;
		}

		return $records;
	}

	public static function readSearchedActivities($idUser, $conditions)
	{
		$conn = DbConnect::connect();

		$sql = "SELECT DISTINCT attivita.id,attivita.titolo,attivita.descrizione,attivita.data_creazione,attivita.posizione,attivita.posti_rimanenti,attivita.completata
						FROM attivita
						JOIN categorie_attivita ON attivita.id = categorie_attivita.attivita
						WHERE $conditions AND attivita.completata = 0 AND attivita.posti_rimanenti > 0 AND attivita.id NOT IN
							(SELECT DISTINCT attivita.id
							FROM attivita
							JOIN partecipanti ON attivita.id = partecipanti.attivita
							WHERE partecipanti.utente = $idUser)";

		$resultSet = $conn->query($sql) or die($conn->error);
		$records = array();
		$i = 0;

		while ($row = $resultSet->fetch_assoc())
		{
			$records[$i] = $row;
			$i++;
		}

		DbConnect::disconnect();

		return $records;
	}
}

?>
