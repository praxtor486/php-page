<?php
    $db_name = "acads";
    $un = "root";
    $pw = "";
    $host = "localhost";
    //open mysql connection
    $mysqli = new mysqli($host, $un, $pw, $db_name) or die($mysqli->connect_error);
    
// function to decide table row color
    function rowBackground($score) {
        if ($score < 50) {
            return "below50";
        } elseif ($score > 80){
            return "above80";
        } else {
            return "regular";
        }      
        
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd"
    >
<html lang="en">
<head>
    <title>Student Test Scores</title>
    
    <style type="text/css">
        table
        {
            border: 2px solid black;
            border-collapse: collapse;
            width: 50%;;
        }
        th, td
        {
           border: 1px solid black;
           font-family: verdana;
           text-align:left;
           padding: 2px;
        }
        th
        {
            background-color: blue;
            font-size: .8em;
            color: white;
        }
        td 
        {
            font-size: .7em;
        }
        .regular
        {
            background-color: rgb(255,255,255);   
            color: rgb(0,0,150)
        }
        .below50
        {
            background-color: rgb(255,0,0);
            color: rgb(255,255,255)
        }
        .above80
        {
            background-color: rgb(0,255,0);
            color: rgb(0,0,150)
        }
        caption
        {
            font-family: arial;
            font-size: 1.3em;
            caption-side: top;
        }
    </style>
 </head>
<body>
    
    <?php
        //Get list of unique students from database
                    
        $sql = "SELECT DISTINCT (StudentName) FROM studentgrades WHERE 1";
        
        if ($studentarr = $mysqli->query($sql, MYSQLI_STORE_RESULT)) 
        {
            while($studentname = $studentarr->fetch_array(MYSQLI_ASSOC)){
                
                echo('<table id="'.$studentname['StudentName'].'">');
                echo("<tr>");
                $tblheading = "Student Name: ".$studentname['StudentName'];
                echo("<th>");
                echo($tblheading);
                echo("</th>");           
                echo("</tr>");
                
                //create data columns with header sort functionality
                echo("<tr>");
                echo('<th onclick="tableSort(this,0)" style="cursor:pointer"> Class Name </th>');
                echo('<th onclick="tableSort(this,1)" style="cursor:pointer"> Test Score </th>');
                echo('<th onclick="tableSort(this,2)" style="cursor:pointer"> Test Date </th>');
                echo("</tr>");
                
                $sql = "SELECT ClassName,TestScore, TestDate FROM studentgrades WHERE StudentName = '";
                $sql .= $studentname['StudentName'];
                $sql .= "' ORDER BY ID,TestDate";
                
                 if ($result = $mysqli->query($sql, MYSQLI_STORE_RESULT)) 
                {
                     $iRecord = 0;
                     $testScorearr = array();
                     while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        $iRecord++;
                        $testScorearr[] = $row['TestScore'];
                        $stl = rowBackground($row['TestScore']);
                        
                        echo("<tr class=" . $stl .">");

                        echo "<td>" . $row['ClassName'] . "</td>"
                            ."<td>" . $row['TestScore'] . "</td>"
                            ."<td>" . $row['TestDate'] . "</td>";
                        echo "</tr>";                       
                        
                    }
                    
                   $stl = rowBackground(51.0);
                    
                    $avgScore = array_sum($testScorearr)/$iRecord;
                    
                    echo("<tr class=" . $stl .">");
                    echo ("<td> Average Test Score: ".round($avgScore,2)."</td>");
                    echo("</tr>");
                    $result->close();
                    
                } else 
                    {
                        die($mysqli->error);
                    }                
            }
            
          echo("</table>");
          $studentarr->close();
        } else
        {
            die($mysqli->error);
        }
         
?>
<script type="text/javascript">
    function tableSort(element,colID) {
        var tableRef, tableRow, continueLoop, i, firstRow, secondRow, rowSwitch, sortType, swcount = 0;
        tblID = element.parentNode.parentNode.parentNode.id;
        tableRef = document.getElementById(tblID);
        continueLoop = true;
        sortType = "ASC";

        while (continueLoop) {

          continueLoop = false;
          tableRow = tableRef.getElementsByTagName("TR");

          for (i = 2; i < (tableRow.length - 3); i++) {

            rowSwitch = false;

            firstRow = tableRow[i].getElementsByTagName("TD")[colID];
            secondRow = tableRow[i + 1].getElementsByTagName("TD")[colID];

            if (sortType === "ASC") {
              if (firstRow.innerHTML.toLowerCase() > secondRow.innerHTML.toLowerCase()) {
                rowSwitch= true;
                break;
              }
            } else if (sortType === "DESC") {
              if (firstRow.innerHTML.toLowerCase() < secondRow.innerHTML.toLowerCase()) {
                rowSwitch= true;
                break;
              }
            }
          }
          if (rowSwitch) {
            tableRow[i].parentNode.insertBefore(tableRow[i + 1], tableRow[i]);
            continueLoop = true;

            swcount ++;
          } else {

            if (swcount === 0 && sortType === "ASC") {
              sortType = "DESC";
              continueLoop = true;
            }
          }
        }
    }
        </script>
</body>
</html>

