<?php
include_once './includes/header.php';
include_once './includes/functions.php';

$users = fetchAllUsers();

if (isset($_POST['delete']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    if (deleteUser($user_id)) {
        // Redirect to the same page to show the updated list after successful deletion
        echo '<script>window.location.replace("' . $_SERVER['PHP_SELF'] . '");</script>';
        exit();
    } else {
        echo "Error deleting user.";
    }
}
?>

<div class="container" id="content">
    <div class="row content-box">
        <div class="col-md-12 box">
            <div class="heading">
                Registered Users List
            </div>

            <?php if (!empty($users)) : ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['firstname']; ?></td>
                            <td><?php echo $user['lastname']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td>
                                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <p>No registered users found.</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include_once './includes/footer.php' ?>
