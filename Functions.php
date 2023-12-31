<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
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


    if ($_POST["Function"] == "ApproveLeaveId") {
        // execute SQL statement
        $LeaveId = $_POST["LeaveId"];
        $LeaveVal = $_POST["LeaveVal"];
        $Approval = $_POST["Approval"];
        $role = $_POST["role"];
        $altStaffEmail = $_POST["altStaffEmail"];
        $approverEmail = $_POST["approverEmail"];
        $okayMsg = '';

        if($Approval == 'Approved'){
        $sql = "UPDATE `erp_leave_alt` SET la_". $role . "acpt = '$LeaveVal' WHERE `erp_leave_alt`.`lv_id` = $LeaveId;";
        if (mysqli_query($conn, $sql)) {
            $okayMsg = 'OK';
        } else {
            $okayMsg .= "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

    } else if($Approval == 'Denied'){
        $sql = "DELETE FROM erp_leave_alt WHERE lv_id= $LeaveId";
        if (mysqli_query($conn, $sql)) {
            $okayMsg = 'OK';
        } else {
            $okayMsg .= "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    
    // Email Snippet Starts

//Load Composer's autoloader
require 'vendor/autoload.php';
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'cta102938@gmail.com';                     //SMTP username
    $mail->Password   = 'btkovkokiqsiwyod';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    //Recipients
    $mail->setFrom('cta102938@gmail.com', 'Grace LR Bot');
    $mail->addAddress($altStaffEmail, 'Staff');     //Add a recipient
    // $mail->addReplyTo('info@example.com', 'Information');
    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Leave Request has been.' . $Approval . '.';
    $mail->Body    = ' -';
    $mail->AltBody = ' -';
    $mail->send();
    $emailResult = 'and a notification message has been sent';
} catch (Exception $e) {
    $emailResult = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

    // Email snippet ends

$responseArray = [];
array_push($responseArray,$okayMsg);
array_push($responseArray,$emailResult);
$responseArray = json_encode($responseArray);

echo $responseArray;

        // close database connection
        mysqli_close($conn);


    }



    if ($_POST["Function"] == "AleternationHourDropdownChange") {
        // execute SQL statement
        $f_id = $_POST["f_id"];
        $periods = $_POST["periods"];
        $todaySubjects = $_POST["todaySubjects"];
        $selectedPeriod = $_POST["selectedPeriod"];
        $cse_classids = $_POST["cse_classids"];
        //         $periods = $_POST['periods'];  
// $periods = json_decode($periods, true);
// Query for matching period with Alternative Class


        $sql = "SELECT DISTINCT erp_timetable.cls_id,erp_timetable.tt_subcode, erp_class.cls_dept, erp_class.cls_sem, erp_class.cls_course FROM `erp_class` INNER JOIN erp_timetable ON erp_class.cls_id = erp_timetable.cls_id WHERE tt_day='Mon' AND tt_period IN ($selectedPeriod";

        // For adding the periods into query
        // foreach ($periods as $period) {
        //     $sql .= "$period, ";
        // }
        // $sql = rtrim($sql, ", ");
        $sql .= ") AND erp_timetable.tt_subcode IN (";
        // For adding the subjects into query
        foreach ($todaySubjects as $todaySubject) {
            $sql .= "'$todaySubject', ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ")";

        $result = $conn->query($sql);
        $TableRows = array();
        // Process the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Do something with each row
            array_push($TableRows, $row);
        }
        // Code for matching period and alternative staffs available


        $query = "SELECT erp_subject.f_id, f_fname, f_lname, tt_day, tt_period FROM erp_subject INNER JOIN erp_timetable ON erp_subject.tt_subcode = erp_timetable.tt_subcode INNER JOIN erp_faculty ON erp_subject.f_id=erp_faculty.f_id WHERE tt_day='Mon' AND tt_period NOT IN ($selectedPeriod) AND erp_subject.f_id NOT IN ('$f_id') AND erp_subject.cls_id IN (";
        // $query = rtrim($query, ", "); // Remove the last comma and space

        // $query .= ")";
        foreach ($cse_classids as $classid) {
            $query .= "$classid, ";
        }
        $query = rtrim($query, ", ");
        $query .= ") GROUP BY CONCAT(f_fname, ' ', f_lname)";
        // Execute the query and fetch the results
        $result = $conn->query($query);

        $altPeriods = array();
        // Check if any rows were returned
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($altPeriods, $row);
            }
        }




        if (count($TableRows) > 0) {
            echo json_encode(array_merge($TableRows, $altPeriods));
            $TableRows = [];
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }

        // close database connection
        mysqli_close($conn);


    }


    if ($_POST["Function"] == "leaveReqUpdation") {
        $lvidValue = $_POST["lvidValue"];
        $sql = "UPDATE `erp_leave_alt` SET `la_staffacpt` = '1' WHERE la_id = $lvidValue;";
        if (mysqli_query($conn, $sql)) {
            echo "OK";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // close database connection
        mysqli_close($conn);
    }

    // Manage Leave Alternative Page's delete operation
    if ($_POST["Function"] == "altRecordDeletion") {
        $la_id = $_POST["la_id"];
        $sql = "DELETE FROM `erp_leave_alt` WHERE la_id = $la_id";
        if (mysqli_query($conn, $sql)) {
            echo "OK";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    if ($_POST["Function"] == "toHodBtnClick") {
        $laIds = $_POST["laIds"]; 
        $laIds = json_decode($laIds);
        // Creating a tuple of the La Ids to be used in the query
        $laIdsStringTuple = "(";
        foreach ($laIds as $laId) {
            $laIdsStringTuple .= "$laId," ;
        }
        $laIdsStringTuple = rtrim($laIdsStringTuple, ", ");
        $laIdsStringTuple .= ")" ;
        $sql = "UPDATE erp_leave_alt SET la_staffacpt = 2 WHERE la_id IN $laIdsStringTuple";
        if (mysqli_query($conn, $sql)) {
            echo "OK";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // close database connection
        mysqli_close($conn);
    }
    

} else {
    echo "Function Parameter Not set";
}