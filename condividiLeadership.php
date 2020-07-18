<?php

  require_once 'activity.php';
  Activity::upgradeOnLeader(filter_var($_GET['user'],FILTER_SANITIZE_NUMBER_INT),filter_var($_GET['act'],FILTER_SANITIZE_NUMBER_INT));
  header ("Location: activity_page.php?id=".filter_var($_GET['act'],FILTER_SANITIZE_NUMBER_INT));

 ?>
