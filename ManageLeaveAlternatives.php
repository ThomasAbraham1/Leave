<?php


session_start();

if (isset($_SESSION['login_data'])) {
    $log_id = $_SESSION['login_data'];
    echo $log_id;
    include("Includes/Header.php") ?>


    <?php
    if (isset($_GET["LeaveId"])) {
        $LeaveId = $_GET["LeaveId"];
    }
    // Include the database connection file
    include('Includes/db_connection.php');
    //For the table
    $sql = "SELECT erp_leave_alt.f_id, erp_leave_alt.la_date, erp_leave_alt.la_hour, la_principalacpt,la_hodacpt, la_staffacpt, erp_leave_alt.cls_id FROM `erp_leave_alt` JOIN erp_faculty on erp_leave_alt.f_id=erp_faculty.f_id WHERE erp_leave_alt.lv_id=" . $LeaveId . "";
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






    // Code from trial.php
// Getting subjects of faculty
    $subjects = [];
    $cse_classids = [];
    $periods = [];
    $sql = "SELECT f_id, tt_subcode FROM erp_subject WHERE f_id = '$log_id'";
    $result = $conn->query($sql);


    // Check if any rows were returned
    if ($result->num_rows > 0) {

        // Output the table header
        // Output the table rows
        $i = 0;
        foreach ($result as $row) {

            $subjects[$i] = $row['tt_subcode'];
            $i++;
        }
        // Output the table footer

    } else {
        echo "0 results";
    }

    $query = "SELECT tt_day, tt_period, tt_subcode FROM erp_timetable WHERE tt_day='Mon' AND (";
    foreach ($subjects as $subject) {
        $q = $query . "tt_subcode='$subject' OR ";
        $query = $q;
    }
    $query = rtrim($query, " OR "); // Remove the last " OR "
    $query .= ")";

    // Execute the query and fetch the results
    $result = $conn->query($query);

    // Check if any rows were returned
    if ($result->num_rows > 0) {

        $i = 0;
        // Output the table header

        // Output the table rows
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $todaySubjects[$i] = $row['tt_subcode'];
            $periods[$i] = $row['tt_period'];
            $i++;
        }
        $todaySubjects = array_unique($todaySubjects);
        // Output the table footer  
    } else {
        echo "0 results";
    }


    $sql = "SELECT DISTINCT cls_deptname, cls_id FROM erp_class WHERE cls_deptname ='Computer Science And Engineering' ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $cse_classids[$i] = $row['cls_id'];
            $i++;
        }
    }

    foreach ($periods as $period) {
        // Build the query string
// $query = "SELECT * FROM erp_subject INNER JOIN erp_timetable ON erp_subject.tt_subcode = erp_timetable.tt_subcode WHERE tt_day='Mon' AND tt_period NOT IN (";
// foreach ($periods as $period) {
//   $query .= "$period, ";
// }

        $query = "SELECT * FROM erp_subject INNER JOIN erp_timetable ON erp_subject.tt_subcode = erp_timetable.tt_subcode INNER JOIN erp_faculty ON erp_subject.f_id=erp_faculty.f_id WHERE tt_day='Mon' AND tt_period NOT IN ($period) AND erp_subject.f_id NOT IN ('$log_id') AND erp_subject.cls_id IN (";
        // $query = rtrim($query, ", "); // Remove the last comma and space

        // $query .= ")";
        foreach ($cse_classids as $classid) {
            $query .= "$classid, ";
        }
        $query = rtrim($query, ", ");
        $query .= ")";
        // Execute the query and fetch the results
        $result = $conn->query($query);

        // Check if any rows were returned
        if ($result->num_rows > 0) {
            // Output the table header

            // Output the table rows
            while ($row = $result->fetch_assoc()) {

            }
            // Output the table footer
        } else {
            echo "0 results";
        }

    }

    //for the class dropdown

    //THIS IS IT
    $sql = "SELECT DISTINCT erp_timetable.cls_id,erp_timetable.tt_subcode, erp_class.cls_id, erp_class.cls_dept, erp_class.cls_sem, erp_class.cls_course FROM `erp_class` INNER JOIN erp_timetable ON erp_class.cls_id = erp_timetable.cls_id WHERE tt_day='Mon' AND tt_period IN (";

    // For adding the periods into query
    foreach ($periods as $period) {
        $sql .= "$period, ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= ") AND erp_timetable.cls_id IN (";

    // For adding the class ids into the query
    foreach ($cse_classids as $classid) {
        $sql .= "$classid, ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= ") AND erp_timetable.tt_subcode IN (";
    // For adding the subjects into query
    foreach ($todaySubjects as $todaySubject) {
        $sql .= "'$todaySubject', ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= ")";

    $result = mysqli_query($conn, $sql);
    $EventRows1 = array();

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($EventRows1, $row);
    }


    $sql = "SELECT erp_class.cls_id, cls_course, cls_dept, cls_sem FROM erp_class INNER JOIN erp_leave_alt ON erp_class.cls_id = erp_leave_alt.cls_id WHERE lv_id = 1";
    $result = mysqli_query($conn, $sql);
    $EventRows2 = array();

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($EventRows2, $row);
    }


    $subjects = json_encode($periods);
    echo $subjects;
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
                                                    <option value="">Select your period</option>
                                                    <?php
                                                    foreach ($periods as $period) {
                                                        echo "<option value='$period'>$period</option>";

                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="AlerationStaff">AlerationStaff</label>
                                                <select class="form-control" id="AlerationStaff" name="AlerationStaff"
                                                    required="required">

                                                    <?php
                                                    // foreach ($EventRows as $Event) {
                                                    //     echo "<option value=" . $Event['f_id'] . ">" . $Event['f_fname'] . " " . $Event['f_lname'] . "</option>";
                                                    // }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="AlterationClass">AlterationClass</label>
                                                <select class="form-control" id="AlterationClass" name="AlterationClass"
                                                    required="required">
                                                    <?php
                                                    foreach ($EventRows1 as $Event) {
                                                        echo "<option value=" . $Event['cls_id'] . ">" . $Event['cls_course'] . "-" . $Event['cls_dept'] . "-Sem-" . $Event['cls_sem'] . "</option>";
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
                                        $staffName = "";
                                        foreach ($EventRows as $row) {
                                            if ($row['f_id'] == $TableRow['f_id'])
                                                $staffName = "$row[f_fname] $row[f_lname]";
                                        }
                                        $ClassName = "";
                                        foreach ($EventRows2 as $row) {
                                            if ($row['cls_id'] == $TableRow['cls_id'])
                                                $ClassName = "$row[cls_course]-$row[cls_dept]-Sem-$row[cls_sem]";
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
    <?php
    $cse_classids = json_encode($cse_classids);
    $periods = json_encode($periods);
    $todaySubjects = json_encode($todaySubjects);

    ?>


    <script>
        $(document).ready(function () {
            // Define selectedPeriod variable and set initial value to default option value
  

            // Listen for the "change" event on the "AlterationHour" dropdown menu
        
            $('#AlterationHour').on('change', function () {
                var staffSelectElement = document.getElementById('AlerationStaff');
            staffSelectElement.innerHTML = "";
                var periods = [];
                var alterationHour = $("#AlterationHour").children();
                var alterationHourLength = Array.from(alterationHour);
                for (var i = 0; i < alterationHourLength.length -1; i++) {
        var element = alterationHourLength[i+1];
        periods.push(element.getAttribute('value'));
    }
    console.log(periods);
                var alterationClass = $("#AlterationClass").children();
    var childrenArray = Array.from(alterationClass);
    for (var i = 0; i < childrenArray.length; i++) {
        var element = childrenArray[i];
        element.removeAttribute('selected');
    }
                var selectedPeriod = $(this).val();
                console.log('Selected period: ' + selectedPeriod);
                var cse_classids = <?php echo $cse_classids ?>;
                var f_id = '<?php echo $log_id ?>';
                var periods = <?php echo $periods ?>;
                var todaySubjects = <?php echo $todaySubjects ?>;
                $.ajax({
                    url: 'Functions.php',
                    type: 'POST',
                    data: {periods: periods,todaySubjects: todaySubjects,f_id: f_id, selectedPeriod: selectedPeriod, cse_classids: cse_classids, Function: "AleternationHourDropdownChange" },
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

                            var data=JSON.parse(response);
                            // console.log(data[0]);
                            // alert(`${data[0].cls_course} ${data[0].cls_sem}`);
                            var course = data[0].cls_course;
                            var sem = data[0].cls_sem;
                            var dept =data[0].cls_dept;
                            var periodMatching = `${course}-${dept}-Sem-${sem}`;
                            var altPeriods = data.slice(1,data.length);
                            console.log(altPeriods);
                // Creating <option> tags for the ALteration Staff in Alterations creation
                   
                        for (var i = 0; i < altPeriods.length; i++) {
                            var row = altPeriods[i];
                            staffSelectElement.innerHTML += `<option value='${row.f_id}'> ${row.f_fname} ${row.f_lname} </option>`;
                        }
    for (var i = 0; i < childrenArray.length; i++) {
        var element = childrenArray[i];
        console.log(element);
        if(periodMatching == element.innerHTML ){
            element.setAttribute('selected','selected');
            break;

        }
    }
                            // $("#Result").html(`<div class="alert alert-danger fade show" role="alert"> ${response}</div>`);
                            // setTimeout(function () {
                            //     $("#Result").html('');
                            // }, 5000);

                        }

                    }
                });

            });
        });

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

<?php
} else {
    header("Location:flogin.php");
}
include("Includes/Footer.php"); ?>