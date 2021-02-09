<?php
/*
Plugin Name: Customers-CRUD-Wordpress
Description: ทดลอง CRUD
Version:     1.0
Author:      Supoth
Author URI:  https://www.bosszi.com
*/

define('crud_bosszi', plugin_dir_path( __FILE__ )); // กำหนดทีอยู่สำหรับ pluginของเราเวลาใช้งานจะง่ายและสั้น /home/user/var/www/wordpress/wp-content/plugins/my-plugin/

/** สร้าง Database และข้อมูลปลอม */
include( crud_bosszi . 'includes/crud_install.php');                // นำ includes/crud_install.php เข้ามาประมวลผล
register_activation_hook(__FILE__, 'customers_crud_install');
register_activation_hook(__FILE__, 'customers_crud_install_data');

/**สร้างเมนู******/
include( crud_bosszi . 'includes/menu.php');

// เตรียมข้อมูลโครงสร้างของตาราง
include( crud_bosszi . 'includes/table.php');

/**  แสดงข้อมูล */
include( crud_bosszi . 'includes/showcustomer.php');

/** insert และ update*/
include( crud_bosszi . 'includes/pagehandler.php');

function customer_languages()   // น่าจะเกี่ยวกับภาษาอ่านยังไม่เข้าใจ
{
    load_plugin_textdomain('', false, dirname(plugin_basename(__FILE__)));
}
add_action('init', 'customer_languages');