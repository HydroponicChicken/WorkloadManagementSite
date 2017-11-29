<?php

//NOTE: THIS CODE NEEDS THE FOLLOWING (AT LEAST)
//   - prepared statements for queries
//   - javascript should be removed to separate file
//    - move php to separate file?


error_reporting(E_ALL);
require_once('proj4connectpath.php');
require_once('utils.php');

session_start();

if(session_id()) {
    echo "session id!";
}
else {
    echo "no session id";
}

//NEED TO check last authentication to see if login needs redone



//make database connection
$connect = $path;
//echo "Connection ", ($connect ? "" : "NOT "), "established.<br />\n";

//prepare and execute select statement
$tasksQueryStmt = $connect->prepare("SELECT title, start, due, hours FROM tasks WHERE email=?");
$email = safeLookup($_SESSION,"email","");
$tasksQueryStmt = $bind_param("s",$email);
$tasksQueryStmt->execute();

$taskArray = array();

//put query results into an array
while($row = mysqli_fetch_assoc($tQuery)) {
    $taskArray[] = $row;
}
//close database connection
mysqli_close($connect);
//var_dump($taskArray);

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="main.css" type="text/css"> 
  <script src="https://d3js.org/d3.v4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dimple/2.3.0/dimple.latest.min.js"> </script>
</head>
<body>
  <nav class="navbar navbar-expand-md bg-secondary navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="index.html">
        <img src="logo.PNG" width="30" height="30" class="d-inline-block align-top p-0" alt=""> </a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.html">Overview</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage.html">Manage Tasks</a>
          </li>
        </ul>
        <a class="btn navbar-btn ml-2 text-white btn-secondary" href="index.php"><i class="fa d-inline fa-lg fa-sign-out"></i> Log Out</a>
      </div>
    </div>
  </nav>
  <div class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="display-1 text-center">Overview</h1>
        </div>
      </div>
    </div>
  </div>
  <div id="graph">
  	<script type="text/javascript">
		var tasks = <?php echo json_encode($taskArray); ?>;
		var coordinates = [];
		//check to see if input is not started yet or not done yet
		function outsideDateRange(date) {
			var today = new Date();
			var result = false;
			if (date <= today)
				result = true;
			
			return result;
		}
		//create point sub-arrays to add to coordinates array
		function pushPoint(task, startDate, dueDate, x, y) {
			var startYet = outsideDateRange(startDate);
			var doneYet = outsideDateRange(dueDate);
			var today = new Date();

			point = {};
			point["title"] = task;
			if(!doneYet) {
				if(startYet && x <= today) {
					point["date"] = today;
					point["hours"] = y;
				}
				else {
					point["date"] = x;
					point["hours"] = y;
				}
				point["due"] = (dueDate.getMonth()+1) + "-" + dueDate.getDate() + "-" + dueDate.getFullYear();
				coordinates.push(point);
			}
		}
		//create array with calculated values; create coordinate array
		for (var i=0; i<tasks.length; i++) {
			//perform date calculations and set variables
			var dueDate = new Date(tasks[i].due);
			var startDate = new Date(tasks[i].start);
			var time = Math.abs(dueDate.getTime() - startDate.getTime());
			var days = Math.ceil(time/(1000*3600*24));
			var timePerDay = Math.round((tasks[i].hours/days + 0.00001)*100)/100;
			var title = tasks[i].title;
			var plus = d3.timeDay.offset(startDate,1);
			//send x,y combinations for range testing and addition to coordinates array
			pushPoint(title, startDate, dueDate, startDate, timePerDay);  //x = startDate
			//add x,y values for intervals from current date startDate until dueDate or 30 days is reached
			while ((plus >= new Date()) && (plus < dueDate) && (plus < d3.timeDay.offset(startDate,30))) {
				pushPoint(title, startDate, dueDate, plus, timePerDay);
				plus = d3.timeDay.offset(plus,1);
			}
			//add final x,y value for dueDate if dueDate is in graph range
			if(dueDate <= d3.timeDay.offset(startDate,30)) {
				pushPoint(title, startDate, dueDate, dueDate, timePerDay);
			
			}
		}

		//console.log(JSON.stringify(coordinates));


	//STACKED BAR CHART	
	var svg = dimple.newSvg("#graph", 690, 500);	
     var myChart = new dimple.chart(svg, coordinates);
      myChart.setBounds(65, 50, 505, 310)
      var x = myChart.addTimeAxis("x", "date");
	  x.tickFormat = "%m-%d-%y";
      var y = myChart.addMeasureAxis("y", "hours");
	  y.tickFormat = ",.1f";
      var s = myChart.addSeries(["due","title"], dimple.plot.bar);
      myChart.addLegend(240, 10, 330, 20, "right");
      myChart.draw();		
		
		
/*
	//ANOTHER AREA PLOT
		var svg = dimple.newSvg("#graph", 590, 400);			

      var myChart = new dimple.chart(svg, coordinates);
      myChart.setBounds(60, 30, 505, 305);
      var x = myChart.addTimeAxis("x", "date");
      x.addOrderRule("Date");
	  x.tickFormat = "%y-%m-%d";
      var y = myChart.addMeasureAxis("y", "hours");
      var s = myChart.addSeries("title", dimple.plot.area);
      myChart.addLegend(60, 10, 500, 20, "right");
      myChart.draw();		

*/		
		
		
/*   AGGREGATE AREA		
  var svg = dimple.newSvg("#graph", 590, 400);		
      var myChart = new dimple.chart(svg, coordinates);
      myChart.setBounds(50, 40, 500, 310)
      var x = myChart.addTimeAxis("x", "date");
	  x.addOrderRule("Date");
	  x.tickFormat = "%y-%m-%d";
      var y = myChart.addMeasureAxis("y", "hours");
      var s = myChart.addSeries(["title", "hours"], dimple.plot.area);
      s.aggregate = dimple.aggregateMethod.avg;
      s.lineMarkers = true;
      myChart.addLegend(30, 10, 500, 35, "right");
      myChart.draw();	
*/	  
/*		
	//AREA PLOT
  var svg = dimple.newSvg("#graph", 590, 400);

      var myChart = new dimple.chart(svg, coordinates);
      myChart.setBounds(60, 30, 505, 305);
      var x = myChart.addCategoryAxis("x", "date");
      x.addOrderRule("Date");
      myChart.addMeasureAxis("y", "hours");
      var s = myChart.addSeries("title", dimple.plot.area);
      s.interpolation = "cardinal";
      myChart.addLegend(60, 10, 500, 20, "right");
      myChart.draw();
*/	  
/*	  
	  LINE PLOT
      var svg = dimple.newSvg("#graph", 590, 400);

	  var myChart = new dimple.chart(svg, coordinates);
      myChart.setBounds(60, 30, 505, 305);
      var x = myChart.addTimeAxis("x", "date");
	  x.tickFormat = "%Y-%m-%d"
      x.addOrderRule("date");
      var y = myChart.addMeasureAxis("y", "hours");
      var s = myChart.addSeries("title", dimple.plot.line);
      s.interpolation = "cardinal";
      myChart.addLegend(60, 10, 500, 20, "top");
      myChart.draw();	  

*/		
		

		
	</script>
  </div>
  <div class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="text-center display-4">Create Tasks</h1>
          <br>
          <form action="main.html" class="text-center"> <b>Task Title: </b>
            <input type="text" name="taskTitle" class="">
            <br>
            <br> <b>Due Date: </b>
            <input type="date" class="" name="dueDate" id="date">
            <br>
            <br> <b>Hours: </b>
            <input name="hours" type="number" max="24" min="1">
            <br>
            <br>
            <input type="submit" value="Submit" class="text-center"> </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>

</html>