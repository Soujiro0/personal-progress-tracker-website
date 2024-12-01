<?php
    session_start();
    require '../includes/db_connection.php'; // Include your database connection file

    // Initialize an error message variable
    $errorMessage = '';

    // Function to validate email
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Function to validate password strength
    function isStrongPassword($password) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    // Registration Logic
    if (isset($_POST['register'])) {
        // Sanitize input
        $username = trim($_POST['username']);
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);
        $email = trim($_POST['email']);

        // Validate email
        if (!isValidEmail($email)) {
            $_SESSION['error'] = "Error: Invalid email format.";
            echo "Error: Invalid email format.";
        } elseif ($password !== $confirmPassword) {
            $_SESSION['error'] = "Error: Passwords do not match.";
            echo "Error: Passwords do not match.";
        } elseif (!isStrongPassword($password)) {
            $_SESSION['error'] = "Error: Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, and one number.";
            echo "Error: Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, and one number.";
        } else {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_users WHERE username = ?");
            $stmt->execute([$username]);
            $usernameExists = $stmt->fetchColumn();

            if ($usernameExists) {
                $_SESSION['error'] = "Error: Username already exists. Please choose a different username.";
                echo "Error: Username already exists. Please choose a different username.";
            } else {
                // Proceed with registration
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO tbl_users (username, first_name, last_name, password, email) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$username, $firstName, $lastName, $hashedPassword, $email])) {
                    header('Location: /pages/landing.html');
                    exit();
                } else {
                    $_SESSION['error'] = "Error in registration! " . htmlspecialchars($stmt->errorInfo()[2]);
                    echo $_SESSION['error'] = "Error in registration! " . htmlspecialchars($stmt->errorInfo()[2]);
                }
            }
        }
    }

    // Login Logic
    if (isset($_POST['login'])) {
        // Sanitize input
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Prepare and execute the select statement
        $stmt = $pdo->prepare("SELECT * FROM tbl_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            // User found, verify password
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                header('Location: ../pages/dashboard.php'); // Redirect to dashboard after login
                exit();
            } else {
                $_SESSION['error'] = "Invalid username or password!";
            }
        } else {
            $_SESSION['error'] = "Invalid username or password!";
        }
    }

    // Redirect back to landing page with error message
    header('Location: ../pages/landing.html');
    exit();
?>