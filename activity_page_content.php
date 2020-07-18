<?php

$activity = $object;
$jsonActivity = json_encode($activity, JSON_HEX_APOS);

//carica tutte le categorie del DB
$categories = Category::readAllFromDB();
$jsonCategories = json_encode($categories, JSON_HEX_APOS);

?>

<?php

if(isset($_GET['error'])){
	if($_GET['error'] == "no_left_positions"){
		?>

		<script type="text/javascript">
			alert("Posti disponibili terminati!");
		</script>

		<?php
	}
}

?>

<div class="container the-post rounded" id="activity">

	<!-- Questo div viene riempito dallo script più in fondo -->

	<!-- elimina attivita -->
	<div class="modal fade" id="delete-activity" data-backdrop="static" tab-index="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header mt-0 mb-3" style="background: #109485;">
					<h5 class="modal-title"> Messaggio di conferma </h5>
				</div>
				<div class="modal-body px-5">
					<p class="my-3"> Sei sicuro di voler eliminare la tua Attività? </p>
					<hr class="mt-4">
					<div class="form-inline mb-3">
						<input type="submit" class="btn btn-dark mr-3" value="Elimina" onclick="deleteActivity(<?php echo filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT)?>)">
						<input type="button" class="btn btn-danger" data-dismiss="modal" value="Annulla">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- elimina attivita -->

	<!-- chiudi attivita -->
		<div class="modal fade" id="close-activity" data-backdrop="static" tab-index="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header mt-0 mb-3" style="background: #109485;">
						<h5 class="modal-title"> Messaggio di conferma </h5>
					</div>
					<div class="modal-body px-5">
						<p class="my-3"> Sei sicuro di voler chiudere la tua Attività? </p>
						<hr class="mt-4">
						<div class="form-inline mb-3">

							<form action="close_activity.php" method="post">

								<input type="text" name="activityId" value="<?php echo $activity->getId() ?>" hidden>
								<input type="submit" value="Conferma" class="btn btn-dark mr-3">

							</form>

							<input type="button" class="btn btn-danger" data-dismiss="modal" value="Annulla">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- chiudi attivita -->
		
			<!-- Rimuovi teammate -->
		<div class="modal fade" id="delete-teammate" data-backdrop="static" tab-index="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header mt-0 mb-3" style="background: #109485;">
						<h5 class="modal-title"> Messaggio di conferma </h5>
					</div>
					<div class="modal-body px-5">
						<p class="my-3"> Sei sicuro di voler rimuovere questo teammate? </p>
						<hr class="mt-4">
						<div class="form-inline mb-3">

							<form action="delete_teammate.php" method="get">

								<input type="text" id="actDelete" name="act" value="" hidden>
								<input type="text" id="userDelete" name="user" value="" hidden>
								<input type="submit" value="Conferma" class="btn btn-dark mr-3">

							</form>

							<input type="button" class="btn btn-danger" data-dismiss="modal" value="Annulla">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Rimuovi Teammate -->
		
		
			<!-- Promuovi a Leader -->
		<div class="modal fade" id="upgrade-teammate" data-backdrop="static" tab-index="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header mt-0 mb-3" style="background: #109485;">
						<h5 class="modal-title"> Messaggio di conferma </h5>
					</div>
					<div class="modal-body px-5">
						<p class="my-3"> Sei sicuro di voler promuovere questo teammate? </p>
						<hr class="mt-4">
						<div class="form-inline mb-3">

							<form action="condividiLeadership.php" method="get">

								<input type="text" id="actUpgrade" name="act" value="" hidden>
								<input type="text" id="userUpgrade" name="user" value="" hidden>
								<input type="submit" value="Conferma" class="btn btn-dark mr-3">

							</form>

							<input type="button" class="btn btn-danger" data-dismiss="modal" value="Annulla">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Promuovi a Leader -->

			<!-- richieste attivita -->
	<div class="modal fade" id="requests" tab-index="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header mt-0 mb-3" style="background: #109485;">
					<h5 class="modal-title"> Richieste di partecipazione </h5>
				</div>
				<div class="modal-body px-5">
					<div class="container">

						<?php

						$requests = Activity::getRichiestePendenti($activity->getId());

						if(count($requests)) {

							foreach($requests as $request) {

								$act = $activity->getId();
								$user = $request['id'];

								$accept = "'richiesta_accettata.php?act=$act&user=$user'";
								$reject = "'richiesta_rifiutata.php?act=$act&user=$user'";
								$rejectEvent = "onclick=\"window.location.replace($reject)\"";
								$acceptEvent = "onclick=\"window.location.replace($accept)\"";

								echo '<div class="row">';

								echo "<div class='col-md-8 py-2'>";
								echo "Da <b>" . $request['username'] . "</b>:<br><span class='badge badge-secondary'><i>" . $request['email'] . "</i></span><div style='word-break: break-all;'>" . $request['descrizione'] . "</div></div>";
								echo "<div class='col-md-4 py-2'>";
								echo "<input type='button' class='btn btn-danger btn-block' value='Rifiuta' $rejectEvent>";

								echo "<input type='button' class='btn btn-dark mr-3 btn-block' value='Recluta' $acceptEvent>";

								echo '</div>';
								echo '</div>';
								echo '<hr class="mt-4">';
							}
						}
						else {

							echo "Al momento non ci sono richieste di partecipazione.";
						}

						?>

					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- richieste attivita -->

	<!-- post modal = a quella dell'homepage -->
	<div class="modal fade" id="activity-parameters" data-backdrop="static" tab-index="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content" id="modal-content">
				<div class="modal-header mt-0 mb-3" style="background: #109485;">
					<h5 class="modal-title"> Manca davvero poco... </h5>
				</div>
				<div class="modal-body px-5">
					<form id="postFormModal">
						<div class="form-group">
							<label> Titolo per la tua Attività </label>
							<input type="text" class="form-control" id="title" name="title" required>
						</div>
						<div class="form-group mt-4">
							<label> La tua descrizione </label>
							<textarea class="form-control overflow-auto" id="description-e" name="description-e" rows="3"></textarea>
						</div>
						<div class="form-group mt-4">
							<div class="dropright">
								<button class="dropdown-toggle form-control btn btn-light" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Scegli una o più categorie </button>
								<div class="dropdown-menu mymenu form-control py-3 px-5" id="dropdown-menu" aria-labelledby="dropdownMenuButton">
								</div>
							</div>
						</div>
						<div class="form-group mt-4">
							<label> Con quante persone vorresti lavorare? </label>
							<input type="number" min="0" class="form-control" placeholder="Inserisci un numero" id="left" name="left" required>
						</div>
						<div class="form-group mt-4">
							<label> La tua vecchia posizione: </label>
							<input type="text" class="form-control" id="previuos-position" readonly>
							<label class="mt-4"> Se desideri cambiarla, premi il bottone qui sotto e completa il form: </label>
							<div class="dropright">
								<button class="dropdown-toggle form-control btn btn-light" type="button" id="dropdownPosition" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Posizione geografica </button>
								<div class="dropdown-menu mymenu form-control" id="panel" aria-labelledby="dropdownPosition">
									<div class="form-inline mx-4">
										<input type="text" class="form-control mr-2" placeholder="Inserisci una città" id="auto-complete-modal" name="position">
										<input type="button" id="cerca-btn" class="btn btn-dark form-control mr-2" value=" Cerca " onclick="getPlaceByName('auto-complete-modal','suggestions-modal')">
										<input type="button" id="annulla-btn"class="form-control btn btn-info" value="Annulla" onclick="erase('auto-complete-modal','suggestions-modal')">
									</div>
									<div id="suggestions-modal">
									</div>
								</div>
							</div>
						</div>
						<div class="form-inline mt-5">
							<input type="button" id="post-btn" name="post-btn" class="form-control btn btn-dark mr-3" value="Pubblica!" onclick="postActivity(<?php echo filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT)?>)">
							<input type="button" class="form-control btn btn-danger mr-3" data-dismiss="modal" value="Annulla">
						</div>
					</form>
					<div class="container mt-4 mb-2" id="contAlert">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end post modal -->


	<!-- invite modal -->
	<div class="modal fade" id="invite" data-backdrop="static" tab-index="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content" id="modal-content">
				<div class="modal-header mt-0 mb-3" style="background: #109485;">
					<h5 class="modal-title"> Conosci qualcuno che potrebbe unirsi? </h5>
				</div>
				<div class="modal-body px-5">
					<form id="invite-person">
						<div class="form-group">
							<label> Inserisci la sua mail, al resto ci pensa <strong>TeamUp!</strong> </label>
							<input class="form-control" id="invite-email" type="email" placeholder="Inserisci l'email">
						</div>
						<div class="form-inline">
							<input type="button" class="form-control btn btn-dark mr-3" value="Invita!" onclick='sendInvite(<?php echo $jsonActivity ?>)'>
							<input type="button" class="form-control btn btn-danger mr-3" data-dismiss="modal" value="Annulla" onclick="erase('invite-email', 'null')">
						</div>
						<div class="container mt-3" id="invite-alert">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- end invite modal -->

	<!-- abbandono attività da teammate-->
	<div class="clearfix">
		<div class="modal fade" id="Abbandona-teammate" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Sei sicuro di voler abbandonare l'attività?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" >
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
					<form action="abbandonoAttivita.php?id=<?php echo filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT) ?>" method="post">
						<button type="submit" class="btn btn-primary">Si</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
	<!-- end abbandono attività da teammate-->

	<!-- abbandona Leadership -->
	<div class="clearfix">
	  <div class="modal fade" id="Abbandona-leadership" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	      <div class="modal-content">
	        <div class="modal-header">
	          <h5 class="modal-title">Sei sicuro di voler abbandonare la Leadership?</h5>
	          <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
	            <span aria-hidden="true">&times;</span>
	          </button>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
						<form action="abbandonoLeadership.php?id=<?php echo filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT) ?>" method="post">
							<button type="submit" class="btn btn-primary">Si</button>
						</form>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- end abbandona Leadership -->

</div>


<script src="hereapi.js"></script>
<script src="post.js"></script>
<script src="activity.js"></script>

<!-- Questo script riempie il div con id "activity" con le informazioni relative all'attività richiesta -->
<script>

	function addPlaceToPagePost(response, flag, activity)
	{
	  var label = 'None';

	  if (flag)
	  {
		var found = new Place (response["Response"]["View"][0]["Result"][0]["Location"]["Address"]["City"],
												   response["Response"]["View"][0]["Result"][0]["Location"]["Address"]["County"],
												   response["Response"]["View"][0]["Result"][0]["Location"]["Address"]["PostalCode"],
												   response["Response"]["View"][0]["Result"][0]["Location"]["Address"]["State"],
												   response["Response"]["View"][0]["Result"][0]["Location"]["Address"]["Country"],
												   response["Response"]["View"][0]["Result"][0]["Location"]["LocationId"]);

		label = found.getLabel();
	  }

	  var obj = document.getElementById("mapicon" + activity["id"]);
	  obj.after(document.createTextNode(" " + label));
	}

	sessionStorage.setItem('id', <?php echo json_encode($_SESSION['id'], JSON_HEX_APOS) ?>);
	var div = document.getElementById("activity");
	var act = new Activity (<?php echo json_encode($activity, JSON_HEX_APOS) ?>);
	var post = new PagePost(act);
	div.appendChild(post["mainContainer"]);
	getPlaceById(act, addPlaceToPagePost);

</script>
<script>
	var btn = document.getElementById("edit-activity");

	if (btn != null)
	{
		btn.onclick = function () {
			var act = new Activity (<?php echo json_encode($activity, JSON_HEX_APOS) ?>);

			//riempio il form come nell'homepage
			completeForm(<?php echo $jsonCategories ?>, "dropdown-menu", act);
		};
	}
</script>

<script type="text/javascript">

	<?php

	if(isset($_GET['alert'])) {

	  $alert = filter_var($_GET['alert'],FILTER_SANITIZE_NUMBER_INT);
	}
	else {
	  $alert = 0;
	}

	?>

	if(<?php echo $alert ?> == 1)
	{
	  alert("Sei L'unico Leader, prima condividi la Leadership");
	}
	if(<?php echo $alert ?> == 2)
	{
	  alert("Ora sei un Teammate");
	}

</script>

<script type="text/javascript">

	function completeModal(idU, utente, idA, attivita) {
		
		var userDelete = document.getElementById(idU);
		var actDelete = document.getElementById(idA);	
		
		userDelete.setAttribute("value", utente);
		actDelete.setAttribute("value", attivita);
	}

</script>
