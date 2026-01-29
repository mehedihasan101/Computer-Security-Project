<?php
include "db.php";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $experience = $_POST['experience'];
    $qualification = $_POST['qualification'];
    $profession = $_POST['profession'];
    $status = $_POST['status'];

    // Handling file upload
    $pic = $_FILES['pic']['name'];
    $target_dir = "medpic/";
    $target_file = $target_dir . basename($pic);
    $uploadOk = 1;

    // Check if image file is a valid image
    $check = getimagesize($_FILES['pic']['tmp_name']);
    if ($check === false) {
        echo '<script>alert("File is not an image.");</script>';
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo '<script>alert("Sorry, file already exists.");</script>';
        $uploadOk = 0;
    }

    // Only upload if there are no errors
    if ($uploadOk == 1) {
        move_uploaded_file($_FILES['pic']['tmp_name'], $target_file);

        // Check if the email already exists
        $sql = "SELECT * FROM mediator WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<script>
                alert("This user already exists. Enter another email.");
                window.location.href="admin_mediator.php";
            </script>';
        } else {
            // Insert into database
            $insert_sql = "INSERT INTO mediator (name, email, phone, experience, qualification, profession, status, pic) 
                           VALUES ('$name', '$email', '$phone', '$experience', '$qualification', '$profession', '$status', '$pic')";
            if ($conn->query($insert_sql) === TRUE) {
                header("Location: admin.php");
            } else {
                echo '<script>alert("Error: ' . $conn->error . '");</script>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Mediator</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <style>
        .card-header {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }
        .form-control, .btn {
            margin-bottom: 15px;
        }
        .container {
            margin-top: 50px;
        }
        .btn-block {
            text-transform: uppercase;
            font-weight: bold;
        }
        label {
            font-weight: bold;
        }
        textarea.form-control {
            resize: vertical;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">Alliance</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="col-lg-6 m-auto">
        <form method="post" enctype="multipart/form-data">
            <div class="card">
                <div class="card-header text-center">
                    <h1>Add New Mediator</h1>
                </div>
                <div class="card-body">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control" required>

                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" required>

                    <label for="phone">Phone</label>
                    <input type="text" name="phone" class="form-control" required>

                    <label for="experience">Experience</label>
                    <textarea name="experience" class="form-control" rows="4" required></textarea>

                    <label for="qualification">Qualification</label>
                    <textarea name="qualification" class="form-control" rows="4" required></textarea>

                    <label for="profession">Profession</label>
                    <textarea name="profession" class="form-control" rows="4" required></textarea>

                    <label for="status">Status</label>
                    <input type="text" name="status" class="form-control" required>

                    <label for="pic">Picture</label>
                    <input type="file" name="pic" class="form-control" required>

                    <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                    <a href="admin.php" class="btn btn-info btn-block">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>
