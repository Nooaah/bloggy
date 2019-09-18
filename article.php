<?php
session_start();

$bdd = new PDO("mysql:host=localhost;dbname=bloggy;charset=utf8", 'root', 'root');




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





if (isset($_GET['id']))
{
    $getid = intval($_GET['id']);
}
else
{
    header('location:index.php');
}

if (isset($_POST['commentaire']))
{
    $commentaire = htmlspecialchars($_POST['commentaire']);

    $ins = $bdd->prepare('INSERT INTO commentaires(id_membre, contenu, date, id_article) VALUES(?,?,?,?)');
    $ins->execute(array($_SESSION['id'], $commentaire, time(), $getid));

    header('location:article.php?id='.$getid.'#commentaires');
}

$req = $bdd->prepare('SELECT * FROM articles WHERE id = ?');
$req->execute(array($getid));
$a = $req->fetch();

$userinfo = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
$userinfo->execute(array($a['id_membre']));
$userinfo = $userinfo->fetch();

$up = $bdd->prepare('UPDATE articles SET views = views + 1 WHERE id = ?');
$up->execute(array($getid));


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= $a['titre'] ?> - BloggyPenguy</title>
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
    <i>Compte de Noah : noah.chtl@gmail.com & 123</i>
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




















    <div class="container mt-5">
        <section id="articles">
            <div class="row">
                <div class="col-md-12 text-center mb-3">
                    <i><h5 style="background-color:#555555;color:white;width:200px;margin:auto;"><b>Votre article</b></h5></i>
                </div>
            </div>
            <hr style="margin-top:-30px;">


            <div class="row">
                <div class="col-md-8 mt-3">

                    <span style="background-color:orange;color:white;padding:5px 10px;border-radius:5px;"><b><b><?= $a['categorie'] ?></b></b></span>

                    <h1 class="mb-3 mt-3"><b><b><?= $a['titre'] ?></b></b></h1>

                    <div class="row">
                        <div class="col-md-1 mb-4 mt-2">
                            <a href="profil.php?id=<?= $userinfo['id'] ?>">
                                <img src="<?= $userinfo['image'] ?>" width="50px" style="border-radius:100%;" alt="">
                            </a>
                        </div>
                        <div class="col-md-11 mb-4" style="color:#BDBDBD;font-size:14px;">
                            <b><b><?= 'Ajouté le ' . date('d/m/Y', $a['date']) . ' à '.date('H:i', time() - $a['date']).'';  ?></b></b>
                            <br>
                            Par <span style="color:black;"><b><b><a style="color:black;" href="profil.php?id=<?= $userinfo['id'] ?>"><?= ucfirst($userinfo['pseudo']) ?></a></b></b></span>
                            <br>
                            <b>Cet article à été vu : <span style="color:black;"><?= $a['views'] + 1 ?> fois</span></b>
                            
                        </div>
                    </div>
                    

                    <img src="<?= $a['image'] ?>" width="100%" alt="">

                    <div class="row">
                        <div class="col-md-1">
                            <a target="_blank" href="http://facebook.com"><img class="mt-4" style="border-radius:100%;" src="https://image.flaticon.com/icons/png/512/124/124010.png" width="35px" alt=""></a>
                            <a target="_blank" href="http://twitter.com"><img class="mt-3" style="border-radius:35px;" src="https://dragonsniortais.fr/wp-content/uploads/2019/04/logo-twitter-circle-png-transparent-image-1.png" width="35px" alt=""></a>
                            <a target="_blank" href="http://Pinterest.com"><img class="mt-3" style="border-radius:35px;" src="https://www.stickpng.com/assets/images/580b57fcd9996e24bc43c52e.png" width="35px" alt=""></a>
                            <a target="_blank" href="mailto:noah.chtl@gmail.com"><img class="mt-3" style="border-radius:10%;margin-left:2px;" src="http://lcdgg.thomascyrix.com/wp-content/uploads/2019/04/Gmail_Icon.png" width="35px" alt=""></a>
                            
                            
                        </div>
                        <div class="col-md-11">
                            <p class="mt-4" style="font-size:18px;">
                                <?= $a['contenu'] ?>
                            </p>
                        
                        </div>
                    </div>



                </div>
                <div class="col-md-4" style="margin-top:80px;">
                        <div class="row">
                            <div class="col-md-12 text-center mb-5">
                                <i><h5 style="font-size:20px;background-color:#555555;color:white;width:150px;margin:auto;margin-top:95px;"><b>Les plus vus</b></h5></i>
                            </div>
                            <hr style="margin-top:-10px;">
                        <?php
                            $mostView = $bdd->prepare('SELECT * FROM articles WHERE id != ? ORDER BY views DESC LIMIT 3');
                            $mostView->execute(array(0));
                            while ($mv = $mostView->fetch()) {
                                ?>
                                    <div class="row">
                                        <div class="col-md-5 mb-3">
                                            <a href="article.php?id=<?= $mv['id'] ?>"><img src="<?= $mv['image'] ?>" width="100%" alt=""></a>
                                        </div>
                                        <div class="col-md-7 mb-3">
                                            <a class="articleMostView" href="article.php?id=<?= $mv['id'] ?>"><b><b><?= $mv['titre'] ?></b></b></a>
                                            <br>
                                            <?= $mv['views'] ?> vues
                                        </div>
                                    </div>
                                <?php
                            }
                            ?>
                            <div class="col-md-12 text-center mb-5">
                                <i><h5 style="font-size:20px;background-color:#555555;color:white;width:170px;margin:auto;margin-top:50px;"><b>Les nouveautés</b></h5></i>
                            </div>
                        <?php
                            $newArticle = $bdd->prepare('SELECT * FROM articles WHERE id != ? ORDER BY id DESC LIMIT 3');
                            $newArticle->execute(array(0));
                            while ($mv = $newArticle->fetch()) {
                                ?>
                                    <div class="row">
                                        <div class="col-md-5 mb-3">
                                            <a href="article.php?id=<?= $mv['id'] ?>"><img src="<?= $mv['image'] ?>" width="100%" alt=""></a>
                                        </div>
                                        <div class="col-md-7 mb-3">
                                            <a class="articlenewArticle" href="article.php?id=<?= $mv['id'] ?>"><b><b><?= $mv['titre'] ?></b></b></a>
                                            <br>
                                            le <?= date('d/m/Y' ,time() - $mv['views']) ?>
                                        </div>
                                    </div>
                                <?php
                            }
                            ?>

                        </div>
                </div>
            </div>

            <?php
                if (isset($_SESSION['id']))
                {
                    if ($a['id_membre'] == $_SESSION['id'])
                    {
                        ?>
                            <a href="modifier.php?id=<?= $a['id'] ?>" class="btn btn-success">Modifier l'article</a>
                        <?php
                    }
                }
            ?>


            <h2 class="mt-4" id="commentaires">Commentaires</h2>
            <hr>

            <?php
            $com = $bdd->prepare('SELECT * FROM commentaires WHERE id_article = ?');
            $com->execute(array($getid));
            $nbCom = $com->rowcount();
            if ($nbCom == 0)
            {
                echo '<br>Il n\'y a aucun commentaire ! Soyez le premier à poster un commentaire sur cet article';
            }
            else
            {
                ?>
                    
                    
                    <?php
                    while ($c = $com->fetch()) {
                        $comuser = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
                        $comuser->execute(array($c['id_membre']));
                        $cuser = $comuser->fetch();
                        ?>
                        <div class="row pt-3">
                            <div class="col-md-1 mb-5">
                                <a href="profil.php?id=<?= $cuser['id'] ?>">
                                    <img src="<?= $cuser['image'] ?>" width="75px" alt="">
                                </a>
                            </div>
                            <div class="col-md-11" style="margin-top:-5px;">
                                <b><b><a style="color:black;" href="profil.php?id=<?= $cuser['id'] ?>"><?= ucfirst($cuser['pseudo']) ?></a></b></b> à commenté le <?= date('d/m/Y', time() - $cuser['date']) ?>
                                <p class="mt-1">
                                    <?= $c['contenu'] ?>
                                </p>
                            </div>
                        </div>
               

                    <?php
                    }
            }
            ?>


            <h3 class="mt-5">Ajouter un commentaire</h3>
            <hr>
            
            <?php
            if (isset($_SESSION['id']))
            {
                ?>
                    <form action="" method="POST">
                        
                        <!--Material textarea-->
                        <div class="md-form">
                            <textarea style="font-size:18px;" id="commentaire" name="commentaire" class="md-textarea form-control" rows="5"></textarea>
                            <label style="font-size:18px;" for="commentaire">Ajouter un commentaire sous le nom de <?= $_SESSION['pseudo'] ?></label>
                        </div>
        
                        <input type="submit" id="submitCommentaire" name="submitCommentaire" class="btn btn-success" value="Envoyer le commentaire">
        
                    </form>
                <?php
            }
            else
            {
                ?>
                

                Vous devez
                <a class="text-primary" data-toggle="modal" data-target="#modalLoginForm"><b>vous connecter</b></a>
                pour écrire un commentaire à propos de l'article : 
                <br>
                <i><?= $a['titre'] ?></i>
                <?php
            }
            ?>
            


            
        </section>
    </div>







<!-- Footer -->
<footer class="page-footer font-small success-color mt-5 pt-2">

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
