<?php
if (!defined("START_ADMIN") || START_ADMIN !== true) {
  echo '
  <h1 class="text-center">Create Customers Table First</h1>
  <script>
  setTimeout(() => {
    location.href="dashboard.php";
  }, 1500);
  </script>
  ';
  exit;
}
// die('Not Admin');

$stmt = $conn->prepare("SELECT users.username, users.update_time, user_data.firstname, user_data.lastname, user_data.user_type FROM users INNER JOIN user_data ON user_data.uid = users.uid");
try {
  $stmt->execute();
  // echo "Customer Selected Successfully";
  $result = $stmt->get_result();
  $get_data = "";
  // $user = $result->fetch_all(MYSQLI_ASSOC); // if more than on record use this then foreach loop ouput data
  if ($result->num_rows > 0) {
    // output data of each row
    $get_data .= '
      <table class="table table-striped table-hover table-inverse table-responsive table-active table-dark my-4 mx-auto container text-center text-capitalize">
      <thead class="thead-inverse">
        <tr>
          <th>User Name</th>
          <th>Fisrt Name</th>
          <th>Last Name</th>
          <th>User Type</th>
          <th>Date Created</th>
        </tr>
        </thead>
        <tbody>
      ';
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $get_data .= "
        <tr>
          <td scope='row'>$row[username]</td>
          <td scope='row'>$row[firstname]</td>
          <td scope='row'>$row[lastname]</td>
          <td scope='row'>$row[user_type]</td>
          <td>$row[update_time]</td>
        </tr>
        ";
    }
    $get_data .= '
      </tbody>
      </table>
      ';
  } else {
    $get_data .= "0 Results";
  }
} catch (Exception $ex) {
  echo "Error Selecting Users Data $ex";
}
  // $conn->close();
