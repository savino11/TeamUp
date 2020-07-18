<?php
  require_once 'category.php';
  require_once 'activity.php';
  require_once 'user.php';

  session_start();

  if(!isset($_SESSION['id']))
  {
    header("Location: index.php");
  }

  //carica tutte le categorie del DB
  $categories = Category::readAllFromDB();
  $jsonCategories = json_encode($categories, JSON_HEX_APOS);

  $idUser = $_SESSION["id"];

  //carica tutte le attivita del DB DELL'utente
  $tempActivitiesOfUser = Activity::readActivityOfUser($idUser);
  $activitiesOfUser = NULL;

  for ($i=0; $i<count($tempActivitiesOfUser); $i++)
  {
    $activitiesOfUser[] = Activity::newFromRecord($tempActivitiesOfUser[$i]);
  }

  $jsonActivitiesOfUser = json_encode($activitiesOfUser, JSON_HEX_APOS);

  //carica tutte le attivita del DB PER l'utente
  //ho bisogno di creare un oggetto User prima
  $userRecord = User::readUserById($idUser);

  $userObj = User::newFromRecord($userRecord);

  $tempActivitiesForUser = Activity::readActivityForUser($userObj, "load");
  $activitiesForUser = NULL;

  for ($i=0; $i<count($tempActivitiesForUser); $i++)
  {
    $activitiesForUser[] = Activity::newFromRecord($tempActivitiesForUser[$i]);
  }

  $jsonActivitiesForUser = json_encode($activitiesForUser, JSON_HEX_APOS);

  //carica le attivita dove l'utente ha inviato una richiesta che è in stato pendente
  $pendingRequestActivities = Activity::activitiesRequestedByUser($idUser);
  $jsonPendingRequestActivities = json_encode($pendingRequestActivities, JSON_HEX_APOS);

 ?>
<!doctype html>
<html lang="it">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="shortcut icon" href="./icona.ico">
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css?dp-version=1578490236" />
    <script type="text/javascript" src="./hereapi.js"></script>
    <script type="text/javascript" src="./post.js"></script>
    <script type="text/javascript" src="./helper.js"></script>
    <script type="text/javascript" src="./activity.js"></script>
    <title> Homepage </title>
  </head>
  <body>
    <div class="container-fluid" id="whole-page">
      <?php
        require_once 'common/navbar.php';
       ?>

      


      <div class="row" id="body-container">

        <!-- sidebar -->
        <div class="col-md-3 py-5" id="sidebar">
          <h3>Le tue Attività</h3><br>
          <h4>Leader</h4>
          <hr>
          <ul id="leaderActs">
          </ul><br><br>
          <h4>Teammate</h4>
          <hr>
          <ul id="teammateActs">
          </ul>
        </div>
        <!-- sidebar -->

        <!-- postare attivita -->
        <div class="col-md-9 py-5" id="homepage">

          <!-- postare attivita -->
          <div class="row" id="post-activity">
            <div class="col-md-10 offset-md-1">
              <div class="form-group">
                <h4> Posta la tua Attività </h4>
                <br>
                <textarea class="form-control" id="description-b" maxlength="500" placeholder="Inserisci la tua descrizione..." rows="5" onkeyup="countchars('description-b','current')" aria-describedby="counter" style="border: 1px solid #0b7569;"></textarea>
                <input type="button" id="btn-invia" class="btn btn-dark mt-2 pull-right" data-toggle="modal" onclick='redirectToModal(textAreaIsOk("description-b",30),"activity-parameters","modal-error"); completeForm(<?php echo $jsonCategories ?>, "dropdown-menu", "null")' value="   Invia   ">
                <small id="counter" class="form-text text-muted">
                  <span id="current"> 0 </span>
                  <span id="max"> / 500 </span>
                </small>
              </div>

              <!-- error modal -->
              <div class="modal fade" id="modal-error" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header mb-3 mt-0" style="background: #109485;">
                      <h5 class="modal-title"> <i class="fa fa-exclamation-triangle mr-3" style="color:#fcca03;" aria-hidden="true"></i> Errore </h5>
                    </div>
                    <div class="modal-body px-5">
                      <p> Inserisci una descrizione di minimo 30 caratteri e massimo 500! </p>
                      <hr class="my-4">
                      <input type="button" class="btn btn-dark pull-left mb-3" data-dismiss="modal" value="  Capito  ">
                    </div>
                  </div>
                </div>
              </div>
              <!-- end error modal -->

              <!-- post modal -->
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
                          <textarea class="form-control overflow-auto" id="description-e" name="description-e" rows="3" readonly></textarea>
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
                          <input type="number" min="1" class="form-control" placeholder="Inserisci un numero" id="left" name="left" required>
                        </div>
                        <div class="form-group mt-4">
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
                          <input type="button" id="post-btn" name="post-btn" class="form-control btn btn-dark mr-3" value="Pubblica!" onclick="postActivity(0)">
                          <input type="button" class="form-control btn btn-danger mr-3" data-dismiss="modal" value="Annulla" onclick="clearForm()">
                        </div>
                      </form>
                      <div class="container mt-4 mb-2" id="contAlert">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- end post modal -->

            </div>
          </div>
          <!-- postare attivita -->

          <hr class="my-4">

          <!-- tutte le attivita per l'utente -->
          <div class="row" id="feed">
            <div class="col-md-10 offset-md-1">
              <div class="row mb-4" id="header-feed">
                <div class="col">
                  <h4> Tutte le attività, fatte per te </h4>
                </div>
              </div>
              <div class="row" id="posts-feed">
                <div class="col" id="all-posts">

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

                </div>

              </div>
            </div>
          </div>
          <!-- tutte le attivita per l'utente -->

        </div>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script>
      $(document).ready(function() {
        $('body').tooltip({ selector: '[data-toggle=tooltip]' });
        $('.alert').alert();

        loadActivitiesForUser(<?php echo $jsonActivitiesForUser ?>, <?php echo $jsonPendingRequestActivities ?>, "all-posts");
        loadActivitiesOfUser(<?php echo $jsonActivitiesOfUser?>, <?php echo $idUser ?>);
      });
    </script>
    <!-- HERE SCRIPT -->
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"> </script>
  </body>
</html>
