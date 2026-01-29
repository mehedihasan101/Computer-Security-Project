<?php
include "db.php";
$id = $_GET["id"];

// Fetch the existing arbitrator's details
$sql = "SELECT * FROM mediator WHERE id = '$id' LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (isset($_POST["submit"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $experience = $_POST["experience"];
    $qualification = $_POST["qualification"];
    $profession = $_POST["profession"];
    $status = $_POST["status"];

    // Handle picture update
    $pic = $_FILES["pic"]["name"];
    if ($pic) {
        $target_dir = "medpic/";
        $target_file = $target_dir . basename($pic);
        move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file);
    } else {
        // Keep the existing picture if no new one is uploaded
        $pic = $row['pic'];
    }

    // Update the database
    $sql = "UPDATE mediator SET name='$name', email='$email', phone='$phone', experience='$experience', qualification='$qualification', profession='$profession', status='$status', pic='$pic' WHERE id='$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: admin.php?msg=Data updated successfully");
    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <title>Edit Mediator Profile</title>

  <style>
    body {
      background-color: #f7f7f7;
    }

    .navbar {
      background-color: #007bff;
      color: white;
    }

    .container {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .form-control {
      margin-bottom: 15px;
    }

    .btn {
      width: 100%;
    }

    .preview-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 10px;
      margin-top: 10px;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-light justify-content-center fs-3 mb-5">
    Admin Mediator Edit Profile
  </nav>

  <div class="container">
    <div class="text-center mb-4">
      <h3>Edit Mediator Information</h3>
      <p class="text-muted">Click update after changing any information</p>
    </div>

    <div class="container d-flex justify-content-center">
      <form action="" method="post" enctype="multipart/form-data" style="width:50vw; min-width:300px;">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <div class="mb-3">
          <label for="name" class="form-label">Name:</label>
          <input type="text" class="form-control" name="name" value="<?php echo $row['name']; ?>" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" name="email" value="<?php echo $row['email']; ?>" required>
        </div>

        <div class="mb-3">
          <label for="phone" class="form-label">Phone:</label>
          <input type="tel" class="form-control" name="phone" value="<?php echo $row['phone']; ?>" required>
        </div>

        <div class="mb-3">
          <label for="qualification" class="form-label">Qualification:</label>
          <textarea class="form-control" name="qualification" rows="3" required><?php echo $row['qualification']; ?></textarea>
        </div>

        <div class="mb-3">
          <label for="experience" class="form-label">Experience:</label>
          <textarea class="form-control" name="experience" rows="3" required><?php echo $row['experience']; ?></textarea>
        </div>

        <div class="mb-3">
          <label for="profession" class="form-label">Profession:</label>
          <textarea class="form-control" name="profession" rows="3" required><?php echo $row['profession']; ?></textarea>
        </div>

        <div class="mb-3">
          <label for="status" class="form-label">Status:</label>
          <input type="text" class="form-control" name="status" value="<?php echo $row['status']; ?>" required>
        </div>

        <div class="mb-3">
          <label for="pic" class="form-label">Picture:</label>
          <input type="file" class="form-control" name="pic" id="pic" accept="image/*" onchange="previewImage(event)">
          <img id="preview" class="preview-img" src="medpic/<?php echo $row['pic']; ?>" alt="Current Image">
        </div>

        <div class="mb-3">
          <button type="submit" name="submit" class="btn btn-success">Update</button>
          <a href="admin.php" class="btn btn-danger">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Image Preview Script -->
  <script>
    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function () {
        const output = document.getElementById('preview');
        output.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</body>

</html>
