<?php
    session_start();
    // Page is only accessible after valid login
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
    }

    // Include database connection
    require_once("database.php");

    // Check if item ID is provided in the URL parameter
    if(isset($_GET['id'])) {
        // Sanitize the input to prevent SQL injection
        $item_id = mysqli_real_escape_string($conn, $_GET['id']);

        // Query to retrieve item details from the database
        $sql = "SELECT * FROM items WHERE ID = '$item_id'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0) {
            // Item found, display its details
            $item = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Item Details</title>
</head>

<body>
    <div class="container">
        <h1>Item Details</h1>
        <p>Title: <?php echo $item['title']; ?></p>
        <p>Description: <?php echo $item['description']; ?></p>
        <p>Category: <?php echo $item['category']; ?></p>
        <p>Price: <?php echo $item['price']; ?> Galactic Credits</p>
        <p>Date Posted: <?php echo $item['posted_at']; ?></p>

        <div class="button-group">
            <a href="search_items.php" class="btn btn-secondary">Back to Search</a>
            <a href="best_items_by_user.php" class="btn btn-secondary">Back to Best Items</a>
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>

    <div class="container">
        <h2>Reviews</h2>
        <?php
            // Query to retrieve reviews associated with the item
            $review_sql = "SELECT * FROM reviews WHERE item_id = '$item_id'";
            $review_result = mysqli_query($conn, $review_sql);

            if(mysqli_num_rows($review_result) > 0) {
                // Display reviews if available
                while($review = mysqli_fetch_assoc($review_result)) {
                    echo "<p>Rating: " . $review['rating'] . "</p>";
                    echo "<p>Comment: " . $review['comment'] . "</p>";
                    echo "<p>Reviewer: " . $review['username'] . "</p>";
                }
            } else {
                // No reviews yet
                echo "<p>No reviews yet.</p>";
            }
        ?>
    </div>

    <?php if($item['user_username'] != $_SESSION['user']) { ?>
    <div class="container">
        <h2>Leave a Review</h2>
        <?php
            if(isset($_GET['error'])) {
                $error = $_GET['error'];
                switch ($error) {
                    case 'review_limit':
                        echo "<div class='alert alert-danger'>You have reached the maximum limit of 3 reviews per day.</div>";
                        break;
                    case 'already_reviewed':
                        echo "<div class='alert alert-danger'>You have already reviewed this item.</div>";
                        break;
                    // Other error cases can be handled here
                    default:
                        echo "<div class='alert alert-danger'>An error occurred.</div>";
                        break;
                }
            }            
        ?>

        <form action="submit_review.php" method="post">
            <div class="form-group">
                <label for="rating">Rating:</label>
                <select name="rating" id="rating">
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            <div class="form-group">
                <label for="comment">Review Comment:</label>
                <textarea name="comment" id="comment" rows="4" cols="50"></textarea>
            </div>
            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
            <input type="submit" class="btn btn-primary" value="Submit Review">
        </form>
    </div>
    <?php } ?>
</body>
</html>

<?php
        } else {
            // Item not found, display an error message
            echo "Item not found.";
        }
    } else {
        // Item ID not provided in the URL parameter, display an error message
        echo "Item ID is required.";
    }
?>
