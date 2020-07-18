
<?php

require_once 'activity.php';
session_start();
if(!isset($_SESSION['id']))
{
  header("Location: ./");
}

$id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);

$partecipanti = Activity::readFromPartecipanti($id);
$numPartecipanti = count($partecipanti);
$numeroLeader = 0;

  for ($i=0; $i < $numPartecipanti  ; $i++)
  {
    foreach ($partecipanti[$i] as $item[$i] => $value[$i])
    {
      if($item[$i] == "is_leader" && $value[$i] == "1")
      {
        $numeroLeader++;
        if ($numeroLeader > 1)
        {
          Activity::downgrade($_SESSION['id']);
          header("Location: activity_page.php?id=" . $id . "&alert=2");
        }
        else
        {
          header("Location: activity_page.php?id=" . $id . "&alert=1");

        }
      }
    }
  }


 ?>
