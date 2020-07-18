<?php

require_once 'activity.php';
require_once 'user.php';

session_start();

if(isset($_SESSION['id'])) {

	$activitiesId = Activity::getAttivitaIdsWhereOnlyLeaderIs($_SESSION['id']);

	foreach($activitiesId as $activityId) {

		Activity::deleteFromDB($activityId);
	}

	User::deleteFromDB($_SESSION['id']);

	header("Location: logout.php");
}
else {

	header("Location: index.php");
}

?>
