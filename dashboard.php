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


$sql = "SELECT * FROM empdata";
$result = $conn->query($sql);


if ($result->num_rows == 0) {
    echo "No user data found.";
    exit;
}

$conn->close();


if (isset($_POST['logout'])) {
    session_unset(); 
    session_destroy(); 
    header("Location: index.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        /* table th {
            background-color: #f2f2f2;
        } */
        table th{
            background-color: #72b84f;
            color: #ffffff;
        }
        
        .password-cell {
            word-wrap: break-word;
            white-space: normal;
            max-width: 136px;
        }
        .image-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin: 5px;
        }
        .image-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .image-container img {
            margin: 5px;
        }
        
        .action-btns {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .action-btns button {
            padding: 6px 12px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .action-btns .delete-btn {
            background-color: #f44336; 
        }
        .action-btns button:hover {
            opacity: 0.8;
        }
        h2{
     /* align-items: center;
    display: grid; */
    text-align: center;
        }
    </style>
</head>
<body>

    <h2>User Dashboard</h2>
    <form method="POST" style="position: absolute; top: 20px; right: 20px;">
    <button type="submit" name="logout" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Logout
    </button>
</form>
    
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Password</th>
                <th>Gender</th>
                <th>Education</th>
                <th>City</th>
                <th>Images</th>
                <th>Actions</th> 
            </tr>
        </thead>
        <tbody>
            <?php
           
            while ($user = $result->fetch_assoc()) {
                $images = explode(", ", $user['images']); 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="password-cell"><?php echo htmlspecialchars($user['password']); ?></td> 
                    <td><?php echo htmlspecialchars($user['gender']); ?></td>
                    <td><?php echo htmlspecialchars($user['education']); ?></td>
                    <td><?php echo htmlspecialchars($user['city']); ?></td>
                    <td>
                    <?php
    if (!empty($user['images'])) { 
        $images = explode(", ", $user['images']); 

        if (count($images) > 0 && $images[0] != "") {
            echo '<div class="image-container">';
            foreach ($images as $index => $image) {
                $imagePath = "uploads/" . basename($image); 
                echo '<img class="image-preview" src="' . $imagePath . '" alt="Image ' . ($index + 1) . '">';
            }
            echo '</div>';
        } else {
            echo 'No image found'; 
        }
    } else {
        echo 'No Images Uploaded'; 
    }
    ?>
                    </td>
                    <td class="action-btns">
                        <button class="edit-btn" onclick="window.location.href='edit.php?id=<?php echo $user['id']; ?>'">Edit</button>
                        <button class="delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                    </td> 
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <script>
        function deleteUser(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
                
                window.location.href = "delete.php?id=" + userId;
            }
        }
    </script>

</body>
</html>
