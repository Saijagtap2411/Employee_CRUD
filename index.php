<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(120deg, #1f2a45, #243b55);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background-color: #111827;
            padding: 54px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
        }

        .form-container h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container label {
            color: #ffffff;
            display: block;
            margin-bottom: 5px;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            background: #1f2937;
            color: #ffffff;
        }

        .form-container .button-group {
            display: flex;
            justify-content: space-between;
        }

        .form-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: #1cb5e0;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .form-container button:hover {
            background: #1592c4;
        }

        .form-container .register-btn {
            background: #ef4444;
        }

        .form-container .register-btn:hover {
            background: #d43d3d;
        }

        .error-message,
        .success-message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error-message {
            color: #dc3545;
        }

        .success-message {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php
        session_start();

       
        $host = "localhost";
        $username = "root"; 
        $password = "root";     
        $database = "techdb";

        $conn = new mysqli($host, $username, $password, $database);

        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];
            $password = $_POST['password'];

            
            $sql = "SELECT id, password FROM empdata WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['email'] = $email;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid password.";
                }
            } else {
                $message = "No account found with this email.";
            }

            $stmt->close();
        }
        $conn->close();
        ?>

        <?php if (!empty($message)): ?>
            <div class="error-message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <div class="button-group">
                <button type="submit">Login</button>
                <button type="button" class="register-btn" onclick="redirectToRegister()">Register</button>
            </div>
        </form>
    </div>

    <script>
        function redirectToRegister() {
            window.location.href = "register.php"; 
        }
    </script>
</body>
</html>
