<?php
session_start();
include("db.php");

$error = $success = "";
$fullname = $note = $email = $phone = $existing_photo = "";

if (isset($_SESSION['user_email'])) {
    $userprofile = $_SESSION['user_email'];

    // Fetch user details
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $userprofile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $fullname = $data["fullname"];
        $note = $data["note"];
        $email = $data["email"];
        $phone = $data["phone"];
        $existing_photo = !empty($data["profilepic"]) ? $data["profilepic"] : "";

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
            $fullname = trim($_POST["fullname"]);
            $note     = trim($_POST["note"]);
            $email    = trim($_POST["email"]);
            $phone    = trim($_POST["phone"]);

            $folder2 = $existing_photo; // keep old photo by default

            // Handle new photo upload
            if (!empty($_FILES['photo']["name"])) {
                $filename2 = $_FILES['photo']["name"];
                $tempname2 = $_FILES['photo']["tmp_name"];
                $filesize  = $_FILES['photo']["size"];
                $filetype  = mime_content_type($tempname2);

                // Ensure images2 directory exists
                if (!file_exists('images2')) {
                    mkdir('images2', 0777, true);
                }

                $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
                $allowed_mimes = array('image/jpeg', 'image/png', 'image/gif');

                $file_extension = strtolower(pathinfo($filename2, PATHINFO_EXTENSION));

                if (!in_array($file_extension, $allowed_extensions)) {
                    $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
                } elseif (!in_array($filetype, $allowed_mimes)) {
                    $error = "Invalid file type. Please upload an image.";
                } elseif ($filesize > 2097152) { // 2 MB in bytes
                    $error = "File size must not exceed 2 MB.";
                } else {
                    $newfilename = uniqid("profile_", true) . "." . $file_extension;
                    $folder2 = "images2/" . $newfilename;

                    if (!move_uploaded_file($tempname2, $folder2)) {
                        $error = "Failed to upload the image.";
                    }
                }
            }

            if (empty($error)) {
                $stmt2 = $conn->prepare("UPDATE user SET fullname=?, email=?, phone=?, profilepic=?, note=? WHERE email=?");
                $stmt2->bind_param("ssssss", $fullname, $email, $phone, $folder2, $note, $userprofile);

                if ($stmt2->execute()) {
                    $_SESSION['user_email'] = $email; // update session if email changed
                    header("Location: profile.php?msg=Profile updated successfully");
                    exit();
                } else {
                    $error = "Error updating profile. Please try again.";
                }
                $stmt2->close();
            }
        }
    } else {
        $error = "No user data found.";
    }
} else {
    $error = "User not logged in. Please <a href='login.php'>Login</a>.";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

    <script>
        function previewPhoto(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.createElement('img');
                output.src = reader.result;
                output.style.maxWidth = '200px';
                document.getElementById('photo-preview').innerHTML = '';
                document.getElementById('photo-preview').appendChild(output);
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fw-bold">
    <div class="container-fluid">
        <a class="navbar-brand" href="HomePage.php">Alliance</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="HomePage.php">Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="col-lg-6 m-auto">
    <form action="#" method="post" enctype="multipart/form-data">
        <br><br>
        <div class="card">
            <div class="card-header bg-warning">
                <h1 class="text-white text-center"> Update Profile </h1>
            </div><br>

            <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
            <?php if (!empty($_GET['msg'])) { echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['msg']) . "</div>"; } ?>

            <label> NAME: </label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" class="form-control"> <br>

            <label> EMAIL: </label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control"> <br>

            <label> PHONE: </label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control"> <br>

            <div class="form-group">
                <label>Note:</label>
                <textarea class="form-control border-1" rows="5" name="note"><?php echo htmlspecialchars($note); ?></textarea>
            </div>

            <div>
                <label for="photo">Upload Photo (Max 2MB):</label>
                <input type="file" id="photo" name="photo" accept="image/*" onchange="previewPhoto(event)">
                <div id="photo-preview" style="margin-top: 10px;">
                    <?php if (!empty($existing_photo)): ?>
                        <img src="<?php echo htmlspecialchars($existing_photo); ?>" style="max-width: 200px;">
                    <?php endif; ?>
                </div>
            </div><br>

            <button class="btn btn-success" type="submit" name="submit"> Save Changes </button><br>
            <a class="btn btn-info" href="profile.php"> Cancel </a><br>
        </div>
    </form>
</div>
</body>
</html>
