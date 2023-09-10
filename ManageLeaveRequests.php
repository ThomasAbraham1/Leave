<?php

session_start();

if (isset($_SESSION['login_data'])) {
    $log_id = $_SESSION['login_data'];
    $erpFacultyRecords = $_SESSION['erpFacultyRecords'];
    include("Includes/Header.php");
    include('Includes/db_connection.php');

    // Finding requests from other staffs
    $alterationStaffs = [];
    $laIdsOfeqs = [];
    $sql = "SELECT * FROM erp_leave_alt WHERE f_id = '$log_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($alterationStaffs, $row);

        }
    }
    $alterationIdsJSON = json_encode($laIdsOfeqs);
    $countOfReqRows = 0;
    $countOfRowsEqual2 = 0;
    foreach ($alterationStaffs as $alterationStaff) {
        $countOfReqRows++;
        if ($alterationStaff['la_staffacpt'] == "2") {
            $countOfRowsEqual2++;
        }
    }


    // Lv_id matches from erp_leave with erp_leave_alt
    $requestingStaffs = [];
    $sql = "SELECT * FROM erp_leave";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($requestingStaffs, $row);
        }

    }

    // Finding staff name using f_id from erp_leave
    $staffNames = [];
    $sql = "SELECT * FROM erp_faculty";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($staffNames, $row);
        }
    }
    
    // Finding requesting staff's class name

    $requestStaffClasses = [];
    $sql = "SELECT * FROM erp_class";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
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
            </div>
            <div class="conatiner-fluid content-inner mt-n5 py-0">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">



                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-striped" data-toggle="data-table">
                                        <thead>
                                            <tr>
                                            <th>Requesting Staff</th>
                                                <th>Alteration Date</th>
                                                <th>Alteration Class</th>
                                                <th>Alteration Hour</th>
                                                <th>Approval</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Looping the found lv_id records for current user
                                            foreach ($alterationStaffs as $alterationStaff) {
                                                // Finding the f_id of the requester through matching lv_id
                                                foreach ($requestingStaffs as $requestingStaff) {
                                                    if ($alterationStaff['lv_id'] == $requestingStaff['lv_id']) {
                                                        $reqStaff = $requestingStaff['f_id'];
                                                    }
                                                }
                                                // Finding the full name of the requester faculty using erp_faculty
                                                foreach ($staffNames as $staffName) {
                                                    if ($reqStaff == $staffName['f_id']) {
                                                        $reqStaffName = $staffName['f_fname'] . " " . $staffName['f_lname'];
                                                    }
                                                }
                                                // Finding the course, dept, sem using erp_class matching cls_id from alt & class
                                                foreach ($requestStaffClasses as $requestStaffClass) {
                                                    if ($alterationStaff['cls_id'] == $requestStaffClass['cls_id']) {
                                                        $reqStaffClass = $requestStaffClass['cls_course'] . " " . $requestStaffClass['cls_dept'] . " Sem - " . $requestStaffClass['cls_sem'];
                                                    }
                                                }
                                                //Checking status of staff accept and changing checkbox
                                                $staffAcceptStatus = $alterationStaff['la_staffacpt'];

                                                // Loading rendered la_ids only
                                                array_push($laIdsOfeqs, $alterationStaff['la_id']);

                                                echo "<tr>
                                                <td>$reqStaffName</td>
                                            <td>$alterationStaff[la_date]</td>
                                            <td>$reqStaffClass</td>
                                            <td>$alterationStaff[la_hour]</td>";
                                                if ($staffAcceptStatus >= 1) {
                                                    echo "<td><input class='approvalCheckbox' type='checkbox' name='leave_approval' value='$alterationStaff[lv_id],$alterationStaff[la_hour],$alterationStaff[la_date]' disabled checked></td>
                                        </tr>";
                                                } else if ($staffAcceptStatus == 0) {
                                                    echo "<td><input class='approvalCheckbox' type='checkbox' name='leave_approval' value='$alterationStaff[la_id]'></td>
                                        </tr>";
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <div>
                                    <?php $alterationIdsJSON = json_encode($laIdsOfeqs); ?>
                <!-- <button type="button" class="btn btn-primary reqHodBtn" value="">Request HOD</button> -->
                <p></p>
               </div>
                                    <div id="staffAcceptAlert" class="m-3"></div>
                                </div>
                            </div>
                        
                            <!-- Nav Header Component End -->
                            <!--Nav End-->
                        </div>
                    </div>
                </div>
           

            </div>
        
            </div>


            <script>

                 $(document).ready(function () {
                    // Function for controlling the send to HOD button
                    // function reqHodBtnController(){
                    // var chkBoxCount = $('.approvalCheckbox');
                    //     var count = 0;
                    //     for (let i = 0; i < chkBoxCount.length; i++) {
                    //         var element = chkBoxCount[i];
                    //         if(element.checked){
                    //             count++;
                    //         }
                    //     }
                    //     if(countOfRowsEqual2 == countOfReqRows){
                    //         $('.reqHodBtn').prop('disabled', true);
                    //         $('.reqHodBtn + p').html('Already sent to HOD');

                    //     }
                    //     else if(count == chkBoxCount.length){
                    //         $('.reqHodBtn').prop('disabled', false);

                    //     } else{
                    //         $('.reqHodBtn').prop('disabled', true);
                    //     }
                    // }
            // Checking if to enable the button when page loads first

                    // Function for when one of the checkboxes is clicked
                    $('.approvalCheckbox').on('click', function () {
                        if (confirm("Are you sure you want to consent?") == true) { // Asks confirmation for ticking checkbox
                            // Function for sending ajax values when checkbox is ticked
                            $('.approvalCheckbox').on('change', function () {
                        var lvidValue = $(this).val();
                        lvidValue =JSON.stringify(lvidValue);
                        console.log(lvidValue);
                        // ajax code to send la_id value of the clicked checkbox to update erp_leave_alt
                        $.ajax({
                            url: 'Functions.php',
                            type: 'POST',
                            data: {lvidValue: lvidValue, Function: "leaveReqUpdation" },
                            success: function (response) {
                            if (response == "OK") {
                                    $("#staffAcceptAlert").html(`<div class="alert alert-success fade show" role="alert"> Staff Accept Updated</div>`);
                                    setTimeout(function () {
                                        $("#staffAcceptAlert").html('');
                                        location.reload();
                                    }, 1000);
                                } else {
                                    $("#staffAcceptAlert").html(`<div class="alert alert-danger fade show" role="alert"> ${response}</div>`);
                                setTimeout(function () {
                                    $("#staffAcceptAlert").html('');
                                    location.reload();
                                }, 5000);
                                }
                            }
                        });
                 });
                        }else{
                            $(this).prop('checked', false) // In-case the user chooses to cancel confirmation for ticking checkbox  
                        }
                         // Checking if button should be abled after onchange function
                });

            
                // Function for when the HOD button is clicked
                // $('.reqHodBtn').on('click', function () {
                //     var laIds = $(this).val();

                //     // ajax for updating the staffacpt to '2' for all current requests user consented to submit
                //     $.ajax({
                //             url: 'Functions.php',
                //             type: 'POST',
                //             data: {laIds: laIds, Function: "toHodBtnClick" },
                //             success: function (response) {
                //             if (response == "OK") {
                //                     $("#staffAcceptAlert").html(`<div class="alert alert-success fade show" role="alert">Sent to HOD</div>`);
                //                     setTimeout(function () {
                //                         $("#staffAcceptAlert").html('');
                //                         location.reload();
                //                     }, 1000);
                //                 } else {
                //                     $("#staffAcceptAlert").html(`<div class="alert alert-danger fade show" role="alert"> ${response}</div>`);
                //                 setTimeout(function () {
                //                     $("#staffAcceptAlert").html('');
                //                     location.reload();
                //                 }, 5000);
                //                 }
                //             }
                //         });

                
                
                // });
                });
            </script>









            <?php
} else {
    header("Location:flogin.php");
}
include("Includes/Footer.php"); ?>