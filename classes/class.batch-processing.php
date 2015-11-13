<?php

class Leads_Batch_Processor {

    /**
     * Leads_Batch_Processor constructor.
     */
    public function __construct(){
        self::load_hooks();
    }

    public static function load_hooks(){
        add_action( 'admin_init' , array( __CLASS__ , 'batch_processing_init_listener'));
    }

    /**
     * Listens for batch processing
     */
    public static function batch_processing_init_listener() {
        self::import_events_table_112015();
    }

    public static function import_events_table_112015() {

    }
}

new Leads_Batch_Processor();