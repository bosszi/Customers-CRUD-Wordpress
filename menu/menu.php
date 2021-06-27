<?php

function job_main_menu()
{
    add_menu_page(__('งานซ่อม', ''),             //page title singular เปลี่ยนได้
                    __('งานซ่อม', ''),           //menu title plural เปลี่ยนได้
                    'activate_plugins',         //capabilities User Roles การกำหนดสิทธิ์
                    'job',                //menu slug ถ้าคลิกที่เมนูจะวิ่งมาที่ URL ที่ page=starcircuit เปลี่ยนได้
                    'job_render_list',   //function
                    'dashicons-groups',         // ไอคอนด้านหน้าเมนู icon_url ถ้าไม่ใส่มันจะเอาเฟืองให้ https://iconify.design/icon-sets/dashicons/
                    '1'                         // ลำดับ                                            
                );          
   
    add_submenu_page('job',                 //parent page slug
                __('งานซ่อม', ''),              //page title singular เปลี่ยนได้
                __('เพิ่มงานซ่อม', ''),           //menu titel plural เปลี่ยนได้
                'activate_plugins',             //manage optios User Roles การกำหนดสิทธิ์
                'job_viu',               //slug 
                'job_viu'); //functionn
                add_menu_page( 'Home Page', 'Home Page', 'manage_options', 'homepage', 'my_custom_menu_page', plugins_url( 'myplugin/images/icon.png' ), 6 );   
}add_action('admin_menu', 'job_main_menu');


/** bootstrap */
function bootstrap_install_css() {
    wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css');
    
    // ไอคอน
    //wp_enqueue_style( 'bootstrap-css', 'https://fonts.googleapis.com/icon?family=Material+Icons');
    
}add_action( 'admin_head', 'bootstrap_install_css' );

function bootstrap_install_js() {
    wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js');
}
add_action('admin_footer', 'bootstrap_install_js');
?>