<?php
$servername = "localhost"; // Remplacez par vos informations de connexion
$username = "root";
$password = "";
$dbname = "ecity";

// Créez une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérez les services
$sql = "SELECT s.id_Services, s.nom AS service_nom, s.adress, s.email, s.phone, s.horraire, c.Specialite, r.prix
        FROM services s
        LEFT JOIN clinique c ON s.id_Services = c.id_Services
        LEFT JOIN restauration r ON s.id_Services = r.id_Services";
$result = $conn->query($sql);
$services = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

$conn->close();
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
                    <li><a href="#contact_page">Contacts</a></li>
                </ul>
                <div class="right-button hidden-xs">
                    <a href="login.php">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- MainMenu-Area-End -->

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <h3>Catégories</h3>
                <div class="list-group" id="categories-list">
                    <a href="#" class="list-group-item list-group-item-action" data-category="clinique">Clinique</a>
                    <a href="#" class="list-group-item list-group-item-action" data-category="restauration">Restauration</a>
                </div>
                <h3 class="mt-4">Services</h3>
                <div id="services-list" class="list-group">
                    <!-- Les services seront chargés dynamiquement ici -->
                </div>
            </div>
            <div class="col-md-8">
                <div id="service-details" class="card">
                    <div class="card-body">
                        <h3 class="card-title">Détails du service</h3>
                        <p class="card-text">Sélectionnez un service pour voir les détails.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer-Area -->
    <footer class="footer-area mt-5 py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Contactez-nous</h5>
                    <p><i class="fas fa-map-marker-alt"></i> Location 1 <br> Location 2</p>
                </div>
                <div class="col-md-4">
                    <h5>Téléphone</h5>
                    <p><i class="fas fa-phone-alt"></i> +000000000 <br> +00000000</p>
                </div>
                <div class="col-md-4">
                    <h5>Email</h5>
                    <p><i class="fas fa-envelope"></i> yourmail@gmail.com <br> your2email@gmail.com</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-center">
                    <p>&copy; Tous droits réservés | Ce site est créé par <a href="#" target="_blank">Adil&Ikram</a></p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer-Area-End -->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
   $(document).ready(function() {
        var services = <?php echo json_encode($services); ?>;
        var selectedCategory = null;

        // Ajoutez un écouteur d'événements aux éléments de la liste des catégories
        $('.list-group-item').on('click', function() {
            selectedCategory = $(this).data('category');
            filterServicesByCategory();
        });

        function filterServicesByCategory() {
            var servicesHtml = '';
            services.forEach(function(service) {
                if ((!selectedCategory || 
                    (selectedCategory === 'clinique' && service.Specialite) ||
                    (selectedCategory === 'restauration' && service.prix))) {
                    servicesHtml += '<a href="#" class="list-group-item list-group-item-action service-item" data-service-id="' + service.id_Services + '">' + service.service_nom + '</a>';
                }
            });
            $('#services-list').html(servicesHtml);

            // Ajoutez un écouteur d'événements aux nouveaux éléments de la liste des services
            $('.service-item').on('click', function() {
                var serviceId = $(this).data('service-id');
                var serviceDetails = services.find(service => service.id_Services == serviceId);

                $('#service-details').html(
                    '<div class="card-body">' +
                    '<h3 class="card-title">' + serviceDetails.service_nom + '</h3>' +
                    '<p class="card-text">Adresse: ' + serviceDetails.adress + '</p>' +
                    '<p class="card-text">Email: ' + serviceDetails.email + '</p>' +
                    '<p class="card-text">Téléphone: ' + serviceDetails.phone + '</p>' +
                    '<p class="card-text">Horraire: ' + serviceDetails.horraire + '</p>' +
                    (serviceDetails.Specialite ? '<p class="card-text">Spécialité: ' + serviceDetails.Specialite + '</p>' : '') +
                    (serviceDetails.prix ? '<p class="card-text">Prix: ' + serviceDetails.prix + '</p>' : '') +
                    '<a href="profile_service.php?id=' + serviceId + '" class="btn btn-primary">Voir Profil du Service</a>'+
                    '</div>'
                );
            });
        }
    });

</script>
</body>
</html>
