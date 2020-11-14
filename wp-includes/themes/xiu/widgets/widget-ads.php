<?php 
class widget_ads extends WP_Widget {

	function __construct(){
		parent::__construct( 'widget_ads', 'XIU 广告', array( 'classname' => 'widget_ssr' ) );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_name', $instance['title']);
		$code = $instance['code'];

		echo $before_widget;
		echo '<div class="widget_ssr_inner">'.$code.'</div>';
		echo $after_widget;
	}

	function form($instance) {
		$defaults = array( 
			'title' => __('广告', 'haoui').' '.date('m-d'), 
			'code' => '<a href="http://www.thefox.cn/alibaixiu.shtml" target="_blank"><img src="http://www.daqianduan.com/wp-content/uploads/2014/09/alibaixiu.png"></a>' 
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<p>
			<label>
				<?php echo __('标题：', 'haoui') ?>
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" class="widefat" />
			</label>
		</p>
		<p>
			<label>
				<?php echo __('广告代码：', 'haoui') ?>
				<textarea id="<?php echo $this->get_field_id('code'); ?>" name="<?php echo $this->get_field_name('code'); ?>" class="widefat" rows="12" style="font-family:Courier New;"><?php echo $instance['code']; ?></textarea>
			</label>
		</p>
<?php
	}
}
