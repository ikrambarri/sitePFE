<?php
// get_markers.php

$host = 'localhost';
$db = 'ecity';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT nom, adress FROM services');
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $geocodedServices = [];
    foreach ($services as $service) {
        $address = urlencode($service['adress']);
        $geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyCcdQlRtMV1LSovbNXn043J7KBYpOhHhvA";

        $response = file_get_contents($geocodeUrl);
        $response = json_decode($response, true);
        if ($response['status'] == 'OK') {
            $lat = $response['results'][0]['geometry']['location']['lat'];
            $lng = $response['results'][0]['geometry']['location']['lng'];

            $geocodedServices[] = [
                'name' => $service['nom'],
                'lat' => $lat,
                'lng' => $lng
            ];
        } 
    }

    $markersData = json_encode($geocodedServices);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <title>Map</title>
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
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <link rel="stylesheet" type="text/css" href="css/map.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcdQlRtMV1LSovbNXn043J7KBYpOhHhvA&callback=initMap"></script>
    <script>
        let map;
        let markersData = <?php echo $markersData; ?>;

        function initMap() {
            var BJ = { lat: 32.90109300, lng: -6.77616860 };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 10,
                center: BJ
            });

            for (var i = 0; i < markersData.length; i++) {
                var markerData = markersData[i];
                var latLng = new google.maps.LatLng(markerData.lat, markerData.lng);
                new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: markerData.name
                });
            }
        }
    </script>
</head>

<body data-spy="scroll" data-target=".mainmenu-area">
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

    <header class="site-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <h1 class="white-color">Our Map</h1>
                    <ul class="breadcrumb">
                        <li><a href="index.html">Home</a></li>
                        <li>Map</li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <div id="map" style="height: 500px;"></div>



    <!-- Footer -->
    <footer class="footer-area" id="contact_page">
        <div class="section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="page-title text-center">
                            <h5 class="title">Contact US</h5>
                            <h3 class="dark-color">Find Us By Below Details</h3>
                            <div class="space-60"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="footer-box">
                            <div class="box-icon">
                                <span class="lnr lnr-map-marker"></span>
                            </div>
                            <p>Location 1 <br /> Location 2</p>
                        </div>
                        <div class="space-30 hidden visible-xs"></div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="footer-box">
                            <div class="box-icon">
                                <span class="lnr lnr-phone-handset"></span>
                            </div>
                            <p>+000000000 <br /> +00000000</p>
                        </div>
                        <div class="space-30 hidden visible-xs"></div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="footer-box">
                            <div class="box-icon">
                                <span class="lnr lnr-envelope"></span>
                            </div>
                            <p>yourmail@gmail.com <br /> your2email@gmail.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-md-5">
                        <span>Copyright &copy; All rights reserved | This Application is made by <a href="" target="_blank">Adil&Ikram</a></span>
                        <div class="space-30 hidden visible-xs"></div>
                    </div>
                  
                </div>
            </div>
        </div>
        <!-- Footer Bottom End -->
    </footer>


    <!-- Vendor JS -->
    <!-- Include your vendor JS files here -->

    <!-- Main active JS -->
    <script src="js/main.js"></script>
</body>

</html>
