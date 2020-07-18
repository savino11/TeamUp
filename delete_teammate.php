<?php

require_once 'dbconnection.php';
require_once 'activity.php';

session_start();

if(isset($_SESSION['id']) && isset($_GET['act']) && isset($_GET['user'])){

  $idLeader = $_SESSION['id'];
  $idAct = filter_var($_GET['act'],FILTER_SANITIZE_NUMBER_INT);
  $idUser = filter_var($_GET['user'],FILTER_SANITIZE_NUMBER_INT);

  $resultL = Activity::getAttivitaIdsWhereLeaderIs($idLeader);
  $flagL = false;

  $i = 0;
  while($i < count($resultL) && !$flagL) {

    if($resultL[$i] == $idAct){

      $flagL = true;

      $resultU = Activity::readFromPartecipanti($idAct);
      $j = 0;
      $flagU = false;

      while($j < count($resultU) && !$flagU){

        if($resultU[$j]['utente'] == $idUser){

          $flagU = true;

          Activity::deleteFromPartecipanti($idUser, $idAct);

          $attivita = Activity::newFromRecord(Activity::readFromDB($idAct));
          $attivita->setPostiRimanenti($attivita->getPostiRimanenti()+1);
          Activity::updateOnDB($attivita);

          $user = User::newFromRecord(User::readUserById($idUser));

          $object = "Rimozione partecipante";
          $message = "Sei stato rimosso dall'attività '".$attivita->getTitolo()."'";
          $from = "From: TeamUp";
          mail($user->getEmail(),$object,$message,$from);

        }
        $j++;

      }

      if($flagU){
        header("Location: activity_page.php?id=$idAct");
      }else{
        header("Location: homepage.php?error=activitynotfound");
      }

    }
    $i++;

  }
  if(!$flagL){
    header("Location: activity_page.php?id=$idAct&error=notleader");
  }

}else{
  header("Location: homepage.php");
}


?>
