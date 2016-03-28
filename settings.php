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

<hr>

<p>
<br><br>
<small>
It is nearly impossible that after installing and activating the plugin something will go wrong in 
your website. But anyway, if some problem happens, just deactivate the plugin and contact 
us(support@guaven.com).  <br>

If you don’t want to wait support respond, then just do these steps:  <br>
1. Deactivate or delete the plugin. And check your website. If it is ok, that’s all, but if 
problem still exists go to next step. <br>
2. Remove .htaccess file from your home folder. <br>
3. Go to wp­admin­>settings­>permalink and just click to click Save Settings button to 
regenerate .htaccess. 

</small>
</p>

<hr>
<p>
<br><br>
Make any donation to support our free plugins :) <br>
<small>Donation is provided via ruble currency (350 rubles ~ 5$) <a target="_blank" href="https://www.google.com/search?q=350+rubles+to+usd&oq=350+rubles&aqs=chrome.0.69i59j69i57.1730j0j1&sourceid=chrome&ie=UTF-8#q=350+rubles+to+usd">Check Google for exact currency for today </a></small>

</p>

<p>
<a href="https://money.yandex.ru/to/410011747184373" target="_blank" rel="attachment wp-att-47">
<img class="alignnone size-medium wp-image-47" src="http://guaven.com/myfiles/uploads/2016/02/md-300x100.png" alt="md" width="150" height="50" ></a></p>
</p>
</form>
</div>

<?php


}

//////////////////////////////////
?>
