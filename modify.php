<?php

require_once 'user.php';
require_once 'category.php';

session_start();

$modify = "Location: modifica.php?alert=1";
$campoEsistente = "Location: modifica.php?alert=3";

if(!isset($_SESSION['id']))
{
  header("Location: ./");
}


if(isset($_POST['username']))
{
  $username = $_POST['username'];
  $id = $_SESSION['id'];
  $utente = User::newFromRecord(User::readUserById($id));
  if($username != $utente->getUsername())
  {
    $utente->setUsername($username);
    $modificaUsername = User::updateOnDB($utente);
    if(!$modificaUsername)
    {
      header($campoEsistente);
    }
    else
    {
        header($modify);
    }
  }
}


if(isset($_POST['email']))
{
  $email = $_POST['email'];
  $id = $_SESSION['id'];
  $utente = User::newFromRecord(User::readUserById($id));
  if($email != $utente->getEmail())
  {
    $utente->setEmail($email);
    $modificaUsername = User::updateOnDB($utente);
    if(!$modificaUsername)
    {
      header($campoEsistente);
    }
    header($modify);
  }
}




if(isset($_POST['password']) && isset($_POST['conferma_password']))
{
  $password = $_POST['password'];
  $c_password = $_POST['conferma_password'];
  if ($c_password == $password)
  {
    $id = $_SESSION['id'];
    $utente = User::newFromRecord(User::readUserById($id));
    if($password != $utente->getPassword())
    {
      $utente->setPassword($password);
      User::updateOnDB($utente);
        header($modify);
    }
  }
  else
  {
      header("Location: modifica.php?alert=2");
  }
}



if (isset($_POST["locationid"]))
{
  for ($i = 0; $i < count($_POST["locationid"]); $i++)
  {
    $posizione = $_POST["locationid"][$i];
    $id = $_SESSION['id'];
    $utente = User::newFromRecord(User::readUserById($id));
    if($posizione != $utente->getPosizione())
    {
      $utente->setPosizione($posizione);
      User::updateOnDB($utente);
      header($modify);

    }
  }
}


if (isset($_POST["categories"]))
{
  $categorieSelezionate = array();
  $j = 0;
  foreach (filter_var($_POST["categories"],FILTER_SANITIZE_NUMBER_INT) as $cb)
  {
    $categorieSelezionate[$j] = $cb;
    $j++;
  }
  User::deleteInterests($_SESSION['id']);
  User::insertUserInterests($_SESSION['id'],$categorieSelezionate);
  header($modify);
}

?>
