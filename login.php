<!doctype html>
<!-- If multi-language site, reconsider usage of html lang declaration here. -->
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Login System</title>

  <!-- 120 word description for SEO purposes goes here. Note: Usage of lang tag. -->
  <meta name="description" lang="en" content="">

  <!-- Keywords to help with SEO go here. Note: Usage of lang tag.  -->
  <meta name="keywords" lang="en" content="">

  <!-- View-port Basics: http://mzl.la/VYREaP -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <!-- Place favicon.ico in the root directory: mathiasbynens.be/notes/touch-icons -->
  <link rel="shortcut icon" href="favicon.ico" />

  <!--font-awesome link for icons-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Default style-sheet is for 'media' type screen (color computer display).  -->
  <link rel="stylesheet" media="screen" href="css/style.css">
</head>
<?php
session_start();
require_once "db.php";

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

class UserAuthentication
{
  private $db;

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  public function generateSecureToken()
  {
    return bin2hex(random_bytes(32));
  }

  public function validateUser($username, $password)
  {
    $stmt = $this->db->getConnection()->prepare("SELECT Email, Password FROM userFormData WHERE Email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($dbUsername, $dbPassword);
    $stmt->fetch();
    $stmt->close();

    if ($dbUsername && password_verify($password, $dbPassword)) {
      return true;
    } else {
      return false;
    }
  }

  public function login($username, $password)
  {
    if ($this->validateUser($username, $password)) {
      $_SESSION['is_authenticated'] = true;
      $_SESSION['user_name'] = $username;
      $_SESSION['security_token'] = $this->generateSecureToken();
      setcookie('security_token', $_SESSION['security_token'], time() + (86400 * 30), '/');

      if ($username === 'admin') {
        $_SESSION['user_role'] = 'admin';
        header('Location: admin.php');
      } else {
        $_SESSION['user_role'] = 'user';
        header('Location: user.php');
      }
      exit();
    } else {
      $GLOBALS['invalidUserErr'] = "Invalid username or password.";
    }
  }
}

session_start();

$db = new Database('localhost', 'your_username', 'your_password', 'your_database_name');
$userAuth = new UserAuthentication($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $userAuth->login($username, $password);
}
?>

<body>
  <!--container starts here-->
  <div class="container">
    <!--main starts here-->
    <main>
      <section class="form-section">
        <div class="wrapper">
          <h1 class="section-heading">Login Page</h1>
          <p style='color: red;'>
            <?php echo $invalidUserErr ?>
          </p>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form"
            enctype="multipart/form-data">
            <div class="input-grp">
              <label for="user-name">User Name: <span class="requred">*</span></label>
              <input type="text" id="user-name" name="username" class="input-write" placeholder="Enter your username"
                value="<?php echo $unameval ?>">
              <span class="error">
                <?php echo $usernameErr; ?>
              </span>
            </div>
            <div class="input-grp">
              <label for="password">Password : <span class="requred">*</span></label>
              <input type="password" id="password" name="password" class="input-write" placeholder="Enter your password"
                value="">
              <span class="error">
                <?php echo $passErr; ?>
              </span>
            </div>
            <input type="submit" name="log-in" value="Login" class="btn submit-btn">
            <span class="register-link">Don't have an account? <a href="register.php">Register!</a></span>
          </form>
        </div>
      </section>
    </main>
    <!--main ends here-->
  </div>
  <!--container ends here-->
  <script src="assets/js/script.js"></script>
</body>

</html>