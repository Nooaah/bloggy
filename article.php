<?php
session_start();

$bdd = new PDO("mysql:host=localhost;dbname=bloggy;charset=utf8", 'root', 'root');

if (isset($_GET['id']))
{
    $getid = intval($_GET['id']);
}
else
{
    header('location:index.php');
}

$req = $bdd->prepare('SELECT * FROM articles WHERE id = ?');
$req->execute(array($getid));
$a = $req->fetch();

$user = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
$user->execute(array($getid));
$user = $user->fetch();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Gif Social Network</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.8/css/mdb.min.css" rel="stylesheet">
    <!-- Your custom styles (optional) -->
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="https://raw.githubusercontent.com/AboutReact/sampleresource/master/old_logo.png">
    <style>
        #linkArticle
        {
            color:black;
            transition : .2s;
        }
        #linkArticle:hover
        {
            color: #7E7E7E;
        }
    </style>
</head>

<body>
    <!-- DEBUT DU PROJET-->






<!--Navbar-->
<nav class="navbar navbar-expand-lg navbar-dark success-color">

  <!-- Navbar brand -->
  <a class="navbar-brand" href="#">Bloggy</a>

  <!-- Collapse button -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav"
    aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Collapsible content -->
  <div class="collapse navbar-collapse" id="basicExampleNav">

    <!-- Links -->
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">Accueil
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Nouveautés</a>
      </li>

      <!-- Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false">Dropdown</a>
        <div class="dropdown-menu dropdown-primary" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="index.php">Accueil</a>
          <a class="dropdown-item" href="index.php">Nouveautés</a>
        </div>
      </li>

    </ul>
    <!-- Links -->

    <form class="form-inline">
      <div class="md-form my-0">
        <input class="form-control mr-sm-2" type="text" placeholder="Rechercher" aria-label="Rechercher">
      </div>
    </form>

  </div>
  <!-- Collapsible content -->

</nav>
<!--/.Navbar-->



    <div class="container mt-5">
        <section id="articles">
            <div class="row">
                <div class="col-md-12 text-center mb-3">
                    <i><h5 style="background-color:#555555;color:white;width:200px;margin:auto;">Votre article</h5></i>
                </div>
            </div>
            <hr style="margin-top:-30px;">


            <div class="row">
                <div class="col-md-10 mt-5">
                    <h1 class="mb-3"><b><b><?= $a['titre'] ?></b></b></h1>

                    <div class="row">
                        <div class="col-md-1 mb-4">
                            <img src="<?= $user['image'] ?>" width="100%" style="border-radius:100%;" alt="">
                        </div>
                        <div class="col-md-11 mb-4 mt-2" style="color:#BDBDBD;font-size:14px;">
                            <b><b><?= 'Il y a ' . date('i', time() - $a['date']) . ' minutes';  ?></b></b>
                            <br>
                            Par <span style="color:black;"><b><b><?= $user['pseudo'] ?></b></b></span>
                        </div>
                    </div>

                    <img src="<?= $a['image'] ?>" width="100%" alt="">

                    <p class="mt-4" style="font-size:18px;">
                        <?= $a['contenu'] ?>
                    </p>


                </div>
            </div>
            


            
        </section>
    </div>







<!-- Footer -->
<footer class="page-footer font-small success-color mt-5">

  <!-- Copyright -->
  <div class="footer-copyright text-center py-3">© <?= date('Y') ?> Copyright
    <a href="http://noah-chatelain.fr"> Noah Châtelain</a>
  </div>
  <!-- Copyright -->

</footer>
<!-- Footer -->






    <!-- /FIN DU PROJET-->

    <!-- JQuery -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.8/js/mdb.js"></script>
    <!-- VueJS
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.js"></script>-->
    <!-- Axios -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <!-- SCRIPTS
    <script src="js/app.js"></script>-->
    <script src="js/ajax.js"></script>
    <script src="js/script.js"></script>
</body>

</html>
