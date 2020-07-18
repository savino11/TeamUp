<?php

//carica le attivita con richiesta pendente
$pendingRequestActivities = Activity::activitiesRequestedByUser($_SESSION['id']);
$jsonPendingRequestActivities = json_encode($pendingRequestActivities, JSON_HEX_APOS);

$results = Activity::readSearchedActivities($_SESSION['id'], $object);

$activities = NULL;

for ($i=0; $i<count($results); $i++)
{
	$activities[] = Activity::newFromRecord($results[$i]);
}

$jsonActivities = json_encode($activities, JSON_HEX_APOS);

$n = count($results);

echo "<center><b>Numero di attività trovate:</b> $n</center><br>";

if($n == 0) {

	echo "<center><img src='notfound.png'><br>Prova a impostare diversamente i parametri di ricerca.</center>";
}

?>

<div id="all-posts">



</div>

<!-- request modal -->
<div class="modal fade" id="modal-request" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header mb-3 mt-0" style="background: #109485;">
				<h5 class="modal-title"> Prima di inviare, inserisci una breve descrizione </h5>
			</div>
			<div class="modal-body px-5">
				<div class="form-group">
					<textarea id="descr-request" name="descr-request" class="form-control" maxlength="500" placeholder="Inserisci la tua descrizione..." rows="5" onkeyup="countchars('descr-request','current-request')" style="border: 1px solid #109485;"></textarea>
					<small id="counter-request" class="form-text text-muted">
						<span id="current-request"> 0 </span>
						<span id="max-request"> / 500 </span>
					</small>
					<div class="form-inline mt-4">
						<input type="submit" class="btn btn-dark mr-3" value="Invia" onclick="sendRequest('modal-request')">
						<input type="button" class="btn btn-danger" data-dismiss="modal" value=" Esci ">
					</div>
				</div>
				<div class="container mt-4 mb-2" id="alertSection">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- request modal -->



<!-- cancel request -->
<div class="modal fade" id="modal-cancel-request" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header mb-3 mt-0" style="background: #109485;">
				<h5 class="modal-title"> Messaggio di conferma </h5>
			</div>
			<div class="modal-body px-5">
				<div class="form-group">
					<p class="my-3"> Sei sicuro di voler annullare la richiesta? </p>
					<hr class="mt-4">
					<div class="form-inline mb-3">
						<input type="submit" class="btn btn-dark mr-3" value="Annulla richiesta" onclick="cancelRequest('modal-cancel-request')">
						<input type="button" class="btn btn-danger" data-dismiss="modal" value=" Esci ">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- cancel request -->

<script type="text/javascript" src="./post.js"></script>
<script src="hereapi.js"></script>
<script type="text/javascript" src="./activity.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>

<script>
$(document).ready(function() {
	$('body').tooltip({ selector: '[data-toggle=tooltip]' });
	$('.alert').alert();

	loadActivitiesForUser(<?php echo $jsonActivities ?>, <?php echo $jsonPendingRequestActivities ?>, "all-posts");
});
</script>
