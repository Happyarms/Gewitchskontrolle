<?php
$link = mysqli_connect("DB_SERVER", "DB_USER", "DB_PASSWD", "DB_NAME");

$aktuellesgewicht = '';
$kErstesGewichtsDatum = '';
$kGesamtTageOhneGewicht = '';

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}




$query = "SELECT gewicht, datum FROM gewichtskontrolle ORDER BY datum desc LIMIT 1";

if ($result = mysqli_query($link, $query)) {

    /* fetch associative array */
    while ($row = mysqli_fetch_assoc($result)) {
        $aktuellesgewicht = $row["gewicht"];
        $kLetztesDatum = $row["datum"];
    }
    
  $max = $aktuellesgewicht + 2;
  $min = $aktuellesgewicht - 2;

    /* free result set */
    mysqli_free_result($result);
}

$query = "SELECT datum FROM gewichtskontrolle ORDER BY datum asc LIMIT 1";

if ($result = mysqli_query($link, $query)) {

    /* fetch associative array */
    while ($row = mysqli_fetch_assoc($result)) {
      $kErstesGewichtsDatum = $row["datum"];
    }

    /* free result set */
    mysqli_free_result($result);
}

$query = "SELECT COUNT(datum) FROM gewichtskontrolle";

if ($result = mysqli_query($link, $query)) {

    /* fetch associative array */
    while ($row = mysqli_fetch_assoc($result)) {
      $kAnzahlTage = $row["COUNT(datum)"];
    }

    /* free result set */
    mysqli_free_result($result);
}


$date1 = new DateTime("$kErstesGewichtsDatum");
$date2 = new DateTime("$kLetztesDatum");
$diff = $date2->diff($date1)->format("%a");
$kTageOhneGewicht = $diff - $kAnzahlTage;

$kTageOhneGewicht = $kTageOhneGewicht - 1;
$kTageOhneGewicht = $kTageOhneGewicht * -1;
$kTageOhneGewicht = $kTageOhneGewicht * 10;


/* Debug Ausgabe für die Tagesberechnung, wenn kein Gewicht eingetragen wurde
echo "-------------------------<br>";
echo "$kErstesGewichtsDatum - $kLetztesDatum ----<br>$_POST[gewicht];";
echo "-------------------------<br><br><br><br><br>";
*/

//BMI Berechnung

$BMI = number_format((float)$aktuellesgewicht,2,",","") / 1.9;


$gewicht = $_POST[gewicht];
/* Select queries return a resultset */
if($gewicht){$datum = date("Y-m-d h:i:s");
  if ($result = mysqli_query($link, "INSERT INTO gewichtskontrolle (id, datum, gewicht)  
  VALUES ('', '$datum', '$gewicht')")) {
      /* free result set */
      mysqli_free_result($result);
	  /* Mail mit den Werten auch an XY */
    // Die Nachricht
    $nachricht = "Gewicht: $gewicht kg";

    // Falls eine Zeile der Nachricht mehr als 70 Zeichen enthälten könnte,
    // sollte wordwrap() benutzt werden
    $nachricht = wordwrap($nachricht, 70);

    // Send
    mail('MAILADRESSE', 'Gewichtsaktualisierung Happyarms', $nachricht);
  }
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gewichtskontrolle</title>
    <link rel="stylesheet" href="css/normalize.css">

    <link rel='stylesheet prefetch' href='http://andreruffert.github.io/rangeslider.js/assets/rangeslider.js/dist/rangeslider.css'>

        <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap Styles-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FontAwesome Styles-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- Morris Chart Styles-->
    <link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
    <!-- Custom Styles-->
    <link href="assets/css/custom-styles.css" rel="stylesheet" />
    <!-- Google Fonts-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>

<body>

    <!--
  rangeslider.js example
  
  https://github.com/andreruffert/rangeslider.js
  by André Ruffert - @andreruffert
-->

  
    <div id="wrapper">
        <!--/. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li>
                        <a class="active-menu" href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                </ul>

            </div>

        </nav>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper">
            <div id="page-inner">


                <div class="row">
                    <div class="col-md-12">
                      <h1 class="page-header">
                          Dashboard <br /><small>Statistikerhebung und auswertung von Körperdaten zu Shano</small>
                      </h1>
                      <!--<ol class="breadcrumb">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Library</a></li>
                        <li class="active">Data</li>
                      </ol>-->
                    </div>
                </div>
				
				
                <!-- /. ROW  -->
<br /><br />

                <div class="row">
                  <div class="col-xs-12">
                    <form action="index.php" method="post" style="text-align:center">
                      <input name="gewicht" type="range"
                      value="<?php echo $aktuellesgewicht; ?>"
                      step="0.1"
                      max="<?php echo $max; ?>"
                      min="<?php echo $min; ?>"
                      >
                      <br />
                      <button class="btn btn-default">Gewicht eintragen</button>
                    </form>
                  </div>
                </div><br />
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="panel panel-primary text-center no-boder bg-color-green green">
                            <div class="panel-left pull-left green">
                                <i class="fa fa-bar-chart-o fa-5x"></i>
                                
                            </div>
                            <div class="panel-right pull-right">
                              <h3>Datum</h3>
                              <strong>Letzter Eintrag am: <?php echo $kLetztesDatum; ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="panel panel-primary text-center no-boder bg-color-red red">
                              <div class="panel-left pull-left red">
                                <i class="fa fa-shopping-cart fa-5x"></i>
								</div>
                                
                            <div class="panel-right pull-right">
                              <h3>Punkte</h3>
                               <strong>Tage ohne Gewicht: <?php echo "$kTageOhneGewicht";?> </strong>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="panel panel-primary text-center no-boder bg-color-blue blue">
                            <div class="panel-left pull-left blue">
                                <i class="fa fa fa-comments fa-5x"></i>
                               
                            </div>
                            <div class="panel-right pull-right">
                                <h3>Gewicht</h3>
                               <strong>Letztes Gewicht: <?php echo number_format((float)$aktuellesgewicht,2,",",""); ?>kg</strong>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="panel panel-primary text-center no-boder bg-color-brown brown">
                            <div class="panel-left pull-left brown">
                                <i class="fa fa-users fa-5x"></i>
                                
                            </div>
                            <div class="panel-right pull-right">
                            <h3>BMI </h3>
                             <strong><?php echo $BMI; ?></strong>

                            </div>
                        </div>
                    </div>
                </div>
				
		
		<div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Eigenschaften
                    </div>
                    <div class="panel-body">
                        <div id="morris-donut-chart"></div>
                    </div>
                </div>
            </div>
		</div><!--/.row-->
			
				<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							Gewichts Statistik
						</div>
						<div class="panel-body">
							<div id="morris-line-chart"></div>
						</div>
					</div>  
					</div>		
				</div> 
				
				
                <!-- /. ROW  -->

            </div>
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
    <!-- /. WRAPPER  -->
    <!-- JS Scripts-->
    <!-- jQuery Js -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- Bootstrap Js -->
    <script src="assets/js/bootstrap.min.js"></script>
	 
    <!-- Metis Menu Js -->
    <script src="assets/js/jquery.metisMenu.js"></script>
    <!-- Morris Chart Js -->
    <script src="assets/js/morris/raphael-2.1.0.min.js"></script>
    <script src="assets/js/morris/morris.js"></script>
	
	
	<script src="assets/js/easypiechart.js"></script>
	<script src="assets/js/easypiechart-data.js"></script>
	
	
    <!-- Custom Js -->
    <script src="assets/js/custom-scripts.js"></script>
    <script src='http://andreruffert.github.io/rangeslider.js/assets/rangeslider.js/dist/rangeslider.min.js'></script>
        
    <script src="js/index.js"></script>
    
    
    <script>
      $(document).ready(function () {
         <?php /* MORRIS LINE CHART ----------------------------------------*/ ?>
        
        Morris.Line({
          element: 'morris-line-chart',
          data: [
        
        <?php
          $query = "SELECT gewicht, datum FROM gewichtskontrolle";

          if ($result = mysqli_query($link, $query)) {

              /* fetch associative array */
              while ($row = mysqli_fetch_assoc($result)) {
                  echo '{ y: \''.$row["datum"].'\', a: '.$row["gewicht"].'},';
              }
              /* free result set */
              mysqli_free_result($result);
          }

          /* close connection */
          mysqli_close($link);
          ?>
          ],
            
          ymax: <?php echo $max; ?>,
          ymin: <?php echo $min; ?>,
          parseTime:false,
          xkey: 'y',
          ykeys: ['a'],
          labels: ['Gewicht', 'Datum'],
          fillOpacity: 0.6,
          hideHover: 'auto',
          behaveLikeLine: true,
          resize: true,
          pointFillColors:['#ffffff'],
          pointStrokeColors: ['black'],
          lineColors:['gray','#2DAFCB']

        });
      });
    </script>
    

</body>

</html>
