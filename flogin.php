<?php
include('Includes/db_connection.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $log_id = mysqli_real_escape_string($conn, $_POST['log_id']);
    $log_pwd = mysqli_real_escape_string($conn, $_POST['log_pwd']);

    $sql = "SELECT * FROM erp_login WHERE log_id='$log_id' AND log_pwd='$log_pwd'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        session_start();
        $_SESSION['login_data'] = $log_id;
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
