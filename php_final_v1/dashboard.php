<?php
include_once './includes/header.php';
require_once "./includes/functions.php";
redirectToSignInIfNotLoggedIn();
?>
<?php

include_once './includes/header.php';
?>
<div class="container" id="content">
    <div class="row content-box">
        <div class="col-md-12 box">

            <div class="heading">
                This is Dashboard screen
            </div>

            <h1>
                Welcome,
                <?php
                if (isset($_SESSION['loggedIn'])) {
                    echo explode(" ", $_SESSION['firstname'])[0] . "  " . $_SESSION['lastname'];
                } else {
                    echo 'Guest';
                }
                ?>
            </h1>
        </div>







    </div>
</div>


</section>


<?php
include_once './includes/footer.php'
?>