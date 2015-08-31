
<?php
/**
 * Inbound Marketing Button in editor
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Inbound_Template_Utils {

    public function __construct() {
        self::init();
    }

    public function init() {
        /* add extra menu items */
        add_action('admin_menu', array( __CLASS__ , 'add_screen' ) );

        //if (isset($_GET['inbound-template-gen'])) {
            //add_action( 'admin_head', array(__CLASS__, 'html'));
        //}

    }

    static function add_screen() {
        add_submenu_page(
            'edit.php?post_type=landing-page',
            __( 'Generate Template' , 'leads' ),
            __( 'Generate Template' , 'leads' ),
            'manage_options',
            'template_utils',
            array( __CLASS__ , 'html' )
        );
    }
    /* Inbound now */
    static function custom_generate() {

        // vars
        $json = self::get_json();


        // validate
        if( $json === false ) {

            acf_add_admin_notice( __("No field groups selected", 'acf') , 'error');
            return;

        }
        $data = array();
        $data['field_groups'] = $json;

    }
    static function get_json() {

        //$keys = $_POST['acf_export_keys'];
        //$keys = array('group_55e23ad63ecc3');
        //$keys = array('group_55d38b033048e');
        $keys = array('group_55d26a506a990');

        // validate
        if( empty($keys) ) {

            return false;

        }


        // vars
        $json = array();


        // construct JSON
        foreach( $keys as $key ) {

            // load field group
            $field_group = acf_get_field_group( $key );


            // validate field group
            if( empty($field_group) ) {

                continue;

            }


            // load fields
            $field_group['fields'] = acf_get_fields( $field_group );


            // prepare fields
            $field_group['fields'] = acf_prepare_fields_for_export( $field_group['fields'] );


            // extract field group ID
            $id = acf_extract_var( $field_group, 'ID' );


            // add to json array
            $json[] = $field_group;

        }


        // return
        return $json;

    }
    /*
     There are two places the marketing button renders:
     in normal WP editors and via JS for ACF normal
     */

    static function html($args) {

        /* get the data */
        $json = self::get_json();


        // validate
        if( $json === false ) {

            acf_add_admin_notice( __("No field groups selected", 'acf') , 'error');
            return;

        }

        // vars
        $field_groups = $json;
        /* Todo intercept and update the special key here */
        //print_r($json); exit;
        ?>
        <div class="wrap acf-settings-wrap">

            <div id="options-available">

                <?php

                // vars
                $choices = array();
                $field_groups_ids = acf_get_field_groups();


                // populate choices
                if( !empty($field_groups_ids) )
                {
                    foreach( $field_groups_ids as $field_group )
                    {
                        $choices[ $field_group['key'] ] = $field_group['title'];
                    }
                }


                // render field
                acf_render_field(array(
                    'type'      => 'checkbox',
                    'name'      => 'acf_export_keys',
                    'prefix'    => false,
                    'value'     => false,
                    'toggle'    => true,
                    'choices'   => $choices,
                ));

                ?>
            </div>

            <h2><?php _e('Import / Export', 'acf'); ?></h2>

            <div class="acf-box">
                <div class="title">
                    <h3><?php _e('Generate Your Template Output', 'inboundnow'); ?></h3>
                </div>

                <div class="inner">
                <p>This page is for helping developing templating super simple.</p>

                    <?php /*
                        <?php $i = 0; if ( have_rows( 'sections' ) ) : ?>

                            <?php while ( have_rows( 'sections' ) ) : the_row();

                                $title = get_sub_field( 'title' );
                                $content_title = get_sub_field( 'content_title' );
                                $content = get_sub_field( 'content' );
                                $image   = get_sub_field( 'background_image' );
                                $selected = ($i === 0) ? "is-selected" : '';
                                if ( $content && $image ) : ?>
                                <li class="<?php echo $selected;?>"
                                style="background-image:url('<?php echo $image['url'];?>');">
                                    <a href="#0">
                                        <h2><?php echo $title;?></h2>
                                    </a>
                                </li>

                                <?php $i++; endif; ?>

                            <?php endwhile; ?>
                        <?php endif; ?>

     <?php if(function_exists('have_rows')) :

                if(have_rows('page_builder')) :

                    while(have_rows('page_builder')) : the_row();

                        switch(get_row_layout()) :

                            case 'hero_1' :

                                $image = get_sub_field('background_image');?>

                                <?php break;

                            case 'hero_2' :

                                $content_alignment = get_sub_field('alignment'); ?>

                                <?php break;
                            case 'wysiwyg': ?>
                                    <div class="content-module">

                                        <div class="container">

                                            <?php if($content = get_sub_field('content')) : ?>

                                                <?php echo wpautop($content); ?>

                                            <?php endif; ?>

                                        </div>
                                    </div>
                            <?php break;

                            endswitch;

                                endwhile;

                            endif;


                     */ ?>
    <p>This is generated output from your landing page options to copy/paste into your index.php</p>
<textarea style="width:100%; height:500px;">
<?php echo "\$post_id = get_the_ID();". "\r\n"; ?>
<?php

//print_r($field_groups); exit;
foreach( $field_groups as $field_group ) {


    foreach( $field_group['fields'] as $field ) {

        if($field['type'] === "repeater") {
        echo '<?php if ( have_rows( "'.$field['name'].'" ) )  { ?>'. "\r\n\r\n";
        echo '<?php while ( have_rows( "'.$field['name'].'" ) ) : the_row();' . "\r\n";
            $count = count($field['sub_fields']);
            foreach ($field['sub_fields'] as $subfield) {
echo "\t$".$subfield['name']. " = " . "get_sub_field(\"".$subfield['name']."\");"."\r\n";
            }
            echo '?>'."\r\n\r\n";
            echo '<!-- your markup here -->'."\r\n\r\n";
            echo '<?php endwhile; ?>'."\r\n\r\n";
            echo '<?php } /* end if have_rows */ ?>';
        } else if($field['type'] === "flexible_content") {
            echo "if(function_exists('have_rows')) :" ."\r\n";
            echo "\tif(have_rows('page_builder')) :" ."\r\n";
            echo "\t\t while(have_rows('page_builder')) : the_row();" ."\r\n";
            echo "\t\t\t switch(get_row_layout()) :" ."\r\n";
            foreach ($field['layouts'] as $layout) {
                $layout['name'];
                echo "\t\t\t case 'hero_1' : " ."\r\n";
                foreach ($layout['sub_fields'] as $layout_subfield) {
                echo "\t\t\t\t$".$layout_subfield['name']. " = " . "get_sub_field(\"".$layout_subfield['name']."\");"."\r\n";

                }
                echo "\t\t\t?>"."\r\n\r\n";
                echo "\t\t\t<!-- your markup here -->"."\r\n\r\n";
                echo "\t\t\t <?php break;" ."\r\n";
            }
            echo "\t\t\tendswitch; /* end switch statement */ "."\r\n";
            echo "\t\tendwhile; /* end while statement */"."\r\n";
            echo "\t endif; /* end have_rows */"."\r\n";
            echo "endif;  /* end function_exists */"."\r\n";

        } else {
            if($field['name']) {
                echo "\t$".$field['name']. " = " . "get_field(\"".$field['name']."\");"."\r\n";
            }

        }
    }


}
?>
</textarea>

                    <textarea class="pre" readonly="true"><?php

                    echo "if( function_exists('acf_add_local_field_group') ):" . "\r\n" . "\r\n";

                    foreach( $field_groups as $field_group ) {

                        // code
                        $code = var_export($field_group, true);

                        // change double spaces to tabs
                        $code = str_replace("  ", "\t", $code);

                        // correctly formats "=> array("
                        $code = preg_replace('/([\t\r\n]+?)array/', 'array', $code);

                        // Remove number keys from array
                        $code = preg_replace('/[0-9]+ => array/', 'array', $code);

                        // echo
                        echo "acf_add_local_field_group({$code});" . "\r\n" . "\r\n";

                    }

                    echo "endif;";

                    ?></textarea>

                </div>

            </div>

        </div>
        <div class="acf-hidden">
            <style type="text/css">
                textarea.pre {
                    width: 100%;
                    padding: 15px;
                    font-size: 14px;
                    line-height: 1.5em;
                    resize: none;
                }
            </style>
            <script type="text/javascript">
            (function($){

                var i = 0;

                $(document).on('click', 'textarea.pre', function(){

                    if( i == 0 )
                    {
                        i++;

                        $(this).focus().select();

                        return false;
                    }

                });

                $(document).on('keyup', 'textarea.pre', function(){

                    $(this).height( 0 );
                    $(this).height( this.scrollHeight );

                });

                $(document).ready(function(){

                    $('textarea.pre').trigger('keyup');

                });

            })(jQuery);
            </script>
        </div>

    <?php

    }
}
$Inbound_Template_Utils = new Inbound_Template_Utils();

