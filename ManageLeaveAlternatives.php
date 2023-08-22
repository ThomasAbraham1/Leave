<?php include("Includes/Header.php") ?>


<?php
if (isset($_GET["LeaveId"])) {
    $LeaveId = $_GET["LeaveId"];
}
if (isset($_GET["fid"])) {
    $f_id = $_GET["fid"];
    echo $f_id;
}
// Include the database connection file
include('Includes/db_connection.php');
//For the table
$sql = "SELECT * FROM `erp_leave_alt` JOIN erp_faculty on erp_leave_alt.f_id=erp_faculty.f_id WHERE erp_leave_alt.lv_id=" . $LeaveId;
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
                        <h1>Manage Leave Alternatives</h1>
                        <p>Here you can find all of your Leave Alternatives Details.</p>
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
                        <!-- <h4 class="card-title">Bootstrap Datatables</h4> -->
                        <button class="btn btn-primary mb-2 " type="button" data-bs-toggle="modal"
                            data-bs-target="#CreateLeaveAlternative"> Create Leave Alternative </button>

                        <!-- Modal -->
                        <div class="modal fade" id="CreateLeaveAlternative" tabindex="-1"
                            aria-labelledby="CreateLeaveAlternativeLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="CreateLeaveAlternativeLabel">Create Leave
                                            Alternative</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="form-group">
                                            <label for="AlterationHour" required="required">Alteration Hour</label>
                                            <select class="form-control" id="AlterationHour" name="AlterationHour"
                                                required="required">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="AlterationClass">AlterationClass</label>
                                            <select class="form-control" id="AlterationClass" name="AlterationClass"
                                                required="required">
                                                <?php
                                                foreach ($EventRows1 as $Event) {
                                                    echo "<option value=" . $Event['cls_id'] . ">" . $Event['cls_course'] . "-" . $Event['cls_deptname'] . "-Sem-" . $Event['cls_sem'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="AlerationStaff">AlerationStaff</label>
                                            <select class="form-control" id="AlerationStaff" name="AlerationStaff"
                                                required="required">
                                                <?php
                                                foreach ($EventRows as $Event) {
                                                    echo "<option value=" . $Event['f_id'] . ">" . $Event['f_fname'] . " " . $Event['f_lname'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <input type="hidden" value="<?php echo $LeaveId; ?>" id='LeaveId'>

                                        <div id="Result" class="m-3">

                                        </div>


                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary"
                                            id="CreateLeaveAlternativeBtn">Create</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <script>
                            $(function () {
                                $("#CreateLeaveAlternativeBtn").click(function () {
                                    var AlterationHour = $("#AlterationHour").val();
                                    var AlterationClass = $("#AlterationClass").val();
                                    var AlerationStaff = $("#AlerationStaff").val();
                                    var LeaveId = $("#LeaveId").val();
                                    console.log(AlterationHour + AlterationClass + AlerationStaff + LeaveId);

                                    $.ajax({
                                        url: 'Functions.php',
                                        type: 'POST',
                                        data: { AlterationHour: AlterationHour, AlterationClass: AlterationClass, AlerationStaff: AlerationStaff, LeaveId: LeaveId, Function: "CreateLeaveAlternatives" },
                                        success: function (response) {
                                            console.log(response);
                                            if (response == "OK") {
                                                $("#Result").html(`<div class="alert alert-success fade show" role="alert"> Event Created Successfully</div>`);
                                                setTimeout(function () {
                                                    $("#Result").html('');
                                                    $('#CreateLeaveAlternative').modal('hide');
                                                    location.reload();
                                                }, 5000);
                                            } else {
                                                $("#Result").html(`<div class="alert alert-danger fade show" role="alert"> ${response}</div>`);
                                                setTimeout(function () {
                                                    $("#Result").html('');
                                                }, 5000);

                                            }

                                        }
                                    });

                                });
                            });
                        </script>




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
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($TableRows as $TableRow) {
                                    $staffaccept = $TableRow['la_staffacpt'] == 0 ? "false" : "true";
                                    $hodaccept = $TableRow['la_hodacpt'] == 0 ? "false" : "true";
                                    $principalaccept = $TableRow['la_principalacpt'] == 0 ? "false" : "true";
                                    $staffName="";
                                    foreach($EventRows as $row){
                                        if($row['f_id']==$TableRow['f_id'])$staffName="$row[f_fname] $row[f_lname]";
                                    }
                                    $ClassName="";
                                    foreach($EventRows1 as $row){
                                        if($row['cls_id']==$TableRow['cls_id'])$ClassName="$row[cls_course]-$row[cls_deptname]-Sem-$row[cls_sem]";
                                    }
                                    echo "<a href ='../Leave/ManageLeaveAlternatives.php'><tr>
                                        <td>$TableRow[la_date]</td>
                                        <td>$TableRow[la_hour]</td>
                                        <td>$ClassName</td>
                                        <td>$staffName</td>
                                        <td>$staffaccept</td>
                                        <td>$hodaccept</td>
                                        <td>$principalaccept</td>
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




<?php include("Includes/Footer.php") ?>