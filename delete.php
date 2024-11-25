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
       
        $images = explode(", ", $user['images']);
        foreach ($images as $image) {
            $imagePath = "uploads/" . basename($image);
            if (file_exists($imagePath)) {
                unlink($imagePath); 
            }
        }

        $sqlDelete = "DELETE FROM empdata WHERE id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $userId);
        
        if ($stmtDelete->execute()) {
            echo "User and associated images deleted successfully!";
            header("Location: dashboard.php"); 
            exit;
        } else {
            echo "Error deleting user: " . $stmtDelete->error;
        }
    } else {
        echo "User not found.";
    }
    $stmt->close();
} else {
    echo "No user ID provided.";
}

$conn->close();
?>
