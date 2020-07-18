<?php
  session_start();
  require_once 'activity.php';
  require_once 'user.php';
  require_once 'serverResponse.php';

  //recupero l'id dell'utente loggato e la richiesta inviata dal client in formato json e la memorizzo in una variabile
  $idUser = $_SESSION["id"];
  $string = file_get_contents('php://input');
  $request = json_decode($string, true);

  //recupero l'id dell'attivitò oggetto di invito e la mail dell'utente da invitare
  $idAct = $request["activityId"];
  $targetEmail = $request["targetEmail"];

  //recupero lo username dell'utente mittente
  $userRecord = User::readUserById($idUser);
  $userObj = User::newFromRecord($userRecord);

  //recupero le info dell'Attività
  $actRecord = Activity::readFromDB($idAct);
  $actObj = Activity::newFromRecord($actRecord);

  $object = "Invito a partecipare ad un'Attività";
  $message = "L'utente \"".$userObj->getUsername()."\" ti ha invitato a partecipare all'Attivita' \"".$actObj->getTitolo()."\" creata il ".$actObj->getDataCreazione().".";

  /*
  Verifico che l'utente da invitare non sia presente nel database.
  Il messaggio della mail conterrà un testo diverso per il singolo caso.
  Il link alla index conterrà id dell'attività da mostrare.
  */
  $userTarget = User::readUserByEmail($targetEmail);
  if ($userTarget == NULL)
  {
    $message = $message."\nMa prima, registrati a TeamUp! \n\nClicca qui: http://localhost/TeamUp/index.php?act=".$idAct;
  }

  else
  {
    $message = $message."\nEffettua il login in TeamUp!\n\nPartecipa subito cliccando qui: http://localhost/TeamUp/index.php?act=".$idAct;
  }

  $from = "From: TeamUp";

  mail($targetEmail, $object, $message, $from);

  $response = new Response (true, "http://localhost/TeamUp/homepage.php");
  echo json_encode($response);
?>
