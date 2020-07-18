<?php

require_once 'user.php';
require_once 'category.php';

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$c_password = $_POST['conferma_password'];
$idAct = $_POST['act'];

$posizione = NULL;

for ($i = 0; $i < count($_POST["locationid"]); $i++)
{
  if (isset($_POST["locationid"]))
  {
    $posizione = $_POST["locationid"][$i];
  }
}

if($c_password == $password){

  $utente = User::new($email, $username, $password, $posizione);

  $idUtente = User::writeOnDB($utente);
  if($idUtente <= 0 || $idUtente == null){
    header("Location: ./index.php?error=error_reg");
  }else{

    $categorieSelezionate = array();
    $j = 0;
    foreach ($_POST["categories"] as $cb)
    {
      $categorieSelezionate[$j] = filter_var($cb,FILTER_SANITIZE_NUMBER_INT);
      $j++;
    }

    User::insertUserInterests($idUtente,$categorieSelezionate);

    $token = $utente->getToken();

    $object = "Conferma email";
    $message = "Premi sul link per confermare l'email: http://localhost/TeamUp/index.php?token=".$token."&email=".$email;

    if ($idAct != NULL)
    {
      $message = $message."&act=".$idAct;
    }

    $from = "From: TeamUp";

    mail($email,$object,$message,$from);
    header("Location: ./index.php");
  }
}else{
  header("Location: ./index.php?error=pswnotequal");
}

?>
