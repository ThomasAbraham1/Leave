<?php
include('Includes/db_connection.php');
// Getting subjects of faculty
$subjects = [];
$cse_classids = [];
$todaySubjects = [];
$sql = "SELECT f_id, tt_subcode FROM erp_subject WHERE f_id = 'f005'";
$result = $conn->query($sql);


// Check if any rows were returned
if ($result->num_rows > 0) {
    
    // Output the table header
    echo "<table border=1><tr><th>f_id</th><th>tt_subcode</th></tr>";
    // Output the table rows
     $i =0;
    foreach ($result as $row) {
       
        echo "<tr><td>" . $row["f_id"] . "</td><td>" . $row["tt_subcode"] . "</td></tr>";
        $subjects[$i] = $row['tt_subcode']; 
        $i++;
      }
    // Output the table footer
    echo "</table>";
} else {
    echo "0 results";
}

// Getting periods of faculty on a day

// Build the query string
$periods = [];
$query = "SELECT tt_day, tt_period, tt_subcode FROM erp_timetable WHERE tt_day='Mon' AND (";
foreach ($subjects as $subject) {
  $q = $query . "tt_subcode='$subject' OR ";
  $query = $q;
}
$query = rtrim($query, " OR "); // Remove the last " OR "
$query .= ")";

echo $query;
// Execute the query and fetch the results
$result = $conn->query($query);

// Check if any rows were returned
if ($result->num_rows > 0) {

  $i=0;
  // Output the table header
  echo "<table border=1><tr><th>Day</th><th>Subject</th><th>Period</th></tr>";
  // Output the table rows
  $i=0;
  while ($row = $result->fetch_assoc()) {
    $todaySubjects[$i] = $row['tt_subcode'];
    echo "<tr><td>" . $row["tt_day"] . "</td><td>" . $row["tt_subcode"] . "</td><td>" . $row["tt_period"] . "</td></tr>";
    echo 'hello';
    $periods[$i] = $row['tt_period'];
    $i++;
  }
  $todaySubjects = array_unique($todaySubjects);
  echo $todaySubjects[1];
  // Output the table footer
  echo "</table>";

} else {
  echo "0 results";
}


$sql = "SELECT DISTINCT cls_deptname, cls_id FROM erp_class WHERE cls_deptname ='Computer Science And Engineering' ";
$result = $conn->query($sql);
if($result->num_rows >0){
  $i = 0;
  while ($row = $result->fetch_assoc()) {
    $cse_classids[$i] = $row['cls_id'];
  echo $row['cls_id'] . "<hr>";
  $i++;
  }
}

foreach ($periods as $period) {
// Build the query string
// $query = "SELECT * FROM erp_subject INNER JOIN erp_timetable ON erp_subject.tt_subcode = erp_timetable.tt_subcode WHERE tt_day='Mon' AND tt_period NOT IN (";
// foreach ($periods as $period) {
//   $query .= "$period, ";
// }

$query = "SELECT * FROM erp_subject INNER JOIN erp_timetable ON erp_subject.tt_subcode = erp_timetable.tt_subcode INNER JOIN erp_faculty ON erp_subject.f_id=erp_faculty.f_id WHERE tt_day='Mon' AND tt_period NOT IN ($period) AND erp_subject.f_id NOT IN ('f005') AND erp_subject.cls_id IN (";
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
  echo '<h3>'. $period . '</h3>';
  echo "<table border=1><tr><th>Fid</th><th>Fname</th><th>Day</th><th>Available alternatives</th></tr>";
  // Output the table rows

  while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row["f_id"] . "</td><td>" . $row["f_fname"] . " " . $row["f_lname"] . "</td><td>" . $row["tt_day"] . "</td><td>" . $row["tt_period"] . "</td></tr>";
    
  }
  echo $query;
  // Output the table footer
  echo "</table>";
} else {
  echo "0 results";
}

}

// Finding alternative staffs using periods of a faculty in a day


// $result = $conn->query($sql);

// // Check if any rows were returned
// if ($result->num_rows > 0) {
    
//     // Output the table header
//     echo "<table border=1><tr><th>f_id</th><th>tt_subcode</th></tr>";
//     // Output the table rows
//      $i =0;
//     foreach ($result as $row) {
       
//         echo "<tr><td>" . $row["f_id"] . "</td><td>" . $row["tt_subcode"] . "</td></tr>";
//         $subjects[$i] = $row['tt_subcode']; 
//         $i++;
//       }
//     // Output the table footer
//     echo "</table>";
// } else {
//     echo "0 results";
// }
// Close the connection
//for the class dropdown
$sql = "SELECT DISTINCT erp_timetable.cls_id,erp_timetable.tt_subcode, erp_class.cls_dept, erp_class.cls_sem, erp_class.cls_course FROM `erp_class` INNER JOIN erp_timetable ON erp_class.cls_id = erp_timetable.cls_id WHERE tt_day='Mon' AND tt_period IN (";

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

echo $sql;
echo "<table border=1><tr><th>class id</th><th>Dept</th><th>Sem</th><th>Course</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
  echo "<tr><td>" . $row["cls_id"] . "</td><td>" . $row["cls_dept"] . "</td><td>" . $row["cls_sem"] . "</td> <td>" . $row["cls_course"] . "</td></tr>";
echo $row['cls_dept'];
    array_push($EventRows1, $row);
}



$sql = "SELECT erp_class.cls_id, cls_course, cls_dept, cls_sem FROM erp_class INNER JOIN erp_leave_alt ON erp_class.cls_id = erp_leave_alt.cls_id WHERE lv_id = 1";
$result = mysqli_query($conn,$sql);
$Eventrows2 = array();

echo "<table border=1><tr><th>class id</th><th>Dept</th><th>Sem</th><th>Course</th></tr>";

while($row = mysqli_fetch_assoc($result)){
  array_push($EventRows1, $row);

}

$conn->close();

echo "<input id='name' type='text'>";
if( isset($_POST['name']) ){
  echo `<form action="">
  <input type="text" id="name" name="name" placeholder></form>`;
  exit;
}





$sql = "SELECT DISTINCT erp_timetable.tt_subcode, erp_class.cls_id,erp_timetable.tt_period,erp_class.cls_dept, erp_class.cls_sem, erp_class.cls_course FROM `erp_class` INNER JOIN erp_timetable ON erp_class.cls_id = erp_timetable.cls_id WHERE tt_day='Mon' AND tt_period IN (";
        foreach ($periods as $period) {
            $sql .= "$period, ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ") AND erp_timetable.cls_id IN (";
        foreach ($cse_classids as $cse_classid) {
            $sql .= "$cse_classid, ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ") AND erp_timetable.tt_subcode IN (";
        foreach ($todaySubjects as $todaySubject) {
            $sql .= "'$todaySubject', ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ") AND erp_timetable.tt_period = $selectedPeriod";

echo $sql;
?>
<script>
$(document).ready(function(){
  $('#name').keyup(function(){
    var name1 = $('#name').val();

    $.ajax({
       type: 'post',
       data: {name: name1},
       success: function(response){
          $('#response').text('name : ' + response);
       }
    });
  });
});

</script>

