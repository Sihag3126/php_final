<?php
include_once './includes/header.php';

// Initialize variables to store user inputs and success message
$header = "";
$paragraph = "";
$successMessage = "";
$content = fetchContent();


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $header = $_POST['header'];
    $paragraph = $_POST['paragraph'];

    // Save content to the database
    if (!empty($header) && !empty($paragraph)) {
        saveContent($header, $paragraph);
        $successMessage = "Content saved successfully!";
    } else {
        $successMessage = "Error saving content.";
    }
}

?>

<div class="container" id="content">
    <div class="row content-box">
        <div class="col-md-12 box">
            <div class="heading">
                Content Page
            </div>

            <form class="w-100" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group mt-5">
                    <label for="header">Header:</label>
                    <input type="text" class="form-control" id="header" name="header" value="<?php echo $header; ?>">
                </div>
                <div class="form-group mt-5">
                    <label for="paragraph">Paragraph:</label>
                    <textarea class="form-control" id="paragraph" name="paragraph"><?php echo $paragraph; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-5">Submit</button>
            </form>

            <!-- Display success message here -->
            <?php if (!empty($successMessage)) : ?>
                <div class="alert alert-success mt-3">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>



        </div>
    </div>
</div>

<?php include_once './includes/footer.php'; ?>