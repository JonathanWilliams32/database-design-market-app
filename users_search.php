<?php
    session_start();
    // Page is only accessible after valid login
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
    }

    // Include database connection
    require_once("database.php");

    // Initialize $dropdown_result variable
    $dropdown_result = null;

    // Fetch usernames from the users table for dropdowns
    $dropdown_sql = "SELECT * FROM users";
    $dropdown_result = mysqli_query($conn, $dropdown_sql);

    $result = null; // Initialize $result variable

    // Check if the form is submitted to favorite a user
    if(isset($_POST['favorite'])) {
        $favorited_username = $_POST['favorite_username'];
        $user_username = $_SESSION['user'];

        // Check if the favorited user already exists in the favorites table
        $check_sql = "SELECT * FROM favorites WHERE user_username = ? AND favorite_username = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $check_sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $user_username, $favorited_username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $num_rows = mysqli_stmt_num_rows($stmt);
            mysqli_stmt_close($stmt);

            // If the favorited user is not already in favorites, insert the favorite relationship
            if ($num_rows == 0) {
                $insert_sql = "INSERT INTO favorites (user_username, favorite_username) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $insert_sql)) {
                    mysqli_stmt_bind_param($stmt, "ss", $user_username, $favorited_username);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    // Check if the form is submitted
    if(isset($_POST['search'])) {
        // Handle search query
        if (isset($_POST['username1']) && isset($_POST['username2'])) {
            $user1 = $_POST['username1'];
            $user2 = $_POST['username2'];

            // Retrieve favorites for User 1 and User 2
            $favorites_sql = "SELECT users.*
                FROM users
                JOIN favorites AS f1 ON users.username = f1.favorite_username
                JOIN favorites AS f2 ON f1.favorite_username = f2.favorite_username
                WHERE f1.user_username = ?
                AND f2.user_username = ?";
            
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $favorites_sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $user1, $user2);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
    elseif(isset($_POST['search-non-excellent'])) {
        // Handle non-excellent posters search
        $sql = "SELECT DISTINCT u.username
        FROM users u
        WHERE u.username NOT IN (
            SELECT u2.username
            FROM users u2
            LEFT JOIN items i ON u2.username = i.user_username
            WHERE i.ID IN (
                SELECT item_id
                FROM reviews
                WHERE rating = 'excellent'
                GROUP BY item_id
                HAVING COUNT(*) >= 3
            )
        )";

        $result = mysqli_query($conn, $sql);
    }
    elseif (isset($_POST['search-poor-reviewers'])) {
        // Handle poor reviewers search
        $sql = "SELECT u.username FROM users u
        LEFT JOIN reviews r ON u.username = r.username
        LEFT JOIN items i ON r.item_id = i.id
        WHERE r.rating = 'poor'
        AND u.username NOT IN (
            SELECT u2.username
            FROM users u2
            JOIN reviews r2 ON u2.username = r2.username
            WHERE r2.rating != 'poor'
        )
        GROUP BY u.username;";

        $result = mysqli_query($conn, $sql);
    }
    else {  // show all users
        // Show all users
        $sql = "SELECT * FROM users";
        $result = mysqli_query($conn, $sql);
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Search Users</title>
</head>

<body>
    <div class="container">
        <h1>Search Users</h1>
        <form action="users_search.php" method="post">
            
        <div class="form-group">
            <label for="username1">Select 2 users to find their common favorite users:</label>
            <select class="form-control" id="username1" name="username1">
                <option value="" selected>Select User 1</option>
                <?php
                // Fetch usernames from the users table and populate the dropdown options
                while ($row = mysqli_fetch_assoc($dropdown_result)) {
                    echo "<option value='".$row["username"]."'>".$row["username"]."</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <select class="form-control" id="username2" name="username2">
                <option value="" selected>Select User 2</option>
                <?php
                // Reset the data seek pointer to fetch usernames again
                mysqli_data_seek($dropdown_result, 0);
                // Fetch usernames from the users table and populate the dropdown options
                while ($row = mysqli_fetch_assoc($dropdown_result)) {
                    echo "<option value='".$row["username"]."'>".$row["username"]."</option>";
                }
                ?>
            </select>
        </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Search" name="search">
                <input type="submit" class="btn btn-primary" value="Find Non-Excellent Posters" name="search-non-excellent">
                <input type="submit" class="btn btn-primary" value="Find Poor Reviewers" name="search-poor-reviewers">
                <button type="submit" class="btn btn-secondary" name="show_all">Show All</button>
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>  
        </form>
    </div>

    <div class="container">
    <?php if ($result) { ?>
            <table class="table">
                <?php
                    // Alerts for favoriting a user
                    if(isset($_POST['favorite'])) {
                        if ($num_rows == 0) {            
                            echo "<div class='alert alert-success'>User added to your favorites</div>";
                        }
                        else {
                            echo "<div class='alert alert-danger'>User already in your favorites</div>";
                        }
                    }

                    // Check if a search query is submitted
                    if(isset($_POST['search'])) {
                        echo "<h2>Union of the favorites of selected User 1 & User 2</h2>";
                        
                    }
                    elseif(isset($_POST['search-non-excellent'])) {
                        echo "<h2>Users that have never posted any 'excellent' items</h2>";
                        
                    }
                    elseif (isset($_POST['search-poor-reviewers'])) {
                        echo "<h2>Users that only left poor Reviews</h2>";

                    }
                    else {  // show all users
                        echo "<h2>All Users</h2>";
                    } 

                ?>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Favoriting</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        // Check if $result is not null and if it contains any rows
                        if ($result && mysqli_num_rows($result) > 0) {
                            // Loop through the retrieved users and display them in the table
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Check if the user is the logged-in user
                                $is_logged_in_user = ($row["username"] === $_SESSION["user"]);
                                //$is_logged_in_user = true;
                    ?>
                                <tr>
                                    <td><?php echo $row["username"]; ?></td>
                                    <?php if (!$is_logged_in_user) { ?>
                                        <td>
                                            <form action="users_search.php" method="post">
                                                <input type="hidden" name="favorite_username" value="<?php echo $row["username"]; ?>">
                                                <button type="submit" class="btn btn-secondary" name="favorite">&#10084;</button>
                                            </form>
                                        </td>
                                    <?php } else { ?>
                                        <td></td>
                                    <?php } ?>
                                </tr>
                        <?php 
                            } 
                            } else {
                                echo "<tr><td colspan='2'>No users found.</td></tr>";
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
