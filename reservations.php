<?php
/*
Plugin Name: Reservation
Description: take reservation on event
Author: KNDRX
Version: 1.0
*/

// First step : Create Database
function reservation_database() {
	global $wpdb;

	$table_name = $wpdb->prefix . "reservations";

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		first_name varchar(55) NOT NULL,
		last_name varchar(55) NOT NULL,
        event_id mediumint(9) NOT NULL,
        PRIMARY KEY (id)
	) $charset_collate;";

	require_once(ABSPATH . "wp-admin/includes/upgrade.php");
	dbDelta($sql);
	add_option("contact_db_version", "1.0");
}

register_activation_hook(__FILE__, "reservation_database");

// admin
function reservation_to_admin_menu() {
	function reservation_content() {
		echo "<h1>Reservation</h1>";
		echo "<div style='margin-right:20px'>";

		if(class_exists("WP_List_Table")) {
			require_once(ABSPATH . "wp-admin/includes/class-wp-list-table.php");
			require_once(plugin_dir_path( __FILE__ ) . "reservation-list-table.php");
			$reservationListTable = new ReservationListTable();
			$reservationListTable->prepare_items();
			$reservationListTable->display();
		} else {
			echo "WP_List_Table n'est pas disponible actuellement.";
		}
		
		echo "</div>";
	}
    add_menu_page("Reservations", "Reservations", "manage_options", "reservation-plugin", "reservation_content");
}

add_action("admin_menu", "reservation_to_admin_menu");


function reservation_form() {

    $output = reservation_fields();

    return $output;
}

add_shortcode("reservation__form", "reservation_form");


// registration form fields
function reservation_fields() {

    ob_start(); ?>
    <h3 class="form_header"><?php _e("Take reservation"); ?></h3>
    
    <form id="reservation_form" class="form" action="" method="POST">
        <fieldset>

            <p>
                <label for="user_first"><?php _e("First Name"); ?></label>
                <input name="user_first" id="user_first" type="text" class="user_first" />
            </p>
            <p>
                <label for="user_last"><?php _e("Last Name"); ?></label>
                <input name="user_last" id="user_last" type="text" class="user_last"/>
            </p>
            <p>
                <input type="hidden" name="csrf" value="<?php echo wp_create_nonce("csrf"); ?>"/>
                <input type="submit" value="<?php _e("Take your reservation"); ?>"/>
            </p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}

// register a new booking
function add_reservation() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'reservations';

    // $user_first = $_POST["user_first"];
    // $user_last = $_POST["user_last"];

    // $post = get_post();
    // $id = $post->ID;

    // $wpdb->insert(
    //     $table_name,
    //     array(
    //     'first_name' => $user_first,
    //     'last_name' => $user_last,
    //     'event_id' => $id ,
    // ));
    
}

add_action("init", "add_reservation");

// used for tracking error messages
function reservation_errors(){
static $wp_error; // global variable handle
return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}
