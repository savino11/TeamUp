<?php

require_once 'category.php';

$categorie = Category::readAllFromDB();
$datiUtente = User::readUserById($_SESSION['id']);
$datiUtenteFinale = User::newFromRecord($datiUtente);

foreach ($datiUtente as $item => $value)
{
  if ( $item == "username")   {   $username = $value;  }
  if ( $item == "email")      {   $email = $value;  }
  if ( $item == "password")   {   $password = $value;  }
  if ( $item == "posizione")  {   $posizione = $value;  }
}

$interessiUtente = $datiUtenteFinale->getInteressi();

 ?>
 <!DOCTYPE html>
 <html lang="it">
   <head>
     <title>Modifica</title>
     <meta charset="utf-8">
     <script src="hereapi.js"></script>
     <script src="activity.js"></script>
    </head>
     <body>


     <div class="col-md-8 py-4">
       <div class="clearfix">
         <button type="button" class=" btn btn-outline-dark float-right" data-toggle="modal" data-target="#EliminaProfilo"> Rimuovi Account</button></strong></li>
         <div class="modal fade" id="EliminaProfilo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
           <div class="modal-dialog" role="document">
             <div class="modal-content">
               <div class="modal-header">
                 <h5 class="modal-title" id="EliminaProfilo">Sei veramente sicuro di voler cancellare il tuo profilo?<br>
        <br>
        <strong><u>ATTENZIONE</u></strong>: Anche le eventuali Attività di cui potresti essere l'unico Leader verranno cancellate.</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                   <span aria-hidden="true">&times;</span>
                 </button>
               </div>

               <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                 <form action="delete_user_script.php" method="post">
                   <button type="submit" class="btn btn-primary">Rimuovi</button>
                 </form>
               </div>
             </div>
           </div>
         </div>
       </div>
         <h2 style="text-align: center;"><strong><em>Modifica il tuo profilo</em></strong></h2>
       <hr>

       <div class="mx-auto" style="width: 200px;">

      </div>
       <div class="input-group-prepend">
     <label for="username"> <strong> <em> Username:</em></strong> </label>
     <div class="container-fluid">
     <input type="text" id="country" name="username" value=" <?php echo $username; ?>" readonly class="form-control"><br><br>
     </div>
     <div class="container-fluid">
       <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#modificaUsername"> Modifica</button></strong></li>
     </div>

     <!-- Modal -->
       <form name="modifica" action="modify.php" method="post" >
     <div class="modal fade" id="modificaUsername" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
       <div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header">
             <h5 class="modal-title" id="modificaUsername">Modifica Username</h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
               <span aria-hidden="true">&times;</span>
             </button>
           </div>
           <div class="modal-body">


               <label> Inserisci il nuovo Username</label>
               <div class="input-group-prepend">
             <input type="text" name="username" value="" placeholder="Username" required pattern="(?=.*[a-z]).{8,16}">
               </div>



           </div>
           <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            <button type="submit" class="btn btn-primary" onclick="">Salva Modifica</button>
           </div>
         </div>
       </div>
     </div>
     </form>

   </div>

     <div class="input-group-prepend">
   <label for="email"> <strong> <em> Email:</em></strong> </label>
   <div class="container-fluid">
   <input type="email" id="Email" name="email" value=" <?php echo $email; ?>" readonly class="form-control "><br><br>
   </div>
   <div class="container-fluid">
     <button type="button" class="btn btn-outline-dark " data-toggle="modal" data-target="#modificaEmail"> Modifica</button></strong></li>
   </div>

   <!-- Modal -->
     <form class="" action="modify.php" method="post">
   <div class="modal fade" id="modificaEmail" tabindex="-1" role="dialog" aria-labelledby="modificaEmail" aria-hidden="true">
     <div class="modal-dialog" role="document">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="modificaEmail">Modifica Email</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
         </div>
         <div class="modal-body">


             <label> Inserisci la nuova Email</label>
             <div class="input-group-prepend">
           <input type="email" name="email" value="" placeholder="Email" required>
             </div>



         </div>
         <div class="modal-footer">
           <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
           <button type="submit" class="btn btn-primary">Salva Modifica</button>
         </div>
       </div>
     </div>
   </div>
   </form>
 </div>

   <div class="input-group-prepend">
 <label for="username"> <strong> <em> Password:</em></strong> </label>
 <div class="container-fluid">
 <input type="password" id="password" name="password" value=" <?php echo $password; ?>" readonly class="form-control"><br><br>
 </div>
 <div class="container-fluid">
   <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#modificaPassword"> Modifica</button></strong></li>
 </div>

 <!-- Modal -->
   <form name="validP"  method="post" action="modify.php">
 <div class="modal fade" id="modificaPassword" tabindex="-1" role="dialog" aria-labelledby="modificaPassword" aria-hidden="true">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="modificaPassword">Modifica Password</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <div class="modal-body">


           <label> Inserisci la nuova Password</label>
           <div class="input-group-prepend">
         <input type="password" name="password" value="" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" placeholder="Password" >
           </div>
         <label> Conferma la nuova Password</label>
         <div class="input-group-prepend">
       <input type="password" name="conferma_password" value="" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" placeholder="Conferma Password">
         </div>



       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
         <button type="submit" class="btn btn-primary">Salva Modifica</button>
       </div>
     </div>
   </div>
 </div>
 </form>

</div>

 <div class="input-group-prepend">
<label for="username"> <strong> <em> Posizione:</em></strong> </label>
<div class="container-fluid">
<input type="text" id="posizione-modifica" name="posizione" value="" readonly class="form-control"><br><br>
</div>
<div class="container-fluid">
 <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#modificaPosizione"> Modifica</button></strong></li>
</div>

<!-- Modal -->
 <form class="" action="modify.php" method="post">
<div class="modal fade" id="modificaPosizione" tabindex="-1" role="dialog" aria-labelledby="modificaPosizione" aria-hidden="true">
 <div class="modal-dialog" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <h5 class="modal-title" id="modificaPosizione">Modifica Posizione</h5>
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
       </button>
     </div>
     <div class="modal-body">


         <label> Inserisci la tua nuova posizione</label>
         <div class="form-group">
           <div class="dropdown">

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


     <div class="modal-footer">
       <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
       <button type="submit" class="btn btn-primary">Salva Modifica</button>
     </div>
   </div>
 </div>
</div>
</form>

</div>

<div class="col-md-8 py-4">
  <form class="" action="modify.php" method="post">
    <div class="form-group">
      <div class="dropdown">
        <button class="dropdown-toggle form-control btn btn-primary" style="background-color: white; border-color: silver; color: gray;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Categorie d'interesse</button>
        <div class="dropdown-menu mymenu form-control" id="categories" aria-labelledby="dropdownMenuButton">
        </div>
      </div>
    </div>
    <button type="submit" class="form-control btn btn-outline-dark" data-target="#modificaInteressi" onclick="return confCat()"> Modifica Interessi</button></strong></li>
  </form>
</div>






</div>


</div>

<script>
  var bd = document.getElementsByTagName('body')[0];
  bd.onload = function ()
  {
    //carico le categorie dal db
    loadCategories(<?php echo json_encode($categorie, JSON_HEX_APOS) ?>, 'categories');

    //carico le categorie dell'utente
    var interessiUtente = <?php echo json_encode($interessiUtente, JSON_HEX_APOS) ?>;

    //tramite un loop, confronto le categorie dell'utente con quelle del db, e flaggo quelle presenti
    for (el in interessiUtente)
    {
      var checkbox = document.getElementById("cb"+interessiUtente[el]["id"]).checked = true;
    }

    var fakeActivity = new Activity();
    fakeActivity["locationid"] = "<?php echo $posizione ?>";
    getPlaceById(fakeActivity, addLabelToProfileEdit);
  }

</script>

<script>
function confCat()
{
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

<script>
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
  alert("Campo modificato correttamente");
}
if(<?php echo $alert ?> == 2)
{
  alert("La password e la sua conferma non coincidono");
}
if(<?php echo $alert ?> == 3)
{
  alert("Campo già esistente, scegline un altro");
}

</script>
   </body>
 </html>
