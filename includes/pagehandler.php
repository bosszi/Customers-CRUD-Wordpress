<?php
/*page handler*/

/** ตรวจสอบก่อนเก็บลงดาต้าเบส */
function customers_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers_crud'; // 
    
    // ล้างค่า $message $notice ให้ว่างเพื่อรอรับข้อความใหม่ที่จะเกิดขึ้นในเหตุการณ์ต่างๆ
    $message = '';  // ตั้งค่าเริ่มต้นให้ตัวแปร $message มีค่า '' ถ้าใช้ isset มาตรวจสอบค่าที่ได้จะเป็น true
    $notice = '';   // ตั้งค่าเริ่มต้นให้ตัวแปร $notice มีค่า ''

    // ตั้งค่าเริ่มต้นให้ตัวแปร $default
    $default = array(
        'id' => 0,      // ให้ id เท่ากับ 0 เพื่อจะเขียนฟังชันให้เลือก insert
        'name' => '',
        'email' => '',
        'age' => null,
    );

    /** Nonce = Number Used Once หรือ ตัวเลขที่ใช้ครั้งเดียว 
     * เจ้า nonce จึงเกิดมาเพื่อการณ์นี้ เอาไว้เช็คว่า Request ที่ถูกส่งมา เป็น Request เดิมหรือเปล่า ถ้า nonce เป็นตัวเดิม แปลว่าเป็น Request ซ้ำนั่นเอง 
     * nonec ใช้ป้องกันเหมือนกับ (CSRF) attack 
     * WordPress Nonce นั้นก็คือ การสร้าง Token ( ตัวอักษรแบบสุ่มมั่ว ๆ ) สำหรับใช้ครั้งเดียวขึ้นมาคู่กับการแสดงหน้าแบบฟอร์ม ซึ่ง Token นี้ WordPress เรียกว่า Nonce นั่นเอง
     * ควรจะใช้ Nonce ในทุกๆ ฟอร์มที่สำคัญๆ
     * ที่มา https://webnocode.com/wordpress-nonces-%E0%B8%84%E0%B8%B7%E0%B8%AD%E0%B8%AD%E0%B8%B0%E0%B9%84%E0%B8%A3%E0%B8%9B%E0%B9%89%E0%B8%AD%E0%B8%87%E0%B8%81%E0%B8%B1%E0%B8%99-cross-site-request-forgerycsrf-%E0%B9%84%E0%B8%94/*/
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {        
        $item = shortcode_atts($default, $_REQUEST);
        $item_valid = validate_customer($item); // เรียกฟังชัน validate_customer กรองข้อมูลและนำไปเก็บที่ $item_valid
        if ($item_valid === true) {
            if ($item['id'] == 0) {     // ถ้าไอดีเท่ากับ 0 ให้ insert
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('เพิ่มลูกค้าสำเร็จ', '');
                    customers_page_handler();
                } else {
                    $notice = __('เกิดปัญหาขณะเพิ่มลูกค้า', '');
                }
            } else {    // ถ้าไอดีไม่เท่ากับ 0 ให้ update ตามค่าไอดีที่ส่งมา
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('แก้ใขข้อมูลลูกค้าสำเร็จ', '');
                } else {
                    $notice = __('เกิดปัญหาขณะแก้ใขข้อมูลลูกค้า', '');
                }
            }
        } else {
            // ถ้า validate_customer ไม่สำเร็จให้นำข้อความที่ส่งมาเก็บ $notice 
            $notice = $item_valid; // notice แจ้งให้ทราบล่วงหน้า
        }
    }
    else {
        // ถ้า _REQUEST ไม่ผ่านจะล้างค่า
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('ไม่พบข้อมูลลูกค้า', '');
            }
        }
    }
    // เพิ่ม meta box ของเรา คือเราจะไปเรียกฟังชันcustomer_form_meta_box ด้านล่าง $callback ที่เขียนใว้แล้วส่งไปที่ do_meta_boxes
    add_meta_box('customer_meta_box', 'เพิ่มลูกค้า', 'customer_form_meta_box', 'customers', 'normal', 'default');
    // add_meta_box($id,$title,$callback,ชี้ไปที่ do_meta_boxes,ตั้งให้ตรง do_meta_boxes',$priority = 'default', ตัวแปรจะไม่ส่งก็ได้)
    ?>

<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('แก้ใขลูกค้า', '')?> <a class="add-new-h2"
        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=customers');?>"><?php _e('หน้ารวมลูกค้า', '')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST"> <!-- ฟอร์มใช้สำหรับ เพิ่ม และ แก้ใขลูกค้า -->
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>    <!-- สร้าง Nonce -->
        <?php /* เก็บค่าไอดีเพื่อจะเพิ่มหรืออัพเดท */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php do_meta_boxes('customers', 'normal', $item); ?>      <!-- เรียก meta box -->
                    <input type="submit" value="<?php _e('บันทึก', '')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>

</div>
<?php
}

/** ฟังชันที่ meta box เรียกไปทำงาน */
function customer_form_meta_box($item)  // ฟังชันสำหรับกรอกข้อมูล  เพิ่ม และ อัพเดท
{
    ?>
<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="name"><?php _e('ชื่อ', '')?></label>
            </th>
            <td>
                <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
                    size="50" class="code" placeholder="<?php _e('กรุณาใส่ชื่อ', '')?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="email"><?php _e('อีเมล์', '')?></label>
            </th>
            <td>
                <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['email'])?>"
                    size="50" class="code" placeholder="<?php _e('กรุณาใส่อีเมล', '')?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="age"><?php _e('อายุ', '')?></label>
            </th>
            <td>
                <input id="age" name="age" type="number" min="1" max="100" style="width: 95%" value="<?php echo esc_attr($item['age'])?>"
                    size="50" class="code" placeholder="<?php _e('กรุณาใส่อายุ', '')?>" required>
            </td>
        </tr>
    </tbody>
</table>
<?php
}

/*** ฟังชัน validates*/
function validate_customer($item)    // ส่วนนี้ถ้ามี Error จะไปแจ้งที่ Notice
{
    $messages = array(); 
    if (empty($item['name'])) $messages[] = __('กรุณาใส่ชื่อ', ''); // ถ้า name เป็นค่าว่างให้เพิ่มเข้า $messages
    if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('รูปแบบอีเมล์ไม่ถูกต้อง', '');
    if (!ctype_digit($item['age'])) $messages[] = __('กรุณาใส่อายุ', '');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...

    if (empty($messages)) return true;
    return implode('<br/>', $messages); // ฟังก์ชัน implode() สร้าง string จากข้อมูลใน array ด้วยฟังก์ชัน implode()
}

?>