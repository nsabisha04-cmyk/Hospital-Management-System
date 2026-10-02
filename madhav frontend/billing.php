<?php
include "db.php";

$message = "";
/* ================================
   DELETE BILL
================================ */

if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $sql = "DELETE FROM billing WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: billing.php?success=deleted");
            exit();
        } else {
            $message = "Error deleting bill.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}
/* ================================
   LOAD BILL FOR EDIT
================================ */

$edit_bill = null;

if (isset($_GET["edit"])) {

    $id = $_GET["edit"];

    $sql = "SELECT * FROM billing WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result_edit = $stmt->get_result();

        if ($result_edit->num_rows == 1) {
            $edit_bill = $result_edit->fetch_assoc();
        }

        $stmt->close();
    }
}
/* ================================
   UPDATE BILL
================================ */

if (isset($_POST["update_bill"])) {

    $id = $_POST["id"];
    $patient_name = $_POST["patient_name"];
    $department = $_POST["department"];
    $bill = $_POST["bill"];
    $payment_status = $_POST["payment_status"];

    $sql = "UPDATE billing
            SET patient_name = ?,
                department = ?,
                bill = ?,
                payment_status = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ssdsi",
            $patient_name,
            $department,
            $bill,
            $payment_status,
            $id
        );

        if ($stmt->execute()) {
            header("Location: billing.php?success=updated");
            exit();
        } else {
            $message = "Error updating bill.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}

/* ================================
   ADD BILL
================================ */

if (isset($_POST["add_bill"])) {

    $patient_name = $_POST["patient_name"];
    $department = $_POST["department"];
    $bill = $_POST["bill"];
    $payment_status = $_POST["payment_status"];

    $sql = "INSERT INTO billing
            (patient_name, department, bill, payment_status)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ssds",
            $patient_name,
            $department,
            $bill,
            $payment_status
        );

        if ($stmt->execute()) {
            header("Location: billing.php?success=added");
            exit();
        } else {
            $message = "Error adding bill.";
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
        $message = "Bill added successfully!";
    }
}


/* ================================
   GET ALL BILLS
================================ */

$sql = "SELECT * FROM billing ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Madhav Hospital - Billing</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="hospital-page">

<div class="doctors-container">

    <h1>💰 Billing Details</h1>

    <?php if ($message != "") { ?>

        <p style="color: green; font-weight: bold;">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>


    <?php if ($edit_bill) { ?>

<h2>Edit Bill</h2>

<?php } else { ?>

<h2>Add New Bill</h2>

<?php } ?>


    <form method="POST">

    <?php if ($edit_bill) { ?>

        <input
            type="hidden"
            name="id"
            value="<?php echo $edit_bill["id"]; ?>"
        >

    <?php } ?>


    <label>Patient Name</label>

    <input
        type="text"
        name="patient_name"
        value="<?php echo $edit_bill ? htmlspecialchars($edit_bill["patient_name"]) : ""; ?>"
        required
    >


    <label>Department</label>

    <input
        type="text"
        name="department"
        value="<?php echo $edit_bill ? htmlspecialchars($edit_bill["department"]) : ""; ?>"
        required
    >


    <label>Bill Amount</label>

    <input
        type="number"
        name="bill"
        step="0.01"
        min="0"
        value="<?php echo $edit_bill ? $edit_bill["bill"] : ""; ?>"
        required
    >


    <label>Payment Status</label>

    <select name="payment_status" required>

        <option value="">Choose Status</option>

        <option value="Paid"
        <?php if ($edit_bill && $edit_bill["payment_status"] == "Paid") echo "selected"; ?>>
            Paid
        </option>

        <option value="Not Paid"
        <?php if ($edit_bill && $edit_bill["payment_status"] == "Not Paid") echo "selected"; ?>>
            Not Paid
        </option>

    </select>


    <?php if ($edit_bill) { ?>

        <button
            type="submit"
            name="update_bill"
        >
            Update Bill
        </button>

    <?php } else { ?>

        <button
            type="submit"
            name="add_bill"
        >
            Add Bill
        </button>

    <?php } ?>

</form>


    <hr>


    <h2>Billing List</h2>


    <table>

        <tr>

            <th>ID</th>
            <th>Patient Name</th>
            <th>Department</th>
            <th>Bill</th>
            <th>Payment Status</th>
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
                <?php echo htmlspecialchars($row["department"]); ?>
            </td>

            <td>
                ₹<?php echo number_format($row["bill"], 2); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["payment_status"]); ?>
            </td>
            <td>

    <a href="billing.php?edit=<?php echo $row["id"]; ?>">
        Edit
    </a>

    |

    <a
        href="billing.php?delete=<?php echo $row["id"]; ?>"
        onclick="return confirm('Are you sure you want to delete this bill?');"
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

            <td colspan="6">
                No bills found
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