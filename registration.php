<?php

// Initialize variables

$name = $email = "";

$errors = [];

$successMessage = "";



// When form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {



    // Getting form data safely

    $name = trim($_POST["name"]);

    $email = trim($_POST["email"]);

    $password = $_POST["password"];

    $confirm_password = $_POST["confirm_password"];



    // -----------------------

    // 1. VALIDATION

    // -----------------------



    if (empty($name)) {

        $errors['name'] = "Name is required.";

    }



    if (empty($email)) {

        $errors['email'] = "Email is required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errors['email'] = "Invalid email format.";

    }



    if (empty($password)) {

        $errors['password'] = "Password is required.";

    } elseif (strlen($password) < 8) {

        $errors['password'] = "Password must be at least 6 characters.";

    } elseif (!preg_match("/[!@#$%^&*]/", $password)) {

        $errors['password'] = "Password must contain at least one special character.";

    }



    if ($password !== $confirm_password) {

        $errors['confirm_password'] = "Passwords do not match.";

    }



    // If no validation errors

    if (count($errors) === 0) {



        $file = "users.json";



        // If file does not exist, create it with an empty array

        if (!file_exists($file)) {

            file_put_contents($file, "[]");

        }



        // Read the existing data

        $json_data = file_get_contents($file);



        if ($json_data === false) {

            die("Error reading JSON file.");

        }



        $users = json_decode($json_data, true);



        if (!is_array($users)) {

            $users = [];

        }



        // Hash the password

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);



        // New user array

        $newUser = [

            "name" => $name,

            "email" => $email,

            "password" => $hashedPassword

        ];



        // Add new user

        $users[] = $newUser;



        // Write to JSON file

        if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT)) === false) {

            die("Error writing to JSON file.");

        }



        $successMessage = "Registration Successful!";

        // Clear form fields

        $name = $email = "";

    }

}

?>



<!DOCTYPE html>

<html>

<head>

    <title>User Registration</title>

    <style>

        .error { color: red; }

        .success { color: green; margin-bottom: 10px; }

        form { width: 300px; margin: auto; }

        div { margin-bottom: 10px; }

    </style>

</head>

<body>



<h2 style="text-align:center;">User Registration</h2>





<form method="POST">



    <div>

        <label>Name:</label><br>

        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">

        <div class="error"><?= $errors['name'] ?? "" ?></div>

    </div>



    <div>

        <label>Email:</label><br>

        <input type="text" name="email" value="<?= htmlspecialchars($email) ?>">

        <div class="error"><?= $errors['email'] ?? "" ?></div>

    </div>



    <div>

        <label>Password:</label><br>

        <input type="password" name="password">

        <div class="error"><?= $errors['password'] ?? "" ?></div>

    </div>



    <div>

        <label>Confirm Password:</label><br>

        <input type="password" name="confirm_password">

        <div class="error"><?= $errors['confirm_password'] ?? "" ?></div>

    </div>



    <button type="submit">Register</button>



    <!-- Success message centered below -->

    <?php if ($successMessage): ?>

        <div class="success" style="text-align:center; margin-top:12px; font-weight:bold; color:green;">

            <?= $successMessage ?>

        </div>

    <?php endif; ?>



</form>





</body>

</html>