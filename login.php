<?php
    session_start();
    // Check if the user is already logged in
    if (isset($_SESSION["user"])) {
        header("Location: index.php"); // Redirect to index.php if already logged in
        exit(); // Ensure script execution stops after redirection
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Login Form</title>
</head>

<body>
    <div class="container">
        <?php
            if (isset($_POST["login"])) {   // Only runs if the "login" button is clicked
                // Retrive item details from the form
                $username = $_POST["username"];
                $password = $_POST["password"];

                if (empty($username) || empty($password)) {
                    echo "<div class='alert alert-danger'>All fields are required</div>"; 
                }

                // Establish connection to MySQL Database
                require_once("database.php");

                // Error check if trying to log in with a valid username
                // Protected from SQL injection
                $sql = "SELECT * FROM users WHERE username = ?";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt, "s", $username);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    mysqli_stmt_close($stmt); // Close the prepared statement
                }

                if ($user) {    // username does exist
                    if (password_verify($password, $user["password"])) {
                        // password matches associated username. Allow login. Also start session.

                        // indicate user is authenticated - and set session variables
                        $_SESSION["user"] = $user["username"];
                        //echo "First Name: " . $user["firstName"]; // Add this line to display the last name
                        //$_SESSION["firstName"] = $user["firstName"];
                        
                        //echo "Session First Name: " . $_SESSION["firstName"]; // Add this line to display the session last name
                        //$_SESSION["lastName"] = $user["lastName"];
                        
                        // redirect to home page
                        header("Location:index.php"); 
                        exit();
                    }else { // Invalid Login
                        echo "<div class='alert alert-danger'>Invalid Login. Would you like to sign up?</div>";
                    }
                }else{  // username does not exist
                    echo "<div class='alert alert-danger'>Invalid Login. Would you like to sign up?</div>";
                }
                
            }// End of If login button clicked block
        ?>

        <form action="login.php" method="post">
            <h1>Log In</h1>

            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username:">
            </div>

            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Login" name="login">
                <a href="registration.php" class="btn btn-secondary">Sign Up</a>
            </div> 
        </form>
    </div>
</body>

</html>