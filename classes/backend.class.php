<?php
/*** CsvCatalog 0.8 beta, Rugoals <rugoals@gmail.com> ***/
class Backend {

	//функция выборки всех тв параметров в базе
	function get_all_tv($id_tv = 0) {
		global $modx;
		
		$q = $modx->db->select("id,name,description",$modx->getFullTableName('site_tmplvars'),"");
		$tv = '<option value="0"></option>';
		while($row = $modx->db->getRow($q)){
			if($id_tv == $row['id']) {
				$tv .= '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
			}
			else {
				$tv .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}
		}
		return $tv;
	}
	//Выбираем все группы
	function get_all_group($id_gr = '') {
		global $modx;
		$q = $modx->db->select("id,templatename as name,category",$modx->getFullTableName('site_templates'),"");
		$gr = '<option value="0"></option>';
		while($row = $modx->db->getRow($q)){
			if($id_gr == $row['id']) {
				$gr .= '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
			}
			else{
				$gr .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}
		}
		return $gr;
	}
	//выборка системны параметров
	function get_setting($name = '') {
		global $modx;
		$value = $modx->db->getValue(
			$modx->db->select("setting_value",
			$modx->getFullTableName('system_settings'),
			"setting_name = '".$modx->db->escape($name)."'")
		);
		
		return (empty($value) ? false : true);
	}
	//занесение информации о инсталляции
	function install_cat(){
		global $modx;
		$modx->db->insert(array('setting_name' => 'csvcatalog', 
								'setting_value' => 1
								), $modx->getFullTableName('system_settings'));
 
	}
	//выборка конфигурации
	function get_config($idconfig = 0) {
		global $modx;
		$q = $modx->db->select("setting,value",$modx->getFullTableName('csvcatalog_config'),
		" config = ".intval($idconfig)."");
		
		$conf = array(
		'ftp' => '','shopkeeper' => array('id' => 0),'zip' => '','tv' => '',
		'group' => array(),'separator' => '','charset' => '',
		'count_field' => '','parent' => '','template' => 0,'begin' => '',
		'tv_title' => 0,'tv_art' => 0,'debug' => '',
		'count_tv' => '','count_group' => '' 
		);
		
		while($row = $modx->db->getRow($q)){
			$conf[$row['setting']] = unserialize($row['value']);	 
		}
		
		return $conf;
	}
	
	//обновляем конфигурацию
	function set_config($value = array(), $id_conf = 0) {
		global $modx;
		
		//Выборка нужной конфигурации
		$id = $modx->db->getValue($modx->db->select("id",
		$modx->getFullTableName("csvcatalog_config"), "config = ".intval($id_conf).""));
		
		if($id > 0) {
			foreach ( $value as $k => $v) {
				$modx->db->update("value = '".$modx->db->escape(serialize($v))."'",  
				$modx->getFullTableName("csvcatalog_config"), 
				"setting = '".$modx->db->escape($k)."' AND config = ".intval($id_conf)." ");
			}
		} else {
			foreach ( $value as $k => $v) {
				$modx->db->insert(array(
				'setting' => $modx->db->escape($k),
				'value' => $modx->db->escape(serialize($v)),
				'config' => intval($id_conf) ), 
				$modx->getFullTableName('csvcatalog_config'));
			}
		}
	}
	//заносим новую конфигурацию в базу
	function set_name_config($name = '') {
		global $modx;
		
		return $modx->db->getInsertId($modx->db->insert(array('name' => $modx->db->escape($name) ), 
		$modx->getFullTableName('csvcatalog_config_name')));
	
	}
	//Выбираем все категории
	function get_name_config($select = 0,$url = '',$view = 0,$lang) {
		global $modx;
		
		$q = $modx->db->select("id,name",$modx->getFullTableName('csvcatalog_config_name'),"");
		
		$out = '';
		while($row = $modx->db->getRow($q)){
			if($view == 0) {
				$js = 'onclick=\'location.href="'.$url.'&n='.$row['id'].'"; return false;\'';
		
				if($select == $row['id']) {
				$out .= '<option '.$js.' value="'.$row['id'].'" selected>'.htmlspecialchars($row['name']).
				'</option>';
				}
				else {
				$out .= '<option '.$js.' value="'.$row['id'].'">'.
				htmlspecialchars($row['name']).'</option>';
				}
			} else {
				 
				$out .= '
				<tr>
					<td>'.htmlspecialchars($row['name']).'</td><td>
					<a href="'.$url.'&d='.$row['id'].'">'.$lang['del'].'</a></td>
				</tr>
				';
			}
		}
		return $out;
	
	}
	
	//выбираем название конфигурации по заданному индификатору
	function get_name_fromid($id = 0) {
		global $modx;
		return $modx->db->getValue($modx->db->select("name",$modx->getFullTableName('csvcatalog_config_name'),
		" id = ".intval($id).""));
	}
	
	//Выбираем категорию
	function get_categories() {
	global $modx;
		$id_cat = $modx->db->getValue($modx->db->select("`id` ",$modx->getFullTableName('categories'),
		"`category` = 'csv' "));
			
			if($id_cat > 0) {
			} else {
				$id_cat = $modx->db->getInsertId($modx->db->insert(array('category' => 'csv' ), 
				$modx->getFullTableName('categories')));
			}
			
		return $id_cat;
	}
	
	//Формируем тв параметры
	function format_tv($tv = array(),$template = 0) {
		global $modx;
		$value = array();
 
		foreach (array_chunk ($tv,3) as $k => $v) {
		
			if(!empty($v[1])) {
				$v[1] = $modx->db->escape($v[1]);
				
				$id_tv = $modx->db->getInsertId($modx->db->insert(array(
				'type' => 'text',
				'name'  => $v[1],
				'caption' => $v[1],
				'description' =>  $v[1],
				'category'  => $this->get_categories()
				 )	,  $modx->getFullTableName('site_tmplvars')));
				$value[] = array('db' => $id_tv, 'csv' => intval($v[2]));

			} else {
				$value[] = array('db' => intval($v[0]), 'csv' => intval($v[2]));
			}
			//прикрепляем к шаблону
			if(intval($template) > 0) {
				
				$id = $modx->db->getValue($modx->db->select("count(*)",$modx->getFullTableName("site_tmplvar_templates"),
				 "tmplvarid = ".(empty($v[1]) ? intval($v[0]) : intval($id_tv))." AND  templateid = ".intval($template)."  "));
				
				if($id > 0) {
				} else {
					$modx->db->insert(array('tmplvarid' => empty($v[1]) ? intval($v[0]) : intval($id_tv),
										'templateid' => intval($template) ),
										$modx->getFullTableName('site_tmplvar_templates'));
				}
			} 
		}
		return $value;
	}
	//форматируем группы
	function format_group ($group = array()) {
	global $modx;
		$value = array();
		
		foreach(array_chunk((array)$group,3) as $k => $v) {
			if($v[1]) {
			
				$id_gr = $modx->db->getInsertId($modx->db->insert(array(
				'templatename' => $modx->db->escape($v[1]),
				'description' => 'Template',
				'editor_type' => 0,
				'category'  => $this->get_categories(),
				'template_type' => 0
				),  $modx->getFullTableName('site_templates')));
				
				$value[] = array('db' => $id_gr, 'csv' => intval($v[2]));
			} else {
			
			$value[] = array('db' => $v[0], 'csv' => intval($v[2]));
			
			}	
		}
		 
		return $value;
	}
	
	//выборка числа позиций в базе	
	function count_content($db = '', $template = array()) {
		global $modx;
		
		$s = $modx->db->select("count(*)", $db['cnt'], 
		"template = ".intval($template[0]['db'])." AND published = 1  ");
		
		return ($s ? $modx->db->getValue($s) : 0);
	}	
	
	//выборка дат выгрузки
	function select_max_date ($db = '') {
		global $modx;
		 
		$s = $modx->db->select ("FROM_UNIXTIME(( SELECT MAX(`createdon`) 
		FROM ".$db['cnt']." ),  '%Y-%d-%m %H:%i:%s') AS last_date", $db['cnt'], '');
		
		return ($s ? $modx->db->getValue($s) : 0);
	}
	
	//выборка дат выгрузки
	function select_date ($db = '',$tv_art = 0) {
		global $modx;
		 
		$res = $modx->db->select ("DISTINCT  FROM_UNIXTIME( createdon, '%Y-%d-%m' ) AS date  ",
		$db['tv'].' tv  LEFT JOIN '.$db['cnt'].' c ON c.id = tv.contentid '
		, 'tmplvarid = '.$tv_art.'');
		
		$out = '';
		while($row = $modx->db->getRow($res)){ 
			$out .= '<option value="'.$row['date'].'">за дату: '.$row['date'].'</option>';
		}
		return $out;
	}
	
 
	//удаление документов
	function delete_docs($tv_art = 0, $db = array()){
		global $modx;
		
		if($db == 0) {
		
			$res = $modx->db->select("contentid as id",$db['tv'].' tv 
			LEFT JOIN '.$db['cnt'].' c ON c.id = tv.contentid ',
			'tmplvarid = '.intval($tv_art).' ');
				$i = 0;
				
			while($row = $modx->db->getRow($res)){
				$i++;
				$modx->db->delete($db['cnt'],"`id` = ".$row['id']."  "); 
				$modx->db->delete($db['tv'],"`contentid` = ".$row['id']."  "); 
			}
			
		} else {
			//полностью очищаем таблицы шопкипера
			$modx->db->query('TRUNCATE TABLE '.$modx->getFullTableName('catalog'));
			$modx->db->query('TRUNCATE TABLE '.$modx->getFullTableName('catalog_tmplvar_contentvalues'));
		}
		
		return $i;
	}
	//удаление страниц еденицы каталога (не групп) за определенную даты
	function old_content($tv_art = 0,$date = '9999-99-99',$db = '', $mod = 0) {
		global $modx;
	 
		if(!empty($db)) {
		 
		$res = $modx->db->select("contentid as id",$db['tv'].' tv 
		LEFT JOIN '.$db['cnt'].' c ON c.id = tv.contentid ',
		'tmplvarid = '.intval($tv_art).' AND 
		FROM_UNIXTIME(c.createdon,"%Y-%d-%m") =	"'.$modx->db->escape($date).'"');
		 
			while($row = $modx->db->getRow($res)){
				$modx->db->delete($db['cnt'],"`id` = ".$row['id']."  "); 
				$modx->db->delete($db['tv'],"`contentid` = ".$row['id']."  "); 
			}
		}
	}
	//проверка существования категориии в базе
	function check($id = 0) {
		global $modx;
		
		 $s = $modx->db->select("id",$modx->getFullTableName('csvcatalog_config'),
			'config = '.intval($id).' ');
			
		return ($s ? $id : 0);
	}
	//очистка базы от вставленных элементов
	function delete_content ($template = array(), $groups = array(), $mod_catalog = 0) {
		global $modx;
	
		if($mod_catalog['id'] == 0) {
		//удаление тв параметров для переданного шаблона
			 
			foreach(array_merge($groups,(array)$template) as $group) {
				$del_cnt = $modx->db->select("id ",$modx->getFullTableName('site_content'),
				"`template` = ".intval($group['db'])." ");
				
				while($row = $modx->db->getRow($del_cnt)){
				$modx->db->delete($modx->getFullTableName('site_tmplvar_contentvalues'),
				"`contentid` = ".$row['id']."  ");   
				}
				
				$modx->db->delete($modx->getFullTableName('site_content'),"`template` = ".intval($group['db'])."  "); 
			}
		} else {
		
			foreach($groups as $group) {
				$del_cnt = $modx->db->select("id ",$modx->getFullTableName('site_content'),
				"`template` = ".intval($group['db'])." ");
				
				while($row = $modx->db->getRow($del_cnt)){
				$modx->db->delete($modx->getFullTableName('site_tmplvar_contentvalues'),
				"`contentid` = ".$row['id']."  ");   
				}
				
				$modx->db->delete($modx->getFullTableName('site_content'),"`template` = ".$group['db']."  "); 
			}
			
			//полностью очищаем таблицы шопкипера
			$modx->db->query('TRUNCATE TABLE '.$modx->getFullTableName('catalog'));
			$modx->db->query('TRUNCATE TABLE '.$modx->getFullTableName('catalog_tmplvar_contentvalues'));
		}
		return true;
	}
 
	function index_db() {
	//ALTER TABLE `modx_site_tmplvar_contentvalues` ADD INDEX `tv_csv` (`value` (3))
	}

	function start_php_timer() {
		$mtime = microtime();        //Считываем текущее время 
		$mtime = explode(" ",$mtime);    //Разделяем секунды и миллисекунды
		// Составляем одно число из секунд и миллисекунд
		// и записываем стартовое время в переменную  
		return $mtime[1] + $mtime[0];
	}
	
	function fin_php_timer($tstart) {
		// Делаем все то же самое, чтобы получить текущее время 
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0];
		$totaltime = ($mtime - $tstart);//Вычисляем разницу 
		// Выводим на экран 
	 
		return 	$totaltime ; //sprintf('%d',$totaltime);
	}
	
	//опеделяем сколько памяти используется
	function convert($size) {
		$unit = array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	 
	function fetchTpl($tpl){
		global $modx;
		$template = "";
		if(substr($tpl,-4)=='.tpl' && preg_match('/^(@FILE:)\s?(assets\/snippets\/shopkeeper\/)((chunks\/)|(module\/templates\/)).+/',$tpl)){
			$tpl_file = MODX_BASE_PATH . substr($tpl, 6);
			if(file_exists($tpl_file)){
			$template = file_get_contents(trim($tpl_file));
			}
		}else if(substr($tpl, 0, 6) == "@CODE:"){
			$template = substr($tpl, 6);
		}else if($modx->getChunk($tpl) != ""){
			$template = $modx->getChunk(trim($tpl));
		}else{
			$template = false;
		}
		return $template;
	}
	//полная очистка кеша
	function clearCache() {
    global $modx;
    
    $modx->clearCache();
    
    include_once MODX_BASE_PATH . 'manager/processors/cache_sync.class.processor.php';
    $sync = new synccache();
    $sync->setCachepath(MODX_BASE_PATH . "assets/cache/");
    $sync->setReport(false);
    $sync->emptyCache();
	}
} 
	 
	 

?>