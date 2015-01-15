<?php

ini_set('error_reporting', E_ALL); 
ini_set ('display_errors', 1);
 
define('CSVCATALOG_PATH', $_SERVER['DOCUMENT_ROOT']."/assets/modules/csvcatalog/");
define('CSVCATALOG_URL', 'http://'.$_SERVER['HTTP_HOST']."/assets/modules/csvcatalog/");
define('MODX_MANAGER_PATH', $_SERVER['DOCUMENT_ROOT']."/manager/");
 
require_once(MODX_MANAGER_PATH . 'includes/config.inc.php');
define('MODX_API_MODE', true);
include_once(MODX_MANAGER_PATH.'includes/document.parser.class.inc.php');

session_name($site_sessionname);
session_id($_COOKIE[session_name()]);
session_start();

$modx = new DocumentParser;
$modx->db->connect();

include_once(CSVCATALOG_PATH.'classes/backend.class.php');
$backend = new backend();
require_once(CSVCATALOG_PATH.'lang/russian.php');

//параметр для определения набора конфигурации
$type_config = isset($_POST['type_config']) ? $_POST['type_config'] : 0;

$confdb = $backend->get_config($type_config); //значения в базе
 
require_once(CSVCATALOG_PATH.'classes/csvcatalog.class.php');
$csv = new csvcatalog($confdb,$lang['err']);

 
//обрабатываем данные из формы загрузки файла
 
if($_POST['token'] == session_id($_COOKIE[session_name()])) {

$timer  = $backend->start_php_timer();
 
	if($csv->load()) {
		$csv->uploadfile(); //наименование загруженного файла
		if(is_array($csv->fget_csv())) {
			//создаем группы	 
			$csv->add_group($confdb['group'],$confdb['parent'],$csv->fget_csv()); 
			 
			
			//удаление тв параметров из базы
			 
			$csv->delete_tv($confdb['tv_art'][0]['db'],$confdb['tv']); 
			//делаем документы неопубликованными
			$modx->db->update("published = 0", $confdb['shopkeeper']['cnt'], " template = ".$confdb['template'][0]['db']." ");
 
			foreach ($csv->fget_csv() as $c) {
				  $csv->content_to_db(
								$confdb['group'], //группы
								$c, 
								$confdb['tv_title'][0]['csv'], //позиция заголовка в csv
								$confdb['tv_art'], //tv артикула
								$confdb['template'][0]['db'], //id шаблона  
								$confdb['tv']  
								);	  
			}
 
		} else {
			echo $csv->fget_csv();
		}
	}
	echo "<p><strong>Используемая память: ".$backend->convert(memory_get_usage(true))."</strong>,
	<strong>Время выполнения ".$backend->fin_php_timer($timer)." секунд </strong></p>";	
} else {
echo "Ошибка: присланы не корректные данные, пожалуйста свяжитесь с техподдержкой";
}


?>