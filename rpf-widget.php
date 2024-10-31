<div class="section">
 <h2 class="green"><?php echo $rpf_title;?></h2>
 <?php

 $args=array();
 $args["numberposts"]=$rpf_post_number;
 $args["category"]=$rpf_category_id;
 
 
 $recent_posts = get_posts($args);
 

 if($recent_posts):
 ?>
<table cellpadding="0" cellspacing="0" border="0">
	<?php
	$display_id= $post->ID;
	foreach( $recent_posts as $post ) :
	//digin($post);
	?>
	<tr>
		<td class="tabledate" align="right"><?php echo date("d/m/y",strtotime($post->post_date));?></td>
		<td><a <?php echo($display_id==$post->ID?"class='active'":""); ?> href="<?php echo get_permalink($post->ID);?>"><?php echo $post->post_title;?></a></td>
	</tr>
	<?php
	endforeach;
	?>
</table>
<?php endif; wp_reset_query();?>
</div>