<?php
session_start();
session_unset();
session_destroy();

// Prevent going back after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login page
echo "<script>
    localStorage.clear();
    sessionStorage.clear();
    window.location.replace('../index.php');
</script>";
exit();
