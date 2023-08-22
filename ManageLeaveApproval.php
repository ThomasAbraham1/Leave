<?php include("Includes/Header.php") ?>


<?php
// Include the database connection file
include('Includes/db_connection.php');
//For the table
$sql = "SELECT * FROM `erp_leave_alt` JOIN erp_faculty on erp_leave_alt.f_id=erp_faculty.f_id ";
$result = mysqli_query($conn, $sql);
$TableRows = array();
while ($row = mysqli_fetch_assoc($result)) {

    array_push($TableRows, $row);
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
                                    <th>alteration date</th>
                                    <th>alteration hour</th>
                                    <th>alteration class</th>
                                    <th>aleration staff</th>
                                    <th>staff accept</th>
                                    <th>hod accept</th>
                                    <th>principal accept</th>
                                    <th>principal Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($TableRows as $TableRow) {
                                    $staffaccept = $TableRow['la_staffacpt'] == 0 ? "false" : "true";
                                    $hodaccept = $TableRow['la_hodacpt'] == 0 ? "false" : "true";
                                    $principalaccept = $TableRow['la_principalacpt'] == 0 ? "false" : "true";
                                    $staffName = "";
                                    foreach ($EventRows as $row) {
                                        if ($row['f_id'] == $TableRow['f_id'])
                                            $staffName = "$row[f_fname] $row[f_lname]";
                                    }
                                    $ClassName = "";
                                    foreach ($EventRows1 as $row) {
                                        if ($row['cls_id'] == $TableRow['cls_id'])
                                            $ClassName = "$row[cls_course]-$row[cls_deptname]-Sem-$row[cls_sem]";
                                    }
                                    $PrincipalApproved=$TableRow['la_principalacpt']==0?"":"checked";
                                    echo "<a href ='../Leave/ManageLeaveAlternatives.php'><tr>
                                        <td>$TableRow[la_date]</td>
                                        <td>$TableRow[la_hour]</td>
                                        <td>$ClassName</td>
                                        <td>$staffName</td>
                                        <td>$staffaccept</td>
                                        <td>$hodaccept</td>
                                        <td>$principalaccept</td>
                                        <td> <div class='form-check form-switch'>
                                        <input class='form-check-input' $PrincipalApproved type='checkbox' role='switch' id='$TableRow[la_id]'>
                                      </div></td>
                                    </tr>";
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
            var LeaveVal=0;
            var Approval="";
            if (this.checked) {LeaveVal=1; Approval="Approved";}
            else{LeaveVal=0; Approval="Denied";}
                $.ajax({
                    url: 'Functions.php',
                    type: 'POST',
                    data: { LeaveAlt: LeaveAlt,LeaveVal:LeaveVal, Function: "ApproveLeaveAlt" },
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


<?php include("Includes/Footer.php") ?>