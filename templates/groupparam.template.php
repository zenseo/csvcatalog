<form action='<?php echo $_SERVER["REQUEST_URI"]; ?>' method='post'>

<div class="sectionHeader">
<?php echo $lang['control']['head_conf']; ?>
 </div>
<div class="sectionBody">

<table>
 <tr>
 <td>
 <b>
 <?php echo $lang['control']['new_conf']; ?>
 </b> 
 </td>
 <td>
	<input name="new_config" type="text" value="" />
 </td>
 <td>
<input name="submit_config_group" type="submit" value=" <?php echo $lang['control']['submit']; ?>" />
</td> <td></td></tr>
 </table>
</div>
 </form>
 
<form action='<?php echo $_SERVER["REQUEST_URI"]; ?>' method='post'>

<div class="sectionHeader"><?php echo $lang['control']['group']; ?></div>
<div class="sectionBody">
<table>
<tr>
<td><b><?php echo $lang['control']['title']; ?></b></td>
<td><b><?php echo $lang['control']['del']; ?> </b></td>
</tr>
<?php  echo $backend->get_name_config($number_conf,$url.'&c='.$_GET['c'],1,$lang['control']); ?>

</table>
</div>
