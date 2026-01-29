 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADR</title>
    <style>
        *{
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'poppins',sans-serif ; 
        }
        header{
            display: flex; 
            justify-content: center; 
            align-items: center;
            height: 100px; 
            width:100%;
            padding: 0 5%;
            position: absolute;
            weight:100px; 
            margin-bottom: 41%; 
            
        }
        header img{
            height: 50px;
            width: 100px;
            position: cover;
            margin-left:50px;
        }
        header nav{ 
            display: flex; 
            gap: 20px;
            /* margin-left: 300px;  */
        }
        header nav a{
            text-decoration: none;
            font-size: 20px;
            font-weight: 600;  
            padding: 8px 15px;
            border-radius: 50px; 
            transition: 0.3s;
            color: white;
        }
        
        header nav a:hover{
            background: skyblue;
        }
        
      .basic:hover{
        background: skyblue;
      }
      header h1 a:hover{
            background: skyblue;
            border-radius:6px; 
        }
    </style>
</head>
<body>
    <header>
        <!-- <a href="HomePage.php"><img src="logo.png" alt="LOGO"></a> -->
        <nav>
            <a href="Med_Proposal_check.php">Mediation</a>
            <a href="medFormcheck.php">Med Form</a>
            <a href="Arb_Proposal_check.php">Arbtration</a>
            <a href="ArbFormcheck.php">Arb Form</a>
            <a href="Admin_Lawyer.php"> Lawyer</a>
            <!-- <a href="Querry.php"> Querry</a> -->
            <a href="logout.php" style="color:red">Logout</a>
            <!-- <a href="Aboutus.php">About Us</a> -->
        </nav>
    </header>  

</body>
</html>