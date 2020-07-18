class Post
{
	constructor(activity)
	{
		var divCont = document.createElement("div");
		divCont.setAttribute("class", "row mb-5");
		this.mainContainer = divCont;

		this.activity = activity;
	}

	static processInput (elements, jsonAttribute)
	{
		var names = '';

		for (var i = 0; i < elements.length; i++)
		{
			names = names + elements[i][jsonAttribute] + ', ';
		}

		return names.substring(0, names.length-2);
	}
}

class HomePost extends Post
{
	constructor(activity, flag)
	{
		super(activity);
		this.hasPendingRequests = flag;

		this.leaders = fetchLeaders(this.activity["teammates"]);
		var funct = '';

		if (!this.hasPendingRequests)
		{
			funct = 'onclick="loadRequest(\'modal-request\', '+this.activity.id+')"';
			this.userIcon = '<button type="button" class="btn" data-toggle="modal" data-target="#modal-request" '+funct+'>' +
			'<i class="fa fa-user-plus fa-lg" data-html="true" data-toggle="tooltip" data-placement="top" '+
			'title="Invia una richiesta di partecipazione" aria-hidden="true"></i>'+
			'</button>';
		}

		else
		{
			funct = 'onclick="loadRequest(\'modal-cancel-request\', '+this.activity.id+')"';
			this.userIcon = '<button type="button" class="btn" data-toggle="modal" data-target="#modal-cancel-request" '+funct+'>'+
			'<i class="fa fa-user-times fa-lg" data-html="true" data-toggle="tooltip" data-placement="top" '+
			'title="Annulla richiesta di partecipazione" aria-hidden="true"></i>'+
			'</button>';
		}


		this.content = '<div class="col-md-10 offset-md-1">'+
		'<div class="container the-post rounded">'+
		'<div class="form-row px-5 pt-4">'+
		'<div class="form-group col-lg-1">'+
		'<button type="button" class="btn">'+
		'<i class="fa fa-suitcase fa-lg" data-html="true" data-toggle="tooltip" data-placement"top" '+
		'title="<u>Categorie</u>:<br>'+ Post.processInput(this.activity["categories"], "nome") +'" aria-hidden="true"></i>'+
		'</button></div>'+
		'<div class="form-group col-lg-5 mt-2 pl-2">'+
		'<p class="text-center" data-toggle="tooltip" data-placement="top" data-html="true" title="<u>Titolo</u>">'+ this.activity["title"] +'</p>'+
		'</div>'+
		'<div class="form-group col-lg-3">'+ this.userIcon +'<button type="button" class="btn">'+
		'<i class="fa fa-calendar pr-2 fa-lg" data-toggle="tooltip" data-placement="top" title="<u>Pubblicato il</u>:<br>'+ this.activity["date"] +'" data-html="true" aria-hidden="true"></i>'+
		'</button>'+
		'<button type="input" class="btn">'+
		'<i class="fa fa-map-marker fa-lg" id="mapicon'+ this.activity["id"] +'" data-toggle="tooltip" data-html="true" data-placement="top" aria-hidden="true"></i>'+
		'</button></div>'+
		'<div class="form-group col-lg-3 pr-3 mt-2">'+
		'<p class="text-center" data-toggle="tooltip" data-placement="top" data-html="true" title="<u>Leader</u>">' + this.leaders + '</p>'+
		'</div></div>'+
		'<div class="form-group mb-4 px-5">'+
		'<textarea class="form-control" rows="5" readonly>'+ this.activity["description"] +'</textarea>'+
		'<small class="form-text text-muted"><span> Posti rimanenti: '+ this.activity["leftPositions"] +' </span></small>'+
		'</div></div></div>'

		this.mainContainer.innerHTML = this.content;

		function fetchLeaders(teammates)
		{
			var names = '';

			for (var i = 0; i < teammates.length; i++)
			{
				if (teammates[i]["is_leader"] == 1)
				{
					names = names + teammates[i]["username"] + ', ';
				}
			}

			return names.substring(0, names.length-2);
		}
	}
}

class PagePost extends Post
{
	constructor(activity)
	{
		super(activity);

		this.isClosed = activity["completed"];

		// Viene determinato se l'utente loggato è leader

		var loggedAsLeader = false;
		var i = 0;

		while(!loggedAsLeader && i < this.activity.teammates.length) {

			if(this.activity.teammates[i].utente == sessionStorage.getItem('id')) {

				loggedAsLeader = this.activity.teammates[i].is_leader;
			}
			else {

				i++;
			}
		}

		// Titolo

		var title = "<h1>" + this.activity.title + " <small>";

		if(this.isClosed) {

			title += "(chiusa)";
		}
		else {

			title += "(" + this.activity.leftPositions + " posti rimanenti)";
		}

		title += "</small></h1>";

		// Descrizione

		var message = "<h5>" + this.activity.description + "</h5>";

		// Categorie

		var categories = '<i class="fa fa-suitcase fa-lg" data-html="true" data-placement"top" aria-hidden="true"></i> ' + Post.processInput(this.activity['categories'], 'nome');

		// Posizione

		var position = '<i class="fa fa-map-marker fa-lg" id="mapicon'+ this.activity.id +'" data-html="true" data-placement="top" aria-hidden="true"></i> ';

		// Data

		var date = '<i class="fa fa-calendar pr-2 fa-lg" data-placement="top" data-html="true" aria-hidden="true"></i>Pubblicato il '+ this.activity.date;

		// Tabella partecipanti

		// Intestazione

		var members = '<table class="table" style="word-break: break-word"><thead><tr><th scope="col"><i class="fa fa-user-o" aria-hidden="true"></i> Membro</th><th scope="col"><i class="fa fa-envelope-o" aria-hidden="true"></i> Indirizzo e-mail</th>';

		if(loggedAsLeader == 1 && !this.isClosed) {

			members += '<th><center><i class="fa fa-crosshairs" aria-hidden="true"></i></center></th><th><center><i class="fa fa-share" aria-hidden="true"></i></center></th></tr>'
		}

		members += '</thead><tbody>';

		// Corpo

		for(i = 0; i < this.activity.teammates.length; i++) {

			var rowUserIsLeader = this.activity.teammates[i].is_leader == 1;

			members += '<tr><th scope="row">' + this.activity.teammates[i].username;

			if(rowUserIsLeader) {

				members += " (Leader)";
			}

			members += '</th>';

			members += "<td>" + this.activity.teammates[i].email + "</td>";

			if(loggedAsLeader == 1 && !rowUserIsLeader && !this.isClosed) {			
				
				var onclickEventRimuovi = 'completeModal("userDelete",' + this.activity.teammates[i].id +', "actDelete",' + this.activity.id +')';
				var onclickEventPromuovi = 'completeModal("userUpgrade",' + this.activity.teammates[i].id +', "actUpgrade",' + this.activity.id +')';

				members += "<td><center><a class='btn btn-outline-dark' href='#' data-toggle='modal' data-target='#delete-teammate' onclick='" + onclickEventRimuovi + "'>Rimuovi</a></center></td>"

				members += "<td><center><a class='btn btn-outline-dark' href='#' data-toggle='modal' data-target='#upgrade-teammate' onclick='" + onclickEventPromuovi + "'>Promuovi</a></center></td>"
			}
		}

		members += "</tbody></table>";

		var buttons = "<div class='list-group' style='word-break: break-word'>";

		if(loggedAsLeader == 1) {

			if(!this.isClosed) {

				buttons += "<a href='#' data-toggle='modal' data-target='#requests' class='list-group-item list-group-item-action'><i class='fa fa-check-square-o' aria-hidden='true'></i> Richieste di partecipazione</a>";
				buttons += "<a href='#Abbandona-leadership' data-toggle='modal' class='list-group-item list-group-item-action'><i class='fa fa-times' aria-hidden='true'></i> Abbandona Leadership</a>";
				buttons += "<a href='#' data-toggle='modal' data-target='#close-activity' class='list-group-item list-group-item-action'><i class='fa fa-lock' aria-hidden='true'></i> Chiudi Attività</a>";
				buttons += "<a id='edit-activity' data-toggle='modal' href='#activity-parameters' class='list-group-item list-group-item-action'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Modifica Attività</a>";
			}

			buttons += "<a data-toggle='modal' href='#delete-activity' class='list-group-item list-group-item-action'><i class='fa fa-trash' aria-hidden='true'></i> Elimina Attività</a>";
		}
		else {

			buttons += "<a data-toggle='modal' href='#invite' class='list-group-item list-group-item-action'><i class='fa fa-paper-plane' aria-hidden='true'></i> Invita</a>";
			buttons += "<a href='#Abbandona-teammate' data-toggle='modal' class='list-group-item list-group-item-action'><i class='fa fa-times' aria-hidden='true'></i> Abbandona</a>";
		}

		buttons += "</div>"

		this.content = "<div class='col-md-9 py-2 px-4'>" +
		title +
		date + " | " + categories + " | " + position +
		"<hr>" +
		message + "<br>" +
		members +
		"</div>" +
		"<div class='col-md-3 py-5 px-4'>" +
		buttons +
		"</div>";

		this.mainContainer.innerHTML = this.content;
	}
}
