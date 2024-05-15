<?php
session_start();

// Include database connection
require_once("database.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];
    $item_id = $_POST["item_id"];
    $username = $_SESSION["user"];

    // Validate and sanitize input
    $rating = mysqli_real_escape_string($conn, $rating);
    $comment = mysqli_real_escape_string($conn, $comment);
    $item_id = mysqli_real_escape_string($conn, $item_id);
    $username = mysqli_real_escape_string($conn, $username);

    // Check user authentication
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit();
    }

    // Check review limit
    //$current_date = date("Y-m-d");
    //$check_review_limit_query = "SELECT COUNT(*) AS num_reviews FROM reviews WHERE DATE(reviewed_at) = '$current_date' AND username = '$username'";
    $check_review_limit_query = "SELECT COUNT(*) AS num_reviews FROM reviews WHERE DATE(reviewed_at) = CURDATE() AND username = '$username'";

    $check_review_limit_result = mysqli_query($conn, $check_review_limit_query);
    $row = mysqli_fetch_assoc($check_review_limit_result);
    $num_reviews_today = $row['num_reviews'];
    if ($num_reviews_today >= 3) {
        // User has reached the review limit for today
        // Redirect back to the item details page with an error message
        header("Location: item_details.php?id=$item_id&error=review_limit");
        exit();
    }

    // Check if user already reviewed this item
    $check_existing_review_query = "SELECT * FROM reviews WHERE item_id = '$item_id' AND username = '$username'";
    $existing_review_result = mysqli_query($conn, $check_existing_review_query);
    if (mysqli_num_rows($existing_review_result) > 0) {
        // User has already reviewed this item
        // Redirect back to the item details page with an error message
        header("Location: item_details.php?id=$item_id&error=already_reviewed");
        exit();
    }

    // Insert review
    $insert_review_query = "INSERT INTO reviews (rating, comment, item_id, username) VALUES ('$rating', '$comment', '$item_id', '$username')";

    // Execute the insertion query
    if (mysqli_query($conn, $insert_review_query)) {
        // Review successfully inserted
        // Redirect back to the item details page with a success message
        header("Location: item_details.php?id=$item_id&success=true");
        exit();
    } else {
        // Error inserting review
        // Redirect back to the item details page with an error message
        header("Location: item_details.php?id=$item_id&error=insert_failed");
        exit();
    }

} else {
    // Redirect to homepage if accessed directly without form submission
    header("Location: index.php");
    exit();
}
?>
