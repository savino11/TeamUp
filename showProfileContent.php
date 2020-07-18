
<?php

if(!isset($_SESSION['id']))
{
  header("Location: ./");
}
$datiUtente = User::readUserById($_SESSION['id']);
$attivitàUtente = Activity::readActivityOfUser($_SESSION['id']);
$attivitàModal = Activity::readActivityOfUser($_SESSION['id']);

$activitiesOfUser = Activity::readActivityOfUser($_SESSION["id"]);
$jsonActivitiesOfUser = json_encode($activitiesOfUser, JSON_HEX_APOS);


$attività = Activity::readActivityOfUser($_SESSION['id']);
$numAttività = count($attività);

$activityPending = Activity::readActivityNameFromRichiestePendenti($_SESSION['id']);
$numAttivitàPendenti = count($activityPending);

$userInterests = User::getUserInterests($_SESSION['id']);
$numInteressi = count($userInterests);


?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title></title>
  <script src="activity.js"></script>
  <script src="hereapi.js"></script>
</head>
<body>
  <div class="container">
    <div class="container">
      <em> <h6>  <h4> <strong>I TUOI DATI</strong> </h4> <p></p><em>
        <?php
        foreach ($datiUtente as $item => $value)
        {
          if ($item != "posizione")
          {
            if ( $value != "" && $item != "id" && $item != "password" && $item != "token")
            {
              echo $item ."  :    ". $value .'<br>';
            }
            else if ( $value == "")
            {
              echo $item ."  :    ". "NON IMPOSTATO" .'<br>';
            }
          }

          else
          {
            echo "<div id='posizione-profilo'></div>";
            $position = $value;
          }
        }

        ?>
        <p></p>
        <em> <h6>  <h4> <strong>I TUOI INTERESSI</strong> </h4> <p></p><em>
          <div class="container">
            <div class="row ">
              <ul>
                <?php

                for ($i=0; $i < $numInteressi  ; $i++)
                {
                  foreach ($userInterests[$i] as $item  => $value)
                  {
                    if ($item == "nome")
                    {
                      $interesse = $value;
                      ?>
                      <div class="row">
                        <li><?php echo $interesse;  ?></li>
                      </div>

                      <?php
                    }
                  }
                }

                ?>
              </ul>
            </div>
          </div>



        </h6></em>
      </div>

      <hr>

      <p></p>


      <div class="container">
        <h6> <em>  <h4> <strong> LE TUE ATTIVITÀ</strong> </h4> <em>

          <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#myModal"><svg class="bi bi-info-circle" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588z"/>
            <circle cx="8" cy="4.5" r="1"/>
          </svg> Maggiori informazioni sulle tue attività</button>

          <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#pending"><svg class="bi bi-question-circle" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M5.25 6.033h1.32c0-.781.458-1.384 1.36-1.384.685 0 1.313.343 1.313 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.007.463h1.307v-.355c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.326 0-2.786.647-2.754 2.533zm1.562 5.516c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
          </svg>  Le tue richieste in attesa di conferma</button></li>  <p>  </p>
          <?php
          for ($i=0; $i < $numAttività  ; $i++)
          {
            foreach ($attivitàUtente[$i] as $item => $value)
            {

              if ($item != "data_creazione"  && $item != "id" && $item != "posizione" && $item != "posti_rimanenti" && $item != "completata" && $item != "is_leader" && $item != "titolo")
              {
                echo $item ."  :  ". $value .'<br>';
              }
              else if ($item == "data_creazione")
              {
                echo "data creazione" ."  :  ". date("d/m/Y",strtotime($value)); '<br> <br>';
              }
              else if ($item=="id")
              {
                $id = $value;
              }
              else if ($item == "titolo")
              {
                $titoloItem = $item;
                $titolo = $value;

                ?> <a href="activity_page.php?id=<?php echo $id; ?>"> <?php echo $titoloItem ."  :  ". $titolo .'<br>'; ?></a> <?php

              }

            }
            ?>
            <div class="container"></div>
            <?php
          }
          ?>

        </div>

      </h6>

      <hr>

      <!-- Trigger the modal with a button -->


      <!-- Modal -->
      <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Informazioni attività</h4>
            </div>
            <div class="modal-body">
              <p>
                <?php
                for ($i=0; $i < $numAttività  ; $i++)
                {
                  foreach ($attivitàModal[$i] as $item => $value)
                  {
                    if ( ($value != "") && ($item != "posti_rimanenti") && ($value != "0") && ($value != "1") && ($item != "is_leader") && ($item != "id") && ($item != "descrizione") && ($item != "data_creazione") && ($item != "posizione"))
                    {
                      echo $item ."  :  ". $value .'<br>';
                    }

                    else if ( $value == "")
                    {
                      echo $item ."  :  ". "NON IMPOSTATO" .'<br>';
                    }
                    else if ( $item == "posti_rimanenti")
                    {
                      echo "posti rimanenti" ."  :  ". $value .'<br>';
                    }
                    else if ( $item == "completata" && $value == "0")
                    {
                      echo "completata" ."  :  ". "NO" .'<br>';
                    }
                    else if ( $item == "completata" && $value == "1")
                    {
                      echo "completata" ."  :  ". "SI" .'<br>';
                    }
                    else if ($item == "is_leader" && $value == "1")
                    {
                      echo "Leader dell'attività" ."  :  ". "SI" .'<br>';

                    }
                    else if ($item == "is_leader" && $value == "0")
                    {
                      echo "Leader dell'attività" ."  :  ". "NO" .'<br>';

                    }

                  }
                  ?>
                  <hr>
                  <?php

                  echo "<br>";
                }
                ?>
              </p>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
            </div>
          </div>
        </div>
      </div>


      <div id="pending" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"> I titoli delle attività a cui hai mandato una richiesta di partecipazione sono:</h4>
            </div>
            <div class="modal-body">
              <p>
                <?php
                for ($i=0; $i < $numAttivitàPendenti  ; $i++)
                {
                  foreach ($activityPending[$i] as $item => $value)
                  {
                    echo $value .'<br>';
                  }
                  ?>

                  <hr>

                  <?php
                  echo "<br>";
                }
                ?>
              </p>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
            </div>
          </div>
        </div>
      </div>

      <script>
      var bd = document.getElementsByTagName('body')[0];
      bd.onload = function ()
      {
        var fakeActivity = new Activity();
        fakeActivity["locationid"] = "<?php echo $position ?>";
        getPlaceById(fakeActivity, addLabelToProfile);
      }

      </script>

    </body>
    </html>
