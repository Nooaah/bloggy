<?php
session_start();

$bdd = new PDO("mysql:host=localhost;dbname=bloggy;charset=utf8", 'root', 'root');

if (empty($_SESSION['id']))
{
    header('location:index.php');
}

if (isset($_POST['submitArticle']))
{
    if (!empty($_POST['titre']) && !empty($_POST['image']) && !empty($_POST['contenu']) && !empty($_POST['categorie']))
    {
        $titre = htmlspecialchars($_POST['titre']);
        $image = htmlspecialchars($_POST['image']);
        $contenu = nl2br($_POST['contenu']);
        $categorie = nl2br($_POST['categorie']);

        $ins = $bdd->prepare('INSERT INTO articles(titre, contenu, date, image, id_membre, categorie) VALUES(?,?,?,?,?,?)');
        $ins->execute(array($titre, $contenu, time(), $image, $_SESSION['id'], $categorie));

        header('location:index.php');

    }
}



if (isset($_POST['pseudoRegister']) && isset($_POST['emailRegister']) && isset($_POST['passwordRegister']))
{
    $pseudo = htmlspecialchars($_POST['pseudoRegister']);
    $email = htmlspecialchars($_POST['emailRegister']);
    $mdp = sha1(htmlspecialchars($_POST['passwordRegister']));

    $ins = $bdd->prepare('INSERT INTO membres(pseudo, mail, mdp, image) VALUES(?,?,?,?)');
    $ins->execute(array($pseudo, $email, $mdp, 'http://placehold.it/100x100'));

    sleep(1);

    $user = $bdd->prepare('SELECT * FROM membres WHERE mail = ? AND mdp = ?');
    $user->execute(array($email, $mdp));
    $user = $user->fetch();


    $_SESSION['id'] = $user['id'];
    $_SESSION['pseudo'] = $user['pseudo'];
    $_SESSION['mdp'] = $user['mdp'];
    $_SESSION['mail'] = $user['mail'];

}




if (isset($_POST['login'])) {
    if (!empty($_POST['mail']) && !empty($_POST['password'])) {
        $mail = htmlspecialchars($_POST['mail']);
        $password = sha1(htmlspecialchars($_POST['password']));
        $req = $bdd->prepare('SELECT * FROM membres WHERE mail = ? AND mdp = ?');
        $req->execute(array($mail, $password));
        $nbMembres = $req->rowcount();

        if ($nbMembres >= 1) {
            $req = $req->fetch();
            $_SESSION['id'] = $req['id'];
            $_SESSION['pseudo'] = $req['pseudo'];
            $_SESSION['mdp'] = $req['mdp'];
            $_SESSION['mail'] = $req['mail'];
        }
    }
}




?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Bloggy</title>
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
    <li class="nav-item">
        <a class="nav-link" href="index.php">Nouveautés</a>
      </li>

      <!-- Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false">Catégories</a>
          <div class="dropdown-menu dropdown-primary" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="index.php">Toutes les catégories</a>
          <?php
            $cat = $bdd->prepare('SELECT * FROM categorie WHERE id != ?');
            $cat->execute(array(0));
            while ($c = $cat->fetch()) {
          ?>
          <a class="dropdown-item" href="index.php?categorie=<?= $c['titre'] ?>"><?= $c['titre'] ?></a>
          <?php
            }
            ?>
        </div>
      </li>



        <?php
        if (empty($_SESSION['id'])) {
            ?>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#modalRegisterForm">Inscription</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#modalLoginForm"><i class="far fa-laugh-wink"></i> Connexion</a>
                </li>
                <?php
        } else {
            ?>
                <li class="nav-item">
                    <a href="deconnexion.php" class="nav-link" >
                    <i class="fas fa-power-off"></i>Deconnexion</a>
                </li>
                <?php
        }
        ?>


                <?php

        if (isset($_SESSION['id']))
        {
            ?>
            <li class="nav-item">
                <a href="ajouter.php" class="nav-link" >
                Ajouter un article</a>
            </li>
            <?php
        }

        ?>

    </ul>
    <!-- Links -->

    <form class="form-inline">
      <div class="md-form my-0">
        <input class="form-control mr-sm-2" type="text" placeholder="Rechercher" aria-label="Rechercher">
        <?php
        if (isset($_SESSION['id']))
        {
            ?>
            <span class="white-text">Connecté en tant que <b><?= $_SESSION['pseudo'] ?></b></span>
            <?php
        }
        ?>
      </div>
    </form>

  </div>
  <!-- Collapsible content -->

</nav>
<!--/.Navbar-->









<form method="POST" action="">


    <div class="modal fade" id="modalRegisterForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header text-center">
            <h4 class="modal-title w-100 font-weight-bold">Inscription</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body mx-3">
            <div class="md-form mb-5">
            <i class="fas fa-user prefix grey-text"></i>
            <input  type="text" id="pseudoRegister" name="pseudoRegister" class="form-control validate">
            <label data-error="wrong" data-success="right" for="pseudoRegister">Pseudo</label>
            </div>
            <div class="md-form mb-5">
            <i class="fas fa-envelope prefix grey-text"></i>
            <input type="email" id="emailRegister" name="emailRegister" class="form-control validate">
            <label data-error="wrong" data-success="right" for="emailRegister">Email</label>
            </div>

            <div class="md-form mb-4">
            <i class="fas fa-lock prefix grey-text"></i>
            <input type="password" id="passwordRegister" name="passwordRegister" class="form-control validate">
            <label data-error="wrong" data-success="right" for="passwordRegister">Mot de passe</label>
            </div>

        </div>
        <div class="modal-footer d-flex justify-content-center">
            <button id="submitRegister" name="submitRegister" class="btn btn-primary white-text">S'inscrire</button>
        </div>
        </div>
    </div>
    </div>

</form>




<form action="" method="POST">
<div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-100 font-weight-bold">Connexion</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mx-3">
        <div class="md-form mb-5">
          <i class="fas fa-envelope prefix grey-text"></i>
          <input id="mail" name="mail" type="email" id="defaultForm-email" class="form-control validate">
          <label data-error="wrong" data-success="right" for="defaultForm-email">Email</label>
        </div>

        <div class="md-form mb-4">
          <i class="fas fa-lock prefix grey-text"></i>
          <input id="password" name="password" type="password" id="defaultForm-pass" class="form-control validate">
          <label data-error="wrong" data-success="right" for="defaultForm-pass">Mot de passe</label>
        </div>

      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button id="login" name="login" class="btn elegant-color white-text">Se connecter</button>
      </div>
    </div>
  </div>
</div>

</form>






<form action="" method="POST">

        <div class="container">

            <h1 class="mt-5">Bonjour <?= $_SESSION['pseudo'] ?>, rédigez votre article</h1>

            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-9">
                        <!-- Material input -->
                        <div class="md-form">
                        <input type="text" id="titre" name="titre" class="form-control" style="font-size:20px;">
                        <label style="font-size:20px;" for="titre">Titre de votre article</label>
                        </div>
                    </div>
                    <div class="col-md-3 pt-4">
                        <select id="categorie" name="categorie" class="browser-default custom-select">
                        <?php
                            $cat = $bdd->prepare('SELECT * FROM categorie WHERE id != ?');
                            $cat->execute(array(0));
                            while ($c = $cat->fetch()) {
                        ?>
                            <option value="<?= $c['titre'] ?>"><?= $c['titre'] ?></option>
                        
                        <?php
                            }
                        ?>
                        </select>
                    </div>
                </div>





                <!-- Material input -->
                <div class="md-form">
                <input style="font-size:18px;" type="text" id="image" name="image" class="form-control">
                <label style="font-size:18px;" for="image">URL de votre image</label>
                </div>

                <!--Material textarea-->
                <div class="md-form">
                <textarea style="font-size:18px;" id="contenu" name="contenu" class="md-textarea form-control" rows="10"></textarea>
                <label style="font-size:18px;" for="contenu">Contenu de votre article</label>
                </div>

                <input type="submit" id="submitArticle" name="submitArticle" class="btn btn-success" value="Créer l'article">

            </div>
        </div>




</form>


<script>
    document.getElementById('titre').addEventListener('keyup', function() {
        document.getElementById('submitArticle').textContent = 'Créer l\'article ' + this.value;
    });
</script>







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
