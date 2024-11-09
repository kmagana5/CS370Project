<?php
include_once("header.php");
$import_attempted = false ;
$import_succeeded = false ;
$import_error_message = "" ;

if( $_SERVER[ "REQUEST_METHOD"] == "POST" )
{
    $import_attempted = true ;

    mysqli_report( MYSQLI_REPORT_ERROR ) ;

    $con = @mysqli_connect( "localhost", "root",
        "password", "sys" ) ;

    if( mysqli_connect_errno() )
    {
        $import_error_message = "Failed to connect to MySQL: "
            . mysqli_connect_error() . "<br/>" ;
    }
    else
    {
        $contents = file_get_contents( $_FILES[ "importFile" ][ "tmp_name" ] ) ;
        $lines = explode( "\n", $contents ) ;

        for( $i = 1 ; $i < sizeof( $lines ) ; ++$i )
        {
            $line = $lines[ $i ] ;

            $parsed_csv_line = str_getcsv( $line ) ;

            // TODO: import logic
        }

        $import_succeeded = true ;
    }
}

?>
<html>
<head>
    <title>News Data Import</title>
</head>
<body>

<h1>News Data Import</h1>

<?php
if( $import_attempted )
{
    if( $import_succeeded )
    {
        ?>
        <h1><span style="color: green;">Import Succeeded!</span></h1>
        <span style="font-style: italic">Tell me how many rows were imported.</span>
        <?php
    }
    else
    {
        ?>
        <h1><span style="color: red;">Import Failed!</span></h1>
        <?php echo $import_error_message ; ?>
        <span style="font-style: italic">Tell me how many rows were imported successfully.</span>
        <br/><span style="font-style: italic">And tell me how many rows failed.</span>
        <?php
    }
    echo "<br/><br/>" ;
}
?>

<form method="post" enctype="multipart/form-data">
    File: <input type="file" name="importFile"/>
    <br/>
    <br/>
    <input type="submit" value="Upload Data" />
</form>

</body>
</html>
<?php
include_once('footer.php');
    ?>