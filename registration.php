<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-header {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-header h2 {
            margin: 0;
            font-size: 24px;
            color: #343a40;
        }
        .form-header p {
            margin: 0;
            color: #6c757d;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-btn {
            text-align: center;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-header">
            <h2>User Registration</h2>
            <p>Please fill in the form to create an account</p>
        </div>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstName = $_POST["firstname"];
            $lastName = $_POST["lastname"];
            $middleName = $_POST["middlename"];
            $username = $_POST["username"];
            $password = $_POST["password"];
            $passwordConfirm = $_POST["confirmpassword"];
            $birthday = $_POST["birthday"];
            $email = $_POST["email"];
            $contactNumber = $_POST["contactnumber"];

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $errors = array();

            if (empty($firstName) || empty($lastName) || empty($username) || empty($password) ||
                empty($passwordConfirm) || empty($birthday) || empty($email) || empty($contactNumber)) {
                array_push($errors, "All fields are required.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Email is not valid.");
            }

            if (strlen($password) < 8) {
                array_push($errors, "Password must be at least 8 characters long.");
            }

            if ($password !== $passwordConfirm) {
                array_push($errors, "Passwords do not match.");
            }

            if (strlen($contactNumber) != 11) {
                array_push($errors, "Contact number is invalid.");
            }

            if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
                array_push($errors, "Username must contain only letters and numbers.");
            }

            $birthdayPattern = "/^\d{4}-\d{2}-\d{2}$/";
            if (!preg_match($birthdayPattern, $birthday)) {
                array_push($errors, "Birthday must be in YYYY-MM-DD format.");
            }

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                require_once "database.php";

                $sql = "INSERT INTO users (first_name, middle_name, last_name, user_name, password, birthday, email, contact_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssssssss", $firstName, $middleName, $lastName, $username, $passwordHash, $birthday, $email, $contactNumber);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<div class='alert alert-success'>Registered successfully.</div>";
                    } else {
                        echo "SQL Error: " . mysqli_stmt_error($stmt);
                    }
                } else {
                    echo "SQL Error: " . mysqli_error($conn);
                }
            }
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="firstname" placeholder="First Name">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="middlename" placeholder="Middle Name">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="lastname" placeholder="Last Name">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="Username">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="confirmpassword" placeholder="Confirm Password">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="birthday" placeholder="Birthday (YYYY-MM-DD)">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="contactnumber" placeholder="Contact Number">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
    </div>
</body>
</html>
