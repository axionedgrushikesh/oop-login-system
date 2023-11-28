<?php

session_start();
require_once "db.php";
// require_once "Database.php"; // Make sure to replace this with your Database class file

class AdminPage
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function displayAdminPage()
    {
        try {
            // Fetch data from the database
            $conn = $this->db->getConnection();
            $sql = "SELECT id, FirstName, LastName, Email, Gender, File, City FROM userFormData";
            $result = $conn->query($sql);

            if (!$result) {
                throw new Exception("Error fetching data: " . $conn->error);
            }

            // Display the fetched data in a table
            echo "<h2>Welcome to the Admin Page</h2>";
            echo "<h2><a href='logout.php'>Logout</a></h2>";

            if ($result->num_rows > 0) {
                $uname = $_SESSION['user_name'];
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
            }
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage();
        } finally {
            // Close the database connection
            $this->db->closeConnection();
        }
    }
}

// Example usage
$database = new Database('localhost', 'your_username', 'your_password', 'your_database_name');
$adminPage = new AdminPage($database);
$adminPage->displayAdminPage();

?>
