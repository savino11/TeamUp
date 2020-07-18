<?php
  require_once 'activity.php';
  require_once 'serverResponse.php';

  session_start();

  //recupero la richiesta inviata dal client in formato json e la memorizzo in una variabile
  $string = file_get_contents('php://input');
  $act = json_decode($string, true);

  //elimino l'attività dopo aver recuperato l'id
  $idAct = $act["id"];
  $result = Activity::deleteFromDB($idAct);

  $response = new Response($result, "");

  if ($result)
  {
    $response->setRedirectLink("http://localhost/TeamUp/homepage.php");
  }

  else
  {
    $response->setRedirectLink("http://localhost/TeamUp/activity_page.php?id=$activityId");
  }

  echo json_encode($response);

 ?>
