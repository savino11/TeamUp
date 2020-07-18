<?php

require "activity.php";

if(isset($_POST['activityId'])) {

	$activityId = filter_var($_POST['activityId'],FILTER_SANITIZE_NUMBER_INT);

	Activity::deleteAllRichiestePendenti($activityId);
	Activity::close($activityId);

	header("Location: activity_page.php?id=$activityId");
}
else {

	header("Location: homepage.php");
}

?>
