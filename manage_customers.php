<?php
session_start();
session_regenerate_id();
// var_dump(isset($_SESSION["manage_customers"]));
// var_dump(($_SESSION["manage_customers"]) === true); // bool(true)
// var_dump(1 === true); // bool(false)
// print_r($_SESSION);


if (!isset($_SESSION["manage_customers"]) || ($_SESSION["manage_customers"]) !== true) {
  echo '
  <h1 class="text-center">Login & Create Customers Table First</h1>
  <script>
  setTimeout(() => {
    location.href="index.php";
  }, 1500);
  </script>
  ';
  exit;
}
// die("<h1>Create Customers Table(Database) First</h1>");

// Required to grab Customers data
define("START_CONFIG", true);
require_once "config.php";
$uid_customers = $_SESSION['uid_customers'];

function test_input($data)
{
  $data = trim($data); // remove spaces from start and end
  $data = stripslashes($data); // remove backslashes from string
  $data = htmlspecialchars($data); // 
  $data = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS); // translate SPECIAL_CHARS to HTML entities
  return $data;
}

$cname = $item = $price = $quantity = $fullprice = "";
$cnameErr = $itemErr = $priceErr = $quantityErr = $fullpriceErr = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_customer"]) && $_POST["add_customer"] === "add") {
  // $_POST["add_customer"] === ""; // to prevent [duplicat] adding same item ervey refresh [Does NOT work]
  if (empty($_POST["cname"])) {
    $cnameErr = "Customer Name is Required";
  } elseif (!preg_match("/^[a-zA-Z'_\s]*$/i", $_POST["cname"])) {
    $cnameErr = "only letter, Underscores & ' & spaces are allowed";
  } else {
    $cname = test_input($_POST["cname"]);
  }

  if (empty($_POST["item"])) {
    $itemErr = "Item Name is Required";
  } elseif (!preg_match("/^[a-zA-Z'_\s]*$/i", $_POST["item"])) {
    $itemErr = "only letter, Underscores & ' & spaces are allowed";
  } else {
    $item = test_input($_POST["item"]);
  }

  if (empty($_POST["price"])) {
    $priceErr = "Price is Required";
  } elseif (!preg_match("/^\d*$/i", $_POST["price"])) {
    $priceErr = "only Digits are allowed";
  } else {
    $price = test_input($_POST["price"]);
  }

  if (empty($_POST["quantity"])) {
    $quantityErr = "quantity is Required";
  } elseif (!preg_match("/^\d*$/i", $_POST["quantity"])) {
    $quantityErr = "only Digits are allowed";
  } else {
    $quantity = test_input($_POST["quantity"]);
  }

  if ($cnameErr === "" && $itemErr === "" && $priceErr === "" && $quantityErr === "") {

    $stmt = $conn->prepare("INSERT INTO $uid_customers (uid ,customername, item, price, quantity, fullprice) VALUES (?, ?, ? , ? , ?, ?)");
    $stmt->bind_param("issiii", $_SESSION['uid'], $cname, $item, $price, $quantity, $fullprice);
    $fullprice = $price * $quantity;
    try {
      $stmt->execute();
      // $_SERVER["REQUEST_METHOD"] = ""; // to prevent it from re adding same item [Does NOT work]
      // echo "Customer Added Successfully";
    } catch (Exception $ex) {
      echo "Error Adding Customer $ex";
    }
  }
} // end of POST


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
  <h1 class="text-center text-primary">Manage Customers</h1>
  <h2 class="ps-3">
    Welcome,
    <?php echo $_SESSION["uname"] ?>
  </h2>
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="container p-1">
    <div class="mb-3">
      <label for="Customer" class="form-label">Add Customer</label>
      <input type="text" name="cname" id="Customer" class="form-control" placeholder="Customer Name">
      <?php echo $cnameErr ?? "" ?>
    </div>
    <div class="mb-3">
      <label for="item" class="form-label">Item Name</label>
      <input type="text" name="item" id="item" class="form-control" placeholder="Item to Sell">
      <?php echo $itemErr ?? "" ?>
    </div>
    <div class="mb-3">
      <label for="price" class="form-label">Price</label>
      <input type="number" name="price" value="1000" id="price" class="form-control" placeholder="price of item">
      <?php echo $priceErr ?? "" ?>
    </div>
    <div class="mb-3">
      <label for="quantity" class="form-label">Quantitiy</label>
      <input type="number" name="quantity" value="1" step="1" id="quantity" class="form-control" placeholder="quantity of items">
      <?php echo $quantityErr ?? "" ?>
    </div>
    <button type="submit" name="add_customer" value="add" class="btn btn-primary">Create</button>
  </form>
  <?php

  $stmt = $conn->prepare("SELECT customername, item, price, quantity, fullprice FROM $uid_customers");
  try {
    $stmt->execute();
    // echo "Customer Selected Successfully";
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      // output data of each row
      echo '
      <table class="table table-striped table-hover table-inverse table-responsive table-active table-dark my-4 mx-auto container text-center text-capitalize">
      <thead class="thead-inverse">
        <tr>
          <th>Customer Name</th>
          <th>Item</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Full Price</th>
        </tr>
        </thead>
        <tbody>
      ';
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        echo "
        <tr>
          <td scope='row'>$row[customername]</td>
          <td>$row[item]</td>
          <td>$row[price]</td>
          <td>$row[quantity]</td>
          <td>$row[fullprice]</td>
        </tr>
        ";
      }
      echo '
      </tbody>
      </table>
      ';
    } else {
      echo "0 Result";
    }
  } catch (Exception $ex) {
    echo "Error Selecting Customers Data $ex";
  }
  // $conn->close();

  // Truncate Table 
  if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["truncate"]) && $_POST["truncate"] === "truncate") {
    $_POST["truncate"] = "";

    //     $stmt = $conn->prepare("TRUNCATE TABLE $uid_customers");
    //     try {
    //       $stmt->execute();
    //       // header("Refresh: 1;"); // same as down
    //       echo '
    // <script>
    //   location.href="manage_customers.php";
    // </script>
    // ';
    //       exit;
    //     } catch (Exception $ex) {
    //       echo "Error Truncating the table $ex";
    //     }

    // Another Way As Above

    $sql = "TRUNCATE TABLE $uid_customers";
    if ($conn->query($sql) === TRUE) {
      echo '
<script>
  location.href="manage_customers.php";
</script>
';
      exit;
    } else {
      echo "Error Truncating Database<br>" . $conn->error;
    }
    // close connection
    $conn->close();
  } // End Of Truncate
  ?>
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="container">
    <button type="submit" name="truncate" value="truncate" class="btn btn-danger text-light d-block mx-auto">Truncate Database</button>
  </form>
  <section class="d-flex justify-content-center px-4 gap-3 my-3 flex-wrap">
    <a href="index.php">Home</a>
    <a href="signup.php">Sign Up</a>
    <a href="login.php">Login</a>
    <a href="logout.php">Logout</a>
    <a href="dashboard.php">Dashboard</a>
  </section>
  <script src="/bootstrap/bootstrap.bundle.min.js"></script>

</body>

</html>