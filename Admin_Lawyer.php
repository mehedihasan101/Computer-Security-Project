<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Verification</title>

    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #0077cc;
            font-size: 28px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .table-box {
            width: 100%;
            margin-top: 20px;
        }

        .table-box table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table-box table th,
        .table-box table td {
            padding: 15px;
            border: 1px solid #e0e0e0;
            text-align: center;
        }

        .table-box table th {
            background-color: #0077cc;
            color: white;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-box table td {
            background-color: #fff;
            color: #333;
        }

        /* Button styles */
        .accept-btn,
        .reject-btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .accept-btn {
            background-color: #4CAF50;
            color: white;
        }

        .accept-btn:hover {
            background-color: #45a049;
        }

        .reject-btn {
            background-color: #f44336;
            color: white;
            margin-left: 5px;
        }

        .reject-btn:hover {
            background-color: #e03131;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .table-box table th,
            .table-box table td {
                font-size: 14px;
                padding: 10px;
            }

            h2 {
                font-size: 24px;
            }

            .accept-btn,
            .reject-btn {
                font-size: 14px;
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
<section class="header">
    <?php
    include("admin_navbar.php");
    ?>
    
    <div class="text-box">
        <h1>Lawyer Verification</h1>
    </div>
</section>
    <div class="container">
        <h2>Lawyer Verification</h2>
        <div class="table-box">
            <table border="1">
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Lawyer ID</th>
                    <th>Action</th>
                </tr>
                <!-- PHP code to fetch data from the database -->
                <?php
                    // Connect to your database
                    $host = 'localhost';
                    $dbuser = 'root';
                    $dbpass = '';
                    $dbname = 'adr';
                    $conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // If accept button is clicked
                    if (isset($_POST['accept'])) {
                        $lawyerId = $_POST['accept'];
                        // Update the status in the database to 'done'
                        $update_sql = "UPDATE lawyer SET status='done' WHERE lawyer_id='$lawyerId'";
                        $update_sql2 = "UPDATE user SET status='lawyer' WHERE user.email= (SELECT email FROM lawyer WHERE lawyer_id='$lawyerId')";
                        if ($conn->query($update_sql) === TRUE && $conn->query($update_sql2) === TRUE) {
                            echo "<script>alert('Record updated successfully');</script>";
                        } else {
                            echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
                        }
                    }

                    // If reject button is clicked
                    if (isset($_POST['reject'])) {
                        $lawyerId = $_POST['reject'];
                        $delete_sql = "DELETE FROM lawyer WHERE lawyer_id='$lawyerId'";
                        if ($conn->query($delete_sql) === TRUE) {
                            echo "<script>alert('Record deleted successfully');</script>";
                        } else {
                            echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
                        }
                    }

                    // SQL query to select data from your table
                    $sql = "SELECT full_name, email, lawyer_id FROM lawyer WHERE status='pending'";
                    $result = $conn->query($sql);

                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['full_name']."</td>";
                        echo "<td>".$row['email']."</td>";
                        echo "<td>".$row['lawyer_id']."</td>";
                        echo "<td>
                                <form method='post'>
                                    <button type='submit' class='accept-btn' name='accept' value='".$row['lawyer_id']."'>Accept</button>
                                    <button type='submit' class='reject-btn' name='reject' value='".$row['lawyer_id']."'>Reject</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    $conn->close();
                ?>
                <!-- End of PHP code -->
            </table>
        </div>
    </div>
</body>
</html>
