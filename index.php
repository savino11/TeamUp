<?php

require_once 'category.php';
require_once 'user.php';

if(isset($_SESSION['id'])){
  header("Location: homepage.php");
}else{

  if(isset($_GET['token']) && isset($_GET['email'])){

    $email = $_GET['email'];
    $token = $_GET['token'];

    $utente = User::newFromRecord(User::readUserByEmail($email));

    if($token == $utente->getToken()){
      $utente->setToken("0");
      User::updateOnDB($utente);
    }

  }

  if(isset($_GET['error_log'])){
    if($_GET['error_log'] == 0){
      $login = 0;
    }else if($_GET['error_log'] == 1){
      $login = 1;
    }else{
      $login = 2;
    }
  }

  $idAct = NULL;
  if (isset($_GET['act']))
  {
    $idAct = $_GET['act'];
  }

  $categorie = Category::readAllFromDB();
  ?>
  <!DOCTYPE html>
  <html lang="it">
  <head>

    <meta charset="utf-8">
    <title>index</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="shortcut icon" href="./icona.ico">

    <script language="JavaScript" type="text/JavaScript" src="helper.js"></script>
    <script language="JavaScript" type="text/JavaScript" src="hereapi.js"></script>

    <script type="text/javascript">

    function convalidation(){
      if(document.getElementById('email').value.length > 0){
        window.location.href = "./recupero.php?email="+document.getElementById('email').value;
      }else if(document.getElementById('username').value.length > 7){
        window.location.href = "./recupero.php?username="+document.getElementById('username').value;
      }else{
        alert("Inserisci email o username");
      }
    }

    function confCat(){
      //categorie dell'attivita
      var checkboxes = document.getElementsByName("categories[]");
      var categories = [];
      var correct = true;

      for (i = 0; i < checkboxes.length; i++)
      {
        if (checkboxes[i].checked)
        {
          categories.push(checkboxes[i].value);
        }
      }

      if (categories.length == 0)
      {
        alert("Non hai selezionato neanche una Categoria!");
        correct = false;
      }
      return correct;
    }

    </script>

    <style media="screen">
    @media screen and (max-width: 600px) {
      .form-reg{
        width: 100%;
        padding-top: 5%;
      }
      .my-drop{
        padding-left: 70%;
      }
      .ico{
        width: 90px;
        height: 120px;
      }
      .mymenu{
        height: 150px;
        overflow-y: scroll;
      }
    }

    @media screen and (min-width: 601px) {
      .form-reg{
        width: 550px;
        padding-top: 5%;
        padding-left: 5%;
      }
      .my-nav{
        height: 100px;
        padding-right: 5%;
      }
      .ico{
        width: 117px;
        height: 150px;
      }
      .mymenu
      {
        height: 200px;
        width: 450px;
        overflow-y: scroll;
      }
    }
    </style>

  </head>

  <body onload='loadCategories(<?php echo json_encode($categorie, JSON_HEX_APOS) ?>, "categories")' style="background-image: url(./background.jpg); background-size: cover; background-attachment: fixed; background-position: center; overflow: auto;">
    <?php if(isset($login)){ ?>
      <script type="text/javascript">
        if(<?php echo json_encode($login); ?> == 0){
          alert("Password errata!");
        }else if(<?php echo json_encode($login); ?> == 1){
          alert("Email errata!");
        }else if(<?php echo json_encode($login); ?> == 2){
          alert("Conferma l'email!");
        }
      </script>
    <?php } ?>

    <?php if(isset($_GET['error'])){ ?>
      <script type="text/javascript">
        if(<?php echo json_encode($_GET['error']); ?> == "user_not_found"){
          alert("L'utente non esiste!");
        }
      </script>
    <?php } ?>

    <?php if(isset($_GET['error'])){ ?>
      <script type="text/javascript">
        if(<?php echo json_encode($_GET['error']); ?> == "error_reg"){
          alert("I dati inseriti non sono validi!");
        }
      </script>
    <?php } ?>

    <!--Navbar-->
    <div class="container-fluid" id="whole-page">
      <div class="row">
        <div class="col-md-12" id="navbar">
          <nav class="navbar navbar-expand-md navbar-dark my-nav">
            <a class="navbar-brand" href="index.php"> <img class="ico" src="icona.png" alt="icona"><img src="logo.png" alt="logo" width="160px" height="200px"> </a>
            <button style="background-color: #b5e6da; outline: 0;" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>

            <!--Log-in-->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto">
              </ul>
              <div class="nav-item active dropdown">
                <a class="nav-link dropdown-toggle my-drop" id="navbar-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#" style="color: black;"> <i class="fa fa-cog fa-2x" aria-hidden="true" style="color: black; font-size: 25px;"><strong>Log-in</strong></i></a>
                <div style="width:320px;" class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <form style="width:300px;padding-top: 5%;margin: 0 auto;" action="autenticazione.php" method="post">

                      <!--Email-->
                      <div class="form-group input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <em class="fa fa-envelope">
                              <svg class="bi bi-envelope-fill" width="1.2em" height="1.2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M.05 3.555L8 8.414l7.95-4.859A2 2 0 0014 2H2A2 2 0 00.05 3.555zM16 4.697l-5.875 3.59L16 11.743V4.697zm-.168 8.108L9.157 8.879 8 9.586l-1.157-.707-6.675 3.926A2 2 0 002 14h12a2 2 0 001.832-1.195zM0 11.743l5.875-3.456L0 4.697v7.046z"/>
                              </svg>
                            </em>
                          </span>
                        </div>
                        <input name="email" class="form-control" placeholder="Email" type="email" required>
                      </div>

                      <!--Password-->
                      <div class="form-group input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <em class="fa fa-lock">
                              <svg class="bi bi-shield-lock-fill" width="1.2em" height="1.2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5.187 1.025C6.23.749 7.337.5 8 .5c.662 0 1.77.249 2.813.525a61.09 61.09 0 012.772.815c.528.168.926.623 1.003 1.184.573 4.197-.756 7.307-2.367 9.365a11.191 11.191 0 01-2.418 2.3 6.942 6.942 0 01-1.007.586c-.27.124-.558.225-.796.225s-.526-.101-.796-.225a6.908 6.908 0 01-1.007-.586 11.192 11.192 0 01-2.417-2.3C2.167 10.331.839 7.221 1.412 3.024A1.454 1.454 0 012.415 1.84a61.11 61.11 0 012.772-.815zm3.328 6.884a1.5 1.5 0 10-1.06-.011.5.5 0 00-.044.136l-.333 2a.5.5 0 00.493.582h.835a.5.5 0 00.493-.585l-.347-2a.5.5 0 00-.037-.122z" clip-rule="evenodd"/>
                              </svg>
                            </em>
                          </span>
                        </div>
                        <input name="password" class="form-control" placeholder="Password" type="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}">
                      </div>

                      <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block" style="color: blue; background-color: white; border:none;" data-toggle="modal" data-target="#exampleModal">Recupera Password</button>
                      </div>

                      <div class="dropdown-divider"></div>
                      <div class="form-group">
                        <button style="background-color: black; border:none;" type="submit" class="btn btn-primary btn-block">Accedi</button>
                      </div>
                      <input type="text" name="act" value="<?php echo filter_var($idAct,FILTER_SANITIZE_NUMBER_INT); ?>" hidden>
                    </form>
                </div>

              </div>
            </div>
          </div>
        </nav>
      </div>
    </div>

    <!--Recupero password-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Recupero Password</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <!--Email-->
            <div class="form-group input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <em class="fa fa-envelope">
                    <svg class="bi bi-envelope-fill" width="1.2em" height="1.2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                      <path d="M.05 3.555L8 8.414l7.95-4.859A2 2 0 0014 2H2A2 2 0 00.05 3.555zM16 4.697l-5.875 3.59L16 11.743V4.697zm-.168 8.108L9.157 8.879 8 9.586l-1.157-.707-6.675 3.926A2 2 0 002 14h12a2 2 0 001.832-1.195zM0 11.743l5.875-3.456L0 4.697v7.046z"/>
                    </svg>
                  </em>
                </span>
              </div>
              <input name="email" id="email" class="form-control" placeholder="Email" type="email">
            </div>

              <p style="text-align: center;">o</p>

            <!--Username-->
            <div class="form-group input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <em class="fa fa-lock">
                    <svg class="bi bi-shield-lock-fill" width="1.2em" height="1.2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" d="M5.187 1.025C6.23.749 7.337.5 8 .5c.662 0 1.77.249 2.813.525a61.09 61.09 0 012.772.815c.528.168.926.623 1.003 1.184.573 4.197-.756 7.307-2.367 9.365a11.191 11.191 0 01-2.418 2.3 6.942 6.942 0 01-1.007.586c-.27.124-.558.225-.796.225s-.526-.101-.796-.225a6.908 6.908 0 01-1.007-.586 11.192 11.192 0 01-2.417-2.3C2.167 10.331.839 7.221 1.412 3.024A1.454 1.454 0 012.415 1.84a61.11 61.11 0 012.772-.815zm3.328 6.884a1.5 1.5 0 10-1.06-.011.5.5 0 00-.044.136l-.333 2a.5.5 0 00.493.582h.835a.5.5 0 00.493-.585l-.347-2a.5.5 0 00-.037-.122z" clip-rule="evenodd"/>
                    </svg>
                  </em>
                </span>
              </div>
              <input name="username" class="form-control" id="username" placeholder="Username" type="text" pattern="(?=.*[a-z]).{8,16}">
            </div>

            <button type="submit" style="background-color: black; border:none;" onclick="convalidation()" class="btn btn-primary btn-block">Recupera</button>

          </div>

        </div>
      </div>
    </div>


    <!--Registrazione-->
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <div class="form-reg">
            <h2 style="text-align: center;"><strong><em>Crea il tuo profilo</em></strong></h2>
            <hr>

              <form style="margin: 0 auto;" action="registrazione.php" method="post">

                <!--Username-->
                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <em class="fa fa-user">
                        <svg class="bi bi-person-fill" width="1.2em" height="1.2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                      </em>
                    </span>
                  </div>
                  <input name="username" class="form-control" placeholder="Username" type="text" required pattern="(?=.*[a-z]).{8,16}">
                </div>

                <!--Email-->
                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <em class="fa fa-envelope">
                        <svg class="bi bi-envelope-fill" width="1.2em" height="1.2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path d="M.05 3.555L8 8.414l7.95-4.859A2 2 0 0014 2H2A2 2 0 00.05 3.555zM16 4.697l-5.875 3.59L16 11.743V4.697zm-.168 8.108L9.157 8.879 8 9.586l-1.157-.707-6.675 3.926A2 2 0 002 14h12a2 2 0 001.832-1.195zM0 11.743l5.875-3.456L0 4.697v7.046z"/>
                        </svg>
                      </em>
                    </span>
                  </div>
                  <input name="email" class="form-control" placeholder="Email" type="email" required>
                </div>

                <!--Password-->
                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <em class="fa fa-lock">
                        <svg class="bi bi-shield-lock-fill" width="1.2em" height="1.2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M5.187 1.025C6.23.749 7.337.5 8 .5c.662 0 1.77.249 2.813.525a61.09 61.09 0 012.772.815c.528.168.926.623 1.003 1.184.573 4.197-.756 7.307-2.367 9.365a11.191 11.191 0 01-2.418 2.3 6.942 6.942 0 01-1.007.586c-.27.124-.558.225-.796.225s-.526-.101-.796-.225a6.908 6.908 0 01-1.007-.586 11.192 11.192 0 01-2.417-2.3C2.167 10.331.839 7.221 1.412 3.024A1.454 1.454 0 012.415 1.84a61.11 61.11 0 012.772-.815zm3.328 6.884a1.5 1.5 0 10-1.06-.011.5.5 0 00-.044.136l-.333 2a.5.5 0 00.493.582h.835a.5.5 0 00.493-.585l-.347-2a.5.5 0 00-.037-.122z" clip-rule="evenodd"/>
                        </svg>
                      </em>
                    </span>
                  </div>
                  <input name="password" class="form-control" placeholder="Password" type="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}">
                </div>

                <!--Conferma password-->
                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <em class="fa fa-lock">
                        <svg class="bi bi-shield-lock-fill" width="1.2em" height="1.2em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M5.187 1.025C6.23.749 7.337.5 8 .5c.662 0 1.77.249 2.813.525a61.09 61.09 0 012.772.815c.528.168.926.623 1.003 1.184.573 4.197-.756 7.307-2.367 9.365a11.191 11.191 0 01-2.418 2.3 6.942 6.942 0 01-1.007.586c-.27.124-.558.225-.796.225s-.526-.101-.796-.225a6.908 6.908 0 01-1.007-.586 11.192 11.192 0 01-2.417-2.3C2.167 10.331.839 7.221 1.412 3.024A1.454 1.454 0 012.415 1.84a61.11 61.11 0 012.772-.815zm3.328 6.884a1.5 1.5 0 10-1.06-.011.5.5 0 00-.044.136l-.333 2a.5.5 0 00.493.582h.835a.5.5 0 00.493-.585l-.347-2a.5.5 0 00-.037-.122z" clip-rule="evenodd"/>
                        </svg>
                      </em>
                    </span>
                  </div>
                  <input name="conferma_password" class="form-control" placeholder="Conferma password" type="password" min="8" max="16" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,16}">
                </div>

                <!--Categorie-->
                <div class="form-group">
                  <div class="dropdown">
                    <button class="dropdown-toggle form-control btn btn-primary" style="background-color: white; border-color: silver; color: gray;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Categoria d'interesse</button>
                    <div class="dropdown-menu mymenu form-control" id="categories" aria-labelledby="dropdownMenuButton">
                    </div>
                  </div>
                </div>

                <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
                <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
                <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
                <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"> </script>

                <!--Posizione geografica-->
                <div class="form-group">
                  <div class="dropdown">
                    <button class="dropdown-toggle form-control btn" style="background-color: white; border-color: silver; color: gray;" type="button" id="dropdownPosition" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Posizione geografica</button>

                    <div class="dropdown-menu mymenu form-control" aria-labelledby="dropdownPosition">

                      <div class="form-inline mx-4">
                        <input type="text" class="form-control mr-2" name="position" id="auto-complete-reg" placeholder="Inserisci la tua città">
                        <input type="button" class="btn btn-dark form-control mr-2" value=" Cerca " onclick="getPlaceByName('auto-complete-reg','suggestions-reg')">
                        <input type="button" class="form-control btn btn-info" value="Annulla" onclick="erase('auto-complete-reg','suggestions-reg')">
                      </div>

                      <div id="suggestions-reg">

                      </div>

                    </div>
                  </div>
                </div>

              <div class="form-group">
                <button style="background-color: black; border:none;" type="submit" class="btn btn-primary btn-block" onclick="return confCat()">Registrati</button>
              </div>

              <input type="text" name="act" value="<?php echo filter_var($idAct,FILTER_SANITIZE_NUMBER_INT); ?>" hidden>
            </form>
        </div>

      </div>
    </div>
  </div>



  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>
<?php
}
?>
