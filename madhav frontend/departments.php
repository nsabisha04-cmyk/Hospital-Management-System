<?php
include "db.php";

$message = "";

/* ================================
   DELETE DEPARTMENT
================================ */

if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $sql = "DELETE FROM departments WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: departments.php?success=deleted");
            exit();
        } else {
            $message = "Error deleting department.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}


/* ================================
   LOAD DEPARTMENT FOR EDIT
================================ */

$edit_department = null;

if (isset($_GET["edit"])) {

    $id = $_GET["edit"];

    $sql = "SELECT * FROM departments WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result_edit = $stmt->get_result();

        if ($result_edit->num_rows == 1) {
            $edit_department = $result_edit->fetch_assoc();
        }

        $stmt->close();
    }
}


/* ================================
   UPDATE DEPARTMENT
================================ */

if (isset($_POST["update_department"])) {

    $id = $_POST["id"];
    $department_name = $_POST["department_name"];
    $doctor_name = $_POST["doctor_name"];
    $patient_name = $_POST["patient_name"];

    $sql = "UPDATE departments
            SET department_name = ?,
                doctor_name = ?,
                patient_name = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sssi",
            $department_name,
            $doctor_name,
            $patient_name,
            $id
        );

        if ($stmt->execute()) {
            header("Location: departments.php?success=updated");
            exit();
        } else {
            $message = "Error updating department.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}


/* ================================
   ADD DEPARTMENT
================================ */

if (isset($_POST["add_department"])) {

    $department_name = $_POST["department_name"];
    $doctor_name = $_POST["doctor_name"];
    $patient_name = $_POST["patient_name"];

    $sql = "INSERT INTO departments
            (department_name, doctor_name, patient_name)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sss",
            $department_name,
            $doctor_name,
            $patient_name
        );

        if ($stmt->execute()) {
            header("Location: departments.php?success=added");
            exit();
        } else {
            $message = "Error adding department.";
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
        $message = "Department added successfully!";
    }

    if ($_GET["success"] == "updated") {
        $message = "Department updated successfully!";
    }

    if ($_GET["success"] == "deleted") {
        $message = "Department deleted successfully!";
    }
}


/* ================================
   GET ALL DEPARTMENTS
================================ */

$sql = "SELECT * FROM departments ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Madhav Hospital - Departments</title>

    <link rel="stylesheet" href="style.css?v=2">

</head>


<body class="hospital-page">


<div class="departments-container">
    <div class="doctors-container">


    <h1>🏥 Department Details</h1>


    <?php if ($message != "") { ?>

        <p style="color: green; font-weight: bold;">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>


    <!-- ================================
         ADD / EDIT DEPARTMENT
    ================================= -->


    <?php if ($edit_department) { ?>

        <h2>Edit Department</h2>

        <form method="POST">

            <input
                type="hidden"
                name="id"
                value="<?php echo $edit_department["id"]; ?>"
            >


            <label>Department Name</label>

            <input
                type="text"
                name="department_name"
                value="<?php echo htmlspecialchars($edit_department["department_name"]); ?>"
                required
            >


            <label>Doctor Name</label>

            <input
                type="text"
                name="doctor_name"
                value="<?php echo htmlspecialchars($edit_department["doctor_name"]); ?>"
                required
            >


            <label>Patient Name</label>

            <input
                type="text"
                name="patient_name"
                value="<?php echo htmlspecialchars($edit_department["patient_name"]); ?>"
                required
            >


            <button
                type="submit"
                name="update_department"
            >
                Update Department
            </button>

        </form>


    <?php } else { ?>


        <h2>Add New Department</h2>

        <form method="POST">


            <label>Department Name</label>

            <input
                type="text"
                name="department_name"
                required
            >


            <label>Doctor Name</label>

            <input
                type="text"
                name="doctor_name"
                required
            >


            <label>Patient Name</label>

            <input
                type="text"
                name="patient_name"
                required
            >


            <button
                type="submit"
                name="add_department"
            >
                Add Department
            </button>


        </form>


    <?php } ?>


    <hr>


    <!-- ================================
         DEPARTMENT LIST
    ================================= -->


    <h2>Department List</h2>


    <table>


        <tr>

            <th>ID</th>

            <th>Department Name</th>

            <th>Doctor Name</th>

            <th>Patient Name</th>

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
                <?php echo htmlspecialchars($row["department_name"]); ?>
            </td>


            <td>
                <?php echo htmlspecialchars($row["doctor_name"]); ?>
            </td>


            <td>
                <?php echo htmlspecialchars($row["patient_name"]); ?>
            </td>


            <td>

                <a href="departments.php?edit=<?php echo $row["id"]; ?>">
                    Edit
                </a>

                |

                <a
                    href="departments.php?delete=<?php echo $row["id"]; ?>"
                    onclick="return confirm('Are you sure you want to delete this department?');"
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

            <td colspan="5">
                No departments found
            </td>

        </tr>


        <?php } ?>


    </table>


</div>


</body>

</html>