<?php
session_start();
session_regenerate_id();
// $_SESSION["logged_in"] = "";
$_SESSION["admin_data"] = "";
$_SESSION["hello_admin"] = "";
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
    $usernameErr = "only letter, Underscores & ' & spaces are allowed";
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
    // $user = $result->fetch_all(MYSQLI_ASSOC); // if more than on record use this then foreach loop ouput data
    // $user = $result->fetch_array(MYSQLI_ASSOC);

    define("START_CONFIG", true);
    require_once "config.php";
    $stmt = $conn->prepare("SELECT password, uid, username FROM admins where username = ?");
    $stmt->bind_param("s", $_POST["username"]);
    try {
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows === 1) {
        $_SESSION["admin_exist"] = true;
        $row = $result->fetch_array(MYSQLI_ASSOC);
        // echo "nums rows = 1";
        if (password_verify($password, $row["password"])) {
          // echo "password is correct"; // Here All Valid
          // $_SESSION["uid"] = $row["uid"];
          $_SESSION["logged_in"] = true;
          $_SESSION["uname"] = $row["username"]; // 
          $_SESSION["validPassword"] = true;
          define("START_ADMIN", true);
          require_once "admin.php";
          $_SESSION["hello_admin"] = "<h1 class='text-center text-success'>Welcome $_SESSION[uname]</h1>";
          $_SESSION["admin_data"] = $get_data;
          // header("Refresh: 2; URL=users.php");
          echo '
          <script>
          setTimeout(() => {
            location.href="users.php";
          }, 1500);
          </script>
          ';
          exit;
        } else {
          $_SESSION["logged_in"] = false;
        }
      } else {
        $_SESSION["admin_exist"] = false;
      }
    } catch (Exception $ex) {
      echo "Error $ex";
    }
    $result->close();
    $stmt->close();
    $conn->close();
    // header("Location: index.php");
    // exit;
  } // end no errors
} // end post


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
  <h1 class="text-center">Login As Admin</h1>
  <?php
  if (isset($_SESSION["admin_exist"]) && $_SESSION["admin_exist"] === true) {
    echo "<h1 class='text-center text-success'>Admin Exists</h1>";
    if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
      echo "<h1 class='text-center text-success'>Valid Password</h1>";
    } elseif (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === false) {
      echo "<h1 class='text-center text-danger'>Unvalid Password</h1>";
    }
  } elseif (isset($_SESSION["admin_exist"]) && $_SESSION["admin_exist"] === false) {
    echo "<h1 class='text-center text-danger'>Admin Account Not Exists</h1>";
  }
  ?>
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
  <hr>
  <section class="d-flex justify-content-center px-4 gap-3 my-3 flex-wrap">
    <a href="index.php">Home</a>
    <a href="add_admin.php">Add Admin</a>
    <a href="logout.php">Logout</a>
  </section>
  <script src="/bootstrap/bootstrap.bundle.min.js"></script>

</body>

</html>