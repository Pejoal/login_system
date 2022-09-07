<?php

session_start();
session_unset();
session_destroy();
// header("Location: index.php"); // doesn't work on the real server so will use javascript
echo '
<script>
  location.href="index.php";
</script>
';
exit;
/* 
echo '
<script>
setTimeout(() => {
  location.href="index.php";
}, 1500);
</script>
';
*/