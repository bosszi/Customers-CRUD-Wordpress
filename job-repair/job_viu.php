<?php

define('UPLOADS', 'wp-content/plugins/star-app/job-repair/job-pictures');	// เปลี่ยนเส้นทางเก็บไฟล์
function job_upload_dir( $dir ) {	// เปลี่ยนเส้นทางของรูป
    return array(
        'path'   => $dir['basedir'] . '',
        'url'    => $dir['baseurl'] . '',
        'subdir' => '',
    ) + $dir; 
}add_filter( 'upload_dir', 'job_upload_dir' );
    //remove_filter( 'upload_dir', 'job_upload_dir' );

function job_validate($item){ //echo '<pre>'; echo print_r($item); echo '</pre>';
    $messages = array(); 
    //if (empty($item['job_number'])){$messages[] = __('กรุณาใส่รหัสงานซ่อม', '');}
    if(!empty($item['job_number']) && !preg_match('/[0-9]{6}/', $item['job_number'])) $messages[] = __('รหัสงานซ่อมจะต้องเป็นตัวเลข 6 หลัก');
    

    
    if(empty($item['job_description'])) $messages[] = __('กรุณาใส่รายระเอียดอาการเสีย', '');
    if(mb_strlen($item['job_description']) <= 5) $messages[] = __('รายระเอียดต้องไม่น้อยกว่า 5 ตัวอักษร ', '');
    if(mb_strlen($item['job_description']) >= 1000) $messages[] = __('รายระเอียดต้องไม่เกิน 1000 ตัวอักษร ', '');
    //if (!ctype_digit($item['age'])) $messages[] = __('กรุณาใส่อายุ', '');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    /**
     * ^[a-zA-Z]+$
     * + หมายถึง ต้องมีอย่างน้อย 1 ตัวอักษร นั่นก็คือต้องกรอกข้อมูล
     * ^ หมายถึง ขึ้นต้นด้วย ค่าที่กำหนด
     * $ หมายถึง ลงท้ายด้วย ค่าที่กำหนด
     * \s ในการตรวจสอบช่องว่าง ต้องมีช่องว่าง
     */

    if (empty($messages)) {return true;}else{
        return implode('<br/>', $messages); // ฟังก์ชันแบ่งข้อความในที่นีให้ขึ้นบรรทัดใหม่ implode() สร้าง string จากข้อมูลใน array ด้วยฟังก์ชัน implode()
    }
}?>
<?php 
function job_viu(){
           // ล้างค่า $message $notice ให้ว่างเพื่อรอรับข้อความใหม่ที่จะเกิดขึ้นในเหตุการณ์ต่างๆ
    $message = '';  // ตั้งค่าเริ่มต้นให้ตัวแปร $message มีค่า '' ถ้าใช้ isset มาตรวจสอบค่าที่ได้จะเป็น true
    $notice = '';   // ตั้งค่าเริ่มต้นให้ตัวแปร $notice มีค่า ''
    global $wpdb;
    $table_job = $wpdb->prefix.'job';
    $table_job_brand = $wpdb->prefix.'job_brand';
    $default = array(
        'job_id'        => 0,      // ให้ id เท่ากับ 0 เพื่อจะเขียนฟังชันให้เลือก insert
        'job_number'    => '',
        'job_brand'     => '',
        'job_name'      => '',
        'job_model'     => '',
        'job_serial'    => '',
        'job_picture'   => '',
        'job_description' => '',
        'job_customer'  => '',
        'job_type'      => '',
        'job_comment'   => '',
        'job_date'      => current_time('mysql'),
        'job_update'    => current_time('mysql')
                       
    );
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        //echo '<pre>'; echo print_r($_REQUEST); echo '</pre>';  exit();
        if($_FILES['job_picture_file']['name'] != ''){ // ถ้ามีไฟใหม่จะทำในปีกกา        
            $job_uploaded_file = $_FILES['job_picture_file'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $job_uploaded_file, $upload_overrides );	// wp_handle_upload( $รายละเอียดไฟล์, $ตำแหน่งที่เราจะเก็บไฟล์ );

        }  
        $item = shortcode_atts($default, $_REQUEST);    // สร้างตัวแปร default และนำค่าที่ได้จาก _REQUEST มาใส่แทน 
        if(isset($job_uploaded_file['name'])){ //echo '555555555555555555555555555555555'; exit(); 
            $item['job_picture'] = $job_uploaded_file['name'];
        }
        //echo '<pre>'; echo print_r($item); echo '</pre>';   exit();      
        $item_valid = job_validate($item); // เรียกฟังชัน validate_job กรองข้อมูลและนำไปเก็บที่ $item_valid
        if ($item_valid === true) {
            if ($item['job_id'] == 0) {    // ถ้าไอดีเท่ากับ 0 ให้ insert
                $job_number_max = $wpdb->get_var( "SELECT MAX(job_number) FROM {$wpdb->prefix}job");                                            
                $PrefixYear = substr(date("Y")+543,2);    // ตัด 25 ออกเอาเฉพาะ 64
                    if($job_number_max==0){$job_number_max = '000000';}else{$job_number_max++;}
                    $item['job_number'] = $PrefixYear.substr($job_number_max,2);    // ตัด 64 ออกเอาเฉพาะรหัส 4 ตัวหลัง      
                    //echo '<pre>'; echo print_r($item); echo '</pre>';                                      
                    $result = $wpdb->insert($table_job, $item);
                    //$item['job_id'] = $wpdb->insert_id;                    
                    if ($result) {  //echo '<pre>'; echo print_r($item); echo '</pre>'; 
                        $item['job_id'] = $wpdb->get_var( "SELECT MAX(job_id) FROM {$wpdb->prefix}job");                                         
                        $message = __('เพิ่มงานซ่อมอัตโนมัติสำเร็จ รหัสงาน  '.$item['job_number'], '');  
                        echo("<script>location.href = '".'https://www.star-circuit.com/job/wp-admin/admin.php?page=job_viu&action=view&job_id='
                        .$item['job_id']."'</script>");                  
                    } else {
                        $notice = __('เกิดปัญหาขณะเพิ่มงานซ่อมอัตโนมัติ', '');
                    }
            }else {    // ถ้าไอดีไม่เท่ากับ 0 ให้ เพิ่ม งานซ่อม        
                //
                    if($item['job_picture'] == ''){$item['job_picture'] = $item['job_picture'];}
                    //echo '<pre>'; echo print_r($item); echo '</pre>'; exit();
                    $result = $wpdb->update($table_job, $item, array('job_id' => $item['job_id']));
                    if ($result) {
                        $message = __('แก้ใขข้อมูลงานซ่อมสำเร็จ รหัสงาน  '.$item['job_number'], '');
                        echo("<script>location.href = '".'https://www.star-circuit.com/job/wp-admin/admin.php?page=job_viu&action=view&job_id='
                        .$item['job_id']."'</script>");             
                    } else {
                        $notice = __('เกิดปัญหาขณะแก้ใขงานซ่อม', '');
                    }
            }
        }else{$notice = $item_valid;}   // ถ้า validate ไม่ผ่านให้แสดง err           
            
    }else{ // ดึงข้อมูล ถ้ามมีไอดีส่งมาด้วย        
        $item = $default;        
        if (isset($_REQUEST['job_id'])) {   // $_REQUEST รับค่า id มาจาก  url            
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_job WHERE job_id = %d", $_REQUEST['job_id']), ARRAY_A);
            //$item['view_disabled'] = '';    // เมื่อดึงข้อมูลมาจะต้องส่งค่าว่างไปถ้าไม่มี action        
            //if(isset($_REQUEST['action'])=='view'){$item['view_disabled'] = 'disabled';} // echo $_REQUEST['action'];
            //echo '<pre>'; echo print_r($item); echo '</pre>';
            if (!$item) {
                $item = $default;
                $notice = __('ไม่พบข้อมูลงานซ่อม', '');
            }//echo '<pre>'; echo print_r($_REQUEST['id']); echo '</pre>';
        }//
    }?>
       
<div class="wrap">
<h2>เพิ่ม งานซ่อม</h2> 
    <div class="col-sm">
        <a class="btn btn-success" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=job');?>">
        <?php _e('ตารางงานซ่อม')?></a>                
    </div>
    <?php if (!empty($notice)): ?>
    <div class="alert alert-danger py-0 px-5 mt-2" role="alert" ><p><?php echo '<h6>'.$notice.'</h6>' ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div class="alert alert-success py-0 px-5 mt-2" role="alert"><p><?php echo '<h6>'.$message.'</h6>' ?></p></div>
    <?php endif;?>
    <?php if($_REQUEST['action'] == 'view'){$disabled_view = "disabled";}else{$disabled_view = '';}?>

<form method="POST" enctype="multipart/form-data"> <!-- ฟอร์มใช้สำหรับ เพิ่ม และ แก้ใขงานซ่อม -->
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>    <!-- สร้าง Nonce -->        
        <div class="row">        
            <div class="col-md-6">
                <label for="job_number" class="form-label">รหัสงานซ่อม</label>
                <input type="number" class="form-control" id="job_number" name="job_number" placeholder="ระบบจะสร้างรหัสงานให้อัตโนมัติ"
                value="<?php echo esc_attr(trim($item['job_number'])); ?>" <?php echo ' '.$disabled_view; ?>>
            </div>            
            <div class="col-md-6"> 
                <label for="job_brand" class="form-label">ยี้ห้อ</label>
                <select id="job_brand" class="form-select" name="job_brand" <?php echo ' '.$disabled_view; ?>>
                <?php $brand_read = $wpdb->get_results("SELECT * FROM $table_job_brand"); // ดึงข้อมูล ไอดี และยี่ห้อออกมา ?>
                <option value="">ยี่ห้อ</option>                
                <?php               
                foreach($brand_read as $name)   // ถ้า insert ให้ ceho เรียงตามตัวอักษร
                {
                     if($item['job_id'] == 0){ // ถ้า insert
                        echo '<option value="'.$name->brand_id.'">'.$name->brand_name.'</option>';
                    }else{ 
                         echo '<option value="'.$name->brand_id.'"'; 
                         if($item['job_brand'] == $name->brand_id){echo ' selected ';}else{echo '';}
                         echo '>'.$name->brand_name.'</option>';
                    } 
                } ?>
                </select>          
            </div>
        <div>

        <div class="row">
            <div class="col-md-6">
                <label for="job_name" class="form-label">ชื่อเครื่อง</label>
                <input type="text" class="form-control" id="job_name" name="job_name" value="<?php echo esc_attr(trim($item['job_name'])); ?>" 
                <?php echo ' '.$disabled_view; ?>>
            </div>
            <div class="col-md-6">
                <label for="job_model" class="form-label">Model/Type</label>
                <input type="text" class="form-control" id="job_model" name="job_model" value="<?php echo esc_attr(trim($item['job_model'])); ?>" 
                <?php echo ' '.$disabled_view; ?>>
            </div>
        <div>               
            <div class="col-md-6">
                <label for="job_serial" class="form-label">S/N:</label>
                <input type="text" class="form-control" id="job_serial" name="job_serial" value="<?php echo esc_attr(trim($item['job_serial'])); ?>" 
                <?php echo ' '.$disabled_view; ?>>
            </div> 
            <div class="mb-3">
            <label for="job_description" class="form-label">อาการเสีย</label>
            <textarea class="form-control" placeholder="กรุณาใส่รายละเอียดอาการเสีย อย่างน้อย 5 ตัวอักษร" id="job_description" style="height: 100px" 
            name="job_description" <?php echo ' '.$disabled_view; ?>><?php echo esc_textarea(trim($item['job_description']));?></textarea>                        
            </div>
    <div class="row">
        <div class="col-6">
            <div class="col-md-12">
                <label for="job_customer" class="form-label">ชื่อลูกค้า</label>
                <input type="text" class="form-control" id="job_customer" name="job_customer" value="<?php echo esc_attr(trim($item['job_customer'])); ?>" 
                <?php echo ' '.$disabled_view; ?>>
            </div>       
        
            <div class="col-md-6">
                <label for="job_type" class="form-label">ประเภทงาน</label>
                <input type="number" class="form-control" id="job_type" name="job_type" value="<?php echo esc_attr(trim($item['job_type'])); ?>" 
                <?php echo ' '.$disabled_view; ?>>
            </div>
            <div class="col-md-12">
                <label for="job_comment" class="form-label">หมายเหตุ</label>
                <input type="text" class="form-control" id="job_comment" name="job_comment" value="<?php echo esc_attr(trim($item['job_comment'])); ?>" 
                <?php echo ' '.$disabled_view; ?>>
            </div>
            <div class="col-md-6">
                <?php if($_REQUEST['action'] == 'edit'|| $_REQUEST['action'] == 'view'){
                    echo '<label for="job_date" class="form-label">วันที่รับเข้า</label>';
                    echo '<input type="text" class="form-control" id="job_date" name="job_date" value="';
                    echo esc_attr($item['job_date']).'" '.$disabled_view; 
                    echo '>';
                }?>
            </div>
            <div class="col-md-6">
                <?php if($_REQUEST['action'] == 'edit' || $_REQUEST['action'] == 'view'){
                    echo '<label for="job_update" class="form-label">วันที่อัพเดท</label>';
                    echo '<input type="text" class="form-control" id="job_update" name="job_update" value="';
                    echo esc_attr($item['job_update']).'" '.$disabled_view; 
                    echo '>';                   
                }?>               
            </div> 
        </div>
        <div class="col-6"> 
            <div class="col-md-12 mt-4"> 
                <?php if($item['job_picture']){ // ถ้ามีรูป?>
                <img src="<?php echo plugins_url('/star-app/job-repair/job-pictures/'.$item['job_picture']);?>" class="img-fluid w-50">
                <?php echo esc_attr($item['job_picture']); 
                echo '<input type="hidden" name="job_picture" value="';
                echo esc_attr($item['job_picture']);
                echo '">';
                if($_REQUEST['action'] == 'edit'){
                    echo '
                    <input type="file" class="form-control" id="job_picture_file" name="job_picture_file" accept="image/*" 
                    multiple="multiple" aria-label="job_picture_file">'; 
                }
            }else{   //ถ้าไม่มีรูป
                    echo '
                    <input type="file" class="form-control" id="job_picture_file" name="job_picture_file" accept="image/*" 
                    multiple="multiple" aria-label="job_picture_file"  
                    '.$disabled_view;  
                    echo '>';
                }?>   
                                
            </div>
        </div> 
    </div>
            <div class="col-12 mt-5"> 
        <?php switch ($_REQUEST['action']) {
                    case "insert":
                        $button_color = "success";
                        $button_name = "เพิ่มข้อมูลงานซ่อม";                        
                        break;
                    case "edit":
                        $button_color = "warning";
                        $button_name = "แก้ใขข้อมูลงานซ่อม";
                        break;
                    default:
                        $button_color = "danger";
                        $button_name = "ไม่พบปุ่มดังกล่าว"; 
                    }                     
                if($_REQUEST['action'] == 'view'){
                    echo '<a class="btn btn-warning" href="';
                    echo get_admin_url(get_current_blog_id(), 'admin.php?page=job_viu&action=edit&job_id=').$item['job_id'];
                    echo '">';
                    _e('แก้ใขข้อมูล');
                    echo '</a>';
                    echo '<a class="btn btn-success mx-5 px-5" href="';
                    echo get_admin_url(get_current_blog_id(), 'admin.php?page=job');
                    echo '">';
                    _e('ข้อมูลถูกต้อง');
                    echo '</a>';
                    }else{
                        echo '<button type="submit" class="btn btn-';
                        echo $button_color; 
                        echo '">';
                        echo $button_name; 
                        echo '</button>';
                        }?>          
            </div>
    </form>
</div>
<?php }?>