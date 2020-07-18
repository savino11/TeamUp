<?php

if ($_POST['keyword'] == "" && !isset($_POST['locationid'][0]) && !isset($_POST['categories'])) {
	
	header("Location: homepage.php");
}

$string = "";

if (isset($_POST['keyword'])) {
	
	$keyword = $_POST['keyword'];
	$string .= "attivita.titolo LIKE '%$keyword%'";
}

if (isset($_POST['locationid'][0])) {
	
	$locationid = $_POST['locationid'][0];
	
	if($string != "") {
		
		$string .= " AND ";
	}
	
	$string .= "attivita.posizione = '$locationid'";
}

if (isset($_POST['categories'])) {
	
	if($string != "") {
		
		$string .= " AND ";
	}
	
	$string .= "(";
	
	for($i = 0; $i < count($_POST['categories']); $i++) {
		
		$category = $_POST['categories'][$i];
		
		if($i > 0) {
		
			$string .= " OR ";
		}
		
		$string .= "categorie_attivita.categoria = $category";
	}
	
	$string .= ")";
}

require 'common/template.php';

show("Ricerca", "search_content.php", $string);

?>