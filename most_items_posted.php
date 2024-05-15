<?php
    session_start();

    // Page is only accessible after valid login
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit(); // Ensure script execution stops after redirection
    }

    // Include database connection
    require_once("database.php");

    $result = null; // Initialize $result variable

    // Check if a search query is submitted
    if(isset($_POST['search'])) {
        $search_date = $_POST['date'];

        $sql = "SELECT users.username, COUNT(items.ID) AS num_items_posted
        FROM users users
        JOIN items ON users.username = items.user_username
        WHERE DATE(items.posted_at) = ?
        GROUP BY users.username
        HAVING COUNT(items.ID) = (
            SELECT COUNT(ID)
            FROM items
            WHERE DATE(posted_at) = ?
            GROUP BY user_username
            ORDER BY COUNT(ID) DESC
            LIMIT 1
        )";

        $stmt = mysqli_stmt_init($conn);
        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

        if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "ss", $search_date, $search_date);
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
    <title>Most Items Posted</title>
</head>

<body>
    <div class="container">
        <h1>Search for User with Most Posts on a Day</h1>

        <form action="most_items_posted.php" method="post">
            <div class="form-group">
                    <label for="title">User(s) with the most post this day:</label>
                    <input type="date" class="form-control" id="date" name="date">
            </div>

            <div class="button-group">
                <input type="submit" class="btn btn-primary" value="Search" name="search">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div> 
        </form>

        <?php if ($result) { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Number of Posts on Date</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Loop through the retrieved listings and display them in the table
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>".$row['username']."</td>";
                            echo "<td>".$row['num_items_posted']."</td>";
                            echo "<td>".$search_date."</td>";
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