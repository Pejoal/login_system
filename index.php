<?php
session_start();
session_regenerate_id();
// // Force SSL
// if ($_SERVER["HTTPS"] !== "on") {
//   die("Must Login Via HTTPS");
//   // header("Location: https://");
// }
// print_r($_SESSION);
// echo session_id();

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
  <h1 class="text-center">Home Page</h1>
  <hr class="d-block w-50 mx-auto">
  <?php


  // Sign In
  $userCreated = "";
  if (isset($_SESSION["userCreated"]) && $_SESSION["userCreated"] === "true") {
    echo "<h3 class='text-center text-success'>Your Account Is Available Now</h3>";
  } elseif (isset($_SESSION["userCreated"]) && $_SESSION["userCreated"] === "false") {
    echo "<h3 class='text-center text-danger'>Account Already Exists</h3>";
  }

  // Login
  $userExist = "";
  $passwordValid = "";
  $uid = "";
  if (isset($_SESSION["userExist"]) && $_SESSION["userExist"] === "true") {
    // here then we should bring all user data [like profile] or something similar like open dashboard
    $user_state = "<h1 class='text-center text-success'>User Found</h1>";
    if (isset($_SESSION["validPassword"]) && $_SESSION["validPassword"] === "true") {
      $password_state = "<h1 class='text-center text-success'>Password Correct</h1>";
      $uid_state = "<h1 class='text-center text-info'>Your id = " . $_SESSION['uid'] . "</h1>";
      //  [If it comes to here that means every thing is ok]
      // More Info | User Data
      define("ACCESS_STATUE", true);
      require_once "user.php";
    } elseif (isset($_SESSION["validPassword"]) && $_SESSION["validPassword"] === "false") {
      // $password_state = "<h1 class='text-center text-danger'>Password NOT Correct</h1>";
      echo "<h1 class='text-center text-danger'>Password NOT Correct</h1>";
    }
  } elseif (isset($_SESSION["userExist"]) && $_SESSION["userExist"] === "false") {
    echo "<h1 class='text-center text-warning'>User NOT Found</h1>";
    // $user_state = "<h1 class='text-center text-danger'>User NOT Found</h1>";
  }

  ?>

  <section class="d-flex justify-content-center px-4 gap-3 my-3 flex-wrap">

    <a href="../index.html">Home Page</a>
    <a href="manage_customers.php">Manage Customers</a>
    <a href="signup.php">Sign Up</a>
    <a href="login.php">Login</a>
    <a href="logout.php">Logout</a>
  </section>
  <script src="/bootstrap/bootstrap.bundle.min.js"></script>

</body>

</html>