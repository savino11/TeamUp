<?php
require_once 'user.php';
session_start();
if(isset($_GET['email'])){
  $email = $_GET['email'];
  $user = User::readUserByEmail($email);
  echo $user['email'];
  if(isset($user['email'])){
    $object = "Recupero password";
    $message = "La tua password è: \"".$user['password']."\"";
    $from = "From: TeamUp";

    mail($user['email'],$object,$message,$from);
    header("Location: ./index.php");
  }else{
    header("Location: ./index.php?error=user_not_found");
  }
}else{
  $username = $_GET['username'];
  $user = User::readUserByUsername($username);
  if(isset($user['username'])){
    $object = "Recupero password";
    $message = "La tua password è: \"".$user['password']."\"";
    $from = "From: TeamUp";

    mail($user['email'],$object,$message,$from);
    header("Location: ./index.php");
  }else{
    header("Location: ./index.php?error=user_not_found");
  }
}
?>
