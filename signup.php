<?php
session_start();
session_regenerate_id();
// clear them
// print_r($_SESSION);
// $_SESSION["admin_created"] = "";
// $_SESSION["admin_exist"] = "";
// $_SESSION["userCreated"] = "";
// $_SESSION["userExist"] = "";
// $_SESSION["validPassword"] = "";
// $_SESSION["logged_in"] = "";
// print_r($_SESSION);
// same as above
session_unset();
// same as above
// require_once "logout.php";

// // Force SSL
// if ($_SERVER["HTTPS"] !== "on") {
//   die("Must Login Via HTTPS");
//   // header("Location: https://");
// }
// print_r($_SESSION);
// echo session_id();

// Start Form Validation
$username = $password = "";
$usernameErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  if (empty($_POST["username"])) {
    $usernameErr = "Username is Required";
  } elseif (!preg_match("/^[a-zA-Z'_\s]*$/i", $_POST["username"])) {
    $usernameErr = "only letter, Underscores & ' are allowed";
  } else {
    $username = test_input($_POST["username"]);
  }

  if (empty($_POST["password"])) {
    $passwordErr = "Password is Required";
  } elseif (strlen($_POST["password"]) < 6) {
    $passwordErr = "Password must contain at least 6 characters";
  } else {
    $password = test_input($_POST["password"]);
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
  }

  // Send Data To Database
  if ($usernameErr === "" && $passwordErr === "") {
    define("START_CONFIG", true);
    require_once "config.php";

    // // Prepare and Bind
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password_hash);
    // set parameters and execute [Alread has been set]
    // $username = $username;
    // $password = $password;

    try {
      $stmt->execute();
      $stmt = $conn->prepare("SELECT uid FROM users where username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows === 1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $stmt = $conn->prepare("INSERT INTO user_data (uid) VALUES (?)");
        $stmt->bind_param("i", $row["uid"]);
        $stmt->execute();
      }
      $_SESSION["userCreated"] = "true";
    } catch (Exception $ex) {
      $_SESSION["userCreated"] = "false";
    } finally {
      $stmt->close();
      $conn->close();
      // header("Location: index.php");
      echo '
<script>
  location.href="index.php";
</script>
';
      exit;
    }
  }
}

function test_input($data)
{
  $data = trim($data); // remove spaces from start and end
  $data = stripslashes($data); // remove backslashes from string
  $data = htmlspecialchars($data); // 
  $data = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS); // translate SPECIAL_CHARS to HTML entities
  return $data;
}
// End of Form

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link rel="stylesheet" href="/bootstrap/bootstrap.min.css" />
</head>

<body>
  <h1 class="text-center">Sign Up</h1>
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="container">
    <div class="mb-3">
      <label for="username" class="form-label">User Name</label>
      <input class="form-control" name="username" id="username" type="text" value="<?php echo $username ?>" placeholder="User Name" />
      <span><?php echo $usernameErr ?></span>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Passowrd</label>
      <input class="form-control" id="password" name="password" type="password" value="<?php echo $password ?>" placeholder="Password" />
      <span><?php echo $passwordErr ?></span>
    </div>
    <button type="submit" class="btn btn-primary d-block mx-auto">Sign Up</button>
  </form>
  <section class="d-flex justify-content-center px-4 gap-3 my-3 flex-wrap">
    <a href="index.php">Home</a>
    <a href="manage_customers.php">Manage Customers</a>
    <a href="login.php">Login</a>
    <a href="logout.php">Logout</a>
  </section>

  <script src="/bootstrap/bootstrap.bundle.min.js"></script>

</body>

</html>