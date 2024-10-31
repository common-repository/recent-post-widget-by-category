<?php
global $RPF;


if(isset($_POST['submit_page']))
{
	
	$this->wpdb->query("delete from wp_post_page_link where page_id=".$_POST['page_id']);
	
	foreach($_POST['post_id'] as $val)
	{
		if($val>0)
		{
		$data=array();
		$data = array('page_id' => $_POST['page_id'],
				  'post_id' => $val);
	
		$this->wpdb->insert( "wp_post_page_link", $data );
		}
	}
}

$pages=$RPF->get_post_tree(0,0,"page");
$RPF->post_tree="";
$posts=$RPF->get_post_tree(0,0,"post");

	
if(isset($_POST['page_id']))
$page_post_id=$RPF->get_post_by_page_id($_POST['page_id']);
else
$page_post_id=$RPF->get_post_by_page_id($pages[0]->ID);

//digin($page_post_id);

?>
<style type="text/css">
label.error{background:none;border:none;color:#FF0000;margin:0px!important; padding:0px!important;}
input.error{margin:0px!important;padding:3px;}
</style>
<div class="wrap">

<?php if (!empty($error)) : ?>
	<div id="message" class="error fade">
		<p><?php echo $error; ?></p>
	</div>
	<?php elseif (!empty($page_id)) : ?>
	<div id="message" class="updated fade">
		<p>
			<strong><?php echo $message;?></strong> 
		</p>
	</div>
<?php endif; ?>


<?php wp_nonce_field($_GET['page']);?>

<div id="poststuff" style="margin-top:10px;">

	<form action="" method="post" enctype="multipart/form-data" name="frmNewsPage" id="frmNewsPage">
	<div class="postbox ">
		<h3>Relate Page & Post</h3>
		<div class="inside">
		<table width="100%" border="0" cellspacing="6" cellpadding="0">
		<tr>
		<td width="173">Pages</td>
		<td width="896">
		
	<select name="page_id" onchange="this.form.submit();">
	  <?php
	  foreach($pages as $page):
		
		if($page->ID==$_POST['page_id'])
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
	  </select>
		
		</td>
		</tr>
		
		<tr>
		<td width="173">Post</td>
		<td width="896">
		
		<select name="post_id[]" multiple="multiple" style="height: 100px; width: 310px;">
		<option value="0">None</option>
		<?php
		foreach($posts as $rowPost):
		
		if(in_array($rowPost->ID,$page_post_id))
		$selected_acc="selected='selected'";
		else
		$selected_acc="";
		?>
		<option <?php echo $selected_acc;?> value="<?php echo $rowPost->ID;?>">
		
		<?php echo $rowPost->post_title;?>
		
		</option>
		<?php
		endforeach;
		?>
		</select>
		
		</td>
		</tr>
		</table>

		</div>
	</div>
	
	<div class="submitbox" id="submitpost">
		<input type="hidden" name="save" />
 		<input name="submit_page" type="submit" class="button button-highlighted" tabindex="4" value="Submit" />
	</div>
	<br/>
	</form>
	
	
</div>




</div>