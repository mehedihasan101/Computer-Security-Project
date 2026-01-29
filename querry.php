
<?php

session_start();
$conn = mysqli_connect("localhost", "root", "", "ADR");

$userprofile = $_SESSION['useremail'];

$sql = "SELECT* From user WHERE email='$userprofile'"; 
$result = $conn->query($sql);
$data = mysqli_fetch_assoc($result);

if(isset($_POST["submit"])){
  $name = $data['fullname'];
  $comment = $_POST["comment"];
  $date = date('F d Y, h:i:s A');
  $email = $data['email'];
  $reply_id = $_POST["reply_id"];
  //$pp = $data['profilepic'];

  $query = "INSERT INTO comment_box VALUES('', '$name','$email', '$comment', '$date', '$reply_id')";
  mysqli_query($conn, $query);
}

?>

<html>
  <head>
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <div class="container-fluid bg-light p-0 wow fadeIn" data-wow-delay="0.1s">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-map-marker-alt text-primary me-2"></small>
                    <small>Baridhara, Gulshan-2, Dhaka-1212</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center py-3">
                    <small class="far fa-clock text-primary me-2"></small>
                    <small>Sat - Thu : 09.00 AM - 09.00 PM</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-phone-alt text-primary me-2"></small>
                    <small>+88 01780337775</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                    <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href=""><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-sm-square rounded-circle bg-white text-primary me-0" href=""><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
    <a href="Homepage.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h1 class="m-0 text-primary"><i class="fas fa-landmark me-3"></i>Alliance</h1>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="Homepage.php" class="nav-item nav-link">Home</a>
            <a href="aboutus.php" class="nav-item nav-link ">About</a>
            <a href="others.php" class="nav-item nav-link ">expertise</a>
            <a href="mediator.php" class="nav-item nav-link">Mediator</a>
            <a href="arbitrator.php" class="nav-item nav-link">Arbitrator</a>
            <a href="querry.php" class="nav-item nav-link active">Query</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle " data-bs-toggle="dropdown">Service</a>
                <div class="dropdown-menu rounded-0 rounded-bottom m-0">
                    <a href="Arbitration_proposal.php" class="dropdown-item">Arbitration Proposal</a>
                    <a href="Arbitration.php" class="dropdown-item">Arbitration Case File</a>
                    <a href="mediation_proposal.php" class="dropdown-item">Mediation Proposal</a>
                    <a href="mediation.php" class="dropdown-item">Mediation Case File</a>
                    <a href="lawyer.php" class="dropdown-item">Lawyers Info</a>
                    
                </div>
            </div>
            <a href="profile.php" class="nav-item nav-link">Profile</a>
        </div>
        <a href="lawyer_registration.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Lawyer Registration</a>
    </div>
</nav>

<!-- <?php// include("header.php"); ?> -->

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
  </head>

  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background-color: #f5f7fa;
      color: #333;
      padding: 20px;
    }

    .container {
      background: #fff;
      width: 60%;
      margin: 0 auto;
      margin-top: 60px;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .comment, .reply {
      padding: 15px;
      border-radius: 8px;
      margin-top: 10px;
      background-color: #fafafa;
      border-left: 4px solid #4CAF50;
      transition: background-color 0.3s ease;
    }

    .comment:hover, .reply:hover {
      background-color: #f0f0f0;
    }

    .reply {
      background-color: #f9f9f9;
      margin-left: 20px;
      border-left: 4px solid #ffa500;
    }

    .comment-header {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .profile-photo {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
      border: 2px solid #ddd;
    }

    .comment-name {
      margin: 0;
      font-size: 1.2em;
      font-weight: 600;
      color: #333;
    }

    .comment p {
      font-size: 1em;
      color: #666;
    }

    .comment-date {
      font-size: 0.85em;
      color: #aaa;
    }

    form {
      margin-top: 30px;
    }

    form h3 {
      margin-bottom: 20px;
      font-size: 1.5em;
      font-weight: 600;
    }

    form input, form textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ddd;
      box-sizing: border-box;
      font-size: 1em;
      background-color: #fafafa;
      transition: border-color 0.3s ease;
    }

    form input:focus, form textarea:focus {
      border-color: #4CAF50;
      outline: none;
    }

    form textarea {
      resize: vertical;
    }

    form button.submit {
      background: #4CAF50;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 1.1em;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    form button.submit:hover {
      background: #45a049;
    }

    button.reply {
      background: #ffa500;
      color: white;
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button.reply:hover {
      background: #e59400;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      font-size: 0.9em;
      color: #333;
    }
  </style>

  <body>
    <div class="container">
      <?php
        $commentposts = mysqli_query($conn, "SELECT u.profilepic,c.id,c.name,c.email,c.date,c.comment, c.reply_id
         FROM comment_box as c join user as u on c.email=u.email  WHERE reply_id = 0"); // only select comment and not select reply
        foreach($commentposts as $commentpost) {
          require 'comment.php';
        }
      ?>

      <form action="" method="post">
        <h3 id="title">Leave a Comment</h3>
        
        <input type="hidden" name="reply_id" id="reply_id">
        
        <div class="form-group">
          
          <input type="hidden" name="profilepic" placeholder="Enter the URL of your profile picture">
        </div>

        <div class="form-group">
          
          <input type="hidden" name="name" placeholder="Your name">
        </div>

        <div class="form-group">
          
          <textarea name="comment" rows="4" placeholder="Write your comment here"></textarea>
        </div>

        <button class="submit" type="submit" name="submit">Submit</button>
      </form>
    </div>

    <script>
      function reply(id, name) {
        title = document.getElementById('title');
        title.innerHTML = "Reply to " + name;
        document.getElementById('reply_id').value = id;
      }
    </script>
  </body>
</html>
