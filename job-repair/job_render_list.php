<?php 
function job_render_list(){   
    global $wpdb;
    
    $job_render_list = new Job_list();   ?>
    <div class="wrap">
        <h2>ตาราง งานซ่อม</h2>                                     
        <?php $job_render_list->prepare_items();?>    
        <form method="post" >                
            <input type="hidden" name="page" value = "<?php echo $_REQUEST['page'] ?>" > 
            <div class="row">
                <div class="col-sm">
                    <a class="btn btn-success" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=job');?>">
                    <?php _e('ตารางงานซ่อม')?></a>
                    <a class="btn btn-success mx-3" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=job_viu&action=insert');?>">
                    <?php _e('เพิ่มงานซ่อม')?></a>                
                </div>              
                <div class="col-sm-4">                    
                    <select name="search_option" class="form-select-3" aria-label="Default select example">
                        <option value="job_number" selected>รหัสงานซ่อม</option>
                        <option value="job_brand">ยี่ห้อ</option>
                        <option value="job_name">ชื่อเครื่อง</option>
                        <option value="job_model">Model</option>
                        <option value="job_serial">serial</option>
                        <option value="job_all">เลือกทั้งหมด</option>
                    </select>
                    <?php $job_render_list->search_box('ค้นหา', '$search_id');?>
                </div>
            </div>
            <?php $job_render_list->display();?>
        </form>
    </div>  
<?php      //echo '<pre>'; echo print_r($value); echo '</pre>';

}?>
