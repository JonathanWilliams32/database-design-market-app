<?php
    session_start();
    // Page is only accessible after valid login
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
    }

     // Include database connection
     require_once("database.php");

    // Query to retrieve user's listings
    $username = $_SESSION["user"];
    $sql = "SELECT * FROM items WHERE user_username = ?";
    $stmt = mysqli_stmt_init($conn);
    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

    if ($prepareStmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt); // Close the prepared statement
    }

     $firstName = isset($_SESSION["firstName"]) ? $_SESSION["firstName"] : "N/A";
     $lastName = isset($_SESSION["lastName"]) ? $_SESSION["lastName"] : "N/A";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Home Page</title>
</head>

<body>
    <div class="container">
        <h2>Username: <?php echo $_SESSION["user"]; ?></h2>
        <div class="button-group">
            <a href="list_item.php" class="btn btn-primary">List Item</a>
            <a href="search_items.php" class="btn btn-secondary">The Market</a>
            <a href="best_items_by_user.php" class="btn btn-secondary">Best Items</a>
            <a href="most_items_posted.php" class="btn btn-secondary">Most Posts</a>
            <a href="users_search.php" class="btn btn-secondary">Search Users</a>
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Your Listings</h2>
        <?php if ($result) { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Date Posted</th>
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
                            echo "<td>".$row['posted_at']."</td>";
                            echo "<td><a href='item_details.php?id=".$row['ID']."' class='btn btn-secondary'>View Details</a></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        <?php } else {
            echo "<p>No listings found.</p>";
        } ?>
    </div>
</body>

</html>