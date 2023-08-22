<?php include("Includes/Header.php") ?>

<?php
include('Includes/db_connection.php');

$sql = "SELECT * FROM `erp_leave_alt` JOIN erp_faculty on erp_leave_alt.f_id=erp_faculty.f_id ";
$result = mysqli_query($conn, $sql);
$TableRows = array();
while ($row = mysqli_fetch_assoc($result)) {
    array_push($TableRows, $row);
}

$sql = 'SELECT * FROM erp_faculty';
$result = mysqli_query($conn, $sql);
$EventRows = array();
while ($row = mysqli_fetch_assoc($result)) {
    array_push($EventRows, $row);
}

$sql = 'SELECT * FROM erp_class';
$result = mysqli_query($conn, $sql);
$EventRows1 = array();
while ($row = mysqli_fetch_assoc($result)) {
    array_push($EventRows1, $row);
}

mysqli_close($conn);
?>

<div class="iq-navbar-header" style="height: 215px;">
    <!-- Navbar header content -->
</div>

<div class="conatiner-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <!-- Card header content -->
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>Alteration Date</th>
                                    <th>Alteration Hour</th>
                                    <th>Alteration Class</th>
                                    <th>Alteration Staff</th>
                                    <th>Staff Accept</th>
                                    <th>HOD Accept</th>
                                    <th>Principal Accept</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($TableRows as $TableRow) {
                                    $staffAccept = $TableRow['la_staffacpt'] == 0 ? "false" : "true";
                                    $hodAccept = $TableRow['la_hodacpt'] == 0 ? "false" : "true";
                                    $principalAccept = $TableRow['la_principalacpt'] == 0 ? "false" : "true";
                                    $staffName = "";
                                    foreach ($EventRows as $row) {
                                        if ($row['f_id'] == $TableRow['f_id'])
                                            $staffName = "$row[f_fname] $row[f_lname]";
                                    }
                                    $className = "";
                                    foreach ($EventRows1 as $row) {
                                        if ($row['cls_id'] == $TableRow['cls_id'])
                                            $className = "$row[cls_course]-$row[cls_deptname]-Sem-$row[cls_sem]";
                                    }
                                    $principalApproved = $TableRow['la_principalacpt'] == 0 ? "" : "checked";
                                    echo "<tr>
                                        <td>$TableRow[la_date]</td>
                                        <td>$TableRow[la_hour]</td>
                                        <td>$className</td>
                                        <td>$staffName</td>
                                        <td>$staffAccept</td>
                                        <td>$hodAccept</td>
                                        <td>$principalAccept</td>
                                        <td>
                                            <div class='form-check form-switch'>
                                                <input class='form-check-input' $principalApproved type='checkbox' role='switch' id='$TableRow[la_id]'>
                                            </div>
                                        </td>
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
            var LeaveAlt = this.id;
            var LeaveVal = 0;
            var Approval = "";
            if (this.checked) {
                LeaveVal = 1;
                Approval = "Approved";
            } else {
                LeaveVal = 0;
                Approval = "Denied";
            }
            
            $.ajax({
                url: 'Functions2.php',
                type: 'POST',
                data: {
                    LeaveAlt: LeaveAlt,
                    LeaveVal: LeaveVal,
                    Function: "PrincipalApproveLeaveAlt"
                },
                success: function (response) {
                    console.log(response);
                    if (response == "OK") {
                        $("#Result").html(`<div class="alert alert-success fade show" role="alert">Leave Alteration ${Approval} Successfully</div>`);
                        setTimeout(function () {
                            $("#Result").html('');
                            location.reload();
                        }, 1000);
                    } else {
                        $("#Result").html(`<div class="alert alert-danger fade show" role="alert">${response}</div>`);
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
