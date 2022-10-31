<?php
/*
Plugin Name: Count Link
Plugin URI: http://sabday.com
Description: Plugin count for links
Version: 1.0
Author: Semenovsky Arthur
Author URI: http://sabday.com
License: GPL2
*/


////////////////////////////////////////////Create Table///////////////////////////////////////////////////////
function counterlinks_table()
{

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $tablename = $wpdb->prefix . "counterlinks";

    $sql = "CREATE TABLE $tablename (
  id int(11) NOT NULL AUTO_INCREMENT,
  counter varchar(255) NOT NULL,  
  PRIMARY KEY  (id)
  ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $counter_number = 0;
    $insert_sql = "INSERT INTO ".$tablename."(counter) values('".$counter_number."') ";
    $wpdb->query($insert_sql);

}

register_activation_hook(__FILE__, 'counterlinks_table');
////////////////////////////////////////End Create Table///////////////////////////////////////////////////////

////////////////////////////////////////////Styles for notification////////////////////////////////////////////
add_action( 'admin_print_styles', 'hide_btn_editor');
function hide_btn_editor () {
    echo '<style>
                .editor-post-publish-button {
                    display: none; 
                }
                
                .edit-post-header__settings {
                    position: relative !important;
                }
                
                .test-warning-message {
                    position: absolute !important;      
                    right: 33% !important;
                    bottom: -10px !important;
                    color: red;
                }
                .test-succsses-message {
                    position: absolute !important;      
                    right: 33% !important;
                    bottom: -10px !important;
                    color: green;
                }
            </style>';
}
////////////////////////////////////////End Styles for notification////////////////////////////////////////////

////////////////////////////////////////////First Start Counter Links//////////////////////////////////////////
add_action( 'the_content', 'first_start_content' );
function first_start_content($content) {

    global $wpdb;
    $tablename = $wpdb->prefix."counterlinks";
    $counter_number_db = "";

    $check_data = $wpdb->get_results("SELECT * FROM  " .$tablename." WHERE id =  1");
    if(count($check_data) > 0){
        foreach($check_data as $entry){
            $counter_number_db = $entry->counter;
        }
    }else{
        echo "Not Found";
    }

    preg_match_all( '/<a(.*?)<\/a>/is', $content, $linksArray);

    $arrayLinks = array();
    foreach ($linksArray[1] as $link)
    {
        if (strpos($link, get_site_url()) !== false)
        {
            $arrayLinks[] = $link;
        }
    }
    $tottal_links = count($arrayLinks);

    if ($tottal_links < $counter_number_db)
    {
        add_action('admin_enqueue_scripts', 'start_lock_btn');
    }
    else
    {
        add_action('admin_enqueue_scripts', 'start_unlock_btn');
    }
}
////////////////////////////////////////End First Start Counter Links//////////////////////////////////////////

/////////////////////////////First Start Lock Publish and Editor Button////////////////////////////////////////
function start_lock_btn() {
    ?>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            let editorButton = document.querySelector('.editor-post-publish-button');
            let publishButton = document.querySelector('.editor-post-publish-panel__toggle');

            //const editorButton = document.querySelector('.editor-post-publish-button');
            if (editorButton)
            {
                editorButton.setAttribute('aria-disabled', 'true');
                editorButton.setAttribute('disabled', 'true');
            }


            //const publishButton = document.querySelector('.editor-post-publish-panel__toggle');
            if (publishButton)
            {
                publishButton.setAttribute('aria-disabled', 'true');
            }

            const sign = document.createElement('span');
            sign.classList.add('test-warning-message');
            sign.textContent = 'Need more links';
            //console.log(document.querySelector('.edit-post-header__settings'));
            document.querySelector('.edit-post-header__settings').append(sign);
        });
    </script>
    <?php
}
/////////////////////////End First Start Lock Publish and Editor Button////////////////////////////////////////

/////////////////////////////First Start Unlock Publish and Editor Button//////////////////////////////////////
function start_unlock_btn() {
    ?>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            let editorButton = document.querySelector('.editor-post-publish-button');
            let publishButton = document.querySelector('.editor-post-publish-panel__toggle');

            //const editorButton = document.querySelector('.editor-post-publish-button');
            if (editorButton)
            {
                editorButton.setAttribute('aria-disabled', 'false');
                editorButton.setAttribute('disabled', 'false');
            }


            //const publishButton = document.querySelector('.editor-post-publish-panel__toggle');
            if (publishButton)
            {
                publishButton.setAttribute('aria-disabled', 'false');
            }

            const sign = document.createElement('span');
            sign.classList.add('test-succsses-message');
            sign.textContent = 'Done!';
            //console.log(document.querySelector('.edit-post-header__settings'));
            document.querySelector('.edit-post-header__settings').append(sign);
        });
    </script>
    <?php
}
/////////////////////////End First Start Unlock Publish and Editor Button//////////////////////////////////////

/////////////////Button to lock and unlock publishing and editing when content is clicked//////////////////////
add_action( 'admin_print_footer_scripts', 'my_action_javascript', 99 );
function my_action_javascript() {
    ?>
    <script>
        function lock_unlock_btn(param){
            let editorButton = document.querySelector('.editor-post-publish-button');
            let publishButton = document.querySelector('.editor-post-publish-panel__toggle');
            let wanrning_msg = document.querySelector('span.test-warning-message');
            let succsses_msg = document.querySelector('span.test-succsses-message');

            if (param == 1)
            {
                if (editorButton)
                {
                    editorButton.setAttribute('aria-disabled', 'true');
                    editorButton.setAttribute('disabled', 'true');
                }

                if (publishButton)
                {
                    publishButton.setAttribute('aria-disabled', 'true');
                    //publishButton.setAttribute('disabled', 'true');
                }

                console.log("Lock Button");

                if (!document.querySelector('span.test-warning-message')){
                    let sign = document.createElement('span');
                    sign.classList.add('test-warning-message');
                    sign.textContent = 'Need more links';
                    console.log(document.querySelector('.edit-post-header__settings'));
                    document.querySelector('.edit-post-header__settings').append(sign);
                    wanrning_msg.style.display = 'block';
                    succsses_msg.style.display = 'none';
                }
                else
                {
                    wanrning_msg.style.display = 'block';
                    succsses_msg.style.display = 'none';
                }
            }
            if (param == 2)
            {
                console.log(editorButton);
                if (editorButton)
                {
                    editorButton.setAttribute('aria-disabled', 'false');
                    editorButton.setAttribute('disabled', 'false');
                }

                if (publishButton)
                {
                    publishButton.setAttribute('aria-disabled', 'false');
                }

                console.log("Unlock Button");


                if (wanrning_msg && !document.querySelector('span.test-succsses-message')) {
                    wanrning_msg.style.display = 'none';
                    const sign_succsses = document.createElement('span');
                    sign_succsses.classList.add('test-succsses-message');
                    sign_succsses.textContent = 'Done!';
                    console.log(document.querySelector('.edit-post-header__settings'));
                    document.querySelector('.edit-post-header__settings').append(sign_succsses);
                }
                else
                {
                    wanrning_msg.style.display = 'none';
                    succsses_msg.style.display = 'block';
                }
            }
        }

        jQuery(document).keydown(function(event) {
            let t = $('.edit-post-visual-editor');
            let str = t.html();

            let data = {
                action: 'my_action',
                whatever: str
            };

            //alert( 'Получено с сервера: ' + str );

            // с версии 2.8 'ajaxurl' всегда определен в админке
            jQuery.post( ajaxurl, data, function( response ){
                //alert( 'Получено с сервера: ' + response );
                if (response == 1)
                {
                    lock_unlock_btn(1);
                }
                else {
                    lock_unlock_btn(2);
                }
            } );
        });

        jQuery(document).mouseover(function(event) {
            let t = $('.edit-post-visual-editor');
            let str = t.html();

            let data = {
                action: 'my_action',
                whatever: str
            };

            //alert( 'Получено с сервера: ' + str );

            // с версии 2.8 'ajaxurl' всегда определен в админке
            jQuery.post( ajaxurl, data, function( response ){
                //alert( 'Получено с сервера: ' + response );
                if (response == 1)
                {
                    lock_unlock_btn(1);
                }
                else {
                    lock_unlock_btn(2);
                }
            } );
        });

    </script>
    <?php
}
/////////////End Button to lock and unlock publishing and editing when content is clicked//////////////////////

//////////////////////////////////////////Counter Links and return result//////////////////////////////////////
add_action( 'wp_ajax_my_action', 'my_action_callback' );
function my_action_callback(){
    global $wpdb;
    $tablename = $wpdb->prefix."counterlinks";
    $counter_number_db = "";

    $check_data = $wpdb->get_results("SELECT * FROM  " .$tablename." WHERE id =  1");
    if(count($check_data) > 0){
        foreach($check_data as $entry){
            $counter_number_db = $entry->counter;
        }
    }else{
        echo "Not Found";
    }

    $content = ($_POST['whatever'] );
    preg_match_all( '/<a(.*?)<\/a>/is', $content, $linksArray);

    $arrayLinks = array();
    foreach ($linksArray[1] as $link)
    {
        if (strpos($link, get_site_url()) !== false)
        {
            $arrayLinks[] = $link;
        }
    }
    $tottal_links = count($arrayLinks);

    if ($tottal_links < $counter_number_db)
    {
        echo "1";
    }
    else {
        echo "2";
    }

    //echo $tottal_links;
    wp_die();
}
//////////////////////////////////////End Counter Links and return result//////////////////////////////////////
 // Include counter-functions.php
require_once plugin_dir_path(__FILE__) . 'includes/counter-functions.php';

?>


