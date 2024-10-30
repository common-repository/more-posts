<?php
/**
 * Plugin Name: More Posts
 * Plugin URI: https://wordpress.org/plugins/more-posts/
 * Description: Appends links to other posts under a particular category.
 * Author: Bimal Poudel
 * Author URI: http://bimal.org.np/
 * Development URI: https://github.com/bimalpoudel/more-posts/
 * License: GPLv2 or later
 * Version: 1.0.0
 */

class more_posts
{
	public function init()
	{
		/**
		 * On-Demand call
		 */
		add_shortcode('mysitemap', array($this, '_shortcode_mysitemap'));
		
		/**
		 * Automatically include other posts within the same category
		 */
		add_filter('the_content', array($this, '_category_page_name'));
	}
	
	/**
	 * @example [mysitemap]: Prints everything
	 * @example [mysitemap id="5"]: restricts to a category
	 * @example [mysitemap id="5" limit="5"]: Limits 5 posts under a given category
	 * @example [mysitemap limit="5"]: Limits 5 globally recent posts
	 */
	public function _shortcode_mysitemap($attributes = array())
	{
		$content = '';

		if(!is_array($attributes)) $attributes = array();

		$attributes = array_map('esc_attr', $attributes);
		$standard_attributes = array(
			'id' => '0',
			'limit' => '9999',
			'title' => 'Category Sitemap',
		);
		$attributes = shortcode_atts($standard_attributes, $attributes);
		
		$category_ids = 

		$args = array();
		$args['post_status'] = 'publish';
		
		if($attributes['id'])
		{
			$args['category__in'] = array($attributes['id']);
		}
		
		/**
		 * When missing, brings default/all: 9999
		 */
		if($attributes['limit'])
		{
			$args['posts_per_page'] = $attributes['limit'];
			$args['post_limits'] = $attributes['limit'];
		}

		$queries = new WP_Query( $args );

		$li = array();
		while ( $queries->have_posts() )
		{
			$queries->the_post();
			$permalink = get_the_permalink($queries->post->ID);
			$li[] = '<li><a href="'.$permalink.'">' . get_the_title( $queries->post->ID ) . '</a></li>';
		}
		if(!count($li)) return $content;

		$links = implode('', $li);
		
		
	$content .= "
	<h2 class='more-posts' style='margin-top: 30px;'>{$attributes['title']}</h2>
	<div><ol>{$links}</ol></div>
	";
		return $content;
	}
	
	/**
	 * Auto Append to the Post Detail in Singular View
	 */
	public function _category_page_name($content)
	{
		#if(!is_single()) return $content;
		if(!is_singular('post')) return $content;
		
		$categories = get_the_category();
		$category_ids = array(0);
		foreach($categories as $category)
		{
			$category_ids[] = $category->cat_ID;
		}
		
		$args = array(
			'posts_per_page' => '50',
			'category__in' => $category_ids,
		);
		$queries = new WP_Query( $args );

		$li = array();
		while ( $queries->have_posts() )
		{
			$queries->the_post();
			$permalink = get_the_permalink($queries->post->ID);
			$li[] = '<li><a href="'.$permalink.'">' . get_the_title( $queries->post->ID ) . '</a></li>';
		}
		if(!count($li)) return $content;

		$links = implode('', $li);
		
		$category_link = esc_url(get_category_link($categories[0]->cat_ID));
		$content .= "
	<h2 class='more-posts' style='margin-top: 30px;'>More from: <a href='{$category_link}'>{$categories[0]->name}</a></h2>
	<div><ol>{$links}</ol></div>
	";
		return $content;
	}
}

$more_posts = new more_posts();
$more_posts->init();
