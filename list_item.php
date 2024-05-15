<?php
session_start();

// Page is only accessible after valid login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit(); // Ensure script execution stops after redirection
}

// Establish connection to MySQL Database
require_once("database.php");

$errors = array(); // Define $errors array outside of the if(isset($_POST["list-item"])) block

if (isset($_POST["list-item"])) {
    // Retrieve item details from the form
    $title = $_POST["title"];
    $description = $_POST["description"];
    $category = $_POST["category"];
    $price = $_POST["price"];

    $username = $_SESSION["user"];

    date_default_timezone_set('America/Los_Angeles');
    $today = date("Y-m-d");

    // Error check for empty fields
    if (empty($title) || empty($description) || empty($category) || empty($price)) {
        array_push($errors, "All fields are required");
    }

    // Error check for a valid price
    if ($price < 0) {
        array_push($errors, "Invalid Price!");
    }

    // Check if the user has already posted 2 items today
    $sql = "SELECT COUNT(*) AS num_items FROM items WHERE user_username = ? AND DATE(posted_at) = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $today);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $numItemsToday = $row['num_items'];

        mysqli_stmt_close($stmt);
    }

    if ($numItemsToday >= 2) {
        array_push($errors, "You have reached the daily posting limit. You can only post 2 items per day.");
    }

    if (count($errors) === 0) {
        // Insert submitted data into database
        $sql = "INSERT INTO items (title, description, category, price, user_username, posted_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssds", $title, $description, $category, $price, $username);
            mysqli_stmt_execute($stmt);

            echo "<div class='alert alert-success'>Item listed successfully.</div>";
            mysqli_stmt_close($stmt);
        } else {
            echo "<div class='alert alert-danger'>Error: Unable to list the item.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>List Item</title>
</head>

<body>
    <div class="container">
        <h1>Create Listing</h1>
        <?php
        // Display error messages if any
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <form action="list_item.php" method="post">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description" required></textarea>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" class="form-control" id="category" name="category" placeholder="Enter category" required>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" placeholder="Enter price" required>
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="List Item" name="list-item">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </form>
    </div>
</body>
</html>
