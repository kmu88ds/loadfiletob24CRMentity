<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['token']) || !isset($_GET['img']) || $_GET['token'] != '639d2edfead547660b15a888a9645c13') {
    die();
}

$imgurl = $_GET['img'];
$b24_webhook = $_GET['b24_webhook'];
$entitytype = $_GET['entitytype'];
$entity_id = $_GET['entity_id'];
$fieldname = $_GET['fieldname'];

$log = '';

function writeToLog($data, $title = '')
{
    global $log;
    $log .= print_r($data, 1) . "\r\n";
}

function writeToLog_old($data, $title = '')
{

    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";

    file_put_contents('app.log', $log, FILE_APPEND);

    return true;
}

writeToLog('----------------------');
writeToLog(date("Y.m.d G:i:s"));


/* Функция конвертации файла из URL в Base64 и отправка в сущность CRM Битрикс24*/
function loadfiletob24CRMentity($img,$b24_webhook,$entitytype,$entity_id,$fieldname,$echo = false){
	$imageSize = getimagesize($img);
    $fileName = urldecode(basename($img));
	$imageData = base64_encode(file_get_contents($img));
    $b24Curl = curl_init();
    curl_setopt_array($b24Curl, array(
    CURLOPT_URL => $b24_webhook.'crm.'.$entitytype.'.update',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(array(
        "id" => $entity_id, 
        "fields" => [
            $fieldname => [
                "fileData" => [
                    $fileName, 
                    $imageData 
                ] 
            ] 
        ] 
    ))
));
$response = curl_exec($b24Curl);
curl_close($b24Curl);
	if($echo == true){
		echo $response;
	} else {
		return json_encode($response);
	}
}

loadfiletob24CRMentity($imgurl,$b24_webhook,$entitytype,$entity_id,$fieldname,$echo = true);

file_put_contents('app.log', $log . "\r\n", FILE_APPEND);
?>
