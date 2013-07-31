<?php
include_once '../inc/config.php';
include_once '../inc/database.php';
include_once '../inc/functions.php';


$select_query = "SELECT DISTINCT scheme_name FROM tbl_mutual_funds ORDER BY scheme_name ASC";
try{
    $adapter = new Database();
    
    $scheme = $adapter->query($select_query);
    if(!is_array($scheme) || count(scheme) <= 0){
        throw new Exception($scheme);
    }
}catch(Exception $e){

    die($e->getMessage());
}

if(!empty($_POST['mutual_fund'])){
       
    $current_date = date('Y-m-d');
    $prev_date = date('Y-m-d', mktime(0,0,0,date('Y'), date('d')-15, date('m') ));
    $search_query = "SELECT net_asset_value, scheme_date FROM tbl_mutual_funds WHERE MATCH (scheme_name) AGAINST ('".trim($_POST['mutual_fund'])."') AND scheme_date BETWEEN '".$prev_date."' AND '".$current_date."' GROUP BY scheme_date ORDER BY scheme_date DESC";
    $search_result = $adapter->query($search_query);
    echo "<pre>";
    //echo $search_query;
    //print_r($search_result);
    if(is_array($search_result) && count($search_result)){
        
        include_once "../inc/charts/Highchart.php";
        $arr = array();
        foreach($search_result as $key => $val){
            
            $arr[] = array(new HighchartJsExpr("Date.UTC(".date('Y, m-1, d', strtotime($val['scheme_date'])).")"), (int) $val['net_asset_value'] );
            
       }
            
        $chart = new Highchart();
         
        $chart->chart->renderTo = 'container';
        $chart->chart->zoomType = 'x';
        $chart->chart->spacingRight = 20;
        $chart->title->text = $_POST['mutual_fund'];
        $chart->subtitle->text = new HighchartJsExpr("'Gaurav Kumar Vivek Valuoo'");
        $chart->xAxis->type = 'datetime';
        $chart->xAxis->maxZoom = 14 * 24 * 3600000;
        $chart->xAxis->title->text = null;
        $chart->yAxis->title->text = 'Net Asset Value';
        $chart->yAxis->min = 0;
        $chart->yAxis->startOnTick = false;
        $chart->yAxis->showFirstLabel = false;
        $chart->tooltip->shared = true;
        $chart->legend->enabled = false;
        $chart->plotOptions->area->fillColor->linearGradient = array(0, 0, 0, 300);
        $chart->plotOptions->area->fillColor->stops = array(array(0, new HighchartJsExpr("Highcharts.getOptions().colors[0]")),
            array(1, 'rgba(2,0,0,0)'));
        $chart->plotOptions->area->lineWidth = 1;
        $chart->plotOptions->area->marker->enabled = false;
        $chart->plotOptions->area->marker->states->hover->enabled = true;
        $chart->plotOptions->area->marker->states->hover->radius = 5;
        $chart->plotOptions->area->shadow = false;
        $chart->plotOptions->area->states->hover->lineWidth = 1;
        
        $chart->series[]->type = "area";
        $chart->series[]->name = "Asset Value";
        $chart->series[0]->data = $arr;
//         $chart->series[0]->data = array(
//             array(new HighchartJsExpr("Date.UTC(1970,  9, 27)"), 0),
//             array(new HighchartJsExpr("Date.UTC(1970, 10, 10)"), 0.6),
//             array(new HighchartJsExpr("Date.UTC(1970, 10, 18)"), 0.7),
//             array(new HighchartJsExpr("Date.UTC(1970, 11, 2)"), 0.8),
//             array(new HighchartJsExpr("Date.UTC(1970, 11, 9)"), 0.6),
//             array(new HighchartJsExpr("Date.UTC(1970, 11, 16)"), 0.6),
//             array(new HighchartJsExpr("Date.UTC(1970, 11, 28)"), 0.67),
//             array(new HighchartJsExpr("Date.UTC(1971, 0, 1)"), 0.81),
//             array(new HighchartJsExpr("Date.UTC(1971, 0, 8)"), 0.78),
//             array(new HighchartJsExpr("Date.UTC(1971, 0, 12)"), 0.98),
//             array(new HighchartJsExpr("Date.UTC(1971, 0, 27)"), 1.84),
//             array(new HighchartJsExpr("Date.UTC(1971, 1, 10)"), 1.80),
//             array(new HighchartJsExpr("Date.UTC(1971, 1, 18)"), 1.80),
//             array(new HighchartJsExpr("Date.UTC(1971, 1, 24)"), 1.92),
//             array(new HighchartJsExpr("Date.UTC(1971, 2, 4)"), 2.49),
//             array(new HighchartJsExpr("Date.UTC(1971, 2, 11)"), 2.79),
//             array(new HighchartJsExpr("Date.UTC(1971, 2, 15)"), 2.73),
//             array(new HighchartJsExpr("Date.UTC(1971, 2, 25)"), 2.61),
//             array(new HighchartJsExpr("Date.UTC(1971, 3, 2)"), 2.76),
//             array(new HighchartJsExpr("Date.UTC(1971, 3, 6)"), 2.82),
//             array(new HighchartJsExpr("Date.UTC(1971, 3, 13)"), 2.8),
//             array(new HighchartJsExpr("Date.UTC(1971, 4, 3)"), 2.1),
//             array(new HighchartJsExpr("Date.UTC(1971, 4, 26)"), 1.1),
//             array(new HighchartJsExpr("Date.UTC(1971, 5, 9)"), 0.25),
//             array(new HighchartJsExpr("Date.UTC(1971, 5, 12)"), 0));
        }
    }
    echo "</pre>";
if(is_array($scheme) && count($scheme)):
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<?php
if(!empty($_POST['mutual_fund'])){
      foreach ($chart->getScripts() as $script) {
         echo '<script type="text/javascript" src="' . $script . '"></script>';
      }
}
?>
<title>Test: Valuoo Technology</title>
</head>
<body>


<form name="search_form" id="search_form" action="" method="post">
<span>Select a scheme</span>
<select name="mutual_fund" id="mutual_fund" onchange="document.getElementById('search_form').submit()" autofocus>
<?php 
    foreach ($scheme as $val):
        if(!empty($val['scheme_name'])):
?><option value="<?php echo $val['scheme_name']; ?>" ><?php echo trim($val['scheme_name']); ?></option><?php 
        endif;
    endforeach;
?>
</select>
</form>
<div id="container">
<?php if(!empty($_POST['mutual_fund'])){ ?>
<script type="text/javascript">
    <?php
      echo $chart->render("chart1");
    ?>
    </script>
    <?php } ?>
    </div>
<?php endif;?>
</body>
</html>

