<?php
/*
Plugin Name: Flexible Recent and Related Post
Version: 0.1
Author: Rameshwor Maharjan
Author URI: http://webavenue.com.au
Plugin URI: http://wordpress.org/plugins/recent-post-widget-by-category/
Description: Sidebar widget to display recent post from specified category.
*/

// Avoid name collisions.
if ( !class_exists('RPF') ) :


class RPF
{
	var $plugin_url;
	var $plugin_dir;

	var $page;
	var $wpdb;
	
	var $post_tree;
	
	var $RPF_db_version = "1.0";
	
	function RPF()
	{
		
		$this->plugin_url = trailingslashit( WP_PLUGIN_URL.'/'.dirname( plugin_basename(__FILE__) ));		
		$this->plugin_dir = trailingslashit( WP_PLUGIN_DIR.'/'.dirname( plugin_basename(__FILE__) ));
		
		
		global $wp_version;
		
		$this->wpdb =& $GLOBALS["wpdb"];
		

		
		if (version_compare($wp_version,"3","<"))
		{
			exit ($exit_msg);
		}
		
		$this->page=$_GET['page'];
		
		add_action('init', array(&$this,'RPF_Init'));
		
		add_shortcode('get_news', array(&$this, 'RPF_get_news'));
		
		add_action('admin_menu', array(&$this, 'admin_menu'));
		
	}
	
	
	function RPF_WidgetControl()
	{
		// get saved options
		$options = get_option('wp_rpf');
		// handle user input
		if ( $_POST["rpf_submit"] )
		{
			$options['rpf_title'] = strip_tags( stripslashes($_POST["rpf_title"] ) );
			$options['rpf_category_id'] = strip_tags( stripslashes($_POST["rpf_category_id"] ) );
			$options['rpf_post_id'] = implode(",",$_POST["rpf_post_id"]);
			$options['rpf_post_number'] = strip_tags( stripslashes($_POST["rpf_post_number"] ) );
			update_option('wp_rpf', $options);
		}
		
		$rpf_title = $options['rpf_title'];
		$rpf_category_id = $options['rpf_category_id'];
		$rpf_post_id = explode(",",$options['rpf_post_id']);
		$rpf_post_number = $options['rpf_post_number'];

		
		// print out the widget control
		$this->post_tree=array();
		$pages=$this->get_post_tree(0,0,"page");
		include('rpf-widget-control.php');
	}
	
	
	
	

	function RPF_Widget($args = array())
	{
		// extract the parameters
		extract($args);
		// get our options
		$options=get_option('wp_rpf');
		
		
		
		if(!empty($options["rpf_post_id"]))
		{
			global $post;
			$exclude_post_page=explode(",",$options["rpf_post_id"]);
			if(in_array($post->ID,$exclude_post_page))
			$widget_display=false;
			else
			$widget_display=true;

		}
		else
		$widget_display=true;
		
		if($widget_display)
		{
		
			$rpf_title=$options['rpf_title'];
			$rpf_category_id=$options['rpf_category_id'];
			$rpf_post_number=$options['rpf_post_number'];
			
			// print the theme compatibility code
			echo $before_widget;
			// include our widget
			include('rpf-widget.php');
			echo $after_widget;
		}
	}
	
	
	function Related_WidgetControl()
	{
		// get saved options
		$options = get_option('wp_rpf');
		// handle user input
		if ( $_POST["rpf_submit"] )
		{
			$options['related_title'] = strip_tags( stripslashes($_POST["related_title"] ) );
			$options['related_post_number'] = strip_tags( stripslashes($_POST["related_post_number"] ) );
			update_option('wp_rpf', $options);
		}
		
		$related_title = $options['related_title'];
		$related_post_number = $options['related_post_number'];

		include('related-widget-control.php');
	}
	

	function Related_Widget($args = array())
	{
		// extract the parameters
		extract($args);
		// get our options
		$options=get_option('wp_rpf');
		
		$related_title = $options['related_title'];
		$related_post_number = $options['related_post_number'];
		
		global $post;

		$arrayRelatedPost=	$this->get_post_by_page_id($post->ID);
		
		if(count($arrayRelatedPost)>0)
		{
			$post_collection_id=implode(",",$arrayRelatedPost);
			include('related-widget.php');
		}
	}
	
	
	
	
	function RPF_Init()
	{
		// register widget
		register_sidebar_widget('Flexible Recent Post', array(&$this,'RPF_Widget'));
		register_widget_control('Flexible Recent Post', array(&$this,'RPF_WidgetControl'));
		
		
		register_sidebar_widget('Flexible Related Post', array(&$this,'Related_Widget'));
		register_widget_control('Flexible Related Post', array(&$this,'Related_WidgetControl'));
		
	}
	
	function install()
	{
		
		$table_name = "wp_post_page_link";
		if($this->wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			
		$sql="CREATE TABLE `wp_post_page_link` (
		`ID` bigint(20) NOT NULL auto_increment,
		`page_id` bigint(20) default NULL,
		`post_id` bigint(20) default NULL,
		PRIMARY KEY  (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			dbDelta($sql);
		
		}
			 
		
		 
		add_option("RPF_db_version", $this->RPF_db_version);
		 
	}
	
	
	function admin_menu()
	{
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page('RPF', 'RPF', 'my-rpf', 'my-rpf-top-level-handle','');

		add_submenu_page('my-rpf-top-level-handle', 'Link Page & Post', 'Link Page & Post', 1 ,'link-page-post', array(&$this, 'admin_router') );

	}
	
	function admin_router()
	{
		switch ($this->page) :
	
		
		case 'link-page-post':
		include( 'link-page-post.php');
		break;
		
		endswitch;
	}
	
	function get_post_tree($origin_id,$level,$post_type="page")
	{
		$query="select * from wp_posts where post_parent=$origin_id and post_type='$post_type' and (post_status='publish') order by menu_order asc";
	
		$rsPost=$this->wpdb->get_results($query);
		
		if(count($rsPost)>0)
		{
			foreach($rsPost as $rowPost)
			{
			
				
					$this->post_tree[]=(object)array("ID"=>stripslashes($rowPost->ID),
											 "post_type"=>stripslashes($rowPost->post_type),
											 "post_parent"=>$rowPost->post_parent,
											 "post_title"=>$rowPost->post_title,
											 "level"=>$level);
		
				$this->get_post_tree($rowPost->ID,$level+1);
				
			}
			
		}
		
		return $this->post_tree;
	}
	
	function get_post_by_page_id($page_id)
	{
		$query="select * from wp_post_page_link where page_id=".$page_id;
		
		//echo $query;

		$rsPosts=$this->wpdb->get_results($query);
		
		$page_post_id=array();
		if(isset($rsPosts))
		{
			foreach($rsPosts as $val)
			$page_post_id[]=$val->post_id;
		}

		return $page_post_id;

	}
	
	
}
endif;
	
if ( class_exists('RPF') ):
$RPF = new RPF();

if (isset($RPF))
{
	register_activation_hook( __FILE__, array(&$RPF,'install') );
	
}

endif;
?>