<?php


session_start();

if (isset($_SESSION['login_data']) and ($_SESSION['erpFacultyRecords'][0]['f_role'] == 'HOD' or $_SESSION['erpFacultyRecords'][0]['f_role'] == 'Principal')) {
    $log_id = $_SESSION['login_data'];
    if (isset($_SESSION['classIds'])) {
        $classIds = $_SESSION['classIds'];
    }
    $erpFacultyRecords = $_SESSION['erpFacultyRecords'];
    echo $log_id;
    include("Includes/Header.php"); ?>


    <?php
    // Include the database connection file
    include('Includes/db_connection.php');
    //For the table
    if ($erpFacultyRecords[0]['f_role'] == 'HOD') {
        $sql = "SELECT * FROM erp_leave_alt WHERE cls_id IN (";
        foreach ($classIds as $classId) {
            $sql .= "" . $classId['cls_id'] . ", ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ") ORDER BY la_date DESC";

        $result = mysqli_query($conn, $sql);
        $TableRows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['la_staffacpt'] == 1) {
                array_push($TableRows, $row);
            }
        }
    } else {
        $sql = "SELECT * FROM erp_leave_alt";
        $result = mysqli_query($conn, $sql);
        $TableRows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['la_hodacpt'] == 1) {
                array_push($TableRows, $row);
            }
        }
    }

    //for the staff dropdown
    $sql = 'SELECT * FROM erp_faculty';
    $result = mysqli_query($conn, $sql);
    $EventRows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($EventRows, $row);
    }

    //for the class dropdown
    $sql = 'SELECT * FROM erp_class';
    $result = mysqli_query($conn, $sql);
    $EventRows1 = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($EventRows1, $row);
    }


    // leave table
    $sql = 'SELECT * FROM erp_leave';
    $result = mysqli_query($conn, $sql);
    $EventRows2 = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($EventRows2, $row);
    }





    mysqli_close($conn);
    ?>







    <div class="iq-navbar-header" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center">
                        <div>
                            <h1>Manage Leave Approval</h1>
                            <p>Here you can find all of your Leave Approval Details here.</p>
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
    <!-- Nav Header Component End -->
    <!--Nav End-->
    </div>



    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header ">
                        <div class="header-title d-flex justify-content-end">
                        <div id="Result" class="m-3"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped" data-toggle="data-table">
                                <thead>
                                    <tr>
                                        <th>Requesting Staff</th>
                                        <th>alteration date</th>
                                        <th>alteration hour</th>
                                        <th>alteration class</th>
                                        <th>aleration staff</th>
                                        <th>staff accept</th>
                                        <th>hod accept</th>
                                        <th>principal accept</th>
                                        <?php if ($erpFacultyRecords[0]['f_role'] == 'HOD') { ?>
                                            <th>HOD approval</th>
                                        <?php } else { ?>
                                                <th>Principal approval</th>
                                            <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($TableRows as $TableRow) {
                                        $staffaccept = $TableRow['la_staffacpt'] == 0 ? "false" : "true";
                                        $hodaccept = $TableRow['la_hodacpt'] == 0 ? "false" : "true";
                                        $principalaccept = $TableRow['la_principalacpt'] == 0 ? "false" : "true";
                                        $staffName = "";
                                        // Alteration staff Name
                                        foreach ($EventRows as $row) {
                                            if ($row['f_id'] == $TableRow['f_id'])
                                                $staffName = "$row[f_fname] $row[f_lname]";
                                        }
                                        // Requesting staff id
                                        $reqStaffId = "";
                                        foreach ($EventRows2 as $row) {
                                            if ($row['lv_id'] == $TableRow['lv_id'])
                                                $reqStaffId = "$row[f_id]";
                                        }
                                        // Requesting staff name
                                        $reqStaffName = "";
                                        foreach ($EventRows as $row) {
                                            if ($row['f_id'] == $reqStaffId)
                                                $reqStaffName = "$row[f_fname] $row[f_lname]";
                                        }

                                        $ClassName = "";
                                        foreach ($EventRows1 as $row) {
                                            if ($row['cls_id'] == $TableRow['cls_id'])
                                                $ClassName = "$row[cls_course]-$row[cls_deptname]-Sem-$row[cls_sem]";
                                        }

                                        echo "<a href ='../Leave/ManageLeaveAlternatives.php'><tr>
                                        <td>$reqStaffName</td>
                                        <td>$TableRow[la_date]</td>
                                        <td>$TableRow[la_hour]</td>
                                        <td>$ClassName</td>
                                        <td>$staffName</td>
                                        <td>$staffaccept</td>
                                        <td>$hodaccept</td>
                                        <td>$principalaccept</td>";
                                        if ($erpFacultyRecords[0]['f_role'] == 'HOD') {
                                            $Approved = $TableRow['la_hodacpt'] == 0 ? "" : "checked";
                                            if ($erpFacultyRecords[0]['f_role'] == 'HOD') {
                                                $erpFacultyRole = 'hod';
                                            }
                                            ;
                                            echo "
                                        <td> <div class='form-check form-switch'>
                                        <input class='form-check-input' $Approved type='checkbox' role='switch' value='$erpFacultyRole' id='$TableRow[la_id]'>
                                      </div></td>
                                    </tr>";
                                        } else {
                                            $Approved = $TableRow['la_principalacpt'] == 0 ? "" : "checked";if ($erpFacultyRecords[0]['f_role'] == 'Principal') {
                                                $erpFacultyRole = 'principal';
                                            } else{
                                                echo "Wrong role instead of Principal role";
                                            }
                                            echo "
                                        <td> <div class='form-check form-switch'>
                                        <input class='form-check-input' $Approved type='checkbox' role='switch' value='$erpFacultyRole' id='$TableRow[la_id]'>
                                      </div></td>
                                    </tr>";
                                        }
                                    }
                                    ?>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $(".form-check-input").click(function (e) {
                var LeaveAlt=this.id;
                var role = $(this).val();
                console.log(role);
                var LeaveVal=0;
                var Approval="";
                if (this.checked) {LeaveVal=1; Approval="Approved";}
                else{LeaveVal=0; Approval="Denied";}
                    $.ajax({
                        url: 'Functions.php',
                        type: 'POST',
                        data: { role: role, LeaveAlt: LeaveAlt,LeaveVal:LeaveVal, Function: "ApproveLeaveAlt" },
                        success: function (response) {
                            console.log(response);
                            if (response == "OK") {
                                $("#Result").html(`<div class="alert alert-success fade show" role="alert"> Leave Aleration ${Approval} Successfully</div>`);
                                setTimeout(function () {
                                    $("#Result").html('');
                                    $('#CreateLeaveAlternative').modal('hide');
                                    location.reload();
                                }, 1000);
                            } else {
                                $("#Result").html(`<div class="alert alert-danger fade show" role="alert"> ${response}</div>`);
                                setTimeout(function () {
                                    $("#Result").html('');
                                }, 1000);

                            }

                        }
                    });
            
            });
        });
    </script>

    <?php
} else {
    header("Location:flogin.php");
}
include("Includes/Footer.php"); ?>