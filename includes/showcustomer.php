<?php 
/*แสดงตารางและ Notice */

function customers_page_handler(){   // เมนูจะเรียกมาที่ฟังชันนี้เพื่อแสดงข้อมูลทั้งหมด
    global $wpdb;

    $table = new Customers_CRUD_List(); // สร้างตัวแปร $table เก็บค่าคลาส Customers_CRUD_List
//echo '<pre>'; echo print_r($table); echo '</pre>';
    $table->prepare_items();    // หลังจากสร้างตัวแปรเก็บคลาสาแล้ว เรียกฟังชัน prepare_items ที่อยู่ในคลาสมาใช้งาน

    /** ส่วนแจ้งเตือน */
    $message = '';
    if ('delete' === $table->current_action()) {    //// current_action เป็นคลาสที่เขียนใว้แล้วสำหรับรับการดำเนินการปัจจุบันที่เลือกจากดรอปดาวน์การดำเนินการจำนวนมาก
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('ท่านได้ทำการลบข้อมูลจำนวน: %d', ''), count(array($_REQUEST['id']))) . '</p></div>';
    }
    ?>

<div class="wrap"> <!-- ส่วนบนและปุ่มเพิ่มลูกค้า -->
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2> <?php _e('ข้อมูลลูกค้า', '')?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=customers_form');?>"><?php _e('เพิ่มลูกค้า', '')?></a>
    </h2>
    <?php echo $message; ?> <!-- ถ้ามี message ให้ปริ้นออกมา -->

    <form id="customers-table" method="GET">    <!-- สำหรับแสดงผลทั้งหมด -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>        
        <?php $table->display() ?>
        <?php //echo '<pre>'; echo print_r($table->display()); echo '</pre>'; ?>
    </form>
</div>
<?php
}
?>