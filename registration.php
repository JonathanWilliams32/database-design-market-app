<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Registration Form</title>
</head>

<body>
    <div class="container">
        <?php
            if (isset($_POST["register"])) {  // Only runs if the "Register" button is clicked
                // Retrive item details from the form
                $firstName = $_POST["firstName"]; 
                $lastName = $_POST["lastName"];
                $username = $_POST["username"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $passwordRepeat = $_POST["repeatPassword"];

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $errors = array();

                // Error check for empty fields
                if (empty($firstName) || empty($lastName) || empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) { 
                    array_push($errors, "All fields are required"); 
                }

                // Error check for valid email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errors, "Invalid email");
                }

                // Error check for matching confirm/repeat password
                if ($password !== $passwordRepeat) {
                    array_push($errors, "Password does not match");
                }

                // Establish connection to MySQL Database
                require_once("database.php");

                // Error check if trying to register with a duplicate email
                // Protected from SQL injection
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = mysqli_num_rows($result);
                    if ($rowCount > 0) {
                        array_push($errors, "Email already exists");
                    }
                    mysqli_stmt_close($stmt); // Close the prepared statement
                }
                
                // Error check if trying to register with a duplicate username
                // Protected from SQL injection
                $sql = "SELECT * FROM users WHERE username = ?";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt, "s", $username);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $rowCount = mysqli_num_rows($result);
                    if ($rowCount > 0) {
                        array_push($errors, "Username already exists");
                    }
                    mysqli_stmt_close($stmt); // Close the prepared statement
                }
                
                // Alert User of any/all error messages
                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                }else{  // No errors --> Insert submitted data into database
                    $sql = "INSERT INTO users (username, password, firstName, lastName, email) VALUES ( ?, ?, ?, ?, ? )";
                    $stmt = mysqli_stmt_init($conn);
                    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                    if ($prepareStmt) {
                        mysqli_stmt_bind_param($stmt,"sssss",$username, $passwordHash, $firstName, $lastName, $email);
                        mysqli_stmt_execute($stmt);
                        echo "<div class='alert alert-success'>You are registered successfully</div>";
                        mysqli_stmt_close($stmt);
                    }
                    else {
                        exit("Could not register this user to the database.");
                    }
                }// End of no $errors if block

            }// End of If Register button clicked block
        // End of php block
        ?>

        <form action="registration.php" method="post">
            <h1>Register</h1>

            <div class="form-group">
                <input type="text" class="form-control" name="firstName" placeholder="First Name:">
            </div>

            <div class="form-group">
                <input type="text" class="form-control" name="lastName" placeholder="Last Name:">
            </div>

            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username:">
            </div>

            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>

            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>  

            <div class="form-group">
                <input type="password" class="form-control" name="repeatPassword" placeholder="Repeat Password:">
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="register">
                <a href="login.php" class="btn btn-secondary">Login</a>
            </div>  
        </form>
    </div>

</body>

</html>