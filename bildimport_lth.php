<?php
die();
ini_set('max_execution_time', 0); //Setze Ausführungszeit auf unendlich
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




//Ordne Artikeln Bilder zu
$host = "localhost";
$user = "dev";
$password = "devsec#543!";
$db = "swopper-muvman";
$connect = mysql_connect($host,$user,$password) or die(mysql_error());
mysql_select_db($db)or die(mysql_error());

$view = mysql_query("
	CREATE OR REPLACE VIEW catalog_product_entity_value AS
	SELECT P.sku AS Artikelnummer, V.value AS Basisfarbe, V1.value AS Fuss, V2.value AS Bezugsfarbe, V3.value AS Federfarbe, V4.value AS Bezugsart
			FROM catalog_product_entity AS P INNER JOIN
			catalog_product_entity_varchar AS V  ON P.entity_id = V.entity_id  AND V.attribute_id = 148 LEFT JOIN
			catalog_product_entity_varchar AS V1 ON P.entity_id = V1.entity_id AND V1.attribute_id = 157 LEFT JOIN
			catalog_product_entity_varchar AS V2 ON P.entity_id = V2.entity_id AND V2.attribute_id = 150 LEFT JOIN
			catalog_product_entity_varchar AS V3 ON P.entity_id = V3.entity_id AND V3.attribute_id = 156 LEFT JOIN
			catalog_product_entity_varchar AS V4 ON P.entity_id = V4.entity_id AND V4.attribute_id = 149") or die(mysql_error());
//Basisfarbe attribut_id=148 / anthrazit value=3 / titan value=4
$basis = array (3,4);
//Fuß attribut_id=157 / Rollen value=36 / Kombigleiter value=37
$fuss = array (36,37);
/* Bezugsfarbe attribut_id=150
weiß, ferraro-rot, smoke-grau, royal-blau, schwarz, sand, terracotta,
choco, violett, pistacchio value=8,9,..,17 */
$bezug = array (8,9,10,11,12,13,14,15,16,17);
//Federfarbe attribut_id=156
$i = 0; $j = 0;	$k = 0;// Schleifenindizes
while ($i <= 1){
    while ($j <= 1) {
        while ($k <= 9) {
            // Artikelnummern ohne Lehne
            $result = mysql_query("
			SELECT * FROM catalog_product_entity_value
			WHERE Basisfarbe = ".$basis[$i]." AND Fuss = ".$fuss[$j]." AND Bezugsfarbe =".$bezug[$k]." AND Bezugsart = 6 AND Artikelnummer NOT LIKE '%LDY%' AND Artikelnummer NOT LIKE '%WK%' AND Artikelnummer NOT LIKE '%SA%'")
                or die(mysql_error());
            while($row = mysql_fetch_array($result)) {
                // Passe Federfarbe "wie Basisfarbe" der Basisfarbe an
                if ($row['Federfarbe'] == 35) {
                    $row['Federfarbe'] = $row['Basisfarbe'];
                }
                // Füge Bild §basis[i]_$fuss[j]_$bezugsfarbe[k].jpg zu artikel mit artikelnummer 'sku' => $result hinzu
                $data[] = array(
                    'sku' => $row['Artikelnummer'],
                    '_media_image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_LTH.jpg',
                    '_media_attribute_id' => '88',
                    '_media_is_disabled' => '0',
                    '_media_position' => '1',
                    '_media_lable' => 'Bild 1',
                    'image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_LTH.jpg',
                    'small_image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_LTH.jpg',
                    'thumbnail' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_LTH.jpg'
                );
            }

            // Artikelnummern mit Lehne
            $result = mysql_query("
			SELECT * FROM catalog_product_entity_value
			WHERE Basisfarbe = ".$basis[$i]." AND Fuss = ".$fuss[$j]." AND Bezugsfarbe =".$bezug[$k]." AND Bezugsart = 6 AND Artikelnummer LIKE '%LDY%'")
                or die(mysql_error());
            while($row = mysql_fetch_array($result)) {
                // Passe Federfarbe "wie Basisfarbe" der Basisfarbe an
                if ($row['Federfarbe'] == 35) {
                    $row['Federfarbe'] = $row['Basisfarbe'];
                }
                // Füge Bild §basis[i]_$fuss[j]_$bezugsfarbe[k].jpg zu artikel mit artikelnummer 'sku' => $result hinzu
                $data[] = array(
                    'sku' => $row['Artikelnummer'],
                    '_media_image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_LDY_LTH.jpg',
                    '_media_attribute_id' => '88',
                    '_media_is_disabled' => '0',
                    '_media_position' => '1',
                    '_media_lable' => 'Bild 1',
                    'image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_LDY_LTH.jpg',
                    'small_image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_LDY_LTH.jpg',
                    'thumbnail' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_LDY_LTH.jpg'
                );
            }
/*            // Artikelnummern Swopper Work
            $result = mysql_query("
			SELECT * FROM catalog_product_entity_value
			WHERE Basisfarbe = ".$basis[$i]." AND Fuss = ".$fuss[$j]." AND Bezugsfarbe =".$bezug[$k]." AND Artikelnummer LIKE '%WK%'")
                or die(mysql_error());
            while($row = mysql_fetch_array($result)) {
                // Passe Federfarbe "wie Basisfarbe" der Basisfarbe an
                if ($row['Federfarbe'] == 35) {
                    $row['Federfarbe'] = $row['Basisfarbe'];
                }
                // Füge Bild §basis[i]_$fuss[j]_$bezugsfarbe[k].jpg zu artikel mit artikelnummer 'sku' => $result hinzu
                $data[] = array(
                    'sku' => $row['Artikelnummer'],
                    '_media_image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_WK.jpg',
                    '_media_attribute_id' => '88',
                    '_media_is_disabled' => '0',
                    '_media_position' => '1',
                    '_media_lable' => 'Bild 1',
                    'image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_WK.jpg',
                    'small_image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_WK.jpg',
                    'thumbnail' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_WK.jpg'
                );
            }

            // Artikelnummern Swopper Sattel
            $result = mysql_query("
			SELECT * FROM catalog_product_entity_value
			WHERE Basisfarbe = ".$basis[$i]." AND Fuss = ".$fuss[$j]." AND Bezugsfarbe =".$bezug[$k]." AND Artikelnummer LIKE '%SA%'")
                or die(mysql_error());
            while($row = mysql_fetch_array($result)) {
                // Passe Federfarbe "wie Basisfarbe" der Basisfarbe an
                if ($row['Federfarbe'] == 35) {
                    $row['Federfarbe'] = $row['Basisfarbe'];
                }
                // Füge Bild §basis[i]_$fuss[j]_$bezugsfarbe[k].jpg zu artikel mit artikelnummer 'sku' => $result hinzu
                $data[] = array(
                    'sku' => $row['Artikelnummer'],
                    '_media_image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_SA.jpg',
                    '_media_attribute_id' => '88',
                    '_media_is_disabled' => '0',
                    '_media_position' => '1',
                    '_media_lable' => 'Bild 1',
                    'image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_SA.jpg',
                    'small_image' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_SA.jpg',
                    'thumbnail' => 'pic_'.$row['Basisfarbe'].'_'.$row['Fuss'].'_'.$row['Bezugsfarbe'].'_'.$row['Federfarbe'].'_SA.jpg'
                );
            }*/
            $k++;
        }
        $j++; $k=0;
    }
    $i++; $j=0; $k=0;
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
