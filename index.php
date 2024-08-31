<?php
session_start(); // Démarrer la session

require("connexion.php");
include 'statistics.php';

// Vérifier si l'utilisateur est connecté et est un créateur de service
if (isset($_SESSION['id'])) {
    $id_CreateurSRV = $_SESSION['id'];

    // Récupérer les données du créateur
    $query_creator = "SELECT * FROM createur_srv WHERE id_Utilisateur = ?";
    $query_util = "SELECT * FROM utilisateur WHERE id_Utilisateur = ?";
    
    // Utilisation des requêtes préparées pour la sécurité
    $stmt_creator = $con->prepare($query_creator);
    $stmt_util = $con->prepare($query_util);
    
    $stmt_creator->bind_param('i', $id_CreateurSRV);
    $stmt_util->bind_param('i', $id_CreateurSRV);
    
    $stmt_util->execute();
    $result_util = $stmt_util->get_result();
    
    $stmt_creator->execute();
    $result_creator = $stmt_creator->get_result();

    // Chercher le nom du créateur
    $util = $result_util->fetch_assoc();

    if ($result_creator->num_rows > 0) {
        $creator_data = $result_creator->fetch_assoc();
    } else {
        // Rediriger si l'utilisateur n'est pas un créateur de service
        header("Location: client_page.php?message=This page is for service creators only. Please make a request to become a service creator.");
        exit();
    }
} else {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../login.php");
    exit();
}

// Traitement du formulaire d'ajout de post
if (isset($_POST['submit_post'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $image = $_FILES['image']['name'];
    $target = "images/carousel/" . basename($image);
    $id_Createur = $_SESSION['id'];

    // Déplacer l'image téléchargée dans le dossier des images
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Utilisation d'une requête préparée pour l'insertion
        $query_insert = "INSERT INTO Post (title, description, image, id_Createur) VALUES (?, ?, ?, ?)";
        $stmt_insert = $con->prepare($query_insert);
        $stmt_insert->bind_param('sssi', $title, $description, $image, $id_Createur);
        
        if ($stmt_insert->execute()) {
            // Rediriger vers la page d'index après l'ajout du post
            header('Location: index.php');
            exit;
        } else {
            echo "Error: " . $stmt_insert->error;
        }
    } else {
        echo "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>E-city Dashboard </title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <link rel="stylesheet" href="style.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="" />
</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.html"><img src="images/" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="index.html"><img src="images/" alt="logo"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-success">
                    <i class="ti-info-alt mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">Application Error</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    Just now
                  </p>
                </div>
              </a>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-warning">
                    <i class="ti-settings mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">Settings</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    Private message
                  </p>
                </div>
              </a>
              <a class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                  <div class="preview-icon bg-info">
                    <i class="ti-user mx-0"></i>
                  </div>
                </div>
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">New user registration</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    2 days ago
                  </p>
                </div>
              </a>
            </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="images/faces/face28.jpg" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item">
                <i class="ti-settings text-primary"></i>
                Settings
              </a>
              <a class="dropdown-item" href="logout.php">
                <i class="ti-power-off text-primary"></i>
                Logout
              </a>
            </div>
          </li>
          <li class="nav-item nav-settings d-none d-lg-flex">
            <a class="nav-link" href="">
              <i class="icon-ellipsis"></i>
            </a>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <div id="right-sidebar" class="settings-panel">
        <i class="settings-close ti-close"></i>
        <ul class="nav nav-tabs border-top" id="setting-panel" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">TO DO LIST</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="add-items d-flex px-3 mb-0">
              <form class="form w-100">
                <div class="form-group d-flex">
                  <input type="text" class="form-control todo-list-input" placeholder="Add To-do">
                  <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task">Add</button>
                </div>
              </form>
            </div>
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Team review meeting at 3.00 PM
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Prepare for presentation
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Resolve all the low priority tickets due today
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li class="completed">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox" checked>
                      Schedule meeting for next week
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li class="completed">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox" checked>
                      Project review
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
              </ul>
            </div>
            <h4 class="px-3 text-muted mt-5 font-weight-light mb-0">Events</h4>
            <div class="events pt-4 px-3">
              <div class="wrapper d-flex mb-2">
                <i class="ti-control-record text-primary mr-2"></i>
                <span>Feb 11 2018</span>
              </div>
              <p class="mb-0 font-weight-thin text-gray">Creating component page build a js</p>
              <p class="text-gray mb-0">The total number of sessions</p>
            </div>
            <div class="events pt-4 px-3">
              <div class="wrapper d-flex mb-2">
                <i class="ti-control-record text-primary mr-2"></i>
                <span>Feb 7 2018</span>
              </div>
              <p class="mb-0 font-weight-thin text-gray">Meeting with Alisa</p>
              <p class="text-gray mb-0 ">Call Sarah Graves</p>
            </div>
          </div>
          <!-- To do section tab ends -->

        </div>
      </div>
      <!-- sidebar -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="index.html">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
        </ul>
      </nav>
      
      <div class="main-panel">
        <div class="content-wrapper">
   <!-- Welcome Part -->
   <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Welcome <?=$util['nom']?></h3>
                  <h6 class="font-weight-normal mb-0">All systems are running smoothly! You have <span class="text-primary">
                    <?php
                      $today = date("Y-m-d");
                      $query = "SELECT COUNT(*) as num_demands 
                                FROM demande d 
                                JOIN services s ON d.id_Service = s.id_Services 
                                WHERE DATE(d.date) = '$today' AND s.id_CreateurSRV = $id_CreateurSRV";
                      $result = $con->query($query);
                      $row = $result->fetch_assoc();
                      echo $row['num_demands']. " new demands today!";
                   ?>
                  </span></h6>
                </div>
              </div>
            </div>
          </div>
          <!-- End Welcome Part -->
          <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card tale-bg">
                <div class="card-people mt-auto">
                  <img src="images/dashboard/people.svg" alt="people">
                  <div class="weather-info">
                    <div class="d-flex">
                    <div>
                                <h2 class="mb-0 font-weight-normal" id="temperature"><i class="icon-sun mr-2"></i><span id="temp-value"></span><sup>C</sup></h2>
                            </div>
                            <div class="ml-2">
                                <h4 class="location font-weight-normal" id="city"></h4>
                                <h6 class="font-weight-normal" id="country"></h6>
                            </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          

            <div class="col-md-6 grid-margin transparent">
              <div class="row">
                <div class="col-md-6 mb-4 stretch-card transparent">
                  <div class="card card-tale">
                    <div class="card-body">
                      <p class="mb-4">Total Demands</p>
                      <p class="fs-30 mb-2"><?= $statistics['total_demands']?></p>
                      <p><?= $statistics['demand_percentage']?>% (30 days)</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                  <div class="card card-dark-blue">
                    <div class="card-body">
                      <p class="mb-4">Total Posts</p>
                      <p class="fs-30 mb-2"><?= $statistics['total_posts']?></p>
                      <p><?= $statistics['post_percentage']?>% (30 days)</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                  <div class="card card-light-blue">
                    <div class="card-body">
                      <p class="mb-4">Total Comments</p>
                      <p class="fs-30 mb-2"><?= $statistics['total_comments']?></p>
                      <p><?= $statistics['comment_percentage']?>% (30 days)</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                  <div class="card card-light-danger">
                    <div class="card-body">
                      <p class="mb-4">Average Demand Response Time</p>
                      <p class="fs-30 mb-2"><?= $statistics['avg_demand_response_time']?> minutes</p>
                      <p><?= $statistics['avg_demand_response_time_percentage']?>% (30 days)</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <!--End of welcom part-->
          <!-- Demande part -->
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card position-relative">
                <div class="card-body">
                  <p class="card-title">Demandes</p>
                  <form method="post" action="">
                    <div class="form-group">
                      <label for="date">Select Date:</label>
                      <input type="date" class="form-control" id="date" name="date">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </form>
                  <?php
// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Get the selected date from the form
$selectedDate = isset($_POST['date']) ? $_POST['date'] : '';


// Query to get the demands of the creator for the selected date
$sql = "SELECT d.* 
        FROM demande d 
        JOIN services s ON d.id_Service = s.id_Services 
        WHERE DATE(d.date) = '$selectedDate' AND s.id_CreateurSRV = $id_CreateurSRV";

$result = $con->query($sql);

if ($result->num_rows > 0) {
?>
    <div id="demandesCarousel" class="carousel carousel-dark slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $firstItem = true;
            while ($row = $result->fetch_assoc()) {
                $date = $row['date'];
                $objet_demander = $row['objet_demander'];
                $etat = $row['etat'];
                $id_Service = $row['id_Service'];
                $autre = $row['autre'];
                $fichier = $row['fichier'];
                $id_Client = $row['id_Client'];
            ?>
                <div class="carousel-item <?= $firstItem ? 'active' : '' ?>">
                    <div class="row">
                        <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                            <div class="ml-xl-4 mt-3">
                                <p class="card-title">Demand Details</p>
                                <h5 class="text-primary"><?= $objet_demander ?></h5>
                                <h5 class="font-weight-500 mb-xl-4 text-primary">Service ID: <?= $id_Service ?></h5>
                                <p class="mb-2 mb-xl-0">State: <?= $etat ?></p>
                                <p class="mb-2 mb-xl-0">Date: <?= $date ?></p>
                                <p class="mb-2 mb-xl-0">Client ID: <?= $id_Client ?></p>
                                <p class="mb-2 mb-xl-0">Other: <?= $autre ?></p>
                                <p class="mb-2 mb-xl-0">Files: 
                                    <?= $fichier ?>
                                    <a href="demandesFiles/<?= $fichier ?>" download="<?= $fichier ?>">Download</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                $firstItem = false;
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#demandesCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#demandesCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
<?php
} else {
    echo "<p>No demands found for the selected date.</p>";
}
?>

                </div>
              </div>
            </div>
          </div>
          <!-- End demande part -->
            <!--comments part-->
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                  <div class="card position-relative">
                    <div class="card-body">
                      <p class="card-title">Comments</p>
                      <?php
                      // Retrieve comments from the database for creator with ID 1
                      $query = "SELECT * FROM Commentaire WHERE id_Service IN (SELECT id_Services FROM services WHERE id_CreateurSRV = $id_CreateurSRV)";
                      $result = mysqli_query($con, $query);

                      // Display the first three comments
                      $counter = 0;
                      while ($row = mysqli_fetch_assoc($result)) {
                        $id_Commentaire = $row['id_Commentaire'];
                        $titre = $row['titre'];
                        $designation = $row['designation'];
                        $auteur = $row['auteur'];

                        if ($counter < 3) {
                    ?>
                      <!-- Display each comment -->
                      <div class="comment">
                        <div class="comment-avatar">
                          <img src="images/faces/face1.jpg" alt="<?php echo $auteur;?>" class="rounded-circle" >
                        </div>
                        <div class="comment-body">
                          <h5><?php echo $auteur;?></h5>
                          <p><?php echo $designation;?></p>
                          <span class="comment-date"><?php echo date("F j, Y");?></span>
                        </div>
                      </div>
                      <?php
                          $counter++;
                        } else {
                          break; // Exit the loop after displaying the first three comments
                        }
                      }

                      // Check if there are more comments to display
                      $remaining_comments = mysqli_num_rows($result) - $counter;
                      if ($remaining_comments > 0) {
                      ?>
                      <!-- Button to show more comments -->
                      <button class="btn btn-primary mt-3" id="showMoreComments">Show More</button>

                      <!-- Hidden div for additional comments -->
                      <div class="additional-comments" style="display: none;">
                        <?php
                        // Reset the result pointer to display remaining comments
                        mysqli_data_seek($result, $counter);
                        while ($row = mysqli_fetch_assoc($result)) {
                          $id_Commentaire = $row['id_Commentaire'];
                          $titre = $row['titre'];
                          $designation = $row['designation'];
                          $auteur = $row['auteur'];
                        ?>
                        <!-- Display each additional comment -->
                        <div class="comment">
                          <div class="comment-avatar">
                            <img src="images/dashboard/people.png" alt="<?php echo $auteur;?>" class="rounded-circle">
                          </div>
                          <div class="comment-body">
                            <h5><?php echo $auteur;?></h5>
                            <p><?php echo $designation;?></p>
                            <span class="comment-date"><?php echo date("F j, Y");?></span>
                          </div>
                        </div>
                        <?php } ?>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>

              <!--End comments part -->
 <!-- Posts part -->
 <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card position-relative">
        <div class="card-body">
          <p class="card-title">Posts</p>
          <?php
          // Check connection
          if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
          }

          // Retrieve posts from the database
          $query = "SELECT * FROM Post WHERE id_Createur = $id_CreateurSRV";
          $result = mysqli_query($con, $query);

          // Check if the query was successful
          if ($result) {
            // Check if there are any posts
            if (mysqli_num_rows($result) > 0) {
          ?>
          <div id="postsCarousel" class="carousel carousel-dark slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <?php
              $firstItem = true;
              while ($row = mysqli_fetch_assoc($result)) {
              ?>
              <div class="carousel-item <?= $firstItem ? 'active' : '' ?>">
                <div class="row">
                  <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                    <div class="ml-xl-4 mt-3">
                      <div class="mb-2">
                        <a href="#" onclick="return confirmDelete(<?= $row['id_Post'] ?>)"><img src="images/delete.png" style="width: 2rem;" alt="Delete"></a>
                        <a href="updatePost.php?id_post=<?= $row['id_Post'] ?>"><img src="images/edit.png" style="width: 2rem;" alt="Edit"></a>
                      </div>
                      <img src="images/carousel/<?= $row['image']?>" class="card-img-top" alt="..." style="width: 200px; height: 120px;">
                      <div class="card-body">
                        <h5 class="card-title"><?= $row['title']?></h5>
                        <p class="card-text"><?= $row['description']?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
                $firstItem = false;
              }
              ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#postsCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#postsCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
          <?php
            } else {
              echo "<p>No posts found.</p>";
            }
          } else {
            echo "<p>Error: " . mysqli_error($con) . "</p>";
          }
          ?>
          <!-- Button to trigger the modal popup -->
          <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addPostModal">Ajouter</button>

          <!-- Modal for adding a new post -->
          <div class="modal fade" id="addPostModal" tabindex="-1" aria-labelledby="addPostModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addPostModalLabel">Ajouter un nouveau post</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form action="index.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                      <label for="title" class="form-label">Titre</label>
                      <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                      <label for="description" class="form-label">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                      <label for="image" class="form-label">Image</label>
                      <input type="file" class="form-control" id="image" name="image" required>
                    </div>
                    <button type="submit" name="submit_post" class="btn btn-primary">Ajouter</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


        <!-- content-wrapper ends -->


        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021.  Premium <a href="https://www.bootstrapdash.com/" target="_blank">Service admin template</a> from ADil & Ikram. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->


  <script>
  // JavaScript to toggle visibility of additional comments
  document.getElementById('showMoreComments').addEventListener('click', function() {
    document.querySelector('.additional-comments').style.display = 'block';
    this.style.display = 'none'; // Hide the "Show More" button
  });
</script>

    <!-- Confirmation de suppression du post -->
<script>
    function confirmDelete(id_post) {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce post?")) {
            window.location.href = "deletePost.php?id_post=" + id_post;
        }
        return false;
    }
</script>


<!--weather script-->
 <script>
        fetch('weather.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('city').textContent = data.city;
                document.getElementById('country').textContent = data.country;
                document.getElementById('temp-value').textContent = Math.round(data.temperature - 273.15); // convert Kelvin to Celsius
            });
    </script>











  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="js/dataTables.select.min.js"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="js/dashboard.js"></script>
  <script src="js/Chart.roundedBarCharts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
  <!-- End custom js for this page-->
</body>

</html>




