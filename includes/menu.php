<?php
/**
 * ส่วน Menu Admin
 */
function customers_admin_menu()
{
    add_menu_page(__('customers', ''),          //page title
                    __('ข้อมูลลูกค้า', ''),        //menu title
                    'activate_plugins',         //capabilities
                    'customers',                //menu slug
                    'customers_page_handler',   //function
                    'dashicons-groups',         // ไอคอนด้านหน้าเมนู icon_url ถ้าไม่ใส่มันจะเอาเฟืองให้ https://iconify.design/icon-sets/dashicons/
                    '1'                         // ลำดับ                                            
                );          
   
    add_submenu_page('customers',                   //parent page slug
                    __('Add new', ''),              //page title
                    __('เพิ่มรายชื่อ', ''),             //menu titel
                    'activate_plugins',             //manage optios
                    'customers_form',               //slug
                    'customers_form_page_handler'); //function
}
add_action('admin_menu', 'customers_admin_menu');
?>