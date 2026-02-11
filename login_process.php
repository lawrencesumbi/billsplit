<?php
session_start();

/* ===== DATABASE CONNECTION ===== */
$host = "localhost";
$dbname = "billsplit";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed.");
}

/* ===== CHECK FORM SUBMISSION ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Validation
    if (empty($username) || empty($password)) {
        $_SESSION["error"] = "Username and password are required.";
        header("Location: login.php");
        exit();
    }

    /* ===== FETCH USER ===== */
    $stmt = $pdo->prepare("SELECT id, lastname, firstname, nickname, email, username, password, type FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check user & password
    if ($user && password_verify($password, $user["password"])) {

        // Store session data
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["lastname"] = $user["lastname"];
        $_SESSION["firstname"] = $user["firstname"];
        $_SESSION["nickname"] = $user["nickname"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["type"]     = $user["type"];

    // Redirect based on role
    

        if ($user["type"] === "standard") {
            header("Location: standard.php");
        } elseif ($user["type"] === "premium") {
            header("Location: premium.php");
        }

    
    exit();


    } else {
        $_SESSION["error"] = "Invalid username or password.";
        header("Location: login.php");
        exit();
    }

} else {
    header("Location: login.php");
    exit();
}
?>
