```php
<?php
include "db.php";

$message = "";
/* Delete Patient */
/* Delete Patient */
if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $sql = "DELETE FROM patients WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: patients.php?success=deleted");
            exit();
        } else {
            $message = "Error deleting patient: " . $stmt->error;
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}
/* Edit Patient - Load Patient */
$edit_patient = null;

if (isset($_GET["edit"])) {

    $id = $_GET["edit"];

    $sql = "SELECT * FROM patients WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result_edit = $stmt->get_result();
    $edit_patient = $result_edit->fetch_assoc();

    $stmt->close();
}
/* Update Patient */
if (isset($_POST["update_patient"])) {

    $id = $_POST["patient_id"];
    $name = $_POST["name"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $disease = $_POST["disease"];
    $mobile = $_POST["mobile"];
    $doctor = $_POST["doctor"];
    $department = $_POST["department"];
    $bill = $_POST["bill"];

    $sql = "UPDATE patients SET
            name = ?,
            age = ?,
            gender = ?,
            disease = ?,
            mobile = ?,
            doctor = ?,
            department = ?,
            bill = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sisssssdi",
        $name,
        $age,
        $gender,
        $disease,
        $mobile,
        $doctor,
        $department,
        $bill,
        $id
    );

    if ($stmt->execute()) {
        header("Location: patients.php?success=updated");
        exit();
    } else {
        $message = "Error updating patient: " . $stmt->error;
    }

    $stmt->close();
}
/* Add Patient */
/* Add Patient */
if (isset($_POST["add_patient"])) {

    $name = $_POST["name"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $disease = $_POST["disease"];
    $mobile = $_POST["mobile"];
    $doctor = $_POST["doctor"];
    $department = $_POST["department"];
    $bill = $_POST["bill"];

    $sql = "INSERT INTO patients
            (name, age, gender, disease, mobile, doctor, department, bill)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $message = "Database error: " . $conn->error;
    } else {

        $stmt->bind_param(
            "sisssssd",
            $name,
            $age,
            $gender,
            $disease,
            $mobile,
            $doctor,
            $department,
            $bill
        );

        if ($stmt->execute()) {
    header("Location: patients.php?success=added");
    exit();
} else {
    $message = "Error adding patient: " . $stmt->error;
}

$stmt->close();
    }
}

    

/* Get Patients */
$result = $conn->query("SELECT * FROM patients ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>

    <title>Madhav Hospital - Patients</title>

    <link rel="stylesheet" href="style.css?v=2">

</head>

<body class="hospital-page">


<div class="doctors-container">

    <h1>🏥 Madhav Hospital</h1>

    <h2>Patient Details</h2>


    <?php

    if ($message != "") {

        echo "<p style='color:green; font-weight:bold;'>$message</p>";

    }

    ?>


    <h3>Add New Patient</h3>


    <form method="POST">


        <label>Patient Name</label>
<input
    type="text"
    name="name"
    value="<?php echo $edit_patient ? htmlspecialchars($edit_patient['name']) : ''; ?>"
    required
>


        <label>Age</label>

        <input
    type="number"
    name="age"
    value="<?php echo $edit_patient ? $edit_patient['age'] : ''; ?>"
    min="0"
    max="120"
    required
>


        <label>Gender</label>

        <select name="gender" required>

    <option value="">-- Select Gender --</option>

    <option value="Male" <?php if ($edit_patient && $edit_patient['gender'] == 'Male') echo 'selected'; ?>>Male</option>

    <option value="Female" <?php if ($edit_patient && $edit_patient['gender'] == 'Female') echo 'selected'; ?>>Female</option>

    <option value="Other" <?php if ($edit_patient && $edit_patient['gender'] == 'Other') echo 'selected'; ?>>Other</option>

</select>

        <label>Disease</label>

        <input
    type="text"
    name="disease"
    value="<?php echo $edit_patient ? htmlspecialchars($edit_patient['disease']) : ''; ?>"
    required
>


        <label>Mobile Number</label>

        <input
    type="text"
    name="mobile"
    value="<?php echo $edit_patient ? htmlspecialchars($edit_patient['mobile']) : ''; ?>"
    maxlength="15"
    required
>
        <label>Doctor</label>

        <input
            <input
    type="text"
    name="doctor"
    value="<?php echo $edit_patient ? htmlspecialchars($edit_patient['doctor']) : ''; ?>"
    required
>


        <label>Department</label>

<select name="department" required>

    <option value="">-- Select Department --</option>

    <option value="General Medicine" <?php if ($edit_patient && $edit_patient['department'] == 'General Medicine') echo 'selected'; ?>>General Medicine</option>

    <option value="Cardiology" <?php if ($edit_patient && $edit_patient['department'] == 'Cardiology') echo 'selected'; ?>>Cardiology</option>

    <option value="Neurology" <?php if ($edit_patient && $edit_patient['department'] == 'Neurology') echo 'selected'; ?>>Neurology</option>

    <option value="Orthopedics" <?php if ($edit_patient && $edit_patient['department'] == 'Orthopedics') echo 'selected'; ?>>Orthopedics</option>

    <option value="Pediatrics" <?php if ($edit_patient && $edit_patient['department'] == 'Pediatrics') echo 'selected'; ?>>Pediatrics</option>

    <option value="Dermatology" <?php if ($edit_patient && $edit_patient['department'] == 'Dermatology') echo 'selected'; ?>>Dermatology</option>

    <option value="Gynecology" <?php if ($edit_patient && $edit_patient['department'] == 'Gynecology') echo 'selected'; ?>>Gynecology</option>

    <option value="Emergency" <?php if ($edit_patient && $edit_patient['department'] == 'Emergency') echo 'selected'; ?>>Emergency</option>

    <option value="Other" <?php if ($edit_patient && $edit_patient['department'] == 'Other') echo 'selected'; ?>>Other</option>

</select>


        <label>Bill</label>

        <input
    type="number"
    name="bill"
    value="<?php echo $edit_patient ? $edit_patient['bill'] : ''; ?>"
    step="0.01"
    min="0"
    required
>


        <?php if ($edit_patient) { ?>

    <input type="hidden" name="patient_id" value="<?php echo $edit_patient['id']; ?>">

    <button type="submit" name="update_patient">
        Update Patient
    </button>

<?php } else { ?>

    <button type="submit" name="add_patient">
        Add Patient
    </button>

<?php } ?>


    </form>


    <h3>Patient List</h3>


    <table>

        <tr>

            <th>ID</th>

            <th>Patient Name</th>

            <th>Age</th>

            <th>Gender</th>

            <th>Disease</th>

            <th>Mobile</th>

            <th>Doctor</th>

            <th>Department</th>

            <th>Bill</th>
            <th>Action</th>

        </tr>


        <?php

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

        ?>

        <tr>

            <td>
                <?php echo $row["id"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["name"]); ?>
            </td>

            <td>
                <?php echo $row["age"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["gender"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["disease"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["mobile"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["doctor"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["department"]); ?>
            </td>

            <td>
                ₹<?php echo number_format($row["bill"], 2); ?>
            </td>
            <td>

    <a href="patients.php?edit=<?php echo $row['id']; ?>">
        Edit
    </a>

    |

    <a
        href="patients.php?delete=<?php echo $row['id']; ?>"
        onclick="return confirm('Are you sure you want to delete this patient?');"
    >
        Delete
    </a>

</td>

        </tr>

        <?php

            }

        } else {

            echo "<tr>";
            echo "<td colspan='10'>No patients found</td>";
            echo "</tr>";

        }

        ?>

    </table>


    <br>


    <a
        href="dashboard.php"
        class="back-button"
    >
        ← Back to Dashboard
    </a>


</div>

</body>

</html>
```
