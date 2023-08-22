<?php
include('Includes/db_connection.php');
// Getting subjects of faculty
$subjects = [];
$cse_classids = [];
$sql = "SELECT f_id, tt_subcode FROM erp_subject WHERE f_id = 'f002'";
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
  // Output the table header
  echo "<table border=1><tr><th>Day</th><th>Subject</th><th>Period</th></tr>";
  // Output the table rows
  $i=0;
  while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row["tt_day"] . "</td><td>" . $row["tt_subcode"] . "</td><td>" . $row["tt_period"] . "</td></tr>";
    echo 'hello';
    $periods[$i] = $row['tt_period'];
    $i++;
    
  }
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

$query = "SELECT * FROM erp_subject INNER JOIN erp_timetable ON erp_subject.tt_subcode = erp_timetable.tt_subcode INNER JOIN erp_faculty ON erp_subject.f_id=erp_faculty.f_id WHERE tt_day='Mon' AND tt_period NOT IN ($period) AND erp_subject.f_id NOT IN ('f002') AND erp_subject.cls_id IN (";
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
$conn->close();
?>