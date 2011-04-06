<?php

/**
	Plugin Name: NOAA Weather
	Plugin URI: http://www.berneman.com/noaa-weather
	Description: Display the current NOAA weather in the sidebar.  Be sure to set your NOAA Code!
	Version: 1.0.5
	Author: Tim Berneman
	Author URI: http://www.berneman.com
	License: GPL2

		Copyright 2010-2011  Tim Berneman  (email: tim@berneman.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
	Credits:
		Thanks to Justin Tadlock and his article "The complete guide to creating widgets in WordPress 2.8"
		and to the Wordpress codex which helped me to write this, my first widget. Justins' article can
		be found here: http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28
*/

/**
	Load our widget and css file and register our hooks.
*/
register_activation_hook( __FILE__ , 'activate_NOAA_Weather_widget' );
register_deactivation_hook( __FILE__ , 'deactivate_NOAA_Weather_widget' );
add_action( 'widgets_init' , 'init_NOAA_Weather_widget' );
add_action( 'widgets_init' , 'load_NOAA_Weather_widget' );
add_action( 'Get_NOAA_Weather' , 'Get_NOAA_Weather_File' );

function activate_NOAA_Weather_widget() {
	// Schedule cron entry to download the weather file
	wp_schedule_event( time() , 'twicehourly' , 'Get_NOAA_Weather' );
	// Get files for any codes currently used
	Get_NOAA_Weather_File();
}

function deactivate_NOAA_Weather_widget() {
	// Remove cron entry that downloads the weather file
	wp_clear_scheduled_hook( 'Get_NOAA_Weather' );
}

function init_NOAA_Weather_widget() {
	// Register our stylesheet
	wp_enqueue_style( 'NOAA_Weather_Widget_Stylesheet' , WP_PLUGIN_URL . '/noaa-weather/noaa-weather.css' );
}

function load_NOAA_Weather_widget() {
	register_widget( 'NOAA_Weather_Widget' );
}

/**
 * Call curl with each weather code
 */
function Get_NOAA_Weather_File() {
	//Look in options for all codes to retrieve weather for, disregarding duplicates
	$options = get_option( "widget_noaa_weather" );
	$codes = array(); // holder for codes to check for duplicates
	foreach ( $options as $key => $value ) {
		if ( is_array($value) ) {
			$code = $options[$key]["noaa_code"];
			if( $code <> null ){
				if( !in_array($code,$codes) ){
					$codes[] = $code;
					Get_NOAA_Weather_File_With_Curl( $code );
				}
			}
		}
	}
}

/**
 * Use curl to get weather file according to the code
 */
function Get_NOAA_Weather_File_With_Curl( $code ) {
	$ch = curl_init( "http://www.weather.gov/xml/current_obs/{$code}.xml" );
	$fp = fopen(dirname( __FILE__) . "\weather-current-{$code}.xml", "w" );
	curl_setopt( $ch, CURLOPT_FILE, $fp );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_exec( $ch );
	curl_close( $ch );
	fclose( $fp );
}

function define_cron_schedule( $schedules ) {
	// add a 'twicehourly' schedule to the existing set
	$schedules['twicehourly'] = array(
		'interval' => 1800,
		'display' => __('Twice Hourly')
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'define_cron_schedule' );

/**
 * Create our widget.
 */
class NOAA_Weather_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function NOAA_Weather_Widget() {
		$widget_ops = array( 'classname' => 'noaa_weather', 'description' => __('Display the current NOAA weather in the sidebar.' ));
		$control_ops = array( 'width' => 400, 'height' => 350, 'id_base' => 'noaa_weather' );
		$this->WP_Widget( 'noaa_weather' , __('NOAA Weather') , $widget_ops , $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$noaa_title = apply_filters( 'widget_title' , $instance['noaa_title'] );
		$noaa_code = $instance['noaa_code'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $noaa_title )
			echo $before_title . $noaa_title . $after_title;
		else
			echo $before_title . "NOAA Weather" . $after_title;

		/* Display name from widget settings. */
		if ( $noaa_code ) {
			$xml = @simplexml_load_file(dirname(__FILE__) . "/weather-current-".$noaa_code.".xml");
			if($xml === false)
				echo("Weather Unavailable or invalid NOAA code.");
			else {
				$wind_full = array( "Northeast" , "Northwest" , "Southeast" , "Southwest" );
				$wind_abbr = array( "NE" , "NW" , "SE" , "SW" );
				echo("<div id='noaa-weather'>");
				echo("<p class='noaa_loc'>".$xml->location."</p>");
				echo("<p class='noaa_update'>".$xml->observation_time."</p>");
				echo("<p class='noaa_link'>Weather by <a href='".$xml->credit_URL."' title='".htmlentities($xml->credit,ENT_QUOTES)."' target='_blank'>NOAA</a>"."</p>");
				echo("<p class='noaa_current'>Current Conditions: ".$xml->weather."</p>");
				echo("<p class='noaa_icon'><a href='http://forecast.weather.gov/MapClick.php?lat=".$xml->latitude."&amp;lon=".$xml->longitude."' title='Click for your 5-day forecast.' target='_blank'><img src='".$xml->icon_url_base.$xml->icon_url_name."' alt='NOAA Icon'/></a>"."</p>");
				echo("<p class='noaa_temp'><span>Temp: </span>".round($xml->temp_f)."&deg;F</p>");
				echo("<p class='noaa_wind'><span>Wind: </span>".str_ireplace($wind_full,$wind_abbr,$xml->wind_dir)." at ".round($xml->wind_mph)."mph</p>");
				echo("<p class='noaa_humidity'><span>Humidity: </span>".$xml->relative_humidity."%</p>");
				if( isset($xml->windchill_f) ) {
					echo("<p class='noaa_windchill'><span>Windchill: </span>".$xml->windchill_f."&deg;F</p>");
				}else{
					echo("<p class='noaa_dewpoint'><span>Dewpoint: </span>".$xml->dewpoint_f."&deg;F</p>");
				}
				echo("<p class='noaa_forecast'><a href='http://forecast.weather.gov/MapClick.php?lat=".$xml->latitude."&amp;lon=".$xml->longitude."' title='Click for your 5-day forecast.' target='_blank'>Your 5-Day Forecast at a Glance</a></p>");
				echo("</div>");
			}
		}else{
			echo '<p>No NOAA Code Found.</p>';
		}

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Trim and strip tags for user provided data */
		$newtitle = trim(strip_tags($new_instance['noaa_title']));
		$newcode = strtoupper(trim(strip_tags($new_instance['noaa_code'])));

		/* Update the widget settings. */
		$instance['noaa_title'] = $newtitle;
		$instance['noaa_code'] = $newcode;

		/* Call the function to get the weather file immediately for this code if not blank*/
		if(strlen($newcode) > 0) {
			Get_NOAA_Weather_File_With_Curl( $newcode );
		}
		
		return $instance;
	}

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'noaa_title' => 'NOAA Weather', 'noaa_code' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'noaa_title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'noaa_title' ); ?>" name="<?php echo $this->get_field_name( 'noaa_title' ); ?>" value="<?php echo $instance['noaa_title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'noaa_code' ); ?>">NOAA Code:</label>
			<input id="<?php echo $this->get_field_id( 'noaa_code' ); ?>" name="<?php echo $this->get_field_name( 'noaa_code' ); ?>" value="<?php echo $instance['noaa_code']; ?>" style="width:100%;" />
		</p>
		<p class='description'>
			Find your code <a href="http://www.weather.gov/xml/current_obs/" target="_blank">here</a> by selecting your state from the dropdown list and then click the 'Find' button. On the next screen find your 'Observation Location' and the code you need is in parenthesis after your location name.
		</p>

	<?php
	}

}

function log_noaa( $msg ) {
	$fh = fopen( dirname(__FILE__)."/noaa-weather.log", "a" ) or die( "Error opening file." );
	fwrite( $fh, "[" . date("d/m/Y h:i.sa") . "] " . $msg."\r\n" );
	fclose( $fh );
}

?>
