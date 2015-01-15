<?php
class Sqlparser {

	function process($filename) {
	    global $modx;
		$prefix = $modx->db->config['table_prefix']; //$prefix = 'modx_';
		// check to make sure file exists
		if (!file_exists($filename)) {
			return false;
		}

		$fh = fopen($filename, 'r');
		$idata = '';

		while (!feof($fh)) {
			$idata .= fread($fh, 3024);
		}

		fclose($fh);
		$idata = str_replace("\r", '', $idata);
 
		// replace {} tags
		$idata = str_replace('{PREFIX}', $prefix, $idata);
		
		$sql_array = explode("\n\n", $idata);
		
 		foreach($sql_array as $sql_entry) {
			$sql_do = trim($sql_entry, "\r\n; ");

			if (preg_match('/^\#/', $sql_do)) continue;
			if ($sql_do) $modx->db->query($sql_do);

		}
	 return $g;
	}
}