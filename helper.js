//link agli script php necessari alla comunicazione con il server
const POST_ACTIVITY_LINK = 'http://localhost/TeamUp/postForm.php';
const DELETE_ACTIVITY_LINK = 'http://localhost/TeamUp/deleteActivity.php';
const SEND_REQUEST_LINK = 'http://localhost/TeamUp/sendRequest.php';
const CANCEL_REQUEST_LINK = 'http://localhost/TeamUp/cancelRequest.php';
const SEND_INVITE_LINK = 'http://localhost/TeamUp/invite.php';

//classe utile a inviare richieste al server
class Form
{
  constructor()
  {
    this.data = {};
  }

  addPair (key, value)
  {
    this.data[key] = value;
  }

  sendData (serverLink, method, message)
  {
    var xhr = new XMLHttpRequest();
    xhr.open(method, serverLink, true);
    xhr.setRequestHeader("Content-type", "application/json");

    xhr.onload = function ()
    {
      if (xhr.status == 200)
      {
        var response = JSON.parse(xhr.response);

        if (!response["result"])
        {
          alert("Ops, qualcosa è andato storto. Problema nostro, ci scusiamo per il disagio :)");
        }

        else
        {
          alert(message);
        }

        window.location.replace(response["redirectLink"]);
      }

      else
      {
        alert("Ops, qualcosa è andato storto. Verrai reindirizzato all'HomePage. Clicca qui sotto: ");
        window.location.replace("http://localhost/TeamUp/homepage.php");
      }
    };

    xhr.send(JSON.stringify(this.data));
  }
}


/*
invia i dati del form al file server php.
la funzione carica i dati dell'attività presente nel form, sia per una creazione ex novo, sia per una modifica.
la distinzione avviene grazie al parametro idAct:
1. se l'id è 0, allora significa che si tratta di una nuova attività;
2. altrimenti, si tratta di modificare una già esistenze.
*/
function postActivity (idAct)
{
  var form = new Form();
  var act = new Activity ();
  var correct = true;

  //se siamo nell'homepage, l'id dell'attivita è settato a 0 perchè non esiste ancora, altrimenti ha un valore definito
  act["id"] = idAct;

  //titolo attivita
  var tempContainer = document.getElementById("title");

  if (tempContainer.value == "")
  {
    displayAlert("contAlert", "Non hai indicato un <strong>Titolo!</strong>");
    correct = false;
  }

  else
  {
    act["title"] = tempContainer.value;
  }

  //descrizione attivita
  tempContainer = document.getElementById("description-e");

  if (tempContainer.value.length < 30)
  {
    displayAlert("contAlert", "La <strong>tua Descrizione</strong> non è sufficientemente lunga!");
    correct = false;
  }

  else
  {
    act["description"] = tempContainer.value;
  }

//posti rimanenti
  tempContainer = document.getElementById("left");
  if (tempContainer.value == "" || tempContainer.value < 0)
  {
    displayAlert("contAlert", "Non hai indicato un numero valido di <strong>partecipanti!</strong>");
    correct = false;
  }

  else
  {
    act["leftPositions"] = tempContainer.value;
  }

  //posizione attivita
  var radios = document.getElementsByName("locationid[]");
  var location = null;
  var i = 0, flag = false;
  while (i < radios.length && !flag)
  {
    if (radios[i].checked)
    {
      location = radios[i].value;
      flag = true;
    }

    i++;
  }

  act["locationid"] = location;

  //nel caso in cui non sia stata inserita la posizione, controllo che ci sia qualcosa nella label readonly
  if (!flag)
  {
    var prevPosition = document.getElementById("previuos-position");

    if (prevPosition != null && prevPosition.getAttribute("name") != "null")
    {
      act["locationid"] = prevPosition.getAttribute("name");
    }
  }

  //categorie dell'attivita
  var checkboxes = document.getElementsByName("categories[]");
  var categories = [];

  for (i = 0; i < checkboxes.length; i++)
  {
    if (checkboxes[i].checked)
    {
      categories.push(checkboxes[i].value);
    }
  }

  if (categories.length == 0)
  {
    displayAlert("contAlert","Non hai selezionato neanche una <strong>Categoria!</strong>");
    correct = false;
  }

  else
  {
    act["categories"] = categories;
  }


  if (correct)
  {
    //invio dei dati al server
    form.addPair("activity", act);
    form.sendData(POST_ACTIVITY_LINK, "POST", "Attività pubblicata!");
  }
}

//invia i dati al server per eliminare l'attività indicata dal parametro idAct
function deleteActivity (idAct)
{
  var form = new Form ();

  form.addPair("id", idAct);
  form.sendData(DELETE_ACTIVITY_LINK, "POST", "Attività eliminata con successo!");
}

//invia i dati al server per inviare una richiesta di partecipazione all'attività presente nel containerId
function sendRequest (containerId)
{
  var form = new Form();
  var isOk = validateForm("descr-request", 30, "alertSection", "Devi inserire almeno 30 caratteri!");

  var idAct = document.getElementById(containerId).getAttribute("value");

  if (isOk)
  {
    form.addPair("activityId", idAct);
    form.addPair("descr-request", document.getElementById("descr-request").value);
    form.sendData(SEND_REQUEST_LINK, "POST", "Richiesta inviata con successo!");
  }
}

//invia i dati al server per annullare una richiesta di partecipazione inviata precedentemente per l'attività presente nel containerId
function cancelRequest (containerId)
{
  var form = new Form();

  var idAct = document.getElementById(containerId).getAttribute("value");

  form.addPair("activityId", idAct);
  form.sendData(CANCEL_REQUEST_LINK, 'POST', 'Richiesta annullata con successo!');
}

//invia i dati al server per la procedura di invito (da parte dell'utente loggato ad un altro) all'attività indicata dal parametro idAct
function sendInvite (activity)
{
  var correct = true;

 

  var form = new Form ();
  var act = new Activity(activity);

 

  form.addPair("activityId", act["id"]);

 

  var target = document.getElementById('invite-email');
  const regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
  if (!regex.test(target.value))
  {
    displayAlert("invite-alert","Non hai inserito <strong>un'email corretta!</strong>");
    correct = false;
  }
  else
  {
    //controllo che l'email inserita non sia già presente tra i partecipanti
    var isIn = false;

    for (var i = 0; i < act["teammates"].length; i++)
    {
      if (target.value == act["teammates"][i]["email"])
      {
        isIn = true;
        break;
      }
    }

    //se non è presente, si può caricare il messaggio
    if (!isIn)
    {
      form.addPair("targetEmail", target.value);
    }
    else
    {
      displayAlert("invite-alert","Hai inserito l'email di un partecipante!");
      correct = false;
    }
  }

  if (correct)
  {
    form.sendData(SEND_INVITE_LINK, 'POST', 'Email inviata con successo!');
  }
}


//conta e visualizza il numero di caratteri presenti in una textarea
function countchars(textareaId, counterId)
{
  var textarea = document.getElementById(textareaId);
  var currentCount = document.getElementById(counterId);
  var chars = textarea.value;
  currentCount.innerHTML = chars.length;
}


/*
completa il form per la pubblicazione/modifica dell'attività.
la distinzione avviene grazie al parametro activity:
1. se contiene la stringa "null", allora si tratta di una nuova attività (sezione homepage)
2. altrimenti si tratta di un'attività esistente (sezione activity_page)
*/
function completeForm(categories, targetId, activity)
{
  //carico le categorie del database
  if (document.getElementById("div-container1") == null)
  {
    loadCategories(categories, targetId);
  }

  //caso homepage: non c'è alcuna attivita, quindi recupero la descrizione inserita dall'utente
  if (activity == 'null')
  {
    var descr = document.getElementById("description-b");
    document.getElementById("description-e").value = descr.value;
  }

  //caso della pagina dell'attività
  else
  {
    //carico il titolo
    document.getElementById("title").value = activity["title"];

    //carico la descrizione
    document.getElementById("description-e").value = activity["description"];

    //carico i posti rimanenti
    document.getElementById("left").value = activity["leftPositions"];

    //carico le categorie
    for (var el in activity["categories"])
    {
      document.getElementById("cb"+activity["categories"][el]["categoria"]).checked = true;
    }

    //carico la posizione
    getPlaceById(activity, getLocationLabel);
  }
}


//carica, nel formato checkbox+nome, le categorie prese da database direttamente nel container targetId
function loadCategories (categories, targetId)
{
  var menu = document.getElementById(targetId);

  for (var el in categories)
  {
    var tempDiv = document.createElement("div");
    tempDiv.setAttribute("class", "input-group");
    tempDiv.setAttribute("id", "div-container"+categories[el]["id"]);
    var obj = '<div class="input-group-prepend"><div class="input-group-text"><input type="checkbox" id="cb'+categories[el]["id"]+'" name="categories[]" value='+categories[el]["id"]+'></div></div><input type="text" class="form-control" style="background-color: white;" value="'+ categories[el]["nome"]+'" readonly>';
    tempDiv.innerHTML = obj;
    menu.appendChild(tempDiv);
  }
}


//cancella il contenuto di una casella di testo e di un contenitore
function erase(textBoxId, targetId)
{
  var obj = document.getElementById(targetId);

  if (obj != null)
  {
    obj.innerHTML = "";
  }

  var obj2 = document.getElementById(textBoxId);

  if (obj2 != null)
  {
    obj2.value = "";
  }
}


//pulisce il form dagli input immessi dall'utente
function clearForm()
{
  var title = document.getElementById("title");
  title.value = "";
  var descr = document.getElementById("description-e");
  descr.innerHTML = "";

  var checkboxes = document.getElementsByName("categories[]");

  for (var i = 0; i < checkboxes.length; i++)
  {
    checkboxes[i].checked = false;
  }

  var left = document.getElementById("left");
  left.value = "";

  erase("description-e", "suggestions-modal");
}


//funzione che controlla che il testo di una textarea sia almeno di minlength caratteri.
function textAreaIsOk(textareaId, minlength)
{
  var flag = false;

  var textarea = document.getElementById(textareaId);
  var length = textarea.value.length;

  if (length >= minlength && length <= 500)
  {
    flag = true;
  }

  return flag;
}


//funzione che apre una modal a seconda che il flag sia vero o falso
function redirectToModal(flag, modalIdSuccess, modalIdFail)
{
  if (flag)
  {
    document.getElementById("btn-invia").setAttribute("data-target","#"+modalIdSuccess);
  }

  else
  {
    document.getElementById("btn-invia").setAttribute("data-target","#"+modalIdFail);
  }
}


//visualizza le attività PER L'UTENTE presenti nel database (passate in ingresso), sia nell'homepage, sia nel caso di un'attività con invito.
//sfrutta le API di HERE per convertire l'idPosizione in una Label, da inserire in un Tooltip attraverso addTooltipToIcon
function loadActivitiesForUser(acts, pendingActs, targetId)
{
  if (acts != null)
  {
    var parent = document.getElementById(targetId);

    for (var i = 0; i < acts.length; i++)
    {
      var flag = hasPending(acts[i]["id"], pendingActs);

      var act = new Activity(acts[i]);
      var tempPost = new HomePost (act, flag);
      parent.insertBefore(tempPost["mainContainer"], parent.childNodes[0]);
      getPlaceById(act, addTooltipToIcon);
    }
  }
}


//verifica se una determinata attivita abbia ricevuto delle richieste di partecipazione da parte dell'utente loggato
function hasPending (idAct, pendingActs)
{
  var flag = false;

  for (var el in pendingActs)
  {
    if (idAct == pendingActs[el]["attivita"])
    {
      flag = true;
      break;
    }
  }

  return flag;
}


//visualizza le attività DELL'UTENTE presenti nel database (passate in ingresso) nella sidebar.
function loadActivitiesOfUser(acts, userId)
{
  var lead = document.getElementById("leaderActs");
  var team = document.getElementById("teammateActs");

  for (var el in acts)
  {
    var tempActivity = new Activity (acts[el]);

    if (tempActivity["completed"] == 0)
    {
      var li = document.createElement("li");
      li.setAttribute("class", "mb-1");
      var content = '<a class="btn btn-outline-dark" href="activity_page.php?id=' + tempActivity["id"] + '">'+tempActivity["title"]+'</a>';
      li.innerHTML = content;

      for (var elem in tempActivity["teammates"])
      {
        if (userId == tempActivity["teammates"][elem]["utente"])
        {
          if (tempActivity["teammates"][elem]["is_leader"] == true)
          {
            lead.appendChild(li);
          }

          else
          {
            team.appendChild(li);
          }
        }
      }
    }
  }
}

//carica l'id dell'attività indicata dal parametro idAct nel containerId (necessario per l'invio di dati al server)
function loadRequest(containerId, idAct)
{
	var obj = document.getElementById(containerId);
	obj.setAttribute("value", idAct);
}


//visualizza un alert warning personalizzato in un container designato
function displayAlert(containerId, message)
{
  var alert = document.createElement("div");

  alert.setAttribute("id", "alert");
  alert.setAttribute("class", "alert alert-warning alert-dismissible fade show");
  alert.setAttribute("role", "alert");

  var content = '<strong>Attenzione!</strong> '+message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
  alert.innerHTML = content;

  var cont = document.getElementById(containerId);
  cont.appendChild(alert);
}


//controlla che una textarea soddisfi il criterio del minimo numero di caratteri.
//se ritorna falso, viene visualizzato un alert
function validateForm (textarea, minlength, containerId, message)
{
  var flag = textAreaIsOk(textarea,minlength);

  if (!flag)
  {
    displayAlert(containerId, message);
  }

  return flag;
}
