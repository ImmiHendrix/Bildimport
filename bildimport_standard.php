<?php
die();
ini_set('max_execution_time', 0);
ini_set('memory_limit', '2048M');
ini_set('display_errors', 1);
function getUniqueCode($length = "")
{
    $code = md5(uniqid(rand(), true));
    if ($length != "") return substr($code, 0, $length);
    else return $code;
}

require_once 'app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$host = "localhost";
$user = "dev";
$password = "devsec#543!";
$db = "swopper-muvman";
$connect = mysql_connect($host,$user,$password) or die(mysql_error());
mysql_select_db($db)or die(mysql_error());


$view = mysql_query("
	CREATE OR REPLACE VIEW catalog_product_entity_value AS
	SELECT P.sku AS Artikelnummer, V.value AS Holzart, V1.value AS Tischfunktion
			FROM catalog_product_entity AS P INNER JOIN
			catalog_product_entity_varchar AS V  ON P.entity_id = V.entity_id  AND V.attribute_id = 245 LEFT JOIN
			catalog_product_entity_varchar AS V1 ON P.entity_id = V1.entity_id AND V1.attribute_id = 248") or die(mysql_error());

/* Infos zu den Attributen+Values
Bildimport Tische Standard
	wood_art - Holzart - id 245
		option values : Kernbuche 241 OR 242
						Buche 239 OR 240
						Eiche 237 OR 238
						Eiche sonoma 236
						Eiche verwittert 235
						Buche kolonial 234
						Kernbuche nussbaum 233
	table_function - Tischfunktion - id 248
		option values : Fest 296
						ausziehbar NOT LIKE 296
*/	
			
$holz = array (233,234,235,236,237,238,239,240,241,242);
	foreach($holz as $_holz_key => $_holz_val) {
            $result = mysql_query("
			SELECT * FROM catalog_product_entity_value
			WHERE Holzart IN ('. implode(',', $_holz_val).')") //übergibt das komplette Array als einen String
                or die(mysql_error());
            while($row = mysql_fetch_array($result)) {
                $data[] = array(
                    'sku' => $row['Artikelnummer'],
                    '_media_image' => 'pic_'.$row['Holzart'].'.jpg',
                    '_media_attribute_id' => '88',
                    '_media_is_disabled' => '0',
                    '_media_position' => '1',
                    '_media_lable' => 'Bild 1',
                    'image' => 'pic_'.$row['Holzart'].'.jpg',
                    'small_image' => 'pic_'.$row['Holzart'].'.jpg',
                    'thumbnail' => 'pic_'.$row['Holzart'].'.jpg'
                );
            }
        }
mysql_close($connect);

$time = microtime(true);

session_start();

if ($_GET['reset']) $_SESSION['page'] = 0;


$page = $_SESSION['page'];
if (!$page) $page = 0;



$newData = array();
$pageSize = 100;

$start = $page * $pageSize;

if ($start > sizeof($data)) {
    die('finished');
}

for ($i = $start; $i < $start + $pageSize; $i++) {
    $newData[] = $data[$i];
    echo sprintf('<div>importing %s</div>', $data[$i]['sku']);

}

$data = $newData;

echo sprintf('<div>starting with %s</div>', $start);
echo sprintf('<div>number of records: %s</div>', sizeof($data));




try {
    /** @var $import AvS_FastSimpleImport_Model_Import */
    $import = Mage::getModel('fastsimpleimport/import');
    $import
        ->setPartialIndexing(false)
        ->setBehavior(Mage_ImportExport_Model_Import::BEHAVIOR_APPEND)
        ->processProductImport($data);
} catch (Exception $e) {
    print_r($import->getErrorMessages());
}

echo 'Elapsed time: ' . round(microtime(true) - $time, 2) . 's' . "\n";

$_SESSION['page']++;

echo '<meta http-equiv="refresh" content="2; url=\''.$_SERVER['PHP_SELF'].'\'">';
