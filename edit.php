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

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $sql = "SELECT * FROM empdata WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit;
    }
} else {
    echo "No user ID provided.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $education = isset($_POST['education']) ? implode(", ", $_POST['education']) : ""; 
    $city = $_POST['city'];
    
    
    $uploadedImages = $user['images']; 
    $deletedImages = isset($_POST['deleted_images']) ? $_POST['deleted_images'] : [];

   
    if (!empty($deletedImages)) {
        foreach ($deletedImages as $deletedImage) {
            $imagePath = "uploads/" . basename($deletedImage);
            if (file_exists($imagePath)) {
                unlink($imagePath); 
            }
            $uploadedImages = str_replace($deletedImage, '', $uploadedImages); 
        }
        $uploadedImages = implode(", ", array_filter(explode(", ", $uploadedImages))); 
    }

    
    for ($i = 1; $i <= 4; $i++) {
        $imageKey = "image" . $i;

       
        if (!empty($_FILES[$imageKey]['name'])) {
            $targetDir = "uploads/";
            $newImageName = $name . "_image" . $i . "." . pathinfo($_FILES[$imageKey]["name"], PATHINFO_EXTENSION);
            $targetFile = $targetDir . $newImageName;

           
            $existingImagePath = "uploads/" . $name . "_image" . $i . ".jpg"; 
            if (file_exists($existingImagePath)) {
                unlink($existingImagePath); 
            }

            if (move_uploaded_file($_FILES[$imageKey]["tmp_name"], $targetFile)) {

                if (empty($uploadedImages)) {
                    $uploadedImages = $newImageName; 
                } else {
                    $uploadedImages .= ", " . $newImageName;
                }
            }
        }
    }

    $sql = "UPDATE empdata SET name = ?, email = ?, password = ?, gender = ?, education = ?, city = ?, images = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $name, $email, $password, $gender, $education, $city, $uploadedImages, $userId);

    if ($stmt->execute()) {
        echo "User data updated successfully!";
        header("Location: dashboard.php"); 
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
       
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
        }

        .form-container input,
        .form-container select,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .form-container input[type="radio"] {
            width: auto;
            margin-right: 5px;
        }

        .form-container .checkbox-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-container .checkbox-group label {
            margin: 0;
        }

        .image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin: 5px;
        }

        .action-btns {
            display: flex;
            justify-content: space-between;
        }

        .action-btns button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        .action-btns .delete-btn {
            background-color: #f44336;
        }

        .action-btns button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Edit User</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required>

            <label>Gender</label>
            <input type="radio" id="male" name="gender" value="male" <?php echo ($user['gender'] == 'male' ? 'checked' : ''); ?>> Male
            <input type="radio" id="female" name="gender" value="female" <?php echo ($user['gender'] == 'female' ? 'checked' : ''); ?>> Female

            <label>Education</label>
            <div class="checkbox-group">
                <input type="checkbox" id="ssc" name="education[]" value="SSC" <?php echo (in_array('SSC', explode(", ", $user['education'])) ? 'checked' : ''); ?>> <label for="ssc">SSC</label>
                <input type="checkbox" id="hsc" name="education[]" value="HSC" <?php echo (in_array('HSC', explode(", ", $user['education'])) ? 'checked' : ''); ?>> <label for="hsc">HSC</label>
                <input type="checkbox" id="bsc" name="education[]" value="BSC" <?php echo (in_array('BSC', explode(", ", $user['education'])) ? 'checked' : ''); ?>> <label for="bsc">BSC</label>
                <input type="checkbox" id="bcom" name="education[]" value="BCOM" <?php echo (in_array('BCOM', explode(", ", $user['education'])) ? 'checked' : ''); ?>> <label for="bcom">BCOM</label>
                <input type="checkbox" id="mca" name="education[]" value="MCA" <?php echo (in_array('MCA', explode(", ", $user['education'])) ? 'checked' : ''); ?>> <label for="mca">MCA</label>

            </div>

            <label for="city">City</label>
            <select id="city" name="city">
                <option value="Delhi" <?php echo ($user['city'] == 'Delhi' ? 'selected' : ''); ?>>Delhi</option>
                <option value="Mumbai" <?php echo ($user['city'] == 'Mumbai' ? 'selected' : ''); ?>>Mumbai</option>
                <option value="Kolkata" <?php echo ($user['city'] == 'Kolkata' ? 'selected' : ''); ?>>Kolkata</option>
                <option value="Chennai" <?php echo ($user['city'] == 'Chennai' ? 'selected' : ''); ?>>Chennai</option>
            </select>

            <label for="images">Upload Images</label>
            <div class="image-preview-container">
                <?php
                $images = explode(", ", $user['images']);
                foreach ($images as $image) {
                    $imagePath = "uploads/" . basename($image);
                    echo "<div style='position: relative; display: inline-block;'>";
                    echo "<img src='$imagePath' class='image-preview'>";
                    echo "<input type='checkbox' name='deleted_images[]' value='$image' style='position: absolute; top: 0; right: 0; background-color: red;'>";
                    echo "</div>";
                }
                ?>
            </div>

            <label>New Image 1</label>
            <input type="file" name="image1">

            <label>New Image 2</label>
            <input type="file" name="image2">

            <label>New Image 3</label>
            <input type="file" name="image3">

            <label>New Image 4</label>
            <input type="file" name="image4">

            <div class="action-btns">
                <button type="submit">Update</button>
                <button type="button" class="delete-btn" onclick="window.location.href='dashboard.php'">Cancel</button>
            </div>
        </form>
    </div>

</body>
</html>
