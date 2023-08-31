<?php

session_start();

if (isset($_SESSION['login_data'])) {
    $log_id = $_SESSION['login_data'];
include("Includes/Header.php");
include('Includes/db_connection.php');

// Finding requests from other staffs
$alterationStaffs = [];
$sql = "SELECT * FROM erp_leave_alt WHERE f_id = '$log_id'";
$result = $conn->query($sql);
if ($result->num_rows  > 0 ){
while ($row = mysqli_fetch_assoc($result)) {
    array_push($alterationStaffs, $row); // This array contains all the records that are requests
}
}

// Lv_id matches from erp_leave with erp_leave_alt
$requestingStaffs = [];
$sql = "SELECT * FROM erp_leave";
$result = $conn->query($sql);
if ($result->num_rows  > 0 ){
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($requestingStaffs, $row); // This array contains all the records that are requests
    }

}

// Finding staff name using f_id from erp_leave
$staffNames = [];
$sql = "SELECT * FROM erp_faculty";
$result = $conn->query($sql);
if ($result->num_rows  > 0 ){
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($staffNames, $row);
    }
 }

 // Finding requesting staff's class name

$requestStaffClasses = [];
$sql = "SELECT * FROM erp_class";
$result = $conn->query($sql);
if ($result->num_rows  > 0 ){
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($requestStaffClasses, $row);
    }
}




?>



    <div class="iq-navbar-header" style="height: 215px;">
            <div class="container-fluid iq-container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="flex-wrap d-flex justify-content-between align-items-center">
                            <div>
                                <h1>Manage Leave Requests</h1>
                                <p>Here you can find all of your Leave Request Details.</p>
                            </div>
                               
                        </div>
                    </div>
                </div>
            </div>
            <div class="iq-header-img">
                <img src="assets/images/dashboard/top-header.png" alt="header"
                    class="theme-color-default-img img-fluid w-100 h-100 animated-scaleX">
                <img src="assets/images/dashboard/top-header1.png" alt="header"
                    class="theme-color-purple-img img-fluid w-100 h-100 animated-scaleX">
                <img src="assets/images/dashboard/top-header2.png" alt="header"
                    class="theme-color-blue-img img-fluid w-100 h-100 animated-scaleX">
                <img src="assets/images/dashboard/top-header3.png" alt="header"
                    class="theme-color-green-img img-fluid w-100 h-100 animated-scaleX">
                <img src="assets/images/dashboard/top-header4.png" alt="header"
                    class="theme-color-yellow-img img-fluid w-100 h-100 animated-scaleX">
                <img src="assets/images/dashboard/top-header5.png" alt="header"
                    class="theme-color-pink-img img-fluid w-100 h-100 animated-scaleX">
            </div>
        </div><div class="conatiner-fluid content-inner mt-n5 py-0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">


<div class="card-body">
     <div class="table-responsive">
             <table id="datatable" class="table table-striped" data-toggle="data-table">
                                    <thead>
                                        <tr>
                                            <th>Alteration Date</th>
                                            <th>Alteration Class</th>
                                            <th>Alteration Hour</th>
                                            <th>Requesting Staff</th>
                                            <th>Approval</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($alterationStaffs as $alterationStaff) {

                                            foreach ($requestingStaffs as $requestingStaff){
                                                if ($alterationStaff['lv_id'] == $requestingStaff['lv_id']){
                                                    $reqStaff = $requestingStaff['f_id'];
                                                }
                                            }
                                            foreach ($staffNames as $staffName){
                                                if($reqStaff == $staffName['f_id']){
                                                    $reqStaffName = $staffName['f_fname'] . " " . $staffName['f_lname'];
                                                }
                                            }
                                            foreach($requestStaffClasses as $requestStaffClass){
                                                if($alterationStaff['cls_id'] == $requestStaffClass['cls_id']){
                                                    $reqStaffClass = $requestStaffClass['cls_course'] . " " . $requestStaffClass['cls_dept'] . " Sem - " . $requestStaffClass['cls_sem']; 
                                                }
                                            }
                                        //     $staffaccept = $TableRow['la_staffacpt'] == 0 ? "false" : "true";
                                        //     $hodaccept = $TableRow['la_hodacpt'] == 0 ? "false" : "true";
                                        //     $principalaccept = $TableRow['la_principalacpt'] == 0 ? "false" : "true";
                                        //     $staffName = "";
                                        //     foreach ($EventRows as $row) {
                                        //         if ($row['f_id'] == $TableRow['f_id'])
                                        //             $staffName = "$row[f_fname] $row[f_lname]";
                                        //     }
                                        //     $ClassName = "";
                                        //     foreach ($EventRows2 as $row) {
                                        //         if ($row['cls_id'] == $TableRow['cls_id'])
                                        //             $ClassName = "$row[cls_course]-$row[cls_dept]-Sem-$row[cls_sem]";
                                        //     }
                                        //     echo "<a href ='../Leave/ManageLeaveAlternatives.php'><tr>
                                        echo "<tr>
                                            <td>$alterationStaff[la_date]</td>
                                            <td>$reqStaffClass</td>
                                            <td>$alterationStaff[la_hour]</td>
                                            <td>$reqStaffName</td>
                                            <td><input type='checkbox' name='leave_approval' value='$alterationStaff[lv_id]'></td>
                                        </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
        <!-- Nav Header Component End -->
        <!--Nav End-->
        </div>
        </div>
        </div>
    </div>
</div>
    








        <?php
} else {
    header("Location:flogin.php");
}
include("Includes/Footer.php"); ?>