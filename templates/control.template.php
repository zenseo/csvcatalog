<div class="sectionHeader"><?php echo $lang['param']['title_conrol']; ?></div>
<div class="sectionBody">
<table>

<tr>
<td><b><?php echo $lang['param']['param']; ?></b></td>
<td><b><?php echo $lang['param']['value']; ?></b></td>
 </tr>
 
<tr>
<td><?php echo $lang['param']['count_db']; ?></td>
<td><?php echo $backend->count_content($confdb['shopkeeper'], $confdb['template']); ?></td>
 </tr>
 
<tr>
<td><?php echo $lang['param']['last_down']; ?></td> 
<td><?php echo $backend->select_max_date($confdb['shopkeeper']); ?></td>
</tr>

<!--
всего выгрузок

-->

</table>
 
</div>

<div class="sectionHeader"><?php echo $lang['param']['title_del']; ?></div>
<div class="sectionBody">
<form action='' method='post'>
<table>
<tr>
 
<td> <b><?php echo $lang['param']['del']; ?></b> 
<select name="delete_catalog">
<option value="0"></option>
<option value="2"><?php echo $lang['param']['del_docs']; ?></option>
<option value="1"><?php echo $lang['param']['del_all']; ?></option>
<?php //echo  $backend->select_date($confdb['shopkeeper'],$confdb['tv_art'][0]['db']); ?>
</select>
</td>
<td>   	<input value='<?php echo $lang['param']['ok']; ?>' type='submit' name="submit_control" /></td>
</tr>
</table>
</form>
</div></div>