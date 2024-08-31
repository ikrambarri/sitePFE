<?php
require_once("connexion.php");

//Check if the user is logged in and has a creator role in the session
if (!isset($_SESSION["connect"]) || $_SESSION["connect"] !== true || !isset($_SESSION["id"])) {
    // Redirect to login page or show an error message
    header("Location: ../login.php");
    exit();
}

// Connect to the database
require("connexion.php");

// Get the creator's ID from the session
$creator_id = $_SESSION["id"];


if (isset($_POST['add_post'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $designation = mysqli_real_escape_string($con, $_POST['designation']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp_image = $_FILES['image']['tmp_name'];
        $upload_dir = "images\carousel"; // Directory to save the uploaded image

        // Create the uploads directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Générer le nom de l'image
        $query = "SELECT MAX(id_Post) as max_id FROM Post";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        $post_id = $row['max_id'] + 1;
        $image_name = "image" . $post_id . ".jpeg"; // Generate the new image name
        $target_file = $upload_dir . $image_name; // Concatenate the new image name with the upload directory

        // Check if image file is a valid image
        $check = getimagesize($tmp_image);
        if ($check !== false) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($tmp_image, $target_file)) {
                // Get the creator's ID from the session variable
                $creator_id = $_SESSION["id"];

                // Insert post data into the database
                $query = "INSERT INTO Post (title, description, id_Createur, image) 
                          VALUES ('$title', '$designation', '$creator_id', '$image_name')";
                if (mysqli_query($con, $query)) {
                    echo "<script>
                        alert('Post added successfully.');
                        window.location.href='index.php'; // Redirection vers index.php
                    </script>";
                } else {
                    echo '<p class="text-danger">Error adding post: ' . mysqli_error($con) . '</p>';
                }
            } else {
                echo '<p class="text-danger">Error uploading image.</p>';
            }
        } else {
            echo '<p class="text-danger">File is not a valid image.</p>';
        }
    } else {
        echo '<p class="text-danger">Error uploading file.</p>';
    }
}
?>