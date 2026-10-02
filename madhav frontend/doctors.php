<?php
include "db.php";

$message = "";
/* Delete Doctor */
if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $sql = "DELETE FROM doctors WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Doctor deleted successfully!";
    } else {
        $message = "Error deleting doctor.";
    }
}
/* Add Doctor */
if (isset($_POST["add_doctor"])) {

    $doctor_name = $_POST["doctor_name"];
    $specialization = $_POST["specialization"];
    $department = $_POST["department"];
    $mobile = $_POST["mobile"];
    $schedule = $_POST["schedule"];

    $sql = "INSERT INTO doctors
            (doctor_name, specialization, department, mobile, schedule)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssss",
        $doctor_name,
        $specialization,
        $department,
        $mobile,
        $schedule
    );

    if ($stmt->execute()) {
        $message = "Doctor added successfully!";
    }
}


/* Update Doctor */
if (isset($_POST["update_doctor"])) {

    $id = $_POST["id"];
    $doctor_name = $_POST["doctor_name"];
    $specialization = $_POST["specialization"];
    $department = $_POST["department"];
    $mobile = $_POST["mobile"];
    $schedule = $_POST["schedule"];

    $sql = "UPDATE doctors
            SET doctor_name = ?,
                specialization = ?,
                department = ?,
                mobile = ?,
                schedule = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssi",
        $doctor_name,
        $specialization,
        $department,
        $mobile,
        $schedule,
        $id
    );

    if ($stmt->execute()) {
        $message = "Doctor updated successfully!";
    }
}


/* Get Doctor for Editing */
$edit_doctor = null;

if (isset($_GET["edit"])) {

    $id = $_GET["edit"];

    $sql = "SELECT * FROM doctors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result_edit = $stmt->get_result();

    if ($result_edit->num_rows == 1) {
        $edit_doctor = $result_edit->fetch_assoc();
    }
}


/* Get All Doctors */
$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Madhav Hospital - Doctors</title>
    <link rel="stylesheet" href="style.css?v=2">

</head>

<body class="hospital-page">

<div class="doctors-container">

    <h1>🏥 Madhav Hospital</h1>

    <h2>Doctor's Details</h2>


    <?php

    if ($message != "") {

        echo "<p style='color:green; font-weight:bold;'>$message</p>";

    }

    ?>


    <?php if ($edit_doctor): ?>

        <h3>Edit Doctor</h3>

        <form method="POST" action="doctors.php">

            <input
                type="hidden"
                name="id"
                value="<?php echo $edit_doctor['id']; ?>"
            >

            <label>Doctor Name</label>

            <input
                type="text"
                name="doctor_name"
                value="<?php echo htmlspecialchars($edit_doctor['doctor_name']); ?>"
                required
            >


            <label>Specialization</label>

            <input
                type="text"
                name="specialization"
                value="<?php echo htmlspecialchars($edit_doctor['specialization']); ?>"
                required
            >


            <label>Department</label>

            <input
                type="text"
                name="department"
                value="<?php echo htmlspecialchars($edit_doctor['department']); ?>"
                required
            >


            <label>Mobile Number</label>

            <input
                type="text"
                name="mobile"
                value="<?php echo htmlspecialchars($edit_doctor['mobile']); ?>"
                maxlength="15"
                required
            >


            <label>Schedule</label>

            <input
                type="text"
                name="schedule"
                value="<?php echo htmlspecialchars($edit_doctor['schedule']); ?>"
                required
            >


            <button type="submit" name="update_doctor">
                Update Doctor
            </button>

            <a href="doctors.php" class="back-button">
                Cancel
            </a>

        </form>


    <?php else: ?>

        <h3>Add New Doctor</h3>

        <form method="POST" action="doctors.php">

            <label>Doctor Name</label>

            <input
                type="text"
                name="doctor_name"
                required
            >


            <label>Specialization</label>

            <input
                type="text"
                name="specialization"
                required
            >


            <label>Department</label>

            <input
                type="text"
                name="department"
                required
            >


            <label>Mobile Number</label>

            <input
                type="text"
                name="mobile"
                maxlength="15"
                required
            >


            <label>Schedule</label>

            <input
                type="text"
                name="schedule"
                placeholder="Example: 9:00 AM - 1:00 PM"
                required
            >


            <button type="submit" name="add_doctor">
                Add Doctor
            </button>

        </form>

    <?php endif; ?>


    <h3>Doctor List</h3>


    <table>

        <tr>

            <th>ID</th>
            <th>Doctor Name</th>
            <th>Specialization</th>
            <th>Department</th>
            <th>Mobile</th>
            <th>Schedule</th>
            <th>Action</th>

        </tr>


        <?php

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                echo "<tr>";

                echo "<td>" . $row["id"] . "</td>";

                echo "<td>" .
                     htmlspecialchars($row["doctor_name"]) .
                     "</td>";

                echo "<td>" .
                     htmlspecialchars($row["specialization"]) .
                     "</td>";

                echo "<td>" .
                     htmlspecialchars($row["department"]) .
                     "</td>";

                echo "<td>" .
                     htmlspecialchars($row["mobile"]) .
                     "</td>";

                echo "<td>" .
                     htmlspecialchars($row["schedule"]) .
                     "</td>";

                echo "<td>";

                echo "<a href='doctors.php?edit=" .
                     $row["id"] .
                        "'>Edit</a> | ";

                echo "<a href='doctors.php?delete=" .
                    $row["id"] .
                    "' onclick=\"return confirm('Are you sure you want to delete this doctor?');\">Delete</a>";

                echo "</td>";

                echo "</tr>";
            }

        } else {

            echo "<tr>";

            echo "<td colspan='7'>No doctors found</td>";

            echo "</tr>";
        }

        ?>

    </table>


    <br>

    <a href="dashboard.php" class="back-button">
        ← Back to Dashboard
    </a>

</div>


</body>

</html>