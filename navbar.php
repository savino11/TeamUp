<?php

require_once 'category.php';

$categories = Category::readAllFromDB();
$jsonCategories = json_encode($categories);

?>

 <div class="row">
        <div class="col-md-12" id="navbar">
          <nav class="navbar navbar-expand-md navbar-dark">
            <a class="navbar-brand" href="homepage.php" style="color: #b5e6da;"> <img src="icona.png" alt="logo" height="70" width=60> </a>
            <button class="navbar-toggler" type="button" style="color: #b5e6da;" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item active mx-5">
                  <a class="nav-link" href="showProfile.php"> <i class="fa fa-user fa-2x" aria-hidden="true" style="color: #b5e6da;"></i> </a>
                </li>
                <li class="nav-item active mx-5">
                  <a class="nav-link" href="#search" data-toggle="modal" onclick='loadCategories(<?php echo $jsonCategories ?>, "dropdown-menu-search")'> <i class="fa fa-search fa-2x" aria-hidden="true" style="color: #b5e6da;"></i> </a>
                </li>
              </ul>
              <div class="nav-item active dropdown">
                <a class="nav-link dropdown-toggle" id="navbar-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#" style="color: #b5e6da;"> <i class="fa fa-cog fa-2x" aria-hidden="true" style="color: #b5e6da;"></i> </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="modifica.php"> Modifica profilo </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="logout.php"> <i class="fa fa-sign-out" aria-hidden="true"></i> Log-Out </a>
                </div>
              </div>
            </div>
          </nav>
        </div>
      </div>

<?php require 'search_modal.php'; ?>
