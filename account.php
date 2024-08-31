<?php
session_start();
require("ServiceCR/connexion.php");

// Vérifier si une session est active
if (isset($_SESSION["connect"]) && $_SESSION["connect"] === true) {
    $user_id = $_SESSION["id"];
    
    // Vérifier si l'utilisateur est un créateur de service
    $req_createur = "SELECT * FROM createur_srv WHERE id_Utilisateur = $user_id";
    $resultat_createur = mysqli_query($con, $req_createur);

    // Récupérer les informations du créateur de service à partir de l'ID utilisateur
    $query_creator_info = "SELECT nom, prenom, email FROM utilisateur WHERE id_Utilisateur = $user_id";
    $result_creator_info = mysqli_query($con, $query_creator_info);
     
    if ($result_creator_info) {
        $creator_info = mysqli_fetch_assoc($result_creator_info);
         
        // Utiliser les informations récupérées dans le formulaire
        $nom_createur = $creator_info['nom'];
        $prenom_createur = $creator_info['prenom'];
        $email_createur = $creator_info['email'];
    } 
    
    if (mysqli_num_rows($resultat_createur) > 0) {
        // L'utilisateur est un créateur de service, redirection vers ServiceCR
        header("Location: ServiceCR/index.php");
        exit();
    } else {
        // L'utilisateur est un client, afficher un message et les formulaires
        $message = "Cette page est réservée aux créateurs de services. Si vous souhaitez devenir un créateur de service, veuillez faire une demande ci-dessous.";
    }
} else {
    // Si aucune session n'est active, rediriger vers la page de connexion
    header("Location: login.php");
    exit();
}

// Insérer les données du formulaire dans la table demandecreationSRV
if (isset($_POST['submit_demande_createur'])) {
    $nom_service = $_POST['nom_service'];
    $adresse_service = $_POST['adresse_service'];
    $email_service = $_POST['email_service'];
    $telephone_service = $_POST['phone_service'];
    $horaire_service = $_POST['horraire_service'];
    $nom_createur = $_POST['nom_createur'];
    $prenom_createur = $_POST['prenom_createur'];
    $email_createur = $_POST['email_createur'];
    
    $query_insert = "INSERT INTO demandecreationSRV (nom_service, adresse_service, email_service, telephone_service, horaire_service, nom_createur, prenom_createur, email_createur)
                     VALUES ('$nom_service', '$adresse_service', '$email_service', '$telephone_service', '$horaire_service', '$nom_createur', '$prenom_createur', '$email_createur')";

    if (mysqli_query($con, $query_insert)) {
        $message = "Votre demande a été envoyée avec succès.";
    } else {
        $message = "Erreur lors de l'envoi de votre demande : " . mysqli_error($con);
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- Place logo.png in the root directory -->
    <link rel="logo" href="images/logo.png">
    <link rel="shortcut icon" type="image/ico" href="images/logo.png" />
    <!-- Plugin-CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/linearicons.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/animate.css">
    <!-- Main-Stylesheets -->
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/service.css">

    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body>
    <!-- MainMenu-Area -->
    <nav class="mainmenu-area navbar-fixed-top" data-spy="affix" data-offset-top="200">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#primary_menu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img src="images/logo.png" alt="Logo"></a>
            </div>
            <div class="collapse navbar-collapse" id="primary_menu">
                <ul class="nav navbar-nav mainmenu">
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="service.php">Services</a></li>
                    <li><a href="map.php">Map</a></li>
                    <li><a href="account.php">Account</a></li>
                    <li><a href="index.php">Contacts</a></li>
                </ul>
                <div class="right-button hidden-xs">
                    <a href="login.php">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- MainMenu-Area-End -->
    <?php if (isset($message)): ?>
    <div class="container mt-4">
        <div class="alert alert-info" role="alert">
            <?php echo $message; ?>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Demande de Créateur de Service</h2>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <!-- Champs pour les informations du service -->
                    <div class="form-group">
                        <label for="nom_service">Nom du Service:</label>
                        <input type="text" class="form-control" id="nom_service" name="nom_service" required>
                    </div>
                    <div class="form-group">
                        <label for="adresse_service">Adresse du Service:</label>
                        <input type="text" class="form-control" id="adresse_service" name="adresse_service" required>
                    </div>
                    <div class="form-group">
                        <label for="email_service">Email du Service:</label>
                        <input type="email" class="form-control" id="email_service" name="email_service" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_service">Téléphone du Service:</label>
                        <input type="text" class="form-control" id="phone_service" name="phone_service" required>
                    </div>
                    <div class="form-group">
                        <label for="horraire_service">Horraire du Service:</label>
                        <input type="text" class="form-control" id="horraire_service" name="horraire_service" required>
                    </div>
                    <!-- Champs pour les informations du créateur de service -->
                    <input type="hidden" id="id_createur_service" name="id_createur_service" value="<?php echo $_SESSION['id']; ?>">
                    <div class="form-group">
                        <label for="nom_createur">Nom du Créateur:</label>
                        <input type="text" class="form-control" id="nom_createur" name="nom_createur" value="<?php echo $nom_createur ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom_createur">Prénom du Créateur:</label>
                        <input type="text" class="form-control" id="prenom_createur" name="prenom_createur" value="<?php echo $prenom_createur ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email_createur">Email du Créateur:</label>
                        <input type="email" class="form-control" id="email_createur" name="email_createur" value="<?php echo $email_createur ?>" required>
                    </div>
                    <!-- Autres champs nécessaires pour la demande -->
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" name="submit_demande_createur" value="Envoyer la demande">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!--Vendor-JS-->
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/vendor/jquery-ui.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <!--Plugin-JS-->
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/contact-form.js"></script>
    <script src="js/jquery.parallax-1.1.3.js"></script>
    <script src="js/scrollUp.min.js"></script>
    <script src="js/magnific-popup.min.js"></script>
    <script src="js/wow.min.js"></script>
    <!--Main-active-JS-->
    <script src="js/main.js"></script>
</body>
</html>
