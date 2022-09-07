<?php
session_start();
session_regenerate_id();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["truncate"]) && $_POST["truncate"] === "truncate") {
  $_POST["truncate"] = ""; // to prevent it re executing itself every refresh
  define("START_CONFIG", true);
  require_once "config.php";
  // $stmt = $conn->prepare("TRUNCATE TABLE users");
  // try {
  //   $stmt->execute();
  //   echo "Done";
  //   // header("Refresh: 1;");
  //   //     exit;
  // } catch (Exception $ex) {
  //   echo "Error Truncating users table -> $ex";
  // }
  // Another Way to do the same
  $sql = "SET FOREIGN_KEY_CHECKS = 0;";
  $sql .= "TRUNCATE TABLE users;";
  $sql .= "TRUNCATE TABLE user_data;";
  if ($conn->multi_query($sql)) {
    echo "Truncating Multi Tables Done Successfully <br>";
    echo '
<script>
setTimeout(() => {
  location.href="dashboard.php";
}, 1500);
</script>
';
  } else {
    echo "Error: Truncating Multi Tables " . $sql . "<br>" . $conn->error;
  }
  // $sql = "SET FOREIGN_KEY_CHECKS = 1;"; // Automatic Return to 1 after session ended [No Need for this line]
  $conn->close();
} // End Of Truncate

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="/bootstrap/bootstrap.min.css" />
</head>

<body>
  <h1 class="text-center">Users Data</h1>
  <hr class="w-50 d-block mx-auto">
  <?php
  echo $_SESSION["hello_admin"] ?? "";
  echo $_SESSION["admin_data"] ?? "";
  ?>
  <hr>
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="container">
    <button type="submit" name="truncate" value="truncate" class="btn btn-danger text-light d-block mx-auto">Truncate Database</button>
  </form>
  <hr>
  <section class="d-flex justify-content-center px-4 gap-3 my-3 flex-wrap">
    <a href="dashboard.php">Dashboard</a>
    <a href="add_admin.php">Add Admin</a>
    <a href="logout.php">Logout</a>
  </section>
  <script src="/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>