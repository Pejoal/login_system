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
    // $password = password_hash($password, PASSWORD_DEFAULT); // Do NOT hash this
  }
  // Send Data To Database
  if ($usernameErr === "" && $passwordErr === "") {
    define("START_CONFIG", true);
    require_once "config.php";

    // $sql = "SELECT password, uid FROM users where username = '$username'";
    // $result = $conn->query($sql);
    // print_r($result); // mysqli_result Object ( [current_field] => 0 [field_count] => 2 [lengths] => [num_rows] => 1 [type] => 0 )

    $stmt = $conn->prepare("SELECT password, uid, username FROM users where username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    // $user = $result->fetch_all(MYSQLI_ASSOC); // if more than on record use this then foreach loop ouput data
    // $user = $result->fetch_array(MYSQLI_ASSOC);
    // print_r($result); // mysqli_result Object ( [current_field] => 0 [field_count] => 2 [lengths] => [num_rows] => 1 [type] => 0 )
    // print_r($user); // Array ( [password] => $2y$10$AEPxABA3.VNpUrXCvN6SpO8LZhkHoNd7lEowNiAppaSbalc.nWkU6 [uid] => 15 )

    $_SESSION["userExist"] = "";
    if ($result->num_rows === 1) {
      $_SESSION["userExist"] = "true";
      $row = $result->fetch_array(MYSQLI_ASSOC);
      // echo "nums rows = 1";
      if (password_verify($password, $row["password"])) {
        echo "password is correct"; // true ya bro
        $_SESSION["validPassword"] = "true";
        $_SESSION["uid"] = $row["uid"];
        $_SESSION["uname"] = $row["username"]; // should i delete this line??
        // echo "Valid password";
      } else {
        $_SESSION["validPassword"] = "false";
      }
    } else {
      // echo "password is NOT correct";
      $_SESSION["userExist"] = "false";
    }
    $result->close();
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
  <h1 class="text-center">Login</h1>
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
    <button type="submit" class="btn btn-primary d-block mx-auto">Login</button>
  </form>
  <section class="d-flex justify-content-center px-4 gap-3 my-3 flex-wrap">
    <a href="index.php">Home</a>
    <a href="manage_customers.php">Manage Customers</a>
    <a href="signup.php">Sign Up</a>
    <a href="logout.php">Logout</a>
  </section>
  <script src="/bootstrap/bootstrap.bundle.min.js"></script>

</body>

</html>