<?php
// database connection: username, password, database name
  $conn = new mysqli("localhost", "cacyof_user", "cacyof@26", "cacyof_db");
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }
?>