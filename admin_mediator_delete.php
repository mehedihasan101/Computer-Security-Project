<?php
include "db.php";
$id = $_GET["id"];
$sql = "DELETE FROM mediator WHERE id = '$id'";
$result = mysqli_query($conn, $sql);

if ($result) {
  header("Location: admin.php?msg=Data deleted successfully");
} else {
  echo "Failed: " . mysqli_error($conn);
}