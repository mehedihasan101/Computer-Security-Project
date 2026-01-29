


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alliance Consultancy Firm</title>
    <link rel="stylesheet" href="index2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<section class="header">
    <?php
    include("admin_navbar.php");
    ?>
    
    <div class="text-box">
        <h1>Admin Dashboard</h1>
    </div>
</section>
<!-- MEDIATION SECTION---->
    <section class="facilities">
        <h1> Our Mediation Service</h1>
        <p>description if needed</p>
        <div class="row">
            <div class="facilities-col">
                <a href="medFormcheck.php">
                    <img src="mediationForm.jpg" alt="">
                    <h3>Mediation Form Service</h3>
                    <p>have to write small paragraph</p>
                </a>
                
            </div>
            <div class="facilities-col">
                <a href="Med_Proposal_check.php">
                    <img src="mediationProposal.jpg" alt="">
                <h3>Mediation Case File Service</h3>
                <p>have to write small paragraph</p>
                </a>
                
            </div>
        </div>
    </section>
    <!-- Arbitration SECTION---->
    <section class="facilities">
        <h1> Our Arbitration Service</h1>
        <p>description if needed</p>
        <div class="row">
            <div class="facilities-col">
                <a href="ArbFormcheck.php">
                    <img src="arbitrationForm.jpg" alt="">
                    <h3>Arbitration Form Service</h3>
                    <p>have to write small paragraph</p>
                </a>
                
            </div>
            <div class="facilities-col">
                <a href="Arb_Proposal_check.php">
                    <img src="arbitrationProposal.jpg" alt="">
                <h3>Arbitration Case File Service</h3>
                <p>have to write small paragraph</p>
                </a>
                
            </div>
        </div>
    </section>
    
    <!-- arbitrator and mediator-->
    <section class="facilities">
        <h1> Arbitrator and mediator update section</h1>
        <p>description if needed</p>
        <div class="row">
            <div class="facilities-col">
                <a href="admin_arbitrator.php">
                    <img src="arbitrationForm.jpg" alt="">
                    <h3>Arbitrator Update Service</h3>
                    <p>have to write small paragraph</p>
                </a>
                
            </div>
            <div class="facilities-col">
                <a href="admin_mediator.php">
                    <img src="arbitrationProposal.jpg" alt="">
                <h3>Mediator Update Service</h3>
                <p>have to write small paragraph</p>
                </a>
                
            </div>
        </div>
    </section>
    <!-- Lawyers SECTION---->
    <section class="facilities">
        <h1> Our Lawyer's recruit Service</h1>
        <p>description if needed</p>
        <div class="row">
            <div class="facilities-col">
                <a href="Admin_Lawyer.php">
                    <img src="lawyer.png" alt="">
                    <h3>Lawyers Verification Service</h3>
                    <p>have to write small paragraph</p>
                </a>
                
            </div>
            
        </div>
    </section>

    <?php //include("footer.php"); ?>
</body>
</html>