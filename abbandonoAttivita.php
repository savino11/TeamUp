<?php

require_once 'activity.php';
require_once 'serverResponse.php';
session_start();

if(!isset($_SESSION['id']))
{
  header("Location: ./");
}

$partecipanti = Activity::readFromPartecipanti(filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT));
$numPartecipanti = count($partecipanti);

  for ($i=0; $i < $numPartecipanti  ; $i++)
  {
    foreach ($partecipanti[$i] as $item[$i] => $value[$i])
    {
      $abbandona = Activity::deleteFromPartecipanti($_SESSION['id'], filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT));
      header("Location: homepage.php?alert=1");
    }
  }

 ?>
