<?php
include('Includes/db_connection.php');
if (isset($_POST["Function"])) {

    if ($_POST["Function"] == "CreateLeave") {
        // execute SQL statement
        $StaffId = $_POST["StaffId"];
        $LeaveType = $_POST["LeaveType"];
        $LeaveStartDate = $_POST["LeaveStartDate"];
        $LeaveEndDate = $_POST["LeaveEndDate"];
        $LeaveStartTime = $_POST["LeaveStartTime"];
        $LeaveEndTime = $_POST["LeaveEndTime"];
        $LeaveReason = $_POST["LeaveReason"];

        $sql = "SELECT f_dept FROM `erp_faculty`WHERE f_id='$StaffId';";
        $result = mysqli_query($conn, $sql);
        $StaffDept = mysqli_fetch_assoc($result)['f_dept'];

        $sql = "INSERT INTO `erp_leave` (`lv_id`, `f_id`, `f_dept`, `lv_dept`, `lv_type`, `lv_reason`, `lv_applyon`, `lv_sdate`, `lv_edate`, `lv_stime`, `lv_etime`) 
        VALUES (NULL, '$StaffId', '$StaffDept', '', '$LeaveType', '$LeaveReason', NOW(), '$LeaveStartDate', '$LeaveEndDate', '$LeaveStartTime', '$LeaveEndTime');";
        if (mysqli_query($conn, $sql)) {
            echo "OK";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // close database connection
        mysqli_close($conn);


    }



    if ($_POST["Function"] == "CreateLeaveAlternatives") {
        // execute SQL statement
        $AlterationHour = $_POST["AlterationHour"];
        $AlterationClass = $_POST["AlterationClass"];
        $AlerationStaff = $_POST["AlerationStaff"];
        $LeaveId = $_POST["LeaveId"];


        $sql = "INSERT INTO `erp_leave_alt` (`la_id`, `lv_id`, `la_date`, `la_hour`, `cls_id`, `f_id`, `la_staffacpt`, `la_hodacpt`, `la_principalacpt`) VALUES (NULL, '$LeaveId', NOW(), '$AlterationHour', '$AlterationClass','$AlerationStaff' , 0, 0, 0);";
        if (mysqli_query($conn, $sql)) {
            echo "OK";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // close database connection
        mysqli_close($conn);


    }


    if ($_POST["Function"] == "ApproveLeaveAlt") {
        // execute SQL statement
        $LeaveAlt = $_POST["LeaveAlt"];
        $LeaveVal = $_POST["LeaveVal"];

        $sql = "UPDATE `erp_leave_alt` SET `la_principalacpt` = '$LeaveVal' WHERE `erp_leave_alt`.`la_id` = $LeaveAlt;";
        if (mysqli_query($conn, $sql)) {
            echo "OK";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // close database connection
        mysqli_close($conn);


    }



    if ($_POST["Function"] == "AleternationHourDropdownChange") {
        // execute SQL statement
        $selectedPeriod = $_POST["selectedPeriod"];

        $sql = "SELECT DISTINCT erp_timetable.tt_subcode, erp_class.cls_id,erp_timetable.tt_period,erp_class.cls_dept, erp_class.cls_sem, erp_class.cls_course FROM `erp_class` INNER JOIN erp_timetable ON erp_class.cls_id = erp_timetable.cls_id WHERE tt_day='Mon' AND tt_period IN (1, 2, 3, 8) AND erp_timetable.cls_id IN (1, 4, 11, 12, 14) AND erp_timetable.tt_subcode IN ('CS8601', 'CS3452') AND erp_timetable.tt_period = $selectedPeriod;";
        $result = mysqli_query($conn, $sql);
        $TableRows = array();
        // Process the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Do something with each row
            array_push($TableRows, $row);
        }

        if (count($TableRows)>0) {
            echo json_encode($TableRows);
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // close database connection
        mysqli_close($conn);


    }



} else {
    echo "Function Parameter Not set";
}