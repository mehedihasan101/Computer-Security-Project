<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Arbitrator Management</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">Arbitrator Management</a>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="admin.php">Home</a>
            </li>
            <li class="nav-item">
              <a type="button" class="btn btn-primary nav-link" href="admin_arbitrator_add.php">Add New</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container my-4">
      <h2 class="text-center mb-4">Arbitrator List</h2>
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Experience</th>
            <th>Qualification</th>
            <th>Picture</th>
            <th>Profession</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          include "db.php";
          $sql = "SELECT * FROM arbitrator";
          $result = $conn->query($sql);
          
          if (!$result) {
              die("Invalid query: " . $conn->error);
          }

          $serial = 1; // Counter for serial numbers
          while ($row = $result->fetch_assoc()) {
              echo "
              <tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['experience']}</td>
                <td>{$row['qualification']}</td>
                <td><img src='arbpic/{$row['pic']}' height='100px' width='100px' alt='Profile Picture'></td>
                <td>{$row['profession']}</td>
                <td>{$row['status']}</td>
                <td>
                  <a class='btn btn-success' href='admin_arbitrator_edit.php?id={$row['id']}'>Edit</a>
                  <a class='btn btn-danger' href='admin_arbitrator_delete.php?id={$row['id']}'>Delete</a>
                </td>
              </tr>";
              $serial++;
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>
