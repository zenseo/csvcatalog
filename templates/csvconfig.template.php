<form action='<?php echo $_SERVER["REQUEST_URI"]; ?>' method='post'>
 
<div class="sectionHeader"><?php echo $lang['conf']['desc_tv']; ?></div>
<div class="sectionBody">
 
<ul>
<?php echo $lang['conf']['help_tv']; ?>
</ul>
<table>
<tr>
<td><input type="text" value="<?php echo $confdb['count_tv']; ?>" name="count_tv"/></td>
<td>
<?php echo $lang['conf']['count_tv']; ?>
</td>
</tr>
</table>

<table>
<tr>
<td> <b><?php echo $lang['conf']['name_tv']; ?></b></td>
<td><b><?php echo $lang['conf']['tv']; ?></b></td>
<td><b><?php echo $lang['conf']['new_tv']; ?></b></td>
<td><b><?php echo $lang['conf']['place_csv']; ?></b></td>
</tr>

<tr>
<td><span>*<span><b> <?php echo $lang['conf']['art']; ?></b></td>
<td>
<select name="tv_art[]">
<?php echo $backend->get_all_tv($confdb['tv_art'][0]['db']); ?>
</select>
</td>
<td><input type="text" value="" name="tv_art[]"/></td>
<td><input type="text" value="<?php echo $confdb['tv_art'][0]['csv']; ?>" name="tv_art[]"/></td>
</tr>

<tr>
<td><span>*<span><b> <?php echo $lang['conf']['pagetitle']; ?></b></td>
<!-- передаем скрытые поля для задания размерности массива -->
<td><input type="hidden" value="" name="tv_title[]"/></td>
<td><input type="hidden" value="" name="tv_title[]"/></td>
<td><input type="text" value="<?php echo $confdb['tv_title'][0]['csv']; ?>" name="tv_title[]"/></td>
</tr>


<tr><td>	&nbsp;</td><td></td><td></td></tr>
<?php  for ($i = 0; $i < $confdb['count_tv'] ; $i++ ) { ?>
<tr>
<td></td>
<td>
<select name="tv[]">
<?php echo $backend->get_all_tv(isset($confdb['tv'][$i]['db']) ? $confdb['tv'][$i]['db'] : ''); ?>
</select>
</td>
<td><input type="text" value="" name="tv[]"/></td>
<td><input type="text" value="<?php echo @isset($confdb['tv'][$i]['csv']) ? $confdb['tv'][$i]['csv'] : ''; ?>" name="tv[]"/></td>
</tr>
<?php } ?>
 
</table>
 </div>
 
<div class="sectionHeader"> <?php echo $lang['conf']['edit_group']; ?></div>
<div class="sectionBody"> 
 
<ul>
<?php echo $lang['conf']['help_group']; ?>
</ul>

<table>
<tr><td><input type="text" value="<?php echo $confdb['count_group']; ?>" name="count_group"/></td>
<td><?php echo $lang['conf']['count_templ']; ?></td></tr>
</table>

<table>
<tr><td><b><?php echo $lang['conf']['name_group']; ?></b></td>
<td><b><?php echo $lang['conf']['templ']; ?></b></td>
<td><b><?php echo $lang['conf']['new_templ']; ?></b></td>
<td><b><?php echo $lang['conf']['pos_templ']; ?></b></td></tr>

<?php  for ($i = 0; $i < $confdb['count_group']; $i++ ) { ?>
<tr>
<td></td>
<td>

<select name="group[]">
<?php   echo $backend->get_all_group(isset($confdb['group'][$i]['db']) ? $confdb['group'][$i]['db'] : ''); ?>
</select>
</td>
<td>
<input type="text" value="" name="group[]"/>
</td>
<td>

<input type="text" value="<?php  echo @isset($confdb['group'][$i]['csv']) ? $confdb['group'][$i]['csv'] : ''; ?>" name="group[]"/>
</td>
</tr>
<?php } ?>
<tr>
<td><span>*<span><?php echo $lang['conf']['templ_ed']; ?></td>
<td>
<select name="template[]">
<?php echo $backend->get_all_group($confdb['template'][0]['db']); ?>
</select>
</td>
<td>
<input type="text" value="" name="template[]"/>
</td>
<td>
<input type="hidden" value="<?php echo $confdb['template'][0]['csv']; ?>" name="template[]"/>
</td>
</tr>
</table>
</div>

<div class="sectionHeader"><?php echo $lang['all']['csv_tv']; ?></div>
<div class="sectionBody">
<h2></h2>
<table>

<tr>
<td><b><?php echo $lang['conf']['add_name']; ?></b></td>
<td><b><?php echo $lang['conf']['add_value']; ?></b></td>
<td><b><?php echo $lang['conf']['add_desc']; ?></b></td>
</tr>

<tr>
<td><?php echo $lang['conf']['load_ftp']; ?></td>
<td><input type="text" value="<?php echo $confdb['ftp']; ?>" name="ftp"/></td>
<td><?php echo $lang['conf']['desc_load_ftp']; ?></td>
</tr>

<tr>
<td><?php echo $lang['conf']['zip']; ?></td>
<td><input type="text" value="<?php echo $confdb['zip']; ?>" name="zip"/></td>
<td><?php echo $lang['conf']['desc_zip']; ?></td>
</tr>

<tr>
<td><span>*</span><?php echo $lang['conf']['pos_str']; ?></td>
<td><input type="text" value="<?php echo empty($confdb['begin']) ? 0 : $confdb['begin']; ?>" name="begin"/></td>
<td>
<?php echo $lang['conf']['desc_pos_str']; ?></td>
</tr>

<tr>
<td><span>*</span><?php echo $lang['conf']['separator']; ?></td>
<td><input type="text" value="<?php echo $confdb['separator']; ?>" name="separator"/></td>
<td>
<?php echo $lang['conf']['desc_sep']; ?>
</td>
</tr>

<tr>
<td><span>*</span><?php echo $lang['conf']['count_field']; ?></td>
<td><input type="text" value="<?php echo $confdb['count_field']; ?>" name="count_field"/></td>
<td>
<?php echo $lang['conf']['desc_count_field']; ?></td>
</tr>

<tr>
<td><span>*</span>
<?php echo $lang['conf']['parent']; ?></td>
<td><input type="text" value="<?php echo $confdb['parent']; ?>" name="parent"/></td>
<td>
<?php echo $lang['conf']['desc_parent']; ?>
</td>
</tr>

<tr>
<td><?php echo $lang['conf']['chars']; ?></td>
<td><input type="checkbox" value="1" name="charset"
<?php echo $confdb['charset'] > 0 ? 'checked="checked"' : ''; ?>/></td>
<td>
<?php echo $lang['conf']['desc_chars']; ?>
</td>
</tr>

<tr>   
<td><?php echo $lang['conf']['sk']; ?></td>
<td><input type="checkbox" value="1" name="shopkeeper" 
<?php echo $confdb['shopkeeper']['id']  > 0 ? 'checked="checked"' : ''; ?>/></td>
<td>
<?php echo $lang['conf']['desc_sk']; ?></td>
</tr>

</table>
<input type="submit" value="Сохранить конфигурацию" name="submit_config"/>

 
</div>

</form>