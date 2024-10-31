
<div class="section">
 <h2 class="green"><?php echo $related_title;?></h2>
 <?php


 $args=array();
 $args["numberposts"]=$related_post_number;
 $args["include"]=$post_collection_id;
 
 //digin($args);
 $related_posts = get_posts($args);
 

 if($related_posts):
 ?>
<table cellpadding="0" cellspacing="0" border="0">
	<?php
	foreach( $related_posts as $post ) :
	//digin($post);
	?>
	<tr>
		<td class="tabledate" align="right"><?php echo date("d/m/y",strtotime($post->post_date));?></td>
		<td><a href="<?php echo get_permalink($post->ID);?>"><?php echo $post->post_title;?></a></td>
	</tr>
	<?php
	endforeach;
	?>
</table>
<?php endif; wp_reset_query();?>
</div>