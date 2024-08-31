<?php
// Connect to the database
$con = mysqli_connect("localhost", "root", "", "ecity");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Retrieve posts from the database
$query = "SELECT * FROM posts";
$result = mysqli_query($con, $query);

// Check if there are any posts
if (mysqli_num_rows($result) > 0) {
    // Display the carousel
    echo '<div id="carouselExampleControls" class="carousel carousel-dark slide" data-bs-ride="carousel">';
    echo '<div class="carousel-inner">';

    // Loop through the posts and display them in the carousel
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="carousel-item ' . ($row['id'] == 1 ? 'active' : '') . '">';
        echo '<div class="card-wrapper container-sm d-flex  justify-content-around">';
        echo '<div class="card" style="width: 18rem;">';
        echo '<img src="uploads/' . $row['image'] . '" class="card-img-top" alt="...">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $row['title'] . '</h5>';
        echo '<p class="card-text">' . $row['designation'] . '</p>';
        echo '<span><a href=""><img src="images/edit.png" style="width: 2rem;" alt=""></a>';
        echo '<a href=""><img src="images/delete.png" style="width: 2rem;" alt=""></a></span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
    echo '<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">';
    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
    echo '<span class="visually-hidden">Previous</span>';
    echo '</button>';
    echo '<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">';
    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
    echo '<span class="visually-hidden">Next</span>';
    echo '</button>';
    echo '</div>';
} else {
    echo '<p>No posts found.</p>';
}

// Close the database connection
mysqli_close($con);
?>