<?php
include('Includes/db_connection.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $log_id = mysqli_real_escape_string($conn, $_POST['log_id']);
    $log_pwd = mysqli_real_escape_string($conn, $_POST['log_pwd']);

    $sql = "SELECT * FROM erp_login WHERE log_id='$log_id' AND log_pwd='$log_pwd'";
    $login_result = mysqli_query($conn, $sql);

    // Getting erp_faculty record for the logging in user
    $sql = "SELECT * FROM erp_faculty WHERE f_id='$log_id'";
    $result = mysqli_query($conn, $sql);
    $erpFacultyRecords = [];
    while ($row = $result->fetch_assoc()) {
        array_push($erpFacultyRecords, $row);
        
    }

    // Getting the cls ids of the user logging in
    $dept = $erpFacultyRecords[0]['f_dept'];
    $sql = "SELECT DISTINCT cls_id FROM erp_class WHERE cls_dept ='$dept'";
    $result = mysqli_query($conn, $sql);
    $classIds = [];
    while ($row = $result->fetch_assoc()) {
        array_push($classIds, $row);
    }


    if (mysqli_num_rows($login_result) == 1) {
        session_start();
        $_SESSION['login_data'] = $log_id;
        $_SESSION['erpFacultyRecords'] = $erpFacultyRecords;
        $_SESSION['classIds'] = $classIds;
        header("Location: ApplyLeave.php?f_id=$log_id"); // Redirect to dropdown page with f_id as parameter
        exit();
    } else {
        echo "Incorrect login details!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <form action="flogin.php" method="post">
        <label for="log_id">Log ID:</label>
        <input type="text" name="log_id" required><br><br>

        <label for="log_pwd">Password:</label>
        <input type="password" name="log_pwd" required><br><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>
