<?php
  session_start();
  require 'activity.php';
  require 'serverResponse.php';

  //recupero l'id dell'utente loggato e la richiesta inviata dal client in formato json e la memorizzo in una variabile
  $idUser = $_SESSION["id"];
  $string = file_get_contents('php://input');
  $request = json_decode($string, true);

  //recupero l'id dell'attività
  $idAct = $request["activityId"];

  //elimino la richiesta inviata
  $flag = Activity::deleteFromRichiestePendenti($idUser, $idAct);

  //recupero l'attività dal database
  $activityRecord = Activity::readFromDB($idAct);
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

  $object = "Annullamento richiesta di partecipazione";
  $message = "Un utente ha annullato la richiesta di partecipazione precedentemente inviata alla tua attività dal titolo: \"".$activityObj->getTitolo()."\".";
  $from = "From: TeamUp";

  //invio la mail generata ai leader
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
