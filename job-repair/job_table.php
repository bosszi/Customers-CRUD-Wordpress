<?php
if (!class_exists('WP_List_Table')) {   // เรียกใช้งานคลาส WP_List_Table ถ้าไม่มีให้ไป ตามที่อยู่ wp-admin/includes/class-wp-list-table.php
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}//ตรวจสอบให้แน่ใจว่าคลาสที่จำเป็นพร้อมใช้งานเนื่องจากWP_List_Tableไม่ได้โหลดโดยอัตโนมัติ

class Job_list extends WP_List_Table{
        public function __construct(){
            parent::__construct(array(      // https://developer.wordpress.org/reference/classes/wp_list_table/__construct/
                'plural' => 'jobs',        // 
                'singular' => 'job',       // 
                'ajax'      => false        //does this table support ajax?
                // ตรงไหนที่จะมีการแสดงจำนวน และต้องมีการระบุหน่วย WP จะดึงข้อความจากตรงนี้ไปแสดง ตรง plural และ singular เป็น label ในเมนูหลังบ้าน
            ));
        }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        function column_default($item, $column_name){  // ส่งชื่อ$column_name ไปที่ฟังก์ชัน คลาสแม่
            switch($column_name){
                case 'job_number':
                case 'brand_name':
                case 'job_name':
                case 'job_model':
                case 'job_serial':
                case 'job_picture':
                case 'job_customer':
                case 'job_date':
                case 'job_action':  
                    return $item[$column_name];
                default: return "ไม่มีค่าใน column_default";
            }           
        } 

        /** สำคัญดึงชื่อฟิลจากตาราง */
        function get_columns(){  // columns หัวตาราง จะให้มีกี่ตาราง ชื่ออะไรบ้าง และดึงข้อมูลจากฟิลชื่ออะไร        
            $columns = array(
                'job_number'    => ('รหัสงานซ่อม'),       // ชื่อ
                'brand_name'    => ('ยี่ห้อ'),    // 
                'job_name'       => ('ชื่อเครื่อง'),    // 
                'job_model'         => ('Model'),    // 
                'job_serial'     => ('serial'),    // 
                'job_picture'       => ('picture'),    // 
                'job_customer'   => ('รหัสลูกค้า'),         // 
                'job_date'       => ('วันที่'),   
                'job_action'    => ('จัดการ')
                // 'ชื่อฟิลในฐานข้อมูล ' => __('ชื่อหัวตาราง') 
            );  //echo '<pre>'; echo print_r($columns); echo '</pre>';
            return $columns;
        }         
	
        function get_sortable_columns() // จัดเรียงข้อมูล ลูกศรเล็กๆที่หัวตาราง 
        {
            $sortable_columns = array(
                'job_number' => array('job_number', false),
                'brand_name' => array('brand_name', false),
                'job_name' => array('job_name', false),
                'job_model' => array('job_model', false),
                'job_serial' => array('job_serial', false),
                //'job_picture' => array('picture', false),
                'job_customer' => array('job_customer', false),
                'job_date' => array('job_date', false),
            );//echo '<pre>'; echo print_r($sortable_columns); echo '</pre>';
            return $sortable_columns;
        } 

        function column_job_picture($item){             
            if(isset($item['job_picture']) && $item['job_picture'] != ''){    
            echo '<img src="';
            echo bloginfo('url').'/wp-content/plugins/star-app/job-repair/job-pictures/'.$item['job_picture'];
            echo '" class="img-fluid">';    
            }    
        }
        
        function column_job_action($item) {
            $actions = array(
                'view'   => sprintf('<a href="?page=job_viu&action=%s&job_id=%d">%s</a>','view',$item['job_id'], __('ดูข้อมูล', '')),
                'edit'   => sprintf('<a href="?page=job_viu&action=%s&job_id=%d">%s</a>','edit',$item['job_id'], __('แก้ใข', '')),
                'delete' => sprintf('<a href="?page=%s&action=%s&job_id=%s">%s</a>', $_REQUEST['page'], 'delete', $item['job_id'], __('ลบ', ''))
           );          
           return sprintf('%s %s',$item['job_number'],$this->row_actions($actions)); 
        }
        
        function delete_row($job_id){
            global $wpdb;
            $table_job = $wpdb->prefix.'job';
            $result = $wpdb->query($wpdb->prepare("DELETE FROM $table_job WHERE job_id = %d",$job_id));
            if ($result) {
                $message = __('ลบสำเร็จ', '');
                echo("<script>location.href = '".'https://www.star-circuit.com/job/wp-admin/admin.php?page=job'."'</script>");             
            } else {
                $notice = __('เกิดปัญหาขณะลบ', '');
            }
        }

        function get_hidden_columns(){  // สำหรับซ่อน column ที่ต้องการเรียกใช้ในฟังชัน prepare_items
            //return array("brand_name");
        }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        function prepare_items() {
			global $wpdb;
            $table_job = $wpdb->prefix.'job';
            $table_job_brand = $wpdb->prefix.'job_brand'; 
			$per_page = 10;

            $columns = $this->get_columns();   // เรียกฟังชัน get_columns() หัวตาราง
            $hidden = array();  //$hidden = $this->get_hidden_columns(); // สำหรับซ่อน column ที่ต้องการ            
            $sortable = $this->get_sortable_columns();  // จัดเรียงข้อมูล ลูกศรเล็กๆที่หัวตาราง ถ้ามีการคลิกซ้ำจะส่ง $_GET และส่ง array-> page,orderby,order           
            $this->_column_headers = array($columns, $hidden, $sortable);   // รับส่วนหัวคอลัมน์สำหรับหน้าแสดงจะให้แสดงอะไรบ้าง

            $total_items = $wpdb->get_var("SELECT COUNT(job_id) FROM $table_job"); //get_var ดึงข้อมูล id จากฐานข้อมูล ว่ามีจำนวนกี่ row
            
            // $paged กำหนดจำนวนที่แสดงต่อหน้า // $orderby จัดเรียงตาม columnไหน // $order จัดเรียงจากน้อยไปมาก หรือมากไปน้อย //
            $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;        
            $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'job_number';  //จัดเรียงข้อมูล
            $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';    //จัดเรียงข้อมูล
            
            if(isset($_REQUEST['s']) && $_REQUEST['s']!='') {   // ระบบค้นหา
                //echo '<pre>'; echo print_r($_REQUEST); echo '</pre>'; 
                switch ($_REQUEST['search_option']) {
                    case 'job_number':  $this->items = $wpdb->get_results($wpdb->prepare(
                        "SELECT $table_job.job_id, $table_job.job_number, 
                                $table_job_brand.brand_name,
                                $table_job.job_name, $table_job.job_model, $table_job.job_serial, $table_job.job_picture, 
                                $table_job.job_description, $table_job.job_customer, $table_job.job_date
                        FROM $table_job
                        LEFT JOIN $table_job_brand ON $table_job.job_brand = $table_job_brand.brand_id
                        WHERE $table_job.job_number LIKE %s" , '%'.$_REQUEST['s'].'%'), ARRAY_A); break; // % หน้าหลังคืออะไรก็ได้
                    case 'job_brand':  $this->items = $wpdb->get_results($wpdb->prepare(
                        "SELECT $table_job.job_id, $table_job.job_number, 
                                $table_job_brand.brand_name,
                                $table_job.job_name, $table_job.job_model, $table_job.job_serial, $table_job.job_picture, 
                                $table_job.job_description, $table_job.job_customer, $table_job.job_date
                        FROM $table_job
                        LEFT JOIN $table_job_brand 
                        ON $table_job.job_brand = $table_job_brand.brand_id
                        WHERE   $table_job_brand.brand_name LIKE %s" , '%'.$_REQUEST['s'].'%'), ARRAY_A); break;
                    case 'job_name':  $this->items = $wpdb->get_results($wpdb->prepare(
                        "SELECT $table_job.job_id, $table_job.job_number, 
                                $table_job_brand.brand_name,
                                $table_job.job_name, $table_job.job_model, $table_job.job_serial, $table_job.job_picture, 
                                $table_job.job_description, $table_job.job_customer, $table_job.job_date
                        FROM $table_job
                        LEFT JOIN $table_job_brand 
                        ON $table_job.job_brand = $table_job_brand.brand_id
                        WHERE   $table_job.job_name LIKE %s" , '%'.$_REQUEST['s'].'%'), ARRAY_A); break; // % หน้าหลังคืออะไรก็ได้
                    case 'job_model':  $this->items = $wpdb->get_results($wpdb->prepare(
                        "SELECT $table_job.job_id, $table_job.job_number, 
                                $table_job_brand.brand_name,
                                $table_job.job_name, $table_job.job_model, $table_job.job_serial, $table_job.job_picture, 
                                $table_job.job_description, $table_job.job_customer, $table_job.job_date
                        FROM $table_job
                        LEFT JOIN $table_job_brand 
                        ON $table_job.job_brand = $table_job_brand.brand_id
                        WHERE   $table_job.job_model LIKE %s" , '%'.$_REQUEST['s'].'%'), ARRAY_A); break;
                    case 'job_serial':  $this->items = $wpdb->get_results($wpdb->prepare(
                        "SELECT $table_job.job_id, $table_job.job_number, 
                                $table_job_brand.brand_name,
                                $table_job.job_name, $table_job.job_model, $table_job.job_serial, $table_job.job_picture, 
                                $table_job.job_description, $table_job.job_customer, $table_job.job_date
                        FROM $table_job
                        LEFT JOIN $table_job_brand 
                        ON $table_job.job_brand = $table_job_brand.brand_id
                        WHERE   $table_job.job_serial LIKE %s" , '%'.$_REQUEST['s'].'%'), ARRAY_A); break;

                    case 'job_all': /** ค้นหาทั้งหมด */                                    
                        $this->items = $wpdb->get_results( "SELECT * 
                        FROM $table_job 
                        LEFT JOIN $table_job_brand 
                        ON $table_job.job_brand = $table_job_brand.brand_id
                        WHERE job_number LIKE '%".$_REQUEST['s']."%'                             
                            OR job_brand LIKE '%".$_REQUEST['s']."%'
                            OR job_name LIKE '%".$_REQUEST['s']."%'
                            OR job_model LIKE '%".$_REQUEST['s']."%'
                            OR job_serial LIKE '%".$_REQUEST['s']."%'
                            OR job_description LIKE '%".$_REQUEST['s']."%'
                            ", ARRAY_A);
                            break;
                    
                    default:
                      echo '<h2>ยังไม่ได้เขียนฟังชันนี้</h2>';
                  } 
            } else {
                $this->items = $wpdb->get_results($wpdb->prepare(
                "SELECT $table_job.job_id,
                        $table_job.job_number,
                        $table_job_brand.brand_name,
                        $table_job.job_name,
                        $table_job.job_model,
                        $table_job.job_serial,
                        $table_job.job_picture,
                        $table_job.job_description,
                        $table_job.job_customer,
                        $table_job.job_date
                FROM $table_job 
                LEFT JOIN $table_job_brand ON $table_job.job_brand = $table_job_brand.brand_id
                ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);                    
                //echo '<pre>'; echo print_r($this->items); echo '</pre>';        
            }

            $this->set_pagination_args(array(   // pagination_args = 
                'total_items' => $total_items, // รวมการนับ id ว่ามีกี่ row
                'per_page' => $per_page, // มาจากที่กำหนดใว้ว่ากี่หน้า 10 row ต่อหน้า
                'total_pages' => ceil($total_items / $per_page) // คำนวนการนับว่าได้กี่หน้า ceil เป็นการหารให้ได้จำนวนหน้าและปัดเศษ
            )); 
            if(isset($_REQUEST['action']) && $_REQUEST['action']!='' && $_REQUEST['action'] == 'delete') {
                if (isset($_REQUEST['job_id'])){
                    $job_id = $_REQUEST['job_id'];
                    $this->delete_row($job_id);                  
                }                
            }        
		}
}
//echo '<pre>'; echo print_r($items); echo '</pre>';
?>