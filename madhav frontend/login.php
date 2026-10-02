<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM admin WHERE name = ? AND password = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $password);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

    $login_date = date("Y-m-d");
    $login_time = date("H:i:s");

    $history_sql = "INSERT INTO login_history
                    (username, login_date, login_time)
                    VALUES (?, ?, ?)";

    $history_stmt = $conn->prepare($history_sql);

    if ($history_stmt) {

        $history_stmt->bind_param(
            "sss",
            $name,
            $login_date,
            $login_time
        );

        $history_stmt->execute();

        $history_stmt->close();
    }

    header("Location: dashboard.php");
    exit();

}else {
        $message = "Invalid name or password!";
    }
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Madhav Hospital - Login</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="login-container">

        <h1>🏥 Madhav Hospital</h1>

        <p>Hospital Management System</p>

        <?php
        if ($message != "") {
            echo "<p style='color:red;'>$message</p>";
        }
        ?>

        <form method="POST" action="login.php">

            <label>Name</label>

            <input
                type="text"
                name="name"
                placeholder="Enter your name"
                required
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <button type="submit">Login</button>

        </form>

    </div>

</body>
</html>