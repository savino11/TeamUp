<?php
  session_start();
  require 'activity.php';

  if(!isset($_SESSION['id']))
  {
    header("Location: index.php");
  }

  //recupero l'id dell'utente loggato dalla sessione
  $idUser = $_SESSION["id"];

  //recupero l'id dell'attività dall'url
  $idInviteActivity = NULL;
  if (isset($_GET['act']))
  {
    $idInviteActivity = $_GET['act'];
  }

  //carica tutte le attivita del DB DELL'utente
  $tempActivitiesOfUser = Activity::readActivityOfUser($idUser);
  $activitiesOfUser = NULL;
  for ($i=0; $i<count($tempActivitiesOfUser); $i++)
  {
    $activitiesOfUser[] = Activity::newFromRecord($tempActivitiesOfUser[$i]);
  }

  $jsonActivitiesOfUser = json_encode($activitiesOfUser, JSON_HEX_APOS);


  //creo un oggetto attività partendo dall'id recuperato dall'url
  $actRecord = Activity::readFromDB($idInviteActivity);
  $actObj = NULL;
  $actObj[] = Activity::newFromRecord($actRecord);
  $jsonActivity = json_encode($actObj, JSON_HEX_APOS);

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
    <title> Partecipa all'attività </title>
  </head>
  <body>
    <div class="container-fluid" id="whole-page">

      <!-- navbar -->
      <div class="row">
        <div class="col-md-12" id="navbar">
          <nav class="navbar navbar-expand-md navbar-dark">
            <a class="navbar-brand" href="./homepage.php" style="color: #b5e6da;"> <img src="icona.png" alt="logo" height="70" width=60> </a>
            <button class="navbar-toggler" type="button" style="color: #b5e6da;" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item active mx-5">
                  <a class="nav-link" href="#"> <i class="fa fa-user fa-2x" aria-hidden="true" style="color: #b5e6da;"></i> </a>
                </li>
                <li class="nav-item active mx-5">
                  <a class="nav-link" href="#"> <i class="fa fa-search fa-2x" aria-hidden="true" style="color: #b5e6da;"></i> </a>
                </li>
              </ul>
              <div class="nav-item active dropdown">
                <a class="nav-link dropdown-toggle" id="navbar-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#" style="color: #b5e6da;"> <i class="fa fa-cog fa-2x" aria-hidden="true" style="color: #b5e6da;"></i> </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="#"> <i class="fa fa-pencil" aria-hidden="true"></i> Modifica profilo </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="logout.php"> <i class="fa fa-sign-out" aria-hidden="true"></i> Log-Out </a>
                </div>
              </div>
            </div>
          </nav>
        </div>
      </div>
      <!-- navbar -->

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

        <!-- homepage -->
        <div class="col-md-9 py-5" id="homepage">
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
        <!-- homepage -->

      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script>
      $(document).ready(function() {
        $('body').tooltip({ selector: '[data-toggle=tooltip]' });
        $('.alert').alert();

        loadActivitiesForUser(<?php echo $jsonActivity ?>, <?php echo $jsonPendingRequestActivities ?>, "homepage");
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
