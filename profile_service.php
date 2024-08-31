<?php
session_start(); // Démarre la session

require("ServiceCR/connexion.php");

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["connect"]) || $_SESSION["connect"] !== true || !isset($_SESSION["id"])) {
    header("Location: login.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

// Récupère l'ID du client à partir de la session
$client_id = $_SESSION["id"];

// Récupère l'ID du service à partir de l'URL
if (isset($_GET['id'])) {
    $service_id = $_GET['id'];
} else {
    header('Location: 404.php'); // Redirige vers une page d'erreur si l'ID du service n'est pas fourni dans l'URL
    exit;
}



// Retrieve the service data from the database
$query = "SELECT * FROM services WHERE id_Services = '$service_id'";
$result = mysqli_query($con, $query);

// Check if the service exists
if (mysqli_num_rows($result) > 0) {
    $service_data = mysqli_fetch_assoc($result);
} else {
    header('Location: 404.php'); // Redirect to a 404 page if the service doesn't exist
    exit;
}

// Retrieve the specialties for the service
$query = "SELECT Specialite FROM clinique WHERE id_Services = '$service_id'";
$result = mysqli_query($con, $query);
$specialties = array();
while ($row = mysqli_fetch_assoc($result)) {
    $specialties[] = $row['Specialite'];
}

// Retrieve the creator information for the service
$query = "SELECT adresse_Createur FROM createur_SRV WHERE id_Createur = '" . $service_data['id_CreateurSRV'] . "'";
$result = mysqli_query($con, $query);
$creator_data = mysqli_fetch_assoc($result);


// Insertion de la demande dans la base de données si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitreq'])) {
    $date = date('Y-m-d H:i:s');
    $query = "INSERT INTO demande (objet_demander, etat, date, fichier, id_Client, id_Service) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssssii", $_POST['objet_demander'], $_POST['etat'], $date, $_POST['fichier'], $client_id, $service_id);
    mysqli_stmt_execute($stmt);
}

// envoyer le message  au mail si la méthode de requête est POST

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitmes'])) {

// Récupérer les données du formulaire
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

// Définir l'adresse email du destinataire
$to = 'adilchagri@gmail.com';

// Définir l'objet de l'email
$subject = 'Nouveau message de ' . $name;

// Construire le corps de l'email
$body = "Bonjour,\n\n";
$body .= "Vous avez reçu un nouveau message de $name ($email):\n\n";
$body .= "Message:\n$message\n\n";
$body .= "Cordialement,\n";
$body .= "Votre Site Web";

// En-têtes de l'email
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";

// Envoyer l'email
$mail_sent = mail($to, $subject, $body, $headers);

// Vérifier si l'email a été envoyé avec succès
if ($mail_sent) {
    echo "Votre message a été envoyé avec succès.";
} else {
    echo "Une erreur s'est produite lors de l'envoi du message.";
}
}



//comments part
// Vérifier si le formulaire a été soumis
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    
    // Assurez-vous que la connexion à la base de données est établie ($con est défini et valide)

    // Récupérer les valeurs soumises par le formulaire
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $designation = mysqli_real_escape_string($con, $_POST['designation']);
    $author = mysqli_real_escape_string($con, $_POST['author']);
    $service_id = mysqli_real_escape_string($con, $_POST['service_id']);

    // Préparer la requête d'insertion en utilisant des requêtes préparées pour éviter les injections SQL
    $sql = "INSERT INTO commentaire (titre, designation, auteur, id_Service) 
            VALUES (?, ?, ?, ?)";
    
    // Préparer la déclaration SQL
    $stmt = mysqli_prepare($con, $sql);
    
    // Vérifier si la préparation a réussi
    if ($stmt) {
        // Liaison des paramètres
        mysqli_stmt_bind_param($stmt, "sssi", $title, $designation, $author, $service_id);
        
        // Exécution de la déclaration
        if (mysqli_stmt_execute($stmt)) {
            echo "Comment added successfully.";
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }

        // Fermer la déclaration
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans&display=swap" rel="stylesheet"> <!-- https://fonts.google.com/specimen/Kumbh+Sans -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="css/magnific-popup.css"> <!-- https://dimsemenov.com/plugins/magnific-popup/ -->
    <link rel="stylesheet" href="css/bootstrap.min.css"> <!-- https://getbootstrap.com/ -->
    <link rel="stylesheet" href="css/profileService.css">
</head>
<body>   
     
    <div class="container-fluid">
        <div class="row">
            <!-- Leftside bar -->
            <div id="tm-sidebar" class="tm-sidebar"> 
                <nav class="tm-nav">
                    <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <div class="tm-brand-box">
                            <h1 class="tm-brand"><?php echo $service_data['nom']; ?></h1>
                        </div>                
                        <ul id="tm-main-nav">
                            <li class="nav-item">                                
                                <a href="#home" class="nav-link current">
                                    <div class="triangle-right"></div>
                                    Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#gallery" class="nav-link">
                                    <div class="triangle-right"></div>
                                    Posts
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#contact" class="nav-link">
                                    <div class="triangle-right"></div>
                                    Contact
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php" class="nav-link external" target="_parent" rel="sponsored">
                                    <div class="triangle-right"></div>
                                    Back
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            
            <div class="tm-main">
                <!-- Home section -->
                <div class="tm-section-wrap">
                    <section id="home" class="tm-section">
                        <h2 class="tm-text-primary">Service Profile</h2>
                        <hr class="mb-5">
                        <div class="row">
                            <div class="col-lg-6 tm-col-home mb-4">
                                <div class="tm-text-container">
                                    <h3>Service Information</h3>
                                    <p>Service Name: <?php echo $service_data['nom']; ?></p>
                                    <p>Address: <?php echo $service_data['adress']; ?></p>
                                    <p>Email: <?php echo $service_data['email']; ?></p>
                                    <p>Phone: <?php echo $service_data['phone']; ?></p>
                                    <p>Horraire: <?php echo $service_data['horraire']; ?></p>
                                </div>
                            </div>
                            <div class="col-lg-6 tm-col-home mb-4">
                                <div class="tm-text-container">
                                    <h3>Specialties</h3>
                                    <ul>
                                        <?php foreach ($specialties as $specialty) { ?>
                                        <li><?php echo $specialty; ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                               
                                <div class="media mb-4">
                                    <div class="media-body">
                                        <h5 class="mt-0">Creator Information</h5>
                                        <p>Address: <?php echo $creator_data['adresse']; ?></p>
                                    </div>
                                </div>
                            
                            </div>
                        </div>
                        <hr class="tm-hr-short mb-5">
                        <div class="row tm-row-home">
                            
                        <div class="col-lg-6">
    <div class="media mb-4">
        <div class="media-body">
            <h5 class="mt-0">Submit a Request</h5>
            <form method="post" action="" id="requestForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="objet_demander">Object:</label>
                    <input type="text" id="objet_demander" name="objet_demander" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="etat">State:</label>
                    <input type="text" id="etat" name="etat" class="form-control" required>
                </div>
                <div class="form-group" id="fileGroup" style="display: none;">
                    <label for="fichier">File:</label>
                    <input type="file" id="fichier" name="fichier" class="form-control">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="addFile">
                    <label class="form-check-label" for="addFile">Add File</label>
                </div>
                <button type="submit" class="btn btn-primary" name="submitreq">Submit</button>
            </form>
        </div>
    </div>
</div>



                        </div>
                    </section>
                </div>
   <!-- Gallery section -->
   <div class="tm-section-wrap">
        <section id="gallery" class="tm-section">
            <!-- Carousel markup -->
            <div id="postCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $query = "SELECT * FROM post WHERE id_Createur = '$service_id'";
                    $result = mysqli_query($con, $query);
                    $active = ' active';
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '
                        <div class="carousel-item' . $active . '">
                            <!-- Card markup for each post -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">' . $row['title'] . '</h5>
                                    <img src="ServiceCR/images/carousel/'. $row['image'] . '</img>
                                    <p class="card-text">' . $row['description'] . '</p>
                                    <a href="#" class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </div>';
                        $active = '';
                    }
                    ?>
                </div>
                <!-- Carousel controls -->
                <a class="carousel-control-prev" href="#postCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#postCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>
    </div>

<!-- Contact section -->
            <div class="tm-section-wrap">
                <div class="tm-parallax" data-parallax="scroll" data-image-src="img/img-03.jpg"></div>
                <section id="contact" class="tm-section">
                    <h2 class="tm-text-primary">Contact Us</h2>
                    <hr class="mb-5">
                    <div class="row">
                        <div class="col-md-6">
                        <form action="" method="post">
                                <div class="form-group">
                                    <label for="title">Title:</label>
                                    <input type="text" id="title" name="title" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="designation">Designation:</label>
                                    <textarea id="designation" name="designation" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="author">Author:</label>
                                    <input type="text" id="author" name="author" class="form-control" required>
                                </div>
                                <!-- Hidden field to store the service ID -->
                                <input type="hidden" id="service_id" name="service_id" value="<?php echo htmlspecialchars($service_id); ?>">
                                <button type="submit" class="btn btn-primary" name="submit_comment">Submit</button>
                            </form>

                        </div>
                        <div class="col-md-6">
                            <h4>Our Address</h4>
                            <p>123 Main Street, Anytown, USA</p>
                            <h4>Email</h4>
                            <p>info@yourdomain.com</p>
                            <h4>Phone</h4>
                            <p>(123) 456-7890</p>
                        </div>
                    </div>
                </section>
            </div>
            </div>
        </div>        
    </div>

    <script>
    document.getElementById('addFile').addEventListener('change', function() {
        var fileGroup = document.getElementById('fileGroup');
        if (this.checked) {
            fileGroup.style.display = 'block';
        } else {
            fileGroup.style.display = 'none';
        }
    });
</script>


    <!-- Load JS -->
    <script src="js/jquery-3.6.0.min.js"></script> <!-- https://jquery.com/download/ -->
    <script src="js/parallax.min.js"></script> <!-- https://pixelcog.github.io/parallax.js/ -->
    <script src="js/imagesloaded.pkgd.min.js"></script> <!-- https://imagesloaded.desandro.com/ -->
    <script src="js/masonry.pkgd.min.js"></script> <!-- https://masonry.desandro.com/ -->
    <script src="js/jquery.magnific-popup.min.js"></script> <!-- https://dimsemenov.com/plugins/magnific-popup/ -->
    <script src="js/bootstrap.bundle.min.js"></script> <!-- https://getbootstrap.com/ -->
    <script src="js/profileService.js"></script>
</body>
</html>
