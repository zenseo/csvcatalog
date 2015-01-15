<div class="sectionHeader"><?php echo $lang['all']['title']; ?></div>
<div class="sectionBody">
 <table>
 <tr>
 <td>
 <b><?php echo $lang['all']['sel_file']; ?></b>
 </td>
 <td>
   <form id='uploadForm' action='<?php echo CSVCATALOG_URL.'index-ajax.php'; ?>' method='post' enctype='multipart/form-data'>
    	<input name='MAX_FILE_SIZE' value='[+size_upload+]' type='hidden'/>
     	 <input name='fileToUpload[]' id='fileToUpload' class='MultiFile' type='file'/> 
	 
		<input value='<?php echo session_id($_COOKIE[session_name()]); ?>' type='hidden' name='token' />
      
 	<img id='loading' src='<?php echo CSVCATALOG_URL; ?>img/loading.gif' style='display:none;'/> 
</td>
</tr>

<tr>	
<td>
	<input name="upload_form" value='Занести в базу' type='submit'/>
</td>
</tr>
</table>	
	
	</form>
	
    <div id='uploadOutput'></div> 
	
</div>