<?php

include_once './includes/header.php';
include_once './includes/functions.php';

// Fetch the content from the database
$content = fetchContent();

?>

<div class="container" id="content">
    <div class="row content-box">
        <div class="col-md-12 box">

            <?php if (isset($_SESSION['loggedIn'])) : ?>
                <a href="content.php"><i class="fas fa-edit"></i></a>
            <?php endif; ?>
            <div class="content-display">
                <h2><?php echo $content['header']; ?></h2>
                <p><?php echo $content['paragraph']; ?></p>
            </div>
        </div>
    </div>
</div>

<?php include_once './includes/footer.php'; ?>