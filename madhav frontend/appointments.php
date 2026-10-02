<?php
include "db.php";

$message = "";
/* ================================
   DELETE APPOINTMENT
================================ */

if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $sql = "DELETE FROM appointments WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: appointments.php?success=deleted");
            exit();
        } else {
            $message = "Error deleting appointment.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}
/* ================================
   LOAD APPOINTMENT FOR EDIT
================================ */

$edit_appointment = null;

if (isset($_GET["edit"])) {

    $id = $_GET["edit"];

    $sql = "SELECT * FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result_edit = $stmt->get_result();

        if ($result_edit->num_rows == 1) {
            $edit_appointment = $result_edit->fetch_assoc();
        }

        $stmt->close();
    }
}
/* ================================
   UPDATE APPOINTMENT
================================ */

if (isset($_POST["update_appointment"])) {

    $id = $_POST["id"];
    $patient_name = $_POST["patient_name"];
    $doctor_name = $_POST["doctor_name"];
    $department = $_POST["department"];
    $appointment_date = $_POST["appointment_date"];
    $appointment_time = $_POST["appointment_time"];
    $reason = $_POST["reason"];

    $sql = "UPDATE appointments
            SET patient_name = ?,
                doctor_name = ?,
                department = ?,
                appointment_date = ?,
                appointment_time = ?,
                reason = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ssssssi",
            $patient_name,
            $doctor_name,
            $department,
            $appointment_date,
            $appointment_time,
            $reason,
            $id
        );

        if ($stmt->execute()) {
            header("Location: appointments.php?success=updated");
            exit();
        } else {
            $message = "Error updating appointment.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}

/* ================================
   ADD APPOINTMENT
================================ */

if (isset($_POST["add_appointment"])) {

    $patient_name = $_POST["patient_name"];
    $doctor_name = $_POST["doctor_name"];
    $department = $_POST["department"];
    $appointment_date = $_POST["appointment_date"];
    $appointment_time = $_POST["appointment_time"];
    $reason = $_POST["reason"];

    $sql = "INSERT INTO appointments
            (patient_name, doctor_name, department, appointment_date, appointment_time, reason)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ssssss",
            $patient_name,
            $doctor_name,
            $department,
            $appointment_date,
            $appointment_time,
            $reason
        );

        if ($stmt->execute()) {
            header("Location: appointments.php?success=added");
            exit();
        } else {
            $message = "Error adding appointment.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}


/* ================================
   SUCCESS MESSAGE
================================ */

if (isset($_GET["success"])) {

    if ($_GET["success"] == "added") {
        $message = "Appointment added successfully!";
    }
}


/* ================================
   GET ALL APPOINTMENTS
================================ */

$sql = "SELECT * FROM appointments ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Madhav Hospital - Appointments</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="hospital-page">

<div class="doctors-container">

    <h1>📅 Appointment Details</h1>

    <?php if ($message != "") { ?>

        <p style="color: green; font-weight: bold;">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>


    <?php if ($edit_appointment) { ?>

<h2>Edit Appointment</h2>

<?php } else { ?>

<h2>Add New Appointment</h2>

<?php } ?>


    <form method="POST">

    <?php if ($edit_appointment) { ?>

        <input
            type="hidden"
            name="id"
            value="<?php echo $edit_appointment["id"]; ?>"
        >

    <?php } ?>


    <label>Patient Name</label>

    <input
        type="text"
        name="patient_name"
        value="<?php echo $edit_appointment ? htmlspecialchars($edit_appointment["patient_name"]) : ""; ?>"
        required
    >


    <label>Doctor Name</label>

    <input
        type="text"
        name="doctor_name"
        value="<?php echo $edit_appointment ? htmlspecialchars($edit_appointment["doctor_name"]) : ""; ?>"
        required
    >


    <label>Department</label>

    <input
        type="text"
        name="department"
        value="<?php echo $edit_appointment ? htmlspecialchars($edit_appointment["department"]) : ""; ?>"
        required
    >


    <label>Appointment Date</label>

    <input
        type="date"
        name="appointment_date"
        value="<?php echo $edit_appointment ? $edit_appointment["appointment_date"] : ""; ?>"
        required
    >


    <label>Appointment Time</label>

    <input
        type="time"
        name="appointment_time"
        value="<?php echo $edit_appointment ? $edit_appointment["appointment_time"] : ""; ?>"
        required
    >


    <label>Reason</label>

    <input
        type="text"
        name="reason"
        value="<?php echo $edit_appointment ? htmlspecialchars($edit_appointment["reason"]) : ""; ?>"
        required
    >


    <?php if ($edit_appointment) { ?>

        <button
            type="submit"
            name="update_appointment"
        >
            Update Appointment
        </button>

    <?php } else { ?>

        <button
            type="submit"
            name="add_appointment"
        >
            Add Appointment
        </button>

    <?php } ?>

</form>


    <hr>


    <h2>Appointment List</h2>


    <table>

        <tr>

            <th>ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Department</th>
            <th>Date</th>
            <th>Time</th>
            <th>Reason</th>
            <th>Action</th>

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
                <?php echo htmlspecialchars($row["patient_name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["doctor_name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["department"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["appointment_date"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["appointment_time"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["reason"]); ?>
            </td>
            <td>
    <a href="appointments.php?edit=<?php echo $row["id"]; ?>">
        Edit
    </a>
    <a
        href="appointments.php?delete=<?php echo $row["id"]; ?>"
        onclick="return confirm('Are you sure you want to delete this appointment?');"
    >
        Delete
    </a>
</td>

        </tr>

        <?php

            }

        } else {

        ?>

        <tr>

            <td colspan="8">
                No appointments found
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