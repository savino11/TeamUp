<?php

require_once 'category.php';
require_once 'dbconnection.php';
require_once 'user.php';

session_start();

$email = $_POST['email'];
$password = $_POST['password'];
$idAct = filter_var($_POST['act'],FILTER_SANITIZE_NUMBER_INT);

$utente = User::newFromRecord(User::readUserByEmail($email));

if($utente->getEmail() == $email){
  if($utente->getToken() == "0"){
    if($utente->getPassword() == $password){
      $_SESSION['id'] = $utente->getId();

      if ($idAct != NULL && strlen($idAct) > 0)
      {
        header("Location: http://localhost/TeamUp/invite_page.php?act=".$idAct);
      }
      else
      {
        header("Location: ./homepage.php");
      }

    }else{
      header("Location: ./index.php?error_log=0");
    }
  }else{
    header("Location: ./index.php?error_log=2");
  }
}else{
  session_unset();
  header("Location: ./index.php?error_log=1");
}

?>
