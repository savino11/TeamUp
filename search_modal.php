<!-- post modal -->
<div class="modal fade" id="search" data-backdrop="static" tab-index="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
  <div class="modal-content" id="modal-content">
	<div class="modal-header mt-0 mb-3" style="background: #109485;">
	  <h5 class="modal-title"> Ricerca </h5>
	</div>
	<div class="modal-body px-5">

		<form action="search.php" method="post">

		<input type="text" class="form-control" name="keyword" placeholder="Parola chiave">

		<div class="form-group mt-4">
		  <div class="dropright">
			<button class="dropdown-toggle form-control btn btn-light" type="button" id="dropdownMenuButton-search" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Scegli una o più categorie </button>
			<div class="dropdown-menu mymenu form-control py-3 px-5" id="dropdown-menu-search" aria-labelledby="dropdownMenuButton-search">
			</div>
		  </div>
		</div>

		<div class="form-group mt-4">
		  <div class="dropright">
			<button class="dropdown-toggle form-control btn btn-light" type="button" id="dropdownPosition-search" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Posizione geografica </button>
			<div class="dropdown-menu mymenu form-control" id="panel-search" aria-labelledby="dropdownPosition-search">
			  <div class="form-inline mx-4">
				<input type="text" class="form-control mr-2" placeholder="Inserisci una città" id="auto-complete-modal-search" name="position">
				<input type="button" id="cerca-btn-search" class="btn btn-dark form-control mr-2" value=" Cerca " onclick="getPlaceByName('auto-complete-modal-search','suggestions-modal-search')">
				<input type="button" id="annulla-btn-search"class="form-control btn btn-info" value="Annulla" onclick="erase('auto-complete-modal-search','suggestions-modal-search')">
			  </div>
			  <div id="suggestions-modal-search">
			  </div>
			</div>
		  </div>
		</div>

		<input type="submit" value="Cerca" class="btn btn-dark btn-block mr-3">

		</form>

		<input type="button" class="btn btn-danger btn-block" data-dismiss="modal" value="Annulla" onclick="clearSearchForm()">

	</div>
  </div>
</div>
</div>
<!-- end post modal -->

<script>

function clearSearchForm()
{
  var checkboxes = document.getElementsByName("categories[]");

  for (var i = 0; i < checkboxes.length; i++)
  {
    checkboxes[i].checked = false;
  }

  var radios = document.getElementsByName("locationid[]");

  for (var i = 0; i < radios.length; i++)
  {
    radios[i].checked = false;
  }
}

</script>
