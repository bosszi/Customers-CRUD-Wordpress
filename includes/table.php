<?php
    //  class_exists() : ใช้ในการตรวจสอบว่ามีคลาสอยู่หรือไม่
    if (!class_exists('WP_List_Table')) {   // เรียกใช้งานคลาส WP_List_Table ถ้าไม่มีให้ไป ตามที่อยู่ wp-admin/includes/class-wp-list-table.php
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    }
    

class Customers_CRUD_List extends WP_List_Table{    //
    // public  คือสามารถเรียกจากภายนอกได้
    // private คือไม่สามารถเรียกจากภายนอกได้ เรียกใช้ได้เฉพาะในฟังชัน
    // protected คือไม่สามารถเรียกจากภายนอกได้ เรียกใช้ได้เฉพาะในฟังชัน แต่ถ้ามีการสืบทอดคุณสมบัติ โดย extends จะสามารถเรียกใช้งานได้
    function __construct(){             // ถ้าไม่ประกาศฟังชันนี้จะเป็น public โดยอัตโนมัติซึ่งจะเรียกจากภายนอกได้
        global $status, $page;                                            // parent สื่อทอดคลาสคอนทรัคแบบสเตติกมา
        parent::__construct(array(          // https://developer.wordpress.org/reference/classes/wp_list_table/__construct/
            'plural' => 'customers',        // 
            'singular' => 'customer',       // 
            // ตรงไหนที่จะมีการแสดงจำนวน และต้องมีการระบุหน่วย WP จะดึงข้อความจากตรงนี้ไปแสดง ตรง plural และ singular เป็น label ในเมนูหลังบ้าน
        ));
    }    
// เริ่มเขียนฟังชั้นแบ่งตาม column //
         //$item - row (key, value array)
         //$column_name - string (key)
         //return HTML         
        function column_default($item, $column_name)    // 
        {
            return $item[$column_name];
        }    

         //$item - row (key, value array)
         //return HTML
        function column_age($item)
        {
            return '<em>' . $item['age'] . '</em>'; // em จะปรับความกว้างตามตัวอักษร เป็นหน่วยคล้าย px, em , rem
        }
        
         // when you hover row "Edit | Delete" links showed
         // $item - row (key, value array)
         //return HTML
        function column_name($item){ //column ชื่อ จะใส่อะไรบ้าง
            // ลิงก์ไปที่ /admin.php?page=[your_plugin_page][&other_params]
            // notice how we used $_REQUEST['page'], so action will be done on curren page
            // also notice how we use $this->_args['singular'] so in this example it will
            // be something like &customer=2
            $actions = array(   //เมื่อเลื่อนเมาส์ไปที่ row จะมีฟังชันแก้ใขและลบขึ้นมา
                'edit' => sprintf('<a href="?page=customers_form&id=%s">%s</a>', $item['id'], __('แก้ใข', '')),
                'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('ลบ', '')),
            );  //sprintf() ใส่สตริงให้ตัวแปร ตามรูปแบบที่กำหนด sprintf(format , arg) ฟอร์เมต %s คือ String ส่วน arg คือ ข้อความที่ใส่ให้ format
    
            return sprintf('%s %s',$item['name'],$this->row_actions($actions));    // เอาตัวแปร $actions มาใส่ใน row_actions เป็นคลาสที่ wp เขียนใว้แล้ว            
        }
    
        //checkbox column renders
        //$item - row (key, value array)
        //return HTML        
        function column_cb($item)   // column_cb เป็น column checkbox 
        {
            return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />',
                $item['id']
            );
        }
    
        function get_columns(){  // columns หัวตาราง จะให้มีกี่ตาราง
        
            $columns = array(
                'cb' => '<input type="checkbox" />',        // สร้างเช็คบ๊อก
                'name' => __('ชื่อ', ''),       // ชื่อ
                'email' => __('E-Mail', ''),    // อีเมล
                'age' => __('อายุ', ''),         // อายุ
            );  //echo '<pre>'; echo print_r($columns); echo '</pre>';
            return $columns;
        }  

             /** จัดเรียงข้อมูล columns */
        function get_sortable_columns() // จัดเรียง columns
        {
            $sortable_columns = array(
                'name' => array('name', true),
                'email' => array('email', false),
                'age' => array('age', false),
            );//echo '<pre>'; echo print_r($sortable_columns); echo '</pre>';
            return $sortable_columns;
        }
    
        /** แถบสำหรับลบที่ละหลายรายการ */
        function get_bulk_actions()
        {
            $actions = array(
                'delete' => 'ลบ'
            );
            return $actions;
        }    
        /** การลบหลาย row พร้อมกันจำนวนมาก         */
        function process_bulk_action()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'customers_crud'; 
    
            if ('delete' === $this->current_action()) { // current_action เป็นคลาสที่เขียนใว้แล้วสำหรับรับการดำเนินการปัจจุบันที่เลือกจากดรอปดาวน์การดำเนินการจำนวนมาก
                $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
                if (is_array($ids)) $ids = implode(',', $ids);
    
                if (!empty($ids)) {
                    $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
                }
            }
        }
    
        /** เตรียมข้อมูลสำหรับแสดงในตาราง*/
        function prepare_items(){
            global $wpdb;   //echo '<pre>'; echo print_r($wpdb); echo '</pre>';
            $table_name = $wpdb->prefix . 'customers_crud';
            
            $per_page = 10; // ตั้งค่า แบ่งจำนวนต่อหน้า
            
            $columns = $this->get_columns();                    // เรียกฟังชัน get_columns()
            $hidden = array();                                 // ประกาศตัวแปร $hidden เก็บค่าอาเร
            $sortable = $this->get_sortable_columns();          // เรียกฟังชัน get_sortable_column
     
            // รับส่วนหัวคอลัมน์สำหรับหน้าแสดง
            $this->_column_headers = array($columns, $hidden, $sortable);// _column_headers ฟังชัน WP

            $this->process_bulk_action();   // // เรียกฟังชัน process_bulk_action ลบหลาย row
    
            // นับรวมจำนวน id row จากตารางเพื่อนำไปคำนวนการแบ่งหน้า
            $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name"); //get_var ดึงข้อมูล id จากฐานข้อมูล ว่ามีจำนวนกี่ row
            //echo '<pre>'; echo print_r($total_items); echo '</pre>'; 
            // เรียงลำดับข้อมูล
            // การใช้ if แบบย่อ (เงื่อนใข ? หารเงื่อนใขถูกต้อง : หากเงื่อนใขไม่ถูกต้อง)
            // max หาค่ามากที่สุดจากชุดข้อมูล 
            // intval แปลงข้อมูลเป็น integer
            // in_array เป็นฟังก์ชันสำหรับตรวจสอบว่ามีข้อมูลอยู่ใน array หรือไม่
            // array_keys ใช้คืนค่า key ทั้งหมดแบบ array โดย array ที่ได้จะมี  key ตั้งแต่ 0 ขั้นไปตามลำดับ
            $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;    // เกี่ยวกับแบ่งหน้า
            $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'name';  //จัดเรียงข้อมูล
            $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';    //จัดเรียงข้อมูล
    
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
            //echo '<pre>'; echo print_r($wpdb); echo '</pre>'; 
            // ตั้งค่าการแบ่งหน้า
            $this->set_pagination_args(array(   // pagination_args = array ตัวแปรที่ประกาศใน คลาสแม่
                'total_items' => $total_items, // รวมการนับ id ว่ามีกี่ row
                'per_page' => $per_page, // มาจากที่กำหนดใว้ว่ากี่หน้า 10 row ต่อหน้า
                'total_pages' => ceil($total_items / $per_page) // คำนวนการนับว่าได้กี่หน้า ceil เป็นการหารให้ได้จำนวนหน้าและปัดเศษ
            ));
        }
}

// ส่งต่อไปที่ไฟล์ pagehandlr.php

//echo '<pre>'; 
//echo print_r($sortable);       
//echo '</pre>'; 

?>