
<?php

if (isset($_POST["Submit"])) {

    // $mysqlUserName      = "root";
    // $mysqlPassword      = "";
    // $mysqlHostName      = "localhost";
    // $DbName             = "cmssite";
    // $backup_name        = "mybackup.sql";
    // $tables             = "admin";
    $mysqlUserName      = $_POST["username"];
    $mysqlPassword      = $_POST["mysqlPassword"];
    $mysqlHostName      = $_POST["mysqlHostName"];
    $DbName             = $_POST["DbName"];
    $backup_name        = $_POST["backup_name"];
    $tables             = $_POST["tables"];

    function Export_Database($host,$user,$pass,$name,  $tables=false, $backup_name=false )
    {
        $mysqli = new mysqli($host,$user,$pass,$name); 
        $mysqli->select_db($name); 
        $mysqli->query("SET NAMES 'utf8'");

        $queryTables    = $mysqli->query('SHOW TABLES'); 
        while($row = $queryTables->fetch_row()) 
        { 
            $target_tables[] = $row[0]; 
        }   
        if($tables !== false) 
        { 
            $target_tables = array_intersect( $target_tables, $tables); 
        }
        foreach($target_tables as $table)
        {
            $result         =   $mysqli->query('SELECT * FROM '.$table);  
            $fields_amount  =   $result->field_count;  
            $rows_num=$mysqli->affected_rows;     
            $res            =   $mysqli->query('SHOW CREATE TABLE '.$table); 
            $TableMLine     =   $res->fetch_row();
            $content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) 
            {
                while($row = $result->fetch_row())  
                { //when started (and every after 100 command cycle):
                    if ($st_counter%100 == 0 || $st_counter == 0 )  
                    {
                            $content .= "\nINSERT INTO ".$table." VALUES";
                    }
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++)  
                    { 
                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); 
                        if (isset($row[$j]))
                        {
                            $content .= '"'.$row[$j].'"' ; 
                        }
                        else 
                        {   
                            $content .= '""';
                        }     
                        if ($j<($fields_amount-1))
                        {
                                $content.= ',';
                        }      
                    }
                    $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) 
                    {   
                        $content .= ";";
                    } 
                    else 
                    {
                        $content .= ",";
                    } 
                    $st_counter=$st_counter+1;
                }
            } $content .="\n\n\n";
        }
        //$backup_name = $backup_name ? $backup_name : $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
        $backup_name = $backup_name ? $backup_name : $name.".sql";
        header('Content-Type: application/octet-stream');   
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$backup_name."\"");  
        echo $content; exit;
    }
    Export_Database($mysqlHostName,$mysqlUserName,$mysqlPassword,$DbName,  $tables=false, $backup_name=false );
   //or add 5th parameter(array) of specific tables:    array("mytable1","mytable2","mytable3") for multiple tables

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
<link rel="stylesheet" href="Css/Styles.css">
<link rel="stylesheet" href="Css/NavSelect.css">
<title>Database Extractor</title>
</head>
<body>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-1">
<div  style="display:block; text-align:center;" class="container">
<a href="index.php?page=1" class="navbar-brand" >Database Extractor</a>
<button class="navbar-toggler" data-toggle="collapse" data-target="#navbarcollapseCMS">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarcollapseCMS">
</div>
</div>
</nav>
<!-- NAVBAR END -->
<!-- HEADER -->
<header class="bg-dark text-white py-3">
<div class="container">
<div class="row">
<div class="col-md-12">
</div>
</div>
</div>
</header>
<!-- HEADER END -->
<!-- Main Area Start -->
<section class="container py-2 mb-4">
<div class="row">
<div class="offset-sm-3 col-sm-6" style="min-height:500px;">

<div class="card bg-dark text-light">
<div class="card-header">
<h4 class="text-center">Enter Details</h4>
</div>
<div class="card-body bg-dark">
<form class="" action="index.php" method="post">
<div class="form-group">
<label for="username"><span class="FieldInfo">Username:</span></label>
<div class="input-group mb-3">
<div class="input-group-prepend">
<span class="input-group-text text-white bg-info"> <i class="fas fa-user"></i> </span>
</div>
<input type="text" class="form-control" name="username" id="username" value="">
</div>
</div>
<div class="form-group">
<label for="password"><span class="FieldInfo">Password:</span></label>
<div class="input-group mb-3">
<div class="input-group-prepend">
<span class="input-group-text text-white bg-info"> <i class="fas fa-lock"></i> </span>
</div>
<input type="password" class="form-control" name="mysqlPassword" id="mysqlPassword" value="">
</div>
</div>


<div class="form-group">
<label for="mysqlHostName"><span class="FieldInfo">mysqlHostName:</span></label>
<div class="input-group mb-3">
<div class="input-group-prepend">
<span class="input-group-text text-white bg-info"> <i class="fas fa-user"></i> </span>
</div>
<input type="text" class="form-control" name="mysqlHostName" id="mysqlHostName" value="">
</div>
</div><div class="form-group">
<label for="DbName"><span class="FieldInfo">DbName:</span></label>
<div class="input-group mb-3">
<div class="input-group-prepend">
<span class="input-group-text text-white bg-info"> <i class="fas fa-user"></i> </span>
</div>
<input type="text" class="form-control" name="DbName" id="DbName" value="">
</div>
</div><div class="form-group">
<label for="backup_name"><span class="FieldInfo">backup_name:</span></label>
<div class="input-group mb-3">
<div class="input-group-prepend">
<span class="input-group-text text-white bg-info"> <i class="fas fa-user"></i> </span>
</div>
<input type="text" class="form-control" name="backup_name" id="backup_name" value="">
</div>
</div>
<div class="form-group">
<label for="tables"><span class="FieldInfo">tables:</span></label>
<div class="input-group mb-3">
<div class="input-group-prepend">
<span class="input-group-text text-white bg-info"> <i class="fas fa-user"></i> </span>
</div>
<input type="text" class="form-control" name="tables" id="tables" value="">
</div>



<input type="submit" name="Submit" class="btn btn-info btn-block text-info bg-dark" value="Extract">
</form>

</div>

</div>

</div>

</div>

</section>
<!-- Main Area End -->

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

</body>
</html>
