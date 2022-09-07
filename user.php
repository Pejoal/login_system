<?php

if (!defined("ACCESS_STATUE") || ACCESS_STATUE !== true) die("Not Allowed");

define("START_CONFIG", true);
require_once "config.php";
$uid = $_SESSION["uid"];
$stmt1 = $conn->prepare("SELECT username, password FROM users where uid = ?");
$stmt1->bind_param("s", $uid);
$stmt1->execute();
$result = $stmt1->get_result();

// if ($result->num_rows !== 1) die("Account Not Found");
if ($result->num_rows === 1) {
  $row = $result->fetch_array(MYSQLI_ASSOC);
}

function test_input($data)
{
  $data = trim($data); // remove spaces from start and end
  $data = stripslashes($data); // remove backslashes from string
  $data = htmlspecialchars($data); // 
  $data = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS); // translate SPECIAL_CHARS to HTML entities
  return $data;
}

$fname = $lname = $utype = "";
$fnameErr = $lnameErr = $utypeErr = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit"])) {

  if (empty($_POST["fname"])) {
    $fnameErr = "First Name is Required";
  } elseif (!preg_match("/^[a-zA-Z'_]*$/i", $_POST["fname"])) {
    $fnameErr = "only letter, Underscores & ' are allowed";
  } else {
    $fname = test_input($_POST["fname"]);
  }
  if (empty($_POST["lname"])) {
    $lnameErr = "Last Name is Required";
  } elseif (!preg_match("/^[a-zA-Z'_]*$/i", $_POST["lname"])) {
    $lnameErr = "only letter, Underscores & ' are allowed";
  } else {
    $lname = test_input($_POST["lname"]);
  }
  if (empty($_POST["utype"])) {
    $utypeErr = "User Type is Required";
  } elseif (!preg_match("/^[a-zA-Z'_\s]*$/i", $_POST["utype"])) {
    $utypeErr = "only letter, Underscores, Whitespace ,' are allowed";
  } else {
    $utype = test_input($_POST["utype"]);
  }

  if ($utypeErr === "" && $lnameErr === "" && $lnameErr === "") {

    $stmt2 = $conn->prepare("UPDATE user_data SET firstname = ?, lastname = ?, user_type = ? WHERE uid = $uid");
    $stmt2->bind_param("sss", $fname, $lname, $utype);

    try {
      $stmt2->execute();
      $edit_state = "<h2 class='text-center text-success'>Data Edited Successfully</h2>";
    } catch (Exception $ex) {
      $edit_state = "<h2 class='text-center text-danger'>Error Editing Data $ex</h2>";
    }
  }
} // end of POST

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["customers"])) {

  // Create Customers
  // uid, customername, item, price, quantity, fullprice [basic table]
  // $sql = "CREATE TABLE `login_system`.`$uid_customers` (`uid` INT(11) NOT NULL , `customername` VARCHAR(255) DEFAULT 'unknown', `item` VARCHAR(255) DEFAULT 'unknown', `price` int(11) DEFAULT 0, `quantity` int(11) DEFAULT 0, `fullprice` int(11) DEFAULT 0, FOREIGN KEY (uid) REFERENCES users(uid) ON UPDATE CASCADE ON DELETE CASCADE ) ENGINE = InnoDB;";
  $uid_customers = $uid . "_customers";
  $stmt3 = $conn->prepare("CREATE TABLE IF NOT EXISTS
$uid_customers (`uid` int(11) NOT NULL , `customername` VARCHAR(255) DEFAULT 'unknown', `item` VARCHAR(255) DEFAULT 'unknown', `price` int(11) DEFAULT 0, `quantity` int(11) DEFAULT 0, `fullprice` int(11) DEFAULT 0, FOREIGN KEY (uid) REFERENCES users(uid) ON UPDATE CASCADE ON DELETE CASCADE ) ENGINE = InnoDB;");
  // $stmt->bind_param("s", $uid_customers);
  try {
    $stmt3->execute();
    $_SESSION["uid_customers"] = $uid_customers;
    $customers_table = "Customers table Created Successfully";
    // Redirect after 2 Seconds
    $_SESSION["manage_customers"] = true;
    // header("Refresh: 2; URL=manage_customers.php"); // another way
    echo '
<script>
setTimeout(() => {
  location.href="manage_customers.php";
}, 1500);
</script>
';
  } catch (Exception $ex) {
    // echo "Error Creating Customers table $ex";
    die;
  }
} // end of create customers

?>

<h1>Welcome, <?php echo $row["username"] ?? "" ?></h1>
<h2>Hope You The Best..</h2>
<hr>
<section class="my-3">
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="d-grid w-100 justify-content-center" method="post">
    <button name="customers" value="create" type="submit" class="btn btn-secondary text-light px-5 py-2 my-3">Create Customers Table</button>
    <h3 class="text-center text-success"><?php echo $customers_table ?? "" ?></h3>
  </form>
</section>
<hr class="my-3">
<?php echo $edit_state ?? "" ?>
<h1 class="text-center">Edit Profile Settings</h1>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="p-3">
  <div class="mb-3">
    <label for="fname" class="form-label">First Name</label>
    <input class="form-control" name="fname" id="fname" value="<?php echo $fname ?>" type="text" placeholder="First Name" />
    <span><?php echo $fnameErr ?></span>
  </div>
  <div class="mb-3">
    <label for="lname" class="form-label">Last Name</label>
    <input class="form-control" id="lname" name="lname" value="<?php echo $lname ?>" type="text" placeholder="Last Name" />
    <span><?php echo $lnameErr ?></span>
  </div>
  <div class="mb-3">
    <label for="utype" class="form-label">User Type</label>
    <input class="form-control" list="utypes" id="utype" name="utype" value="<?php echo $utype ?>" type="text" placeholder="Type Could Be, Admin, User, Manager etc.." />
    <span><?php echo $utypeErr ?></span>
    <datalist id="utypes">
      <option value="Owner">
      <option value="Manager">
      <option value="Admin">
      <option value="Normal User">
    </datalist>
  </div>
  <section class="d-grid w-100 justify-content-center">
    <button type="submit" name="edit" value="edit" class="btn btn-primary px-5 py-2 my-3 d-block mx-auto">Edit</button>
  </section>
</form>
<hr>