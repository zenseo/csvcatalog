<form action='<?php echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
<table>
 
 <tr>
<td>
<select name="type_config">
<option onclick='location.href="<?php echo $url.'&c='.$_GET['c']; ?>&n=0"; return false;' 
value="0" <?php echo !empty($number_conf) ? '' : 'selected'; ?> >
 <?php echo $lang['control']['default']; ?>
</option>
<?php  echo $backend->get_name_config($number_conf,$url.'&c='.$_GET['c']); ?>
</select>
</td>

  <td>
 <b>
  <?php echo $lang['control']['sel_current']; ?>
 </b>
 </td>
</tr>
 </table>
 
 </form>