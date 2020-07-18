<?php
  require 'activity.php';
  require_once 'serverResponse.php';

  session_start();

  //recupero la richiesta inviata dal client in formato json e la memorizzo in una variabile
  $string = file_get_contents('php://input');
  $act = json_decode($string, true);

  //recupero l'id dell'utente loggato e creo un oggetto Activity popolandolo con i dati ricevuti dal client
  $idUser = $_SESSION["id"];
  $activity = Activity::new($act["activity"]["title"], $act["activity"]["description"], $act["activity"]["locationid"], $act["activity"]["leftPositions"], $idUser);
  $activity->setCategorie($act["activity"]["categories"]);

  //i flag, settati inizialmente a NULL, necessari per verificare i corretti accessi al database
  $flag1 = NULL;
  $flag2 = NULL;
  $flag3 = NULL;

  $activityId = NULL;

  //gestisco il caso della nuova attività
  if ($act["activity"]["id"] == 0)
  {
    $activityId = Activity::writeOnDB($activity);
    $flag1 = $activityId > 0 ? true : false;

    //scrivi in Partecipanti
    $isLeader = true;
    $flag2 = Activity::writeOnPartecipanti($idUser, $activityId, $isLeader);
  }

  //gestisco il caso di una modifica
  else
  {
    //aggiorno l'attività
    $activityId = $act["activity"]["id"];
    $activity->setId($activityId);
    $resUpdate = Activity::updateOnDB($activity);
    $resDelete = Activity::deleteFromCategoriesActivity($activity->getId());

    $flag1 = ($resUpdate + $resDelete) == 2 ? true : false;
    $flag2 = true;
  }

  //scrivo le categorie
  $sum = 0;
  foreach ((array)$act["activity"]["categories"] as $i)
  {
    $sum = $sum + Activity::writeOnCategoriesActivity($activityId, $i);
  }
  $flag3 = ($sum == count($act["activity"]["categories"])) ? true : false;

  //parte finale, assemblo i flag e creo un oggetto di tipo Response, necessario a comunicare con il client
  $finalFlag = ($flag1 + $flag2 + $flag3) == 3 ? true : false;
  $response = new Response ($finalFlag, "");

  if ($finalFlag)
  {
    $response->setRedirectLink("http://localhost/TeamUp/activity_page.php?id=$activityId");
  }

  else
  {
    $response->setRedirectLink("http://localhost/TeamUp/homepage.php");
  }

  echo json_encode($response);
 ?>
