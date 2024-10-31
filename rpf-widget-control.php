
<p>
<label for="rpf_title">Title:</label><br/>
<input name="rpf_title" type="text" value="<?php echo $rpf_title; ?>" /><br/><br/>
<label for="rpf_category_id">From category id:</label>
<br/>
 <input name="rpf_category_id" type="text" value="<?php echo $rpf_category_id; ?>" /><br/>
 <label for="rpf_note">Please seperate category id by comma for multiple category</label><br/><br/>
 
 <label for="rpf_post_id">Disable for following pages:</label>
 <br/>
 

 
	<select name="rpf_post_id[]" id="rpf_post_id" multiple="multiple" style="width:225px;height:150px">
	<option value="">Show For All</option>
	<?php
	foreach($pages as $page):
	
	if(in_array($page->ID,$rpf_post_id))
	$selected_acc="selected='selected'";
	else
	$selected_acc="";
	
	?>
	<option <?php echo $selected_acc;?>  value="<?php echo $page->ID;?>">
	<?php
	for($i=1;$i<=$page->level;$i++)
	echo "-";
	?>
	<?php echo $page->post_title;?>
	
	</option>
	<?php
	endforeach;
	?>
	</select><br/><br/>


<label for="rpf_post_number">No. of post:</label>
<br/>
 <input name="rpf_post_number" type="text" value="<?php echo $rpf_post_number; ?>" /><br/>

<input type="hidden" id="rpf_submit" name="rpf_submit" value="1" />
</p>