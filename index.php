<!DOCTYPE html>
<html lang="en">
<head>
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<?

require_once 'library/PHPExcel.php';
require_once 'function.php';
?>
<!--<button type="button" onclick="AddDataInDB()">Загрузить</button>-->
<input  type="button" value="Загрузить" onclick="adddb()">
<input id="load_btn"  type="button" value="Сбросить"  >
<div>
<h2 id ='error_message'></h2>
<h2 id ='success_message'></h2>
</div>
<div>
    <text>
        Показать товары, у которых
    </text>
    <select name="" id="typePrice" >
        <option value="cost">Розничная</option>
        <option value="cost_opt">Оптовая</option>
    </select>
    <text>от</text>
    <input type="number" min="0"  id="minPrices"  class="number-only">
    <text>до</text>
    <input type="number"  min="0"  id="maxPrices" class="number-only">
    <text>рублей и на складе</text>
    <select name="" id="more_less" >
        <option value="less">Менее</option>
        <option value="more">Более</option>
    </select>
    <input type="number" min="0"  id="counts" onkeypress="return numbersP(this, event);"
           onpaste="return false;">
    <text>штук.</text>
    <input type="button" value="Показать товар" id="btn_view_filter">
</div>
<div id="table_container">

</div>
<?
echo "<br>";
//Вывести под таблицей общее количество товаров на Складе1 и на Складе2
$totalTwoSkld = Count_two_skld();
foreach ($totalTwoSkld as $total) {
    echo "общее количество товаров на Складе1" . " - " . $total["countFirstSkld"] . "  на Складе2 " . " - " . $total["countSecondSkld"] . "  В сумме " . ($total["countFirstSkld"]+$total["countSecondSkld"]);
}
echo "<br>";
//среднюю стоимость розничной цены товара
$avg = AvgPrice();
echo "среднюю стоимость розничной цены товара  " . $avg["Average"];
echo "<br>";
//среднюю стоимость оптовой цены товара
$avg = AvgOptPrice();
echo "среднюю стоимость оптовой цены товара  " . $avg["Average"];

?>

<script src="js/scriptAjax.js?<? echo mt_rand();?>"></script>
</body>
</html>