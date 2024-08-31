<?php
require("connexion.php");

if (isset($_GET['id_post'])) {
    // Récupérer l'ID du post à supprimer depuis l'URL
    $id_post = intval($_GET['id_post']);

    // Vérifier si l'ID du post est valide
    if ($id_post > 0) {
        // Supprimer le post de la base de données
        $deleteQuery = "DELETE FROM Post WHERE id_Post = $id_post";
        $deleteResult = mysqli_query($con, $deleteQuery);

        // Vérifier si la suppression a réussi
        if ($deleteResult) {
            echo "<script>
                alert('Le post a été supprimé avec succès.');
                window.location.href='index.php';
            </script>";
        } else {
            echo '<p>Erreur lors de la suppression du post : '. mysqli_error($con). '</p>';
        }
    } else {
        echo '<p>ID de post invalide.</p>';
    }

    // Fermer la connexion à la base de données
    mysqli_close($con);
} else {
    echo '<p>Aucun ID de post spécifié.</p>';
}
?>