<form action='<?php echo $_SERVER["REQUEST_URI"]; ?>' method='post'>

<div class="sectionHeader">
<?php echo $lang['control']['head_sel']; ?>
</div>
<div class="sectionBody">

<table>
 
 <tr>
 <td>
 <b> <?php echo $lang['control']['sel_config']; ?></b>
 </td>
 
 <td>
<select name="select_config">
<option value="0" <?php echo !empty($select_config) ? '' : 'selected'; ?>>
<?php echo $lang['control']['default']; ?></option>
<?php  echo $backend->get_name_config($select_config); ?>
 
</select>
</td>
</tr>
 <tr><td>
<input name="control_conf" type="submit" value="<?php echo $lang['control']['submit']; ?>" />
</td> <td></td></tr>
 </table>
</div>
 </form>