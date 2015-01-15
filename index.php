<?php
/*** CsvCatalog 0.8 beta, Rugoals <rugoals@gmail.com> ***/
/***
надо импорт данных из базы
	- генерация зашифрованной папки
	- или опция с предложением удаления файла
	- или название-дата-небольшой хеш
проверку кодировки и ошибок вынести в отдельный обработчик
протетировать 
работа с кроном
	- генерация зашифрованного урл
	- урл-секретный ключ
***/
//ini_set ('error_reporting', E_ALL); 
ini_set ('display_errors', 1);

define('CSVCATALOG_PATH', $_SERVER['DOCUMENT_ROOT']."/assets/modules/csvcatalog/");
define('CSVCATALOG_URL', 'http://'.$_SERVER['HTTP_HOST']."/assets/modules/csvcatalog/");

require_once(CSVCATALOG_PATH.'classes/backend.class.php');
$backend = new backend();
require_once(CSVCATALOG_PATH.'lang/russian.php');
require_once(CSVCATALOG_PATH.'templates/header.template.php');
 
//инсталлятор
if($backend->get_setting('csvcatalog') > 0) {
 
} else {
	
	$backend->install_cat();
 	include_once(CSVCATALOG_PATH.'classes/sqlparser.class.php');
	$parser = new sqlparser();
	$parser->process(CSVCATALOG_PATH.'install/setup.data.sql');	
} 
 
$url = "http://".$_SERVER['HTTP_HOST']."/manager/index.php?a=".intval($_GET['a'])."&id=".intval($_GET['id']);
$_GET['c'] = isset($_GET['c']) ? intval($_GET['c']) : 0;   
$out = '';
 
//Выбор номера параметра конфигурации
if(isset($_GET['n'])) {
	$_SESSION['number_conf'] = $number_conf = intval($_GET['n']);
} else {
	$number_conf = isset($_SESSION['number_conf']) ? $_SESSION['number_conf'] : 0;
	$number_conf = $backend->check($number_conf);
}

//обработчик для вкладки групп
if($_GET['c'] == 3) {
	//создание новой группы
	if(isset($_POST['submit_config_group'])) {
	$new_config = isset($_POST['new_config']) ? $_POST['new_config'] : 0;

		if(!empty($new_config)) {
		$backend->set_name_config($new_config);
		}
	}
	//Удаление группы
	$d = isset($_GET['d']) ? intval($_GET['d']) : 0;
	if($d > 0) {
		$modx->db->delete($modx->getFullTableName('csvcatalog_config_name')," id = ".$d."");  
		$modx->db->delete($modx->getFullTableName('csvcatalog_config'),"config = ".$d."");
	}
}

//обработка конфигурационных данных
if(isset($_POST['submit_config'])) {

//Выбираем значения из формы
$conf['template'] = isset($_POST['template']) ? $backend->format_group($_POST['template']) : '';
$conf['group'] = isset($_POST['group']) ? $backend->format_group($_POST['group']) : '';
$conf['tv'] = isset($_POST['tv']) ? $backend->format_tv($_POST['tv'],$conf['template'][0]['db']) : array();
$conf['tv_art'] = isset($_POST['tv_art']) ? $backend->format_tv($_POST['tv_art'],$conf['template'][0]['db']) : 0;
$conf['tv_title'] = isset($_POST['tv_title']) ? $backend->format_tv($_POST['tv_title'],$conf['template'][0]['db']) : '';
$conf['shopkeeper'] = isset($_POST['shopkeeper']) ? $_POST['shopkeeper'] : '';
$conf['ftp'] = isset($_POST['ftp']) ? $_POST['ftp'] : '';
$conf['zip'] = isset($_POST['zip']) ? $_POST['zip'] : '';
$conf['separator'] = isset($_POST['separator']) ? $_POST['separator'] : ',';
$conf['charset'] = isset($_POST['charset']) ? $_POST['charset'] : '';
$conf['count_field'] = isset($_POST['count_field']) ? $_POST['count_field'] : '';
$conf['parent'] = isset($_POST['parent']) ? $_POST['parent'] : '';
$conf['begin'] = isset($_POST['begin']) ? $_POST['begin'] : 0;
$conf['debug'] = isset($_POST['debug']) ? $_POST['debug'] : '';
$conf['count_tv'] = isset($_POST['count_tv']) ? $_POST['count_tv'] : '';
$conf['count_group'] = isset($_POST['count_group']) ? $_POST['count_group'] : '';

$conf['shopkeeper'] = $conf['shopkeeper'] == 0 ? 
array('cnt' => $modx->getFullTableName('site_content'),'tv' => $modx->getFullTableName('site_tmplvar_contentvalues'),'id' => 0) :
array('cnt' => $modx->getFullTableName('catalog'),'tv' => $modx->getFullTableName('catalog_tmplvar_contentvalues'),'id' => 1);

//обновляем значения в базе
$backend->set_config($conf,$number_conf);
$out .= 'Параметры успешно обновлены';
}
$confdb = $backend->get_config($number_conf); //получаем значения из базы

//обработчик для вкладки - дополнительно
if($_GET['c'] == 2) {
	if(isset($_POST['submit_control'])){
		$conf['delete_catalog'] = isset($_POST['delete_catalog']) ? $_POST['delete_catalog'] : 0;
		if($conf['delete_catalog'] > 0) {
		//очистка каталога
			if($conf['delete_catalog'] == 1) {
			$out .=  'Каталог успешно удален!';
			
			$backend->delete_content ($confdb['template'], $confdb['group'], $confdb['shopkeeper']);
			
			} else if ($conf['delete_catalog'] == 2) {
				$out .=  'Удалено '.$backend->delete_docs($confdb['tv_art'][0]['db'], $confdb['shopkeeper']).' док.';
			} else {
				$out .=  'Удалены документы за '.htmlspecialchars($conf['delete_catalog']).' период.';
				$backend->old_content($confdb['tv_art'][0]['db'], $conf['delete_catalog'],$confdb['shopkeeper']);
			}
		}
	}
}

if (!empty($out)) {
?>
<div class="sectionHeader"> </div>
<div class="sectionBody">
<?php echo $out; ?>
</div>
<?php
}
?>
 
<div class="sectionHeader">
<?php  include_once(CSVCATALOG_PATH.'templates/selectconfig.template.php'); ?>
</div>
<div class="sectionBody">
<div id="docManagerPane" class="dynamic-tab-pane-control tab-pane">

	<div class="tab-row">
		<h2 class="tab <?php echo $_GET['c'] == 0 ? 'selected' : ''; ?>">
		<a href="<?php echo  $url.'&c=0' ?>">Страница загрузки</a></h2>
		<h2 class="tab <?php echo $_GET['c'] == 1 ? 'selected' : ''; ?>">
		<a href="<?php echo  $url.'&c=1' ?>">Параметры</a></h2>
		<h2 class="tab <?php echo $_GET['c'] == 3 ? 'selected' : ''; ?>">
		<a href="<?php echo $url.'&c=3' ?>">Группы параметров</a></h2>
		<h2 class="tab <?php echo $_GET['c'] == 2 ? 'selected' : ''; ?>">
		<a href="<?php echo $url.'&c=2' ?>">Дополнительно</a></h2>
	</div>

	<div  class="tab-page"> 
	
<?php 

	if($_GET['c'] == 0) {
		include_once(CSVCATALOG_PATH.'templates/formaload.template.php');
	} else if ($_GET['c'] == 1){
	
		include_once(CSVCATALOG_PATH.'templates/csvconfig.template.php'); 
		
	} else if ($_GET['c'] == 2){
		
		include_once(CSVCATALOG_PATH.'templates/control.template.php');
	
	} else if ($_GET['c'] == 3){
	
	include_once(CSVCATALOG_PATH.'templates/groupparam.template.php');
	
	}

?>

	</div>
</div>
</div>

<body>
</html>
 
 
