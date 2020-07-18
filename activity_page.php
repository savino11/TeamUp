<?php

require 'common/template.php';

$loc = "Location: homepage.php";

if(!isset($_GET['id'])) {

	header($loc);
}

$id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);
$record = Activity::readFromDB($id);

$activity = NULL;

if($record != NULL) {

	$partecipanti = Activity::readFromPartecipanti($id);

	$loggedAsTeammate = false;
	$i = 0;

	while(!$loggedAsTeammate && $i < count($partecipanti)) {

		$loggedAsTeammate = $partecipanti[$i]['utente'] == $_SESSION['id'];
		$i++;
	}

	if($loggedAsTeammate) {

		$activity = Activity::newFromRecord($record);
	}
	else {

		header($loc);
	}
}
else {

	header($loc);
}


show($activity->getTitolo(), "activity_page_content.php", $activity);

?>
