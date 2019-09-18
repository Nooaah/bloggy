<?php
session_start();

$bdd = new PDO("mysql:host=localhost;dbname=bloggy;charset=utf8", 'root', 'root');


if (isset($_POST['titreCategorie']))
{
    $categorie = htmlspecialchars($_POST['titreCategorie']);
    $ins = $bdd->prepare('INSERT INTO categorie (titre) VALUES(?)');
    $ins->execute(array($categorie));
}

if (empty($_SESSION['id']))
{
    header('location:index.php');
}
if (isset($_GET['id']))
{
    $getid = intval($_GET['id']);
}
else
{
    header('location:index.php');
}

if (isset($_GET['del']))
{
    $idel = intval($_GET['del']);
    $del = $bdd->prepare('DELETE FROM articles WHERE id = ?');
    $del->execute(array($idel));
    header('location:profil.php?id='.$_SESSION['id']);
}

if (isset($_GET['delcat']))
{
    $idel = intval($_GET['delcat']);
    $del = $bdd->prepare('DELETE FROM categorie WHERE id = ?');
    $del->execute(array($idel));
    header('location:profil.php?id='.$_SESSION['id']);
}


?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= $_SESSION['pseudo'] ?> - BloggyPenguy</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.8/css/mdb.min.css" rel="stylesheet">
    <!-- Your custom styles (optional) -->
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="https://data.whicdn.com/images/48855885/original.png">
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
  <a class="navbar-brand" href="#"><img src="https://data.whicdn.com/images/48855885/original.png" class="mr-3" width="30px" alt="">BloggyPenguy</a>

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
        <a class="nav-link" href="index.php">Accueil</a>
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
            <li class="nav-item">
                <a href="profil.php?id=<?= $_SESSION['id'] ?>" class="nav-link" >
                Mon profil</a>
            </li>
            <?php
        }

        ?>

    </ul>
    <!-- Links -->

    <form class="form-inline">
      <div class="md-form my-0">
        <?php
        if (isset($_SESSION['id']))
        {
            ?>
            <span class="white-text">Connecté en tant que <b><a id="profilLink" href="profil.php?id=<?= $_SESSION['id'] ?>"><?= ucfirst($_SESSION['pseudo']) ?></a></b></span>
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


<?php
$req = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
$req->execute(array($getid));
$userinfo = $req->fetch();
?>


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($userinfo['id'] == $_SESSION['id'])
            {
                ?>
                    <h1 class="mt-5">Bienvenue sur votre profil <?= ucfirst($_SESSION['pseudo']) ?></h1>
                <?php
            }
            else
            {
                ?>
                    <h1 class="mt-5">Bienvenue sur le profil de <?= ucfirst($userinfo['pseudo']) ?></h1>
                <?php
            }
            ?>

            <hr>

            <div class="row">
                <div class="col-md-3">
                    <img src="<?= $userinfo['image'] ?>" width="100%" style="border-radius:100%;" class="mt-5" alt="">
                    <h4 class="text-center mt-4"><?= $_SESSION['pseudo'] ?></h4 class="text-center">
                    <p class="mt-2 text-center">
                        <a href="mailto:<?= $_SESSION['mail'] ?>"><?= $_SESSION['mail'] ?></a>
                        <hr>
                        <div class="mt-1 text-center" id="nb_article"></div>
                        
                    </p>
                </div>
                <div class="col-md-9">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col" width="10%">Date de création</th>
                        <th scope="col">Titre</th>
                        <th scope="col" width="10%">Catégorie</th>
                        <th scope="col" width="10%">Vues</th>
                        <th scope="col" width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $article = $bdd->prepare('SELECT * FROM articles WHERE id_membre = ? ORDER BY id');
                    $article->execute(array($userinfo['id']));
                    $nb_articles = $article->rowcount();
                    while ($a = $article->fetch()) {
                    ?>
                            <tr>
                                <th><?= date('d/m/Y', $a['date']) ?><br><?= date('H:i', $a['date']) ?></th>
                                <th><a href="article.php?id=<?= $a['id'] ?>"><?= $a['titre'] ?></a></th>
                                <th><?= $a['categorie'] ?></th>
                                <th><?= $a['views'] ?></th>
                                <th>
                                    <?php
                                    if ($userinfo['id'] == $_SESSION['id']) {
                                    ?>
                                    <a href="profil.php?id=<?= $_SESSION['id'] ?>&del=<?= $a['id'] ?>"><i style="color:red;" class="fas fa-trash-alt"></i></a>
                                    <a href="modifier.php?id=<?= $a['id'] ?>"><i style="color:green;" class="fas fa-pencil-alt ml-3"></i></a>
                                    <?php
                                    }
                                    else
                                    {
                                        echo 'Modif. impossible';
                                    }
                                    ?>
                                </th>
                            </tr>
                            <?php
                    }
                    ?>
                        </tbody>
                    </table>
                    <br>
                    <hr>
                    <h5>Catégories</h5>
                    <hr>


                    <script>
                        if (<?= $nb_articles ?> <= 1)
                        {
                            document.getElementById('nb_article').innerHTML = '<b><?= $nb_articles ?></b> article créé';
                        }
                        else
                        {
                            document.getElementById('nb_article').innerHTML = '<b><?= $nb_articles ?></b> articles créés'; 
                        }
                    </script>



                    <div class="row">
                        <div class="col-md-5">
                        

                            <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Titre</th>
                                    <th scope="col" class="text-center" width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $cate = $bdd->prepare('SELECT * FROM categorie WHERE id != ? ORDER BY id');
                            $cate->execute(array(0));
                            while ($c = $cate->fetch()) {
                            ?>
                                    <tr>
                                        <th><?= $c['titre'] ?></th>
                                        <th>
                                        <?php
                                        if ($userinfo['id'] == $_SESSION['id']) {
                                        ?>
                                        <a href="profil.php?delcat=<?= $c['id'] ?>"><i style="color:red;" class="ml-5 fas fa-trash-alt">
                                        <?php
                                        }
                                        else
                                        {
                                            echo 'Modif. impossible';
                                        }
                                        ?>
                                        
                                        </th>
                                    </tr>
                                    <?php
                            }
                            ?>
                                </tbody>
                            </table>

                        </div>
                            <div class="col-md-7">
                                <!-- Material input -->
                        <form action="" method="POST">
                                <div class="md-form">
                                <input type="text" id="titreCategorie" name="titreCategorie" maxlength="100" class="form-control" style="font-size:20px;">
                                <label style="font-size:20px;" for="titreCategorie">Ajouter une catégorie au site</label>
                                </div>
                                <input type="submit" class="btn btn-success" id="addCategorie" name="addCategorie" value="Ajouter cette catégorie">
                        </form>
                            </div>  


                            
                    </div>





                </div>
            </div>

        </div>
    </div>
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
