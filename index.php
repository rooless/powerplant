<?php
header("Content-Type: text/html; charset=cp1251");
ini_set('memory_limit', '256M');

function repl($tab)
{
    return number_format(floatval($tab), 0, '', '+');
}

function getArr($arr)
{
    $strRequest = '';
    $rooms = ($_POST['typis'] == 'city/rooms') ? 'rooms_total%5B%5D=' : 'rooms%5B%5D=';
    foreach ($arr as $value) {
        $strRequest .= '&' . $rooms . $value;
    }

    return $strRequest;
}

$_POST = json_decode(file_get_contents('php://input'), true);

$kom = ($_POST['typis'] == 'city/rooms') ? '&rooms%5Bfrom%5D=&rooms%5Bto%5D=' : '';

$sort = (($_POST['price_from'] != '') || ($_POST['price_to'] != '') || (count($_POST['rooms']) > 0) || ($_POST['only_photo'] == 1)) ? $_POST['sort'] : '';
$sortOrder = (($_POST['price_from'] != '') || ($_POST['price_to'] != '') || (count($_POST['rooms']) > 0) || ($_POST['only_photo'] == 1)) ? $_POST['sortorder'] : '';

$type = isset($_POST['typis']) ? $_POST['typis'] : 'city/flats';
$price_from = ($_POST['price_from'] != '') ? repl($_POST['price_from']) : '';
$price_to = ($_POST['price_to'] != '') ? repl($_POST['price_to']) : '';
$rooms = isset($_POST['rooms']) ? getArr($_POST['rooms']) : '';
$photo = $_POST['only_photo'] == 1 ? '&only_photo=' . $_POST['only_photo'] : '';
$continueQuery = ($sort != '') ? $kom . '&square_living%5Bfrom%5D=&square_living%5Bto%5D=&square_kitchen%5Bfrom%5D=&square_kitchen%5Bto%5D=&floor%5Bfrom%5D=&floor%5Bto%5D=&date_create=0&firm_name=&description=&text=' : '';

$content = file_get_contents('http://www.50.bn.ru/sale/' . $type . '/?sort=' . $sort . '&sortorder=' . $sortOrder . '&price%5Bfrom%5D=' . $price_from . '&price%5Bto%5D='. $price_to . $rooms . $continueQuery . $photo);

$posA = strpos($content, '<div class="result">');
$pager = strpos($content, '<div class="pager">');
$lenght = $pager - $posA;

$cont = substr($content, $posA, $lenght);
echo $lenght > 0 ? $cont . '</div>' : mb_convert_encoding('К сожалению, по Вашим критериям поиска не найдено предложений. <br><br> Попробуйте изменить условия поиска.', 'cp1251', 'utf-8');

$sort = null;
$sortorder = null;
$type = null;
$price_from = null;
$price_to = null;
$rooms = null;
$photo = null;
$cont = null;
