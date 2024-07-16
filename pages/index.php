<?php
    session_start();
    include ("../php/connection.php");

    if(isset($_POST["add"])){
        // Prepare and bind SQL statement
        $_SESSION['form_data'] = $_POST;
        $stmt = $conn->prepare("INSERT INTO info (fname, lname) VALUES (?, ?)");
        $stmt->bind_param("ss", $fname, $lname);
        // Set parameters and execute
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $stmt->execute();
        $conn->close();
        unset($_SESSION['form_data']);
        header("Location: ".$_SERVER['PHP_SELF']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD</title>
</head>
<body>
    <div style="width: 10%;">
        <h1>Add Info</h1>
        <form method="POST">
            <input type="text" name="fname" placeholder="First name">
            <input type="text" name="lname" placeholder="Last name">
            <button type="submit" name="add">Add</button>
            
        </form>
    </div>
    <div>
        <h1>Display Info</h1>
        <?php
            // Check if delete button is clicked
            if(isset($_GET['delete_id'])) {
                $delete_id = $_GET['delete_id'];
                
                // SQL to delete record
                $sql_delete = "DELETE FROM info WHERE id = $delete_id";
                
                // Execute the delete statement
                if ($conn->query($sql_delete) === TRUE) {
                    echo "";
                } else {
                    echo "Error deleting record: " . $conn->error . "<br>";
                }
            }
            
            // Check if edit button is clicked (from GET request)
            if(isset($_GET['edit_id'])) {
                $edit_id = $_GET['edit_id'];

                // Fetch current record from database to populate the edit form
                $sql_fetch = "SELECT * FROM info WHERE id = ?";
                $stmt_fetch = $conn->prepare($sql_fetch);

                if ($stmt_fetch) {
                    // Bind parameter
                    $stmt_fetch->bind_param("i", $edit_id);

                    // Execute the statement
                    $stmt_fetch->execute();

                    // Bind result variables
                    $stmt_fetch->bind_result($id, $fname, $lname);

                    // Fetch the record
                    $stmt_fetch->fetch();

                    // Close statement
                    $stmt_fetch->close();
                } else {
                    echo "Error preparing fetch statement: " . $conn->error . "<br>";
                }
            }

            // Handle form submission for update
            if(isset($_POST['update'])) {
                $new_fname = $_POST['new_fname'];
                $new_lname = $_POST['new_lname'];
                $edit_id = $_GET['edit_id'];

                // Update query
                $sql_update = "UPDATE info SET fname = ?, lname = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);

                if ($stmt_update) {
                    // Bind parameters
                    $stmt_update->bind_param("ssi", $new_fname, $new_lname, $edit_id);

                    // Execute the statement
                    if ($stmt_update->execute()) {
                        // Redirect to clear the GET parameter
                        header("Location: {$_SERVER['PHP_SELF']}");
                        exit();
                    } else {
                        echo "Error updating record: " . $stmt_update->error;
                    }

                    // Close statement
                    $stmt_update->close();
                } else {
                    echo "Error preparing update statement: " . $conn->error;
                }
            }
            // Select query to display existing records
            $sql = "SELECT * FROM info";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<div>";
                    
                    // Display edit and delete links inline
                    echo " <a href='?delete_id=" . $row["id"] . "'>Delete</a>";
                    echo " <a href='?edit_id=" . $row["id"] . "'>Edit</a>" . " ";
                    // Display normal row data
                    echo "ID: " . $row["id"]. " - Name: " . $row["fname"]. " " . $row["lname"];


                    echo "</div>";

                     // Display edit form if edit_id matches current row's id
                    if(isset($_GET['edit_id']) && $_GET['edit_id'] == $row['id']) {
                        echo "<form id='editForm-" . $row['id'] . "' action='' method='POST'>
                                <input type='text' name='new_fname' value='" . $row['fname'] . "'>
                                <input type='text' name='new_lname' value='" . $row['lname'] . "'>
                                <input type='submit' name='update' value='Update'>
                                <a href='javascript:void(0);' onclick='cancelEdit(" . $row['id'] . ")'>Cancel</a>
                            </form>";
                    }
                }
            } else {
                echo "0 Data Found";
            }
            ?>
        <!-- Hide Form -->
        <script>
            function cancelEdit(rowId) {
                var editForm = document.getElementById('editForm-' + rowId);
                if (editForm) {
                    editForm.style.display = 'none';
                }
            }
        </script>
    </div>
</body>
</html>
