<?php 
function job_install(){   // สร้าง Database 
    global $wpdb;
    $collate = '';                              // Set charset
    if ( $wpdb->has_cap( 'collation' ) ) {
        $collate = $wpdb->get_charset_collate() . ' engine = innoDB';   //เพื่อใช้งาน FOREIGN KEY
    }
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $queries = array();
    $table_job = $wpdb->prefix.'job';
    array_push($queries, "CREATE TABLE IF NOT EXISTS $table_job (    /* IF NOT EXISTS ถ้าไม่มีตารางนี้จะสร้างใหม่*/
        job_id int(10) unsigned NOT NULL  AUTO_INCREMENT COMMENT 'เก็บจำนวนงานรัน AUTO',
        job_number mediumint(6) unsigned ZEROFILL NOT NULL COMMENT 'เก็บรหัสงาน 634352', /** UNIQUE ถ้าใส่ข้อมูลที่เหมือนกันจะไม่สามารถเขียนทับได้ */
        job_brand smallint(4) unsigned NOT NULL COMMENT 'เก็บ id ชื่อยี่ห้อตามตาราง',
        job_name varchar(25) COMMENT 'เก็บชื่องานซ่อม',
        job_model varchar(25) COMMENT 'เก็บชื่อ model หรือ type',
        job_serial varchar(25) COMMENT 'เก็บ serial',
        job_picture varchar(25) COMMENT 'เก็บชื่อรูปภาพ',
        job_description varchar(1000) COMMENT 'เก็บรายละเอียดอาการเสีย', 
        job_customer int(10) unsigned NOT NULL COMMENT 'เก็บรายชื่อลูกค้าอ้างอิงตามตารางลูกค้า',
        job_type mediumint(6) unsigned NOT NULL default '1' COMMENT 'เก็บงานซ่อมหรือขายหรือก๊อปปี้ตามตารางชนิด',
        job_comment varchar(1000) COMMENT 'เก็บหมายเหตุ',        
        job_date datetime default '0000-00-00 00:00:00' NOT NULL  COMMENT 'เก็บวันที่รับงานเข้า', 
        job_update datetime default '0000-00-00 00:00:00' NOT NULL  COMMENT 'เก็บวันที่แก้ใข',
        PRIMARY KEY  (job_id),
        UNIQUE KEY job_number (job_number) 
        /*FOREIGN KEY  (job_brand) REFERENCES ตารางtable_aj_job_brand(brand_id) ถ้าในตาราง aj_job_brand ไม่มีข้อมูลที่ตรงกันจะ insert ไม่ได้*/ 
    ) {$collate}");
    $table_job_brand = $wpdb->prefix.'job_brand';
    array_push($queries, "CREATE TABLE IF NOT EXISTS $table_job_brand (
        brand_id smallint(4) unsigned NOT NULL  AUTO_INCREMENT COMMENT 'เก็บจำนวนงานรัน AUTO',        
        brand_name varchar(25) NOT NULL COMMENT 'เก็บชื่อยี่ห้อตามตาราง',        
        PRIMARY KEY  (brand_id),
        UNIQUE KEY brand_name (brand_name)
    ) {$collate}");
    foreach ($queries as $key => $sql) {
        dbDelta( $sql );
    }
}

function job_install_data(){ // สร้างขอมูลทดลอง
    global $wpdb;
    $table_job       = $wpdb->prefix.'job';
    $table_job_brand = $wpdb->prefix.'job_brand'; // $table_aj_job เก็บชื่อตารางที่เราจะใส่ข้อมูลปลอมลงไป

    $wpdb->insert($table_job,
    array(
        'job_number'        => 633114,
        'job_brand'         => 1,
        'job_name'          => 'servo',
        'job_model'         => 'A06B-1112',
        'job_serial'        => 'EA7777',
        'job_picture'       => 'youtube.jpg',
        'job_description'   => 'AL-9',
        'job_customer'      => 1,
        'job_type'          => 1,
        'job_comment'       => 'หมายเหตุ',
        'job_date'          => current_time('mysql'),
        'job_update'        => current_time('mysql')
    ));

    $wpdb->insert($table_job,
    array(
        'job_number'        => 644354,
        'job_brand'         => 2,
        'job_name'          => 'air',
        'job_model'         => 'Eghui-53gg',
        'job_serial'        => 'dd652255',
        'job_picture'       => 'พักก่อน.jpg',
        'job_description'   => 'ดับ',
        'job_customer'      => 2,
        'job_type'          => 2,
        'job_comment'       => 'หมายเหตุ',
        'job_date'          => current_time('mysql'),
        'job_update'        => current_time('mysql')
    ));
    $wpdb->insert($table_job,
    array(
        'job_number'        => 644355,
        'job_brand'         => 3,
        'job_name'          => 'air',
        'job_model'         => 'Eghui-53gg',
        'job_serial'        => 'dd652255',
        'job_picture'       => 'ยุงบินวน.jpg',
        'job_description'   => 'ดับ',
        'job_customer'      => 2,
        'job_type'          => 2,
        'job_comment'       => 'หมายเหตุ',
        'job_date'          => current_time('mysql'),
        'job_update'        => current_time('mysql')
    ));
    $wpdb->insert($table_job,
    array(
        'job_number'        => 644356,
        'job_brand'         => 4,
        'job_name'          => 'air',
        'job_model'         => 'Eghui-53gg',
        'job_serial'        => 'dd652255',
        'job_picture'       => 'circuit.jpg',
        'job_description'   => 'ดับ',
        'job_customer'      => 2,
        'job_type'          => 2,
        'job_comment'       => 'หมายเหตุ',
        'job_date'          => current_time('mysql'),
        'job_update'        => current_time('mysql')
    ));
    $wpdb->insert($table_job,
    array(
        'job_number'        => 644357,
        'job_brand'         => 2,
        'job_name'          => 'air',
        'job_model'         => 'Eghui-53gg',
        'job_serial'        => 'dd652255',
        'job_picture'       => '',
        'job_description'   => 'ดับ',
        'job_customer'      => 2,
        'job_type'          => 2,
        'job_comment'       => 'หมายเหตุ',
        'job_date'          => current_time('mysql'),
        'job_update'        => current_time('mysql')
    ));
    $wpdb->insert($table_job,
    array(
        'job_number'        => 644358,
        'job_brand'         => 1,
        'job_name'          => 'air',
        'job_model'         => 'Eghui-53gg',
        'job_serial'        => 'dd652255',
        'job_picture'       => 'shock.jpg',
        'job_description'   => 'ดับ',
        'job_customer'      => 2,
        'job_type'          => 2,
        'job_comment'       => 'หมายเหตุ',
        'job_date'          => current_time('mysql'),
        'job_update'        => current_time('mysql')
    ));

    $wpdb->insert($table_job_brand,
    array(
        'brand_id'     => 1,
        'brand_name'   => 'FANUC'
    ));
    $wpdb->insert($table_job_brand,
    array(
        'brand_id'     => 2,
        'brand_name'   => 'SAMSUNG'
    ));
    $wpdb->insert($table_job_brand,
    array(
        'brand_id'     => 3,
        'brand_name'   => 'YASKAWA'
    ));
    $wpdb->insert($table_job_brand,
    array(
        'brand_id'     => 4,
        'brand_name'   => 'SANYO DENKI'
    ));
    $wpdb->insert($table_job_brand,
    array(
        'brand_id'     => 5,
        'brand_name'   => 'COSEL'
    ));
}
?>