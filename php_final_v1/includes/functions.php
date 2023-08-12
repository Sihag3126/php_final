<?php
function dbConnect()
{
    $dbHost = "";
    $dbUser = "";
    $dbPass = "";
    $dbName = "";

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
function createUser($firstname, $lastname, $email, $password)
{
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        return "All fields are required to be filled.";
    }

    $conn = dbConnect();

    // Check if the users table exists, create it if not
    if (!tableExists($conn, 'users')) {
        createUsersTable($conn);
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return "User already registered with this email.";
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $firstname, $lastname, $email, $hashedPassword);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();

        // Store user data in a session
        session_start();

        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['email'] = $email;
        $_SESSION['loggedIn'] = true;

        return true;
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}

function tableExists($conn, $tableName)
{
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

function createUsersTable($conn)
{
    $query = "
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(50) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL
    )";
    $conn->query($query);
}




function authenticateUser($email, $password)
{
    $conn = dbConnect();

    $email = mysqli_real_escape_string($conn, $email);

    // Retrieve hashed password from the database
    $query = "SELECT id, firstname, lastname, password FROM users WHERE email = '$email'";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['password'];


        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Password is correct, user is authenticated
            // Store user data in a session
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['loggedIn'] = true;
            return true;
        } else {
            // Password is incorrect
            $_SESSION['loggedIn'] = false;
            return "Incorrect password";
        }
    } else {
        // User not found
        $_SESSION['loggedIn'] = false;
        return "User not found";
    }
}




function redirectToSignInIfNotLoggedIn()
{

    if (!isset($_SESSION['loggedIn'])) {
        header("Location: ./login.php"); // Redirect to signin page if user is not logged in
        exit();
    }
}

function redirectToDashboardIfLoggedIn()
{
    session_start();

    if (isset($_SESSION['loggedIn'])) {
        header("Location: dashboard.php"); // Redirect to dashboard if user is already logged in
        exit();
    }
}



function logout()
{
    session_start();
    session_unset();
    session_destroy();
    header("Location: index.php"); // Redirect to the home page after logout
    exit();
}

function navigateToDashboard()
{
    header("Location: dashboard.php"); // Redirect to dashboard page
    exit();
}
function navigateToHome()
{
    header("Location: index.php"); // Redirect to index page
    exit();
}
function navigateToUsers()
{
    header("Location: users.php"); // Redirect to user page
    exit();
}
function navigateToContent()
{
    header("Location: content.php"); // Redirect to content page
    exit();
}
function fetchAllUsers()
{
    $conn = dbConnect();

    $sql = "SELECT id, firstname, lastname, email FROM users";
    $result = $conn->query($sql);

    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    $conn->close();

    return $users;
}


function deleteUser($user_id)
{
    $conn = dbConnect();

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}
function saveContent($header, $paragraph)
{
    $conn = dbConnect();

    // Check if the content table exists, create it if not
    if (!tableExists($conn, 'content')) {
        createContentTable($conn);
    }

    // Check if there is existing data in the content table
    $existingContent = fetchContent();

    if ($existingContent) {
        // Delete the existing content
        $deleteQuery = "DELETE FROM content";
        if (!$conn->query($deleteQuery)) {
            echo "Error deleting existing content: " . $conn->error;
            $conn->close();
            return false;
        }
    }

    // Insert the new content
    $stmt = $conn->prepare("INSERT INTO content (header, paragraph) VALUES (?, ?)");
    $stmt->bind_param("ss", $header, $paragraph);

    if ($stmt->execute()) {
        // Content saved successfully
        $stmt->close();
        $conn->close();
        return true;
    } else {
        echo "Error saving content: " . $conn->error;
        return false;
    }
}

function createContentTable($conn)
{
    $query = "
    CREATE TABLE content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        header VARCHAR(255) NOT NULL,
        paragraph TEXT NOT NULL
    )";
    $conn->query($query);
}
function fetchContent()
{
    $conn = dbConnect();

    $query = "SELECT * FROM content LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $content = mysqli_fetch_assoc($result);
        return $content;
    } else {
        return false;
    }
}
function updateContent($header, $paragraph)
{
    $conn = dbConnect();

    $header = mysqli_real_escape_string($conn, $header);
    $paragraph = mysqli_real_escape_string($conn, $paragraph);

    $query = "UPDATE content SET header = '$header', paragraph = '$paragraph' WHERE id = 1";

    if (mysqli_query($conn, $query)) {
        return true;
    } else {
        return false;
    }
}
