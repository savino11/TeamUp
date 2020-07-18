<?php
  session_start();
  require_once 'activity.php';
  require_once 'serverResponse.php';
  require_once 'user.php';

  //recupero l'id dell'utente loggato e le sue info
  $idUser = $_SESSION["id"];
  $userRecord = User::readUserById($idUser);
  $userObj = User::newFromRecord($userRecord);

  //recupero la richiesta inviata dal client in formato json e la memorizzo in una variabile
  $string = file_get_contents('php://input');
  $request = json_decode($string, true);

  //recupero le info dell'attività
  $idActivity = $request["activityId"];
  $date = date("Y-m-d");
  $descr = $request["descr-request"];

  //scrivo nella tabella delle richieste
  $flag = Activity::writeOnRichiestePendenti($idActivity, $idUser, $date, $descr);

  //recupero l'attività e tutti i suoi attributi dal database
  $activityRecord = Activity::readFromDB($idActivity);
  $activityObj = Activity::newFromRecord($activityRecord);

  //recupero le email dei leader dell'attività
  $leadersEmail = array();
  $teammates = $activityObj->getPartecipanti();
  for ($i=0; $i<count($teammates); $i++)
  {
    if ($teammates[$i]["is_leader"] == 1)
    {
      $leadersEmail[] = $teammates[$i]["email"];
    }
  }

  $object = "Nuova richiesta di partecipazione";
  $message = "Hai ricevuto una richiesta di partecipazione per la tua attività, dal titolo \"".$activityObj->getTitolo()."\", ";
  $message = $message."dall'utente ".$userObj->getUsername().", con la seguente descrizione: ".$descr.".\nControlla subito!";
  $from = "From: TeamUp";

  //invio al mail ai leader
  if ($flag)
  {
    for ($i=0; $i<count($leadersEmail); $i++)
    {
      mail($leadersEmail[$i],$object,$message,$from);
    }
  }

  $response = new Response ($flag, "http://localhost/TeamUp/homepage.php");

  echo json_encode($response);

 ?>
