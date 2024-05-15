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
        // Determine which serch type based on the input field
        if (!empty($_POST['price'])) {
            // Search for top 3 most expensive items by category
            $search_query = $_POST['price'];
            $search_type = 'expensive_items';
        }
        else {
             // Default search by category
            $search_query = $_POST['title'];
            $search_type = 'category';
        }

        // Modify the SQL query based on the search type
        if ($search_type == 'category') {
            $sql = " SELECT * FROM items WHERE category LIKE ?";
        } elseif ($search_type == 'expensive_items') {
            $sql = "SELECT * FROM items WHERE category LIKE ? ORDER BY price DESC LIMIT 3";
        }

        $stmt = mysqli_stmt_init($conn);
        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
        if ($prepareStmt) {
            $search_query = '%' . $search_query . '%'; // Add wildcards to search for partial matches
            mysqli_stmt_bind_param($stmt, "s", $search_query);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt); // Close the prepared statement
        }

    }
    else {
        // Default query to show all items
        $sql = "SELECT * FROM items";
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
    <title>Search Items</title>
</head>

<body>
    <div class="container">
        <h1>Search for Items</h1>
        <form action="search_items.php" method="post">
            <div class="form-group">
                <label for="title">Search by Category:</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Enter category">
            </div>

            <div class="form-group">
                <label for="title">Top 3 Most Expensive Items by Category:</label>
                <input type="text" class="form-control" id="price" name="price" placeholder="Enter category">
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Search" name="search">
                <button type="submit" class="btn btn-secondary" name="show_all">Show All</button>
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