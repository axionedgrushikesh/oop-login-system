<?php
session_start();

if (!isset($_SESSION['is_authenticated']) || $_SESSION['user_role'] !== 'user') {
    header('Location: login.php');
    exit();
}

require_once "db.php";

$uname = $_SESSION['user_name'];
try {
    // Fetch data from the database
    $sql = "SELECT * FROM userFormData WHERE Email = '$uname'";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error fetching data: " . $conn->error);
    }

    // Display the fetched data in a table
    echo "<h2>Welcome to the User Page</h2>";
    echo "<h2><a href='logout.php'>Logout</a></h2>";
    if ($result->num_rows > 0) {
        echo "<p>Hello, $uname!</p>";
        echo "<table border='1'>";
        echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Gender</th><th>File</th><th>City</th><th>Actions</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['FirstName']}</td>";
            echo "<td>{$row['LastName']}</td>";
            echo "<td>{$row['Email']}</td>";
            echo "<td>{$row['Gender']}</td>";
            echo "<td>{$row['File']}</td>";
            echo "<td>{$row['City']}</td>";
            echo "<td><a href='register.php?id={$row['id']}'>Edit</a> | <a href='delete.php?id={$row['id']}'>Delete</a></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No data available";
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}

// Close the database connection
$conn->close();
?>
