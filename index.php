<?php
include('db.php');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Solution Consultancy Firm</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <section class="header">
<!--
        include("navbar.php");
-->
        <div class="text-box">
            <h1>Alliance Consultancy Firm</h1>
            <p>
                We will fight for you like a friend. We provide high quality law advice and support.
            </p>
                <a href="login.php"class="hero-btn">login</a>
        </div>
    </section>
    <section class="features">
        <h2>Our Aims & Objective </h2>
        <p>
            According to 2024,now over 42 lacs cases are still pending in Bangladesh. in our country,
            We don’t have enough lawyer and judge to solve these cases. Our aim is to provide all types of legal services 
            to the customer through expert mediators, arbitrators and lawyers in short time and at low cost. Also we want to 
            give the facility of juniorship to the junior lawyers so that they can work as an apprentice lawyer after 
            completion of graduation. 
        </p>
    </section>
    <section class="features">
        <h2>Our Service List </h2>
        <div class="row g-3">
            <div>
                <ul style="text-align:left;">
                    <li><p>Alternative Dispute Resolution</p></li>
                    <li><p>Land Acqusition</p></li>
                    <li><p>Commercial Leases</p></li>
                    <li><p>Real Estate</p></li>
                    <li><p>Re-development</p></li>
                </ul>
            </div>
            <div>
                <ul style="text-align:left;">
                    <li><p>Devorce / Separation</p></li>
                    <li><p>Wills And Estates,Power of Attorney</p></li>
                    <li><p>Division of Family Property</p></li>
                    <li><p>Legal Opinion & Litigation</p></li>
                    <li><p>Joint Development Agreement</p></li>
                </ul>
            </div>
            <div>
                <ul style="text-align:left;">
                    <li><p>IT Park</p></li>
                    <li><p>General Documentation</p></li>
                    <li><p>Advisory, Due Diligence & Title Investigation</p></li>
                    <li><p>Business Acqusition, Mergers & Demergers</p></li>
                    <li><p>Adoption</p></li>
                </ul>
            </div>
            
        </div>
        
        
              
    </section>
    <!-- Features We want to show on the initial view-page-->
    <section class="features">
        <h2>Most Active Lawyer </h2>
        <!-- <p>description if needed</p> -->
        <!-- <div class="feature-row"> -->
            <?php
               $queryL= "SELECT u.profilepic as pp,c.email,l.full_name as fullname,l.catagory as catagory,l.court as court ,l.lawyer_id as lawyer_id,count(*)
               FROM 
               user AS u 
                   JOIN
                   comment_box AS c
                   ON (c.email=u.email)
                   JOIN lawyer as l
                   ON (u.email=l.email)
           
                   WHERE u.status='lawyer'
                   GROUP BY c.email
                   ORDER BY COUNT(*) DESC
                   limit 4";
               $resultL = mysqli_query($conn, $queryL)
            ?>
            <style>
                /* Custom animation for lawyer cards */
                .lawyer-card {
                    position: relative;
                    overflow: hidden;
                    transition: transform 0.4s ease, box-shadow 0.4s ease;
                }
                .lawyer-card:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
                }

                /* Increase circle size, border, and padding for images */
                .lawyer-image {
                    width: 200px;
                    height: 200px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 5px solid #007bff;
                    padding: 7px;
                }
            </style>
            
            <div class="row g-4">
            <?php
                // Check if there are lawyers in the result
                if (mysqli_num_rows($resultL) > 0) {
                    while ($lawyer = mysqli_fetch_assoc($resultL)) {
                        ?>
                        <div class="col-lg-3 col-md-6 wow fadeInUp lawyer-card" data-wow-delay="0.2s">
                            <div class="card border-0 shadow-sm h-100 text-center p-4">
                                <div class="card-body">
                                    <!-- Add image in the card with larger circle, border, and padding -->
                                    <?php echo "<img class='img-fluid rounded-circle mb-4 lawyer-image' src='".$lawyer['pp']."'>"; ?>
                                    <h6 class="card-title text-black"><?php echo htmlspecialchars($lawyer['fullname']); ?></h6>
                                    <p class="card-text text-black">Category: <?php echo htmlspecialchars($lawyer['catagory']); ?></p>
                                    <p class="card-text">Court: <?php echo htmlspecialchars($lawyer['court']); ?></p>
                                    </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-center'>No lawyers found.</p>";
                }
                ?>
            </div>

        <!-- </div> -->
    </section>

    <section class="features">
        <h2>Our Mediator </h2>
        <?php
            
            // Fetch mediators from the database based on the search term
            $query = "SELECT * FROM mediator Limit 4";
            // Execute query
            $result = mysqli_query($conn, $query);
        ?>
        <div class="container">
            <div class="row g-4">
                <?php
                // Check if any mediators found
                if (mysqli_num_rows($result) > 0) {
                    // Fetch mediators and display their info
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Retrieve mediator information
                        $name = $row['name'];
                        $prof = $row['profession'];
                        $exp = $row['qualification'];
                        $image = 'medpic/' . $row['pic']; // Adjusted image path
                        ?>
                        <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="lawyer-card team-item text-center rounded overflow-hidden">
                                <div class="team-img position-relative">
                                    <img class="lawyer-image img-fluid" src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
                                </div>
                                <div class="p-4">
                                    <h5 class="mb-0"><?php echo $name; ?></h5>
                                    <small><?php echo $prof; ?></small><br>
                                    <small><?php echo $exp; ?></small>
                                    
                                </div>
                                </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No mediators found.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <section class="features">
        <h2>Our Arbitrator </h2>
        <?php
            $query = "SELECT * FROM arbitrator Limit 3";

            // Execute query
            $result = mysqli_query($conn, $query);

        ?>
        <div class="container">
            <div class="row g-4">
                <?php
                // Check if any mediators found
                if (mysqli_num_rows($result) > 0) {
                    // Fetch mediators and display their info
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Retrieve mediator information
                        $name = $row['name'];
                        $prof = $row['profession'];
                        $qual = $row['qualification'];
                        $image = 'arbpic/' . $row['pic']; // Adjusted image path
                        ?>
                        <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="lawyer-card team-item text-center rounded overflow-hidden">
                                <div class="team-img position-relative">
                                    <img class="lawyer-image img-fluid" src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
                                </div>
                                <div class="p-4">
                                    <h5 class="mb-0"><?php echo $name; ?></h5>
                                    <small><?php echo $prof; ?></small><br>
                                    <small><?php echo $qual; ?></small>
                                    
                                </div>
                                </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No mediators found.</p>';
                }
                ?>
            </div>
        </div>
    </section>
    
</body>
</html>