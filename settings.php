<?php
function guaven_pnh_settings()
{

  
if (isset($_POST['guaven_pnh_nonce_f']) and wp_verify_nonce($_POST['guaven_pnh_nonce_f'],'guaven_pnh_nonce')) {

	$settings_result=guaven_pnh_make_uncommented_css();
	settings_errors();
    flush_rewrite_rules();
}
    
    
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br></div><h2>Hide your theme name from WP detectors</h2>

<form action="" method="post">
<?php wp_nonce_field( 'guaven_pnh_nonce','guaven_pnh_nonce_f'); ?>



<table class="form-table">
<tr>
<th scope="row"><label for="guaven_pnh_themename"> Enter any desired theme for your theme</label></th>
<td><input name="guaven_pnh_themename" type="text" id="guaven_pnh_themename" value="<?php
    echo get_option("guaven_pnh_themename");
?>" >
<p><i>Keep blank if you don't want to use it yet</i></p>
</td>
</tr>


<tr>
<th scope="row"><label for="guaven_pnh_compress"> Compress output code</label></th>
<td>
<p>
<input name="guaven_pnh_compress" type="checkbox"  id="guaven_pnh_compress" value="1" <?php
    if(get_option("guaven_pnh_compress")!='') echo 'checked'; ?> > Compress output to make your website a litle faster
</p>
<p>
	<i>This feature removes all whitespaces, linebreaks from your output source code and stript all comments except IE conditional tags.</i>
</p>
    </td>
</tr>


<tr>
<th scope="row"><label for="guaven_pnh_strip"> Strip HTML comments.</label></th>
<td>
<p>
<input name="guaven_pnh_strip" type="checkbox"  id="guaven_pnh_strip" value="1" <?php
    if(get_option("guaven_pnh_strip")!='') echo 'checked'; ?> > Strip HTML  all comments from source code
</p>
<p>
	<i>Some comments may contain your theme name.</i>
</p>
    </td>
</tr>

</table>



<input type="hidden" name="guaven_settings" value="1">
<input type="submit" class="button button-primary" value="Save changes">


<p><br>If you have any problem about using the plugin or you can not get
 the needed result, 
don't worry, just email us via support@guaven.com, we will be glad to help you</p>
</form>
</div>

<?php


}

//////////////////////////////////
?>
