<?php
/*
Plugin Name: Star-circuit
Description: ระบบงานซ่อม
Version:     1.0
Author:      Supoth
Author URI:  https://www.bosszi.com
*/

define('starcircit', plugin_dir_path( __FILE__ )); // กำหนดทีอยู่สำหรับ pluginของเราเวลาใช้งานจะง่ายและสั้น /home/user/var/www/wordpress/wp-content/plugins/my-plugin/

/** สร้าง Database และข้อมูลปลอม */
include( starcircit . 'job-repair/job_install.php');    // นำ includes/starcurcuit_install.php เข้ามาประมวลผล
register_activation_hook(__FILE__, 'job_install');
register_activation_hook(__FILE__, 'job_install_data');

include( starcircit . 'menu/menu.php');         /**สร้างเมนู และ bootstrap ******/

include( starcircit . 'job-repair/job_table.php');    
include( starcircit . 'job-repair/job_render_list.php');    
include( starcircit . 'job-repair/job_viu.php'); 

function job_languages()   // น่าจะเกี่ยวกับภาษาอ่านยังไม่เข้าใจ
{
    load_plugin_textdomain('', false, dirname(plugin_basename(__FILE__)));
    /** น่าจะเหมือน $mysqli -> set_charset("utf8"); คำสั่งนี้ใส่ก่อนที่จะดึงข้อมูลจากด้าต้าเบสเพื่อให้ข้อมูลที่ออกมาเป็น utf8 ไม่เป็นภาษาต่างดาวหรือ ??? */
}
add_action('init', 'job_languages');

?>