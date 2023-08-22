<!DOCTYPE html>
<html>
<head>
	<title>Notices Table</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<!-- Bootstrap JS -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmY" crossorigin="anonymous"></script>
    <style>
        h2 {
  font-size: 24px; /* Change the font size */
  font-weight: bold; /* Make the text bold */
  color: #333; /* Change the text color */
  text-align: center; /* Align the text to the center */
  text-transform: uppercase; /* Change the text to uppercase */
  letter-spacing: 2px; /* Add space between letters */
  margin-bottom: 20px; /* Add margin to the bottom of the text */
}

</style>
</head>
<body>
    



<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">My Website</a>
  <button class="navbar-toggler hamburger hamburger--collapse" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="hamburger-box">
      <span class="hamburger-inner"></span>
    </span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">About</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Contact</a>
      </li>
    </ul>
  </div>
</nav>
<?php 
	include('includes/db_connection.php');
    $sql = "SELECT * FROM erp_leave_alt ORDER BY la_id DESC LIMIT 1;";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result)>0){

        $row = mysqli_fetch_assoc($result);
      

    }
    ?>
<div class="container mt-5">
<?php
	// SQL query
	$sql = "SELECT * FROM `erp_leave_alt` WHERE f_id = 'f006'";
	$result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    echo $row['lv_id'];
    // FOUND THE LEAVE ID OF THE PERSON TAKING LEAVE
    $lv_id = $row['lv_id'];
    $sql1 = "SELECT * FROM `erp_leave` WHERE lv_id = $lv_id";
    $result = mysqli_query($conn, $sql1);
    $row1 = mysqli_fetch_assoc($result);
    echo $row1['f_id'];
    // FOUND THE F_ID OF THE PERSON TAKING LEAVE
    $f_id = $row1['f_id'];
    $sql2 = "SELECT * FROM `erp_faculty` WHERE f_id = '$f_id'";
    $result = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result);

    echo $row2['f_fname'] . $row2['f_lname'];
    
    //echo "<option value=" . $Event['f_id'] . ">" . $Event['f_fname'] . " " . $Event['f_lname'] . "</option>";
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h2>Notification </h2>
			<table class="table table-bordered">
				<thead>
        <tr>
						<th>Alteration requester</th>
            <!-- <th>Date</th> -->
                        <th>Accept/Reject</th>
					</tr>
				</thead>
				<tbody>
					<?php

if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = (int)$_GET['page'];
} else {
    $currentPage = 1;
}
$sql = "SELECT * FROM `erp_leave_alt` WHERE f_id = 'f006'";
	$result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
        // Preparation for table

    echo $row['lv_id'];
    // FOUND THE LEAVE ID OF THE PERSON TAKING LEAVE
    $lv_id = $row['lv_id'];
    $sql1 = "SELECT * FROM `erp_leave` WHERE lv_id = $lv_id";
    $result = mysqli_query($conn, $sql1);
    $row1 = mysqli_fetch_assoc($result);
    echo $row1['f_id'];
    // FOUND THE F_ID OF THE PERSON TAKING LEAVE
    $f_id = $row1['f_id'];
    $sql2 = "SELECT * FROM `erp_faculty` WHERE f_id = '$f_id'";
    $result = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result);

    echo $row2['f_fname'] . $row2['f_lname'];






      echo "<tr id=\"" . $row['news_id'] . "\">";
        echo "<td>" . $row2['f_fname'] . " " . $row2['f_lname'] . "</td>";
        echo "<td>" . $row["news_desc"] . "</td>";
        // echo "<td>" . $row["date"] . "</td>";
        echo "<td>" . $row["news_pby"] . "</td>";
    }
    ?>
    </tbody>
			</table>
            <a href="notices_manage.php" class="btn btn-primary">Manage</a> <br>
            <br>
            <a href="notices_insert.php" class="btn btn-primary">Insert</a>

		</div>
        
	</div>
  </div>
    
</body>
<script type="text/javascript">
    $(".remove").click(function(){
        var id = $(this).parents("tr").attr("id");


        if(confirm('Are you sure to remove this record ?'))
        {
            $.ajax({
               url: '/delete_for_notices.php',
               type: 'GET',
               data: {id: id},
               error: function() {
                  alert('Something is wrong');
               },
               success: function(data) {
                    $("#"+id).remove();
                    alert("The record has shruken into oblivion");   
               }
            });
            window.location.href = "delete_for_notices.php?id=" + id + "&db=notices";
        }
    });


</script>
</html>
