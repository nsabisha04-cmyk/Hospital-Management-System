<?php
include "db.php";

$sql = "SELECT * FROM login_history ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Madhav Hospital - Login History</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="hospital-page">

<div class="doctors-container">

    <h1>👤 Login History</h1>

    <table>

        <tr>

            <th>ID</th>
            <th>Username</th>
            <th>Login Date</th>
            <th>Login Time</th>

        </tr>

        <?php

        if ($result && $result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

        ?>

        <tr>

            <td>
                <?php echo $row["id"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["username"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["login_date"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["login_time"]); ?>
            </td>

        </tr>

        <?php

            }

        } else {

        ?>

        <tr>

            <td colspan="4">
                No login history found
            </td>

        </tr>

        <?php } ?>

    </table>

    <br>

    <a href="dashboard.php" class="back-button">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>