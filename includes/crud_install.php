<?php

global $CRUD_version;    // สำหรับเอาค่า version ไปเก็บในตาราง wp_options และจะตรวจสอบการอัพเดทเวอร์ชั้น
$CRUD_version = '1'; // ถ้ามีการเปลี่ยน version ให้ใส่ version ที่สูงกว่า ระบบจะตรวจสอบและอัพเดท

/**
 *สร้างดาต้าเบส และเพิ่มขอมูล CRUD_version
 */
function customers_crud_install(){   // สร้าง Database 

    global $wpdb;               // ประกาศเรียกตัวแปร global $wpdb;  ติดต่อกับดาต้าเบส
    global $CRUD_version;
    $table_name = $wpdb->prefix . 'customers_crud'; // prefix คือนำหน้าตาราง ในที่นี้หมายถึง wp_

    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL AUTO_INCREMENT,
      name tinytext NOT NULL,
      email VARCHAR(100) NOT NULL,
      age int(11) NULL,
      PRIMARY KEY (id)
    );";
// ABSPATH เป็นการตั้งที่อยู่ path ของไฟล์ทั้งหมดไม่ว่าเราจะย้ายไปโฟลเดอร์ไหน เราแค่แน่ใจว่าเรา require_once มาถูกต้อง
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);  //ฟังก์ชัน dbDeltaวิเคราะห์โครงสร้างตารางปัจจุบันเปรียบเทียบกับโครงสร้างตารางที่ต้องการและเพิ่มหรือเปลี่ยนแปลง

    add_option('CRUD_version', $CRUD_version);  // เก็บ $CRUD_version เข้าไปที่ ตาราง wp_options ด้วยชื่อ CRUD_version
}

function customers_crud_install_data(){ // สร้างขอมูลทดลอง
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers_crud'; // $table_name เก็บชื่อตารางที่เราจะใส่ข้อมูลปลอมลงไป

    $wpdb->insert($table_name, array(
        'name' => 'สมาน',
        'email' => 'saman@thai.com',
        'age' => 25
    ));
    $wpdb->insert($table_name, array(
        'name' => 'หาญกลjา',
        'email' => 'hanga@thai.com',
        'age' => 22
    ));
    $wpdb->insert($table_name, array(
        'name' => 'บุญสนอง',
        'email' => 'sanong@thai.com',
        'age' => 23
    ));
    $wpdb->insert($table_name, array(
        'name' => 'เฉลิมพล',
        'email' => 'ghjkmn@thai.com',
        'age' => 24
    ));
    $wpdb->insert($table_name, array(
        'name' => 'สนิท',
        'email' => 'sanit@thai.com',
        'age' => 25
    ));
    $wpdb->insert($table_name, array(
        'name' => 'สมควร',
        'email' => 'somkurmthai.com',
        'age' => 26
    ));
    $wpdb->insert($table_name, array(
        'name' => 'สมหมาย',
        'email' => 'somai@thai.com',
        'age' => 27
    ));
    $wpdb->insert($table_name, array(
        'name' => 'สาบาน',
        'email' => 'saban@thai.com',
        'age' => 28
    ));
    $wpdb->insert($table_name, array(
        'name' => 'นานมั้ย',
        'email' => 'poiu@thai.com',
        'age' => 29
    ));
    $wpdb->insert($table_name, array(
        'name' => 'Ama',
        'email' => 'ama@thai.com',
        'age' => 30
    ));
    $wpdb->insert($table_name, array(
        'name' => 'janny',
        'email' => 'janny@thai.com',
        'age' => 31
    ));
    $wpdb->insert($table_name, array(
        'name' => 'juk',
        'email' => 'mfgh@thai.com',
        'age' => 32
    ));
    $wpdb->insert($table_name, array(
        'name' => 'mark',
        'email' => 'mark@thai.com',
        'age' => 33
    ));
    $wpdb->insert($table_name, array(
        'name' => 'peeter',
        'email' => 'peeter@thai.com',
        'age' => 34
    ));
    $wpdb->insert($table_name, array(
        'name' => 'crack',
        'email' => 'crack@thai.com',
        'age' => 20
    ));
    $wpdb->insert($table_name, array(
        'name' => 'Lisa',
        'email' => 'iisa@thai.com',
        'age' => 35
    ));
    $wpdb->insert($table_name, array(
        'name' => 'Roze',
        'email' => 'rozehba@thai.com',
        'age' => 36
    ));
}
?>