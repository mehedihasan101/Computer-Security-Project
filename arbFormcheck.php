<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arbitration Form Check</title>

    <style>
        /* Reset and box-sizing */
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
            max-width: 1000px;
            margin: 40px auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
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
            overflow-x: auto;
        }

        .table-box table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f9f9f9;
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
        .reject-btn {
            padding: 10px 15px;
            background-color: #f44336;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .reject-btn:hover {
            background-color: #e03131;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .table-box table th,
            .table-box table td {
                font-size: 16px;
                padding: 10px;
            }

            h2 {
                font-size: 22px;
            }

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
        <h1>Arbitration Default Form cancel request</h1>
    </div>
</section>
    <div class="container">
        <h2>Arbitration Form Verification</h2>
        <div class="table-box">
            <table border="1">
                <tr>
                    <th>Case Number</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
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

                    // If reject button is clicked
                    if (isset($_POST['reject'])) {
                        $casenumber = $_POST['reject'];
                        $delete_sql = "DELETE FROM arbitration_case WHERE casenumber='$casenumber'";
                        if ($conn->query($delete_sql) === TRUE) {
                            echo "<script>alert('Record deleted successfully');</script>";
                        } else {
                            echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
                        }
                    }

                    // SQL query to select data from your table
                    $sql = "SELECT ac.casenumber, ac.fullname, ac.email, ac.phone
                            FROM arbitration_proposal AS ap
                            JOIN arbitration_case AS ac 
                            ON (ac.casenumber = ap.casenumber)
                            WHERE ap.status ='pending' ORDER BY ac.casenumber;";
                    $result = $conn->query($sql);

                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['casenumber']."</td>";
                        echo "<td>".$row['fullname']."</td>";
                        echo "<td>".$row['email']."</td>";
                        echo "<td>".$row['phone']."</td>";
                        echo "<td>
                                <form method='post'>
                                    <button type='submit' class='reject-btn' name='reject' value='".$row['casenumber']."'>Remove</button>
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
