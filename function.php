<?php
require_once "library/PHPExcel.php";
$connection = new mysqli("localhost", "root", "", "brainforce");
if (mysqli_connect_errno()) {
    echo "Не удалось подключиться: %s\n", mysqli_connect_error();
    exit();
}

if ($_POST["method"]=="view") {
    view();
}
if ($_POST["action"]=="add"){
    AddDataInDB();
}
if ($_POST["method"]== "getResult"){
    GetResultQuery();
}


function getDataXlS()
{
    $excelData = PHPExcel_IOFactory::load('pricelist.xls');
    $excelData->getActiveSheet()->removeRow(1);
    foreach ($excelData->getWorksheetIterator() as $sheetExl) {
        $rows = $sheetExl->getHighestRow();
        $columns = $sheetExl->getHighestColumn();
        $columnIndex = PHPExcel_Cell::columnIndexFromString($columns);
        //$nrcolumn = ord($columns) - 64;

        // $all_row = $rows;
        //  $all_colls = $columns;
        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 0; $col < $columnIndex; $col++) {
                $value = $sheetExl->getCellByColumnAndRow($col, $row)->getValue();
                $data[$col][$row] = $value;
            }
        }
    }
    return $data;
}

function view(){
    $output='';
    $results = Output();
    $max = MaxPrice();
    $min = MinPrice();
    $output .= '
    <table >
      <tr>
      <th>Наименование</th>
      <th>Стоимость</th>
      <th>Стоимость оптом</th>
      <th>Количество на 1м складе</th>
      <th>Количество на 2м складе</th>
      <th>Страна производства</th>
      <th>Примичание</th>
      </tr>';

    foreach ($results as $result){
        if($result["cost"] == $max["cost"]){
            $output .='<tr class="maxPrice">';
        }
        elseif ($result["cost_opt"]==$min["cost_opt"]){
            $output .='<tr class="minPrice">';
        }
        else{
            $output .='<tr>';
        }
        $output .= '
              <td>'.$result["name"].'</td>
              <td>'.$result["cost"].'</td>
              <td>'.$result["cost_opt"].'</td>
              <td>'.$result["сount_for_fitst_skld"].'</td>
              <td>'.$result["сount_for_second_skld"].'</td>
              <td>'.$result["сountry"].'</td>
        ';
        if ($result["сount_for_fitst_skld"]<=20 || $result["сount_for_second_skld"]<=20){
            $output .= '<td>Осталось мало!! Срочно докупите!!!</td>';
        }
        else{
            $output .= '<td></td>';
        }
        $output .= '</tr>';
    }
    $output .= '</table>';
    echo $output;
}

function filterData($data, $field, $n, $mas = NULL){
    $i = 1;
    foreach ($data[$n] as $item){
        $i++;
        $mas[$i][$field] = $item;
    }
    return $mas;
}
function GetDataForXLS(){
    $data = getDataXlS();
    $datares = filterData($data, "name", 0);
    $datares = filterData($data, "Cost", 1, $datares);
    $datares = filterData($data, "Costopt", 2, $datares);
    $datares = filterData($data, "countFirst", 3, $datares);
    $datares = filterData($data, "countSecond", 4, $datares);
    $datares = filterData($data, "country", 5, $datares);
    return $datares;
}


//
//if ($typePrice=="cost"){
//    $query .= " WHERE cost>=$minPrice and cost<=$maxPrice";
//}
//else{
//    $query .= " WHERE cost_opt>=$minPrice and cost_opt<=$maxPrice";
//}
//if ($more_less=="less"){
//    $query .=" and (сount_for_fitst_skld+сount_for_second_skld)<$counts";
//}
//else{
//    $query .=" and (сount_for_fitst_skld+сount_for_second_skld)>$counts";
//}
function GetResultQuery(){
    global $connection;
    $typePrice = $_POST["typePrice"];
    $minPrice = $_POST["minPrice"];
    $maxPrice = $_POST["maxPrice"];
    $more_less = $_POST["more_less"];
    $counts = $_POST["counts"];
    $query = "SELECT name, cost, cost_opt, сount_for_fitst_skld, сount_for_second_skld, сountry FROM pricelist ";
    if ($minPrice!="" && $maxPrice!="" && $counts!="")
        if ($typePrice=="cost"){
            $query .= "WHERE cost >= ? and cost <= ? ";
        }
        else{
            $query .= " WHERE cost_opt >= ? and cost_opt <= ? ";
        }
        if ($more_less=="less"){
            $query .="and (сount_for_fitst_skld+сount_for_second_skld) < ? ";
        }
        else{
            $query .="and (сount_for_fitst_skld+сount_for_second_skld) > ? ";
        }
//        if($results=mysqli_query($connection, $query))
//            $results = mysqli_fetch_all($results, MYSQLI_ASSOC);
    if ( $results = $connection->prepare($query))
        $results->bind_param('sss', $minPrice, $maxPrice, $counts );
        $results->execute();
        $results->bind_result($name, $cost, $cost_opt,$сount_for_fitst_skld, $сount_for_second_skld, $сountry);
        //$results = $results->get_result();
//        $row = $results->fetch_all();

            $output = '';
            $output .= '
      <table >
      <tr>
      <th>Наименование</th>
      <th>Стоимость</th>
      <th>Стоимость оптом</th>
      <th>Количество на 1м складе</th>
      <th>Количество на 2м складе</th>
      <th>Страна производства</th>
      <th>Примичание</th>
      </tr>';
    while ($results->fetch()) {
        $output .= '<tr>';
                    $output .= '
                    <td>'.$name.'</td>
                    <td>'.$cost.'</td>
                    <td>'.$cost_opt.'</td>
                    <td>'.$сount_for_fitst_skld.'</td>
                    <td>'.$сount_for_second_skld.'</td>
                    <td>'.$сountry.'</td>';
                    if ($сount_for_fitst_skld <= 20 || $сount_for_second_skld <= 20) {
                        $output .= '<td>Осталось мало!! Срочно докупите!!!</td>';
                    } else {
                        $output .= '<td></td>';
                    }
                    $output .= '</tr>';
    }
//            foreach ($row as $res) {
//                //foreach ($res as $result) {
//                    $output .= '<tr>';
//                    $output .= '
//                    <td>' . $res["name"] . '</td>
//                    <td>' . $res["cost"] . '</td>
//                    <td>' . $res["cost_opt"] . '</td>
//                    <td>' . $res["сount_for_fitst_skld"] . '</td>
//                    <td>' . $res["сount_for_second_skld"] . '</td>
//                    <td>' . $res["сountry"] . '</td>';
//                    if ($res["сount_for_fitst_skld"] <= 20 || $res["сount_for_second_skld"] <= 20) {
//                        $output .= '<td>Осталось мало!! Срочно докупите!!!</td>';
//                    } else {
//                        $output .= '<td></td>';
//                    }
//                    $output .= '</tr>';
//            }
           // }
            $output .= '</table>';

        echo $output;
}

function generate_exception($string){
    exit(json_encode(array('result' => 'false', 'string'=> $string)));
}

function AddDataInDB(){
    global $connection;
    $datares = GetDataForXLS();
    $result = mysqli_query($connection, "select * from pricelist");
    if (mysqli_num_rows($result) == 0){
        foreach ($datares as $item) {
            $query = "INSERT INTO pricelist value ('{$item["name"]}', '{$item["Cost"]}', '{$item["Costopt"]}',
                     '{$item["countFirst"]}', '{$item["countSecond"]}', '{$item["country"]}')";
            mysqli_query($connection, $query) or die(generate_exception("Error of add in db"));
        }
        exit(json_encode(array('result' => 'true')));
    }
    else{
        generate_exception("Error record in db exists");
    }

}



function Count_two_skld(){
    global $connection;
    $query = "Select sum(сount_for_fitst_skld) as countFirstSkld, sum(сount_for_second_skld) as countSecondSkld from pricelist";
    $results = mysqli_query($connection, $query);
    return mysqli_fetch_all($results, MYSQLI_ASSOC);
}
function AvgPrice(){
    global $connection;
    $query = "Select avg(cost) as Average from pricelist";
    $results = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($results);
}
function AvgOptPrice(){
    global $connection;
    $query = "Select avg(cost_opt) Average from pricelist";
    $results = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($results);
}
function MaxPrice(){
    global $connection;
    $query = "select cost from pricelist where cost=(select max(cost) from pricelist)";
    $results = mysqli_query($connection,$query);
    return mysqli_fetch_assoc($results);
}
function MinPrice(){
    global $connection;
    $query = "select cost_opt from pricelist where cost_opt=(select min(cost_opt) from pricelist)";
    $results = mysqli_query($connection,$query);
    return mysqli_fetch_assoc($results);
}
function Output(){
    global $connection;
    $query = "SELECT name, cost, cost_opt, сount_for_fitst_skld, сount_for_second_skld, сountry FROM pricelist";
    $results = mysqli_query($connection, $query);
    return mysqli_fetch_all($results, MYSQLI_ASSOC);
}


