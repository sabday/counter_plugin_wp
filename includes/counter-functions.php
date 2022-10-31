<?php
/*
 * Add new menu in Admin Console
 */

add_action( 'admin_menu', 'Counter_Admin_Link' );
 
// Add New link in menu Admin Console
function Counter_Admin_Link()
{
 add_menu_page(
 'Counter Links', // Title Page
 'Counter Links', // Text link in menu
 'manage_options', // Option Link
 'countlinks/includes/counter-form-page.php' // 'slug'
 );
}

