<?php
    session_start();
    // Page is only accessible after valid login
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
    }

    // Include database connection
    require_once("database.php");

    $result = null; // Initialize $result variable

    // Check if a search query is submitted
    if(isset($_POST['search'])) {
        $search_query = $_POST['username'];

        $sql = "SELECT *
        FROM items
        JOIN (
            SELECT item_id
            FROM reviews
            GROUP BY item_id
            HAVING SUM(CASE WHEN rating NOT IN ('excellent', 'good') THEN 1 ELSE 0 END) = 0
        ) AS max_reviews ON items.ID = max_reviews.item_id
        WHERE items.user_username = ?";

        $stmt = mysqli_stmt_init($conn);
        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

        if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "s", $search_query);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt); // Close the prepared statement
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
    <title>Best Items by User</title>
</head>

<body>
    <div class="container">
        <h1>Search a User's Best Items</h1>
        <form action="best_items_by_user.php" method="post">
            <div class="form-group">
                <label for="username">Search by Username:</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Search" name="search">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>  
        </form>
    </div>

    <div class="container">
        <?php if ($result) { ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Posted By</th>
                            <th>Posted at</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Loop through the retrieved listings and display them in the table
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>".$row['title']."</td>";
                                echo "<td>".$row['category']."</td>";
                                echo "<td>".$row['price']."</td>";
                                echo "<td>".$row["user_username"]."</td>";
                                echo "<td>".$row['posted_at']."</td>";
                                echo "<td><a href='item_details.php?id=".$row['ID']."' class='btn btn-secondary'>View Details</a></td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            <?php } else {
                echo "<p>No items found.</p>";
            } ?>
    </div>
</body>
</html>