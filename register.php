<?php

$host = "localhost";
$username = "root"; 
$password = "root";  
$database = "techdb";

$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $gender = $_POST['gender'];
    $education = isset($_POST['education']) ? implode(", ", $_POST['education']) : ""; 
    $city = $_POST['city'];

   
    $checkEmailQuery = "SELECT id FROM empdata WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        
        $message = "User with this email already exists!";
        $stmt->close();
    } else {
        
        $uploadedImages = [];
        for ($i = 1; $i <= 4; $i++) {
            $imageKey = "image" . $i;
            if (!empty($_FILES[$imageKey]['name'])) {
               
                $fileExtension = pathinfo($_FILES[$imageKey]["name"], PATHINFO_EXTENSION);
                $targetDir = "uploads/";
                $targetFile = $targetDir . $name . "_image" . $i . "." . $fileExtension;
                if (move_uploaded_file($_FILES[$imageKey]["tmp_name"], $targetFile)) {
                    $uploadedImages[] = $targetFile;
                }
            }
        }

        $images = implode(", ", $uploadedImages);
        $sql = "INSERT INTO empdata (name, email, password, gender, education, city, images) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $email, $password, $gender, $education, $city, $images);

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

         
         a {
            position: fixed;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: white;
            font-size: 20px;
            background-color: #333;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
            z-index: 1000; 
        }

        a:hover {
            background-color: #007bff;
            color: white; 
        }

        .form-container {
            margin-top:55vh;
            background-color: #111827;
            padding: 67px;
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

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            background: #1f2937;
            color: #ffffff;
        }

        .form-container input[type="radio"],
        .form-container input[type="checkbox"] {
            width: auto;
            margin-right: 5px;
        }

        .form-container .radio-group,
        .form-container .checkbox-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-container .radio-group label,
        .form-container .checkbox-group label {
            margin: 0;
            color: #ffffff;
            margin-left: -1vw;

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

        .form-container .clear-btn {
            background: #ef4444;
        }

        .form-container .clear-btn:hover {
            background: #d43d3d;
        }

        .success-message {
            text-align: center;
            color: #28a745;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .error-message {
            text-align: center;
            color: #dc3545;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<a href="index.php">&larr;</a>
    <div class="form-container">
        <h2>Register</h2>
        <?php if (!empty($message)): ?>
            <div class="<?php echo strpos($message, 'successful') !== false ? 'success-message' : 'error-message'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form id="registerForm" method="POST" enctype="multipart/form-data">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label>Upload Images</label>
            <input type="file" id="image1" name="image1" accept="image/*">
            <input type="file" id="image2" name="image2" accept="image/*">
            <input type="file" id="image3" name="image3" accept="image/*">
            <input type="file" id="image4" name="image4" accept="image/*">

            <label>Gender</label>
            <div class="radio-group">
                <input type="radio" id="male" name="gender" value="male" required>
                <label for="male">Male</label>
                <input type="radio" id="female" name="gender" value="female" required>
                <label for="female">Female</label>
            </div>

            <label>Education</label>
            <div class="checkbox-group">
                <input type="checkbox" id="ssc" name="education[]" value="SSC">
                <label for="ssc">SSC</label>
                <input type="checkbox" id="hsc" name="education[]" value="HSC">
                <label for="hsc">HSC</label>
                <input type="checkbox" id="bsc" name="education[]" value="BSC">
                <label for="bsc">BSC</label>
                <input type="checkbox" id="bcom" name="education[]" value="BCOM">
                <label for="bcom">BCOM</label>
                <input type="checkbox" id="mca" name="education[]" value="MCA">
                <label for="mca">MCA</label>
            </div>

            <label for="city">City</label>
            <select id="city" name="city" required>
                <option value="" disabled selected>Select City</option>
                <option value="mumbai">Mumbai</option>
                <option value="pune">Pune</option>
                <option value="delhi">Delhi</option>
                <option value="bangalore">Bangalore</option>
                <option value="chennai">Chennai</option>
            </select>

            <div class="button-group">
                <button type="submit">Register</button>
                <button type="reset" class="clear-btn">Clear</button>
            </div>
        </form>
    </div>
</body>
</html>
