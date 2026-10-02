<?php
include "db.php";

$message = "";
/* ================================
   DELETE MEDICINE
================================ */

if (isset($_GET["delete"])) {

    $id = $_GET["delete"];

    $sql = "DELETE FROM medicines WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: medicines.php?success=deleted");
            exit();
        } else {
            $message = "Error deleting medicine.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}
/* ================================
   LOAD MEDICINE FOR EDIT
================================ */


$edit_medicine = null;

if (isset($_GET["edit"])) {

    $id = $_GET["edit"];

    $sql = "SELECT * FROM medicines WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result_edit = $stmt->get_result();

        if ($result_edit->num_rows == 1) {
            $edit_medicine = $result_edit->fetch_assoc();
        }

        $stmt->close();
    }
}

/* ================================
   UPDATE MEDICINE
================================ */

if (isset($_POST["update_medicine"])) {

    $id = $_POST["id"];
    $medicine_name = $_POST["medicine_name"];
    $medicine_type = $_POST["medicine_type"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];
    $expiry_date = $_POST["expiry_date"];

    $sql = "UPDATE medicines
            SET medicine_name = ?,
                medicine_type = ?,
                quantity = ?,
                price = ?,
                expiry_date = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ssidsi",
            $medicine_name,
            $medicine_type,
            $quantity,
            $price,
            $expiry_date,
            $id
        );

        if ($stmt->execute()) {
            header("Location: medicines.php?success=updated");
            exit();
        } else {
            $message = "Error updating medicine.";
        }

        $stmt->close();

    } else {
        $message = "Database error: " . $conn->error;
    }
}
/* ================================
   ADD MEDICINE
================================ */

if (isset($_POST["add_medicine"])) {

    $medicine_name = $_POST["medicine_name"];
    $medicine_type = $_POST["medicine_type"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];
    $expiry_date = $_POST["expiry_date"];

    $sql = "INSERT INTO medicines
            (medicine_name, medicine_type, quantity, price, expiry_date)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "ssids",
            $medicine_name,
            $medicine_type,
            $quantity,
            $price,
            $expiry_date
        );

        if ($stmt->execute()) {
            header("Location: medicines.php?success=added");
            exit();
        } else {
            $message = "Error adding medicine.";
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
        $message = "Medicine added successfully!";
    }
}


/* ================================
   GET ALL MEDICINES
================================ */

$sql = "SELECT * FROM medicines ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Madhav Hospital - Medicines</title>

    <link rel="stylesheet" href="style.css">

</head>


<body class="hospital-page">


<div class="doctors-container">


    <h1>💊 Medicine Details</h1>


    <?php if ($message != "") { ?>

        <p style="color: green; font-weight: bold;">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>


    <!-- ADD MEDICINE -->

    <?php if ($edit_medicine) { ?>

<h2>Edit Medicine</h2>

<?php } else { ?>

<h2>Add New Medicine</h2>

<?php } ?>


    <form method="POST">

    <?php if ($edit_medicine) { ?>

        <input
            type="hidden"
            name="id"
            value="<?php echo $edit_medicine["id"]; ?>"
        >

    <?php } ?>


    <label>Medicine Name</label>

    <input
        type="text"
        name="medicine_name"
        value="<?php echo $edit_medicine ? htmlspecialchars($edit_medicine["medicine_name"]) : ""; ?>"
        required
    >


    <label>Medicine Type</label>

    <select name="medicine_type" required>

        <option value="">Choose Type</option>

        <option value="Tablet"
        <?php if ($edit_medicine && $edit_medicine["medicine_type"] == "Tablet") echo "selected"; ?>>
            Tablet
        </option>

        <option value="Capsule"
        <?php if ($edit_medicine && $edit_medicine["medicine_type"] == "Capsule") echo "selected"; ?>>
            Capsule
        </option>

        <option value="Syrup"
        <?php if ($edit_medicine && $edit_medicine["medicine_type"] == "Syrup") echo "selected"; ?>>
            Syrup
        </option>

        <option value="Injection"
        <?php if ($edit_medicine && $edit_medicine["medicine_type"] == "Injection") echo "selected"; ?>>
            Injection
        </option>

        <option value="Other"
        <?php if ($edit_medicine && $edit_medicine["medicine_type"] == "Other") echo "selected"; ?>>
            Other
        </option>

    </select>


    <label>Quantity</label>

    <input
        type="number"
        name="quantity"
        min="1"
        value="<?php echo $edit_medicine ? $edit_medicine["quantity"] : ""; ?>"
        required
    >


    <label>Price</label>

    <input
        type="number"
        name="price"
        step="0.01"
        min="0"
        value="<?php echo $edit_medicine ? $edit_medicine["price"] : ""; ?>"
        required
    >


    <label>Expiry Date</label>

    <input
        type="date"
        name="expiry_date"
        value="<?php echo $edit_medicine ? $edit_medicine["expiry_date"] : ""; ?>"
        required
    >


    <?php if ($edit_medicine) { ?>

        <button
            type="submit"
            name="update_medicine"
        >
            Update Medicine
        </button>

    <?php } else { ?>

        <button
            type="submit"
            name="add_medicine"
        >
            Add Medicine
        </button>

    <?php } ?>

</form>

    <hr>


    <!-- MEDICINE LIST -->

    <h2>Medicine List</h2>


    <table>


        <tr>

            <th>ID</th>

            <th>Medicine Name</th>

            <th>Type</th>

            <th>Quantity</th>

            <th>Price</th>

            <th>Expiry Date</th>
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
                <?php echo htmlspecialchars($row["medicine_name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["medicine_type"]); ?>
            </td>

            <td>
                <?php echo $row["quantity"]; ?>
            </td>

            <td>
                ₹<?php echo number_format($row["price"], 2); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row["expiry_date"]); ?>
            </td>
            <td>

    <a href="medicines.php?edit=<?php echo $row["id"]; ?>">
        Edit
    </a>

    |

    <a
        href="medicines.php?delete=<?php echo $row["id"]; ?>"
        onclick="return confirm('Are you sure you want to delete this medicine?');"
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

            <td colspan="7">
                No medicines found
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