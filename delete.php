<?php

class Database
{
    private $conn;

    public function __construct($servername, $username, $password, $dbname)
    {
        try {
            $this->conn = new mysqli($servername, $username, $password, $dbname);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

class RecordDeletion
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function deleteRecord($id)
    {
        try {
            // Check if an ID is provided
            if ($id) {
                $conn = $this->db->getConnection();

                // Delete the record based on the provided ID
                $sql = "DELETE FROM userFormData WHERE id=$id";

                if ($conn->query($sql) === TRUE) {
                    header("Location: admin.php");
                    exit();
                } else {
                    throw new Exception("Error deleting record: " . $conn->error);
                }
            } else {
                throw new Exception("ID not provided");
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
$recordDeletion = new RecordDeletion($database);

// Check if an ID is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $recordDeletion->deleteRecord($id);
} else {
    echo "ID not provided";
}

?>
