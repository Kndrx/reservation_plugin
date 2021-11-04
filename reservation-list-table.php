<?php
/**
 * Create a new table class that will extend the WP_List_Table
 */
class ReservationListTable extends WP_List_Table
{
    /*
    /* GET DATA
    */
    public function get_data(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservations';
        return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'id' => 'Identifiant',
            'first_name' => 'Prénom',
            'last_name' => 'Nom de Famille',
            'phone'        => 'N° de Téléphone',
            'event_id'    => 'Evènement',
        );
        return $columns;
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */

	public function get_sortable_columns() {
		return array(
			'id' => array('id', false),
		);
	}

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item,$column_name)
    {
        switch( $column_name ) {
            case 'id':
            case 'first_name':
            case 'last_name':
            case 'phone':
            case 'event__id':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a,$b)
    {
        // Set defaults
        $orderby = 'id';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }

        $result = strcmp($a[$orderby],$b[$orderby]);

        if($order === 'asc')
        {
            return $result;
        }

        return -$result;
    }


    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->get_data();
        usort($data, array($this,'sort_data'));

        $this->_column_headers = array($columns, array(), $sortable);
        $this->items = $data;
    }
}
?>