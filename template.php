<?php

require_once'activity.php';

session_start();

if(!isset($_SESSION['id'])) {

	header("Location: index.php");
}

function show($title, $page, $object) {
	
	echo '<!doctype html>';
	
	echo '<html>';
	
	require 'common/head.php'; 
	
	echo '<body>';
	
	echo '<div class="container-fluid" id="whole-page">';

	require 'common/navbar.php';
	
	echo '<div class="row" id="body-container">';
	
	require 'common/sidebar.php';
	
	echo '<div class="col-md-9 py-5" id="homepage">';
	
	include $page;
	
	echo '</div>';
	
	echo '</div>';

	echo '</div>';
	
	require 'common/scripts.php';
	
	echo '</body>';
	
	echo '</html>';
}

?>