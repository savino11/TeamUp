//valori comuni a tutte le funzioni, sono essenziali per poter utilizzare le API
const APIKEY = 'oI_HSWS0RGGQvReYyh5r7LsDdLleHbSZeyRK2tcZkEc';
const URL = 'https://geocoder.ls.hereapi.com/6.2/geocode.json';

//classe utile a memorizzare in un oggetto la rielaborazione del Response di una richiesta API HERE
class Place
{
  constructor(city, county, postalCode, state, country, locationid)
  {
    this.city = city;
    this.county = county;
    this.postalCode = postalCode;
    this.state = state;
    this.country = country;
    this.locationid = locationid;
  }

  getLabel()
  {
    return this.city + ', ' + this.county + ', ' + this.postalCode + ', ' + this.state + ', ' + this.country;
  }

}

//recupera il testo selezionato dalla casella di testo e invia una richiesta ad HERE.
//dopodichè mostra i risultati trovati nel dropdown menu, indicato dal parametro targetId, con radio button
function getPlaceByName (textBoxId, targetId)
{
  var textBox = document.getElementById(textBoxId);
  var ajaxRequest = new XMLHttpRequest();

  var params = '?' +
  'apiKey=' + APIKEY +
  '&maxresults=10' +
  '&searchtext=' + textBox.value;

  if (textBox.value.length)
  {
    ajaxRequest.open('GET', URL + params);
    ajaxRequest.send();
  }

  function onSuccess ()
  {
    addSuggestionsToPanel(ajaxRequest.response, targetId);
  }

  function onFailure()
  {
    alert('Ooops!');
  }

  ajaxRequest.addEventListener("load", onSuccess);
  ajaxRequest.addEventListener("error", onFailure);
  ajaxRequest.responseType = "json";
}

//funzione che crea la forma del singolo risultato, ovvero radioButton + label
//i dati del response in ingresso vengono memorizzati in un oggetto di tipo Place per avere un facile accesso degli stessi.
//inoltre, vengono eliminati tutti i risultati che contengono un valore null o undefined per alcuni campi di interesse
function addSuggestionsToPanel(response, targetId)
{
  var suggestions = document.getElementById(targetId);
  suggestions.innerHTML = "";

  for (var el in response["Response"]["View"][0]["Result"])
  {
    var choice = new Place(response["Response"]["View"][0]["Result"][el]["Location"]["Address"]["City"],
                           response["Response"]["View"][0]["Result"][el]["Location"]["Address"]["County"],
                           response["Response"]["View"][0]["Result"][el]["Location"]["Address"]["PostalCode"],
                           response["Response"]["View"][0]["Result"][el]["Location"]["Address"]["State"],
                           response["Response"]["View"][0]["Result"][el]["Location"]["Address"]["Country"],
                           response["Response"]["View"][0]["Result"][el]["Location"]["LocationId"]);

    if (isCorrect(choice))
    {
      var divCont = document.createElement("div");
      divCont.setAttribute("class", "input-group pt-3");
      var option = choice.getLabel();

      var content = '<div class="input-group-prepend"><div class="input-group-text"><input type="radio" class="radio-group" name="locationid[]" value="'+choice.locationid+'" required></div></div><input type="text" class="form-control" style="background-color: white;" value="'+option+'" readonly>';

      divCont.innerHTML = content;

      suggestions.appendChild(divCont);
    }
  }
}

//funzione utilizzata nel caricamento dell'homepage.
//preso da database il locationid di un'attività, il client fa una richiesta alle API HERE per ottenere il nome della città
//corrispondente all'id passato in ingresso, dopodichè lo inserisce come tooltip di un'icona, nel frame dell'attività
function getPlaceById (activity, callback)
{
  var exist = true;

  if (activity["locationid"] === null)
  {
    exist = false;
  }

  var ajaxRequest = new XMLHttpRequest();

  var params = '?' +
  'apiKey=' + APIKEY +
  '&locationid=' + activity["locationid"];

  ajaxRequest.open('GET', URL + params);
  ajaxRequest.send();

  function onSuccess ()
  {
    if (typeof callback === "function")
    {
      callback(ajaxRequest.response, exist, activity);
    }
  }

  function onFailure()
  {
    alert('Ooops!');
  }

  ajaxRequest.addEventListener("load", onSuccess);
  ajaxRequest.addEventListener("error", onFailure);
  ajaxRequest.responseType = "json";
}

//aggiorna l'oggetto activity passato in ingresso, completando il campo relativo alla label della posizione
function getLocationLabel (response, flag, activity)
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

  var pos = document.getElementById("previuos-position");
  pos.setAttribute("value", label);

  if (flag)
  {
    pos.setAttribute("name", found["locationid"]);
  }

  else
  {
    pos.setAttribute("name", null);
  }
}

function addLabelToProfileEdit (response, flag, activity)
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

  var obj = document.getElementById("posizione-modifica");
  obj.setAttribute("value", label);
}

function addLabelToProfile (response, flag, activity)
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

  var obj = document.getElementById("posizione-profilo");
  obj.innerHTML = "posizione : " + label;
}

//crea un tooltip contenente la label della posizione
function addTooltipToIcon(response, flag, activity)
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

  var obj = document.getElementById("mapicon"+activity["id"]);
  obj.setAttribute("title", "<u>Posizione</u>:<br>" +label);
}

//verifica che all'interno di un oggetto di tipo Place non ci siano elementi null o undefined
function isCorrect (choice)
{
  for (var el in choice)
  {
    if (typeof choice[el] === 'undefined')
    {
      return false;
    }
  }

  return true;
}
