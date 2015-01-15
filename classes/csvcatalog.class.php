<?php
/*** CsvCatalog 0.8 beta, Rugoals <rugoals@gmail.com> ***/
class Csvcatalog {
 
	function __construct($confdb,$lang) {
 
		$this->begin_str = $confdb['begin'];
		$this->charset = $confdb['charset'];
		$this->separator = empty($confdb['separator']) ? ',' : $confdb['separator']; 
		$this->count_separate = $confdb['count_field'];
		$this->table_cnt = $confdb['shopkeeper']['cnt'];
		$this->table_tv = $confdb['shopkeeper']['tv'];
		$this->file_ftp = $confdb['ftp'];
		$this->zip = $confdb['zip'];
		$this->lang['err'] = $lang;
		
	}
	
	//распаковка zip
	function unzip($file) {
		if(!empty($file)) {
			if(file_exists($this->dir().$file)) {
				@chmod($this->dir().$file, 777); 
				$zip = zip_open($this->dir().$file);
				if ($zip) {
					while ($zip_entry = zip_read($zip)) {
						if (zip_entry_open($zip, $zip_entry, "r")) {
							$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							$fp = fopen($this->dir().zip_entry_name($zip_entry),"w"); //открываем фаил в архиве
							fwrite($fp,$buf); //записываем фаил
							@chmod($this->dir().zip_entry_name($zip_entry), 0777); 
							zip_entry_close($zip_entry);
						}
					}
					zip_close($zip);
				}
			}	
		}		
	}
	
	//путь до места хранения файлов импорта 
	function dir ($d = '/assets/files/') {
		return $_SERVER['DOCUMENT_ROOT'].$d;
	}
	
	//директория для загружаемых файлов
	function uploadfile ($i = 0) {  
		$f = '';
		$error = '';
		if(!empty($this->file_ftp)) {
			if(empty($this->zip)) {
				$f = $this->file_ftp;
			} else {
				$this->unzip($this->dir().$this->file_ftp);
				$f = $this->zip;
			}
		} else {
			if(empty($this->zip)) {
				$f = basename($_FILES['fileToUpload']['name'][$i]); 	
			} else {
				$this->unzip($this->dir().$_FILES['fileToUpload']['name'][$i]);
				$f = $this->zip;
			}
		}
		
		if(!empty($f) || strlen($f) > 3) {
			if(!file_exists($this->dir().$f)) {
				$error .= 'Файл отсутствует.';
			}  
		} else {
			$error .= 'Не указано наименование файла.';
		}
		$this->uploadfile = empty($error) ? $this->dir().$f : $error ;
	}
	//проверка кодировки
	function isUTF8($str){
		if($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32"))
		return true;
		else
		return false;
	}
 
	// загрузка Файла на сервер
	function load ($i = 0, $max_size = 3000000) {
 
		//если с фтп, то пропускаем загрузку из формы
		if(!empty($this->file_ftp))  return true;
		//для первого загрузаемого файла пришедшего из формы
		switch($_FILES['fileToUpload']['error'][$i]){ 
			case 0 :
			if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'][$i], 
			$this->dir(). basename($_FILES['fileToUpload']['name'][$i]) )  && 
				($_FILES['fileToUpload']['size'][$i] <= $max_size) ){
				
				echo  $this->lang['err']['true_load'][0].$_FILES['fileToUpload']['name'][0].
				$this->lang['err']['true_load'][1].sprintf('%d',$_FILES['fileToUpload']['size'][0] / 1000).
				$this->lang['err']['true_load'][2];
				return true;

			} else {
				echo $this->lang['err']['not_file'] ;
				return false;
			}
			
			break;
			case 1:
			echo $this->lang['err']['false_down'];
			return false; break;
			case 2:
			echo $this->lang['err']['false_down_form'];
			return false;  break;
			case 3:
			echo $this->lang['err']['false_size'];
			return false; break;
			case 4:
			echo $this->lang['err']['not_file'] ;
			return false; break;
			default: return false; break;
		}		
	}
	
	//приведем присланный фаил к utf-8
	function win1251_2_utf8 ($x) {
		return  iconv("windows-1251", "utf-8", $x);
	}

	//парсинг файла на ошибки
	function fget_csv() {
		$error = '';
		$out = '';
		
		$f = file($this->uploadfile);	
		$c_f = count($f);
		  
		if ($this->charset > 0) {
			for($i = $this->begin_str, $j = 1 ; $i < $c_f; $i++, $j++) {		
				
				$x = explode ($this->separator,$this->win1251_2_utf8($f[$i]));
		
				if(count($x) == $this->count_separate) {
				 $out[] = $x;
				}
				else {
					$error .= "<tr>";
					$error .= "<td> ". $j."</td><td> ".$this->count_separate."</td> <td>".count($x) ." </td>\n\r";
					$error .= "</tr>";
				}
			}
		
		} else {
			for($i = $this->begin_str, $j = 1 ; $i <  $c_f; $i++, $j++) {		
				$x = explode ($this->separator,$f[$i]);
		
				if(count($x) == $this->count_separate) {
				 $out[] = $x;
				}
				else {
					$error .= "<tr>";
					$error .= "<td> ". $j."</td><td> ".$this->count_separate."</td> <td>".count($x) ." </td>\n\r";
					$error .= "</tr>";
				}
			}
		}
		
		if(strlen($error) > 0) {
			$out = file_get_contents(CSVCATALOG_PATH.'templates/error.template.php');
			$out .= $error."</table>";
		}
		
		return $out;
	}

	//создает группы вложенности для позиций из csv файла
	function add_group ($array_group = array(),$parent = 0,$content = array()) { 
		global $modx;
		$first_group = array(); //массив с первым уровнем каталога
		//создаем массив каталога
		foreach($content as $k => $v) {
			$r = '';
			foreach ($array_group as  $i => $group) {
				$r .= trim($v[$group['csv']]).'-';
			}
			 $sum[] = $r;
			 
			$first_group[] = trim($v[$array_group[0]['csv']]); //выбираем названия групп
		}
		
		//заносим в базу первую вложенность каталога, нужно чтобы сократить запросы к базе
		$first_cat = array_unique($first_group);
		 
		foreach($first_cat as $a) {
			 
			//делаем проверку и вставляем в базу
			$id = $modx->db->getValue($modx->db->select('id',$modx->getFullTableName('site_content'),'longtitle="'.
			$modx->db->escape($a).'" 
			AND  template = '.intval($array_group[0]['db']).''));
 
			if($id > 0) {
			 
			} else {
			 
				$modx->db->insert(array(
							'pagetitle' => $modx->db->escape($a), 
							'longtitle' => $modx->db->escape($a), //для вставки кода группы
							'alias' => '',
							'published' => 1,
							'parent' => intval($parent),
							'isfolder' => 1,
							'introtext' => NULL,
							'content' => NULL,
							'template' => intval($array_group[0]['db']),
							'menuindex' => 0,
							'createdon' => time(),
							'hidemenu' => 0
					), $modx->getFullTableName('site_content'));
			}
		}
		
		
		$uniq_sum =  array_unique($sum); //выбираем уникальные каталоги
		//создаем массив из уникальных групп
		foreach($uniq_sum as $str) {
			$cat = explode('-',$str);
			$count = count($cat) - 1; //последний элемент массива пустой
			
			for ($i = 1; $i < $count; $i++ ) { // в первом элементе находится первая вложенность уже занесенная в базу
			
			if(empty($cat[$i])) continue;
			
			$id_parent = $modx->db->getValue($modx->db->select('id',$modx->getFullTableName('site_content'),
			'longtitle = "'.$modx->db->escape($cat[($i-1)]).'"  AND template = '.intval($array_group[($i-1)]['db']).' 
			'.($i > 1 ? '
			
			AND parent IN (SELECT id FROM modx_site_content WHERE longtitle="'.$modx->db->escape($cat[($i-2)]).'"
			AND  template = '.intval($array_group[($i-2)]['db']).')
			
			' : '' ).'
			
			'));
 
			//делаем проверку
			$id = $modx->db->getValue($modx->db->select('id',$modx->getFullTableName('site_content'),'longtitle="'.
			$modx->db->escape($cat[$i]).'" 
			AND  template = '.intval($array_group[$i]['db']).'
			AND parent = '.intval($id_parent).'
			'));
				if($id > 0) {
				
				} else {
					 
					$modx->db->insert(array(
							'pagetitle' => $modx->db->escape($cat[$i]), 
							'longtitle' => $modx->db->escape($cat[$i]), //для вставки кода группы
							'alias' => '',
							'published' => 1,
							'parent' => intval($id_parent),
							'isfolder' => 1,
							'introtext' => NULL,
							'content' => NULL,
							'template' => intval($array_group[$i]['db']),
							'menuindex' => 0,
							'createdon' => time(),
							'hidemenu' => 0
					), $modx->getFullTableName('site_content'));
				 
				}
			}
			
		}
 
		return $sum; 
	}
 
	//удаляем все tv параметры, оставляя только артикул
	function delete_tv($dbart = '',$tv_cat = array()) {
	global $modx;
		foreach($tv_cat as $tv) {
			$dbart == $tv['db'] ? '' : $modx->db->delete($this->table_tv,"`tmplvarid` = ".$tv['db']."  ") ;   
		}
	}
 
	//выбираем группу для вставки
	function group_for_insert($groups = array(),$c = array()) {
		global $modx;
		//rsort($groups);
		$s = count($groups);
		for($i = $s, $j = $s - 1; $i > 0; $i--, $j--) {
		  $out[] = $groups[$j];
		}
		$groups = $out;
	 
		$id = 0;
		$count = count($groups) - 1;
		foreach ($groups as $k => $v) {
		
			if(empty($c[$v['csv']])) {
				continue;
			}
			else {
			
				$id = $modx->db->getValue(
				
				$modx->db->select("id",
				$modx->getFullTableName('site_content'), 
				' 
				template = '.$v['db'].' AND  longtitle = "'.$modx->db->escape(trim($c[$v['csv']])).'"
				
				'.($k == $count ? '' : '
				
				 AND parent IN  ( SELECT id FROM modx_site_content WHERE 
				longtitle = "'.$modx->db->escape(trim($c[$groups[($k+1)]['csv']])).'" 
				AND template = '.intval($groups[($k+1)]['db']).'
				)').'
				
				'));
					
				break;
			}
		}
		return $id;
	}
 
	//занесение позиций в базу
	function content_to_db($groups = array() ,$c = array(),$ptitle = 1,$art = array(),  $ttovar = '', $tv_cat = array()) {
		global $modx;
		
		$tv_cat = array_merge($art,$tv_cat);
		
		//Выбирает группу в базе
		   $group = $this->group_for_insert($groups,$c);
	
		if($group > 0) {  //родительский каталог
			//выбираем идентификатор добавляемой позиции
			$id_up = $modx->db->getValue($modx->db->select("`contentid`",$this->table_tv,
			"`tmplvarid` = '".intval($art[0]['db'])."' AND 
			`value` = '".$modx->db->escape(trim($c[$art[0]['csv']]))."'  LIMIT 1")); 
			
			if($id_up > 0) {
				if ( 
				$modx->db->update( 
				"pagetitle = '".$modx->db->escape(trim($c[$ptitle]))."', createdon = '".time()."', published = 1
				", $this->table_cnt,
				" `id` = ".$id_up." AND `template` = ".$ttovar." AND `parent` = ".$group." ")
				) {
					foreach($tv_cat as $tv) {
						if($tv['db'] == $art[0]['db']) {
						} else {
							$modx->db->insert(array(
								'tmplvarid' => intval($tv['db']), 
								'contentid' => $id_up,
								'value' => $modx->db->escape(trim($c[$tv['csv']])) ), $this->table_tv);
						}
					}
				 }
			} else {
				//вставляем новые позиции
				$id_cnt = $modx->db->getInsertId($modx->db->insert(array(
								'pagetitle' =>  $modx->db->escape(trim($c[$ptitle])), 
								//'alias' =>  '',
								'published' =>  1,
								'parent' => $group,
								'template' => intval($ttovar),
								'createdon' => time()
								), $this->table_cnt));
	 
					//заносим тв параметры
				if($id_cnt > 0){	
					foreach($tv_cat as $i => $tv) {
						$modx->db->insert(array(
								'tmplvarid' => intval($tv['db']), 
								'contentid' => $id_cnt,
								'value' => $modx->db->escape(trim($c[$tv['csv']])) ), $this->table_tv);
  
					}
				} 	 		
			}
		}	
	}
}

?>