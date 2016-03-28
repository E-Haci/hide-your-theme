<?php
function guaven_pnh_load_defaults() {
    if (get_option("guaven_pnh_already_installed") === false) {
        update_option("guaven_pnh_already_installed", "1");
    }
}


function guaven_pnh_admin() {
    add_submenu_page('options-general.php', 'Hide Your Theme', 
        'Hide Your Theme', 'manage_options', __FILE__, 'guaven_pnh_settings');
}

function guaven_pnh_real_and_pseudo() {
    
    $theme_name = get_stylesheet();
    $theme_name_t = get_template();
    
    $pseudo_themename = get_option('guaven_pnh_themename');
    if ($pseudo_themename != '') {
        return array($theme_name_t, $pseudo_themename, $theme_name);
    }
    return array($theme_name_t, $theme_name_t, $theme_name);
}

add_action('generate_rewrite_rules', 'guaven_pnh_themes_dir_add_rewrites');
function guaven_pnh_themes_dir_add_rewrites() {
    $rt_wp_content_dir=rtrim(WP_CONTENT_DIR, '/');
    $wp_content_path_arr=explode('/', $rt_wp_content_dir);
    $wp_content_dir_name = end($wp_content_path_arr);
    if (empty($wp_content_dir_name)) $wp_content_dir_name = 'wp-content';
    
    $rt_plugin_dir=rtrim(WP_PLUGIN_DIR, '/');
    $plugin_path_arr=explode('/', $rt_plugin_dir);
    $wp_plugin_dir_name = end($plugin_path_arr);
    if (empty($wp_plugin_dir_name)) $wp_plugin_dir_name = 'plugins';
    
    global $wp_rewrite;
    $info_about_pseudo = guaven_pnh_real_and_pseudo();
    
    if ($info_about_pseudo[0]!=$info_about_pseudo[1]) {
        if ($info_about_pseudo[0] != $info_about_pseudo[2]) {
            $new_non_wp_rules_t = array(
            $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[1]) . '-child' . '/(.*)' => $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[2]) . '/$1', 
            $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[2]) . '/screenshot.png' => $wp_content_dir_name . '/' . $wp_plugin_dir_name . '/'.end(explode("/",plugins_url('',__FILE__))).'/screenshot.png',
            $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[2]) . '/style.css' => $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[2]) . '/style-theme.css');
            $wp_rewrite->non_wp_rules+= $new_non_wp_rules_t;

        }
        
        $new_non_wp_rules = array(
            $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[1]) . '/(.*)' => $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[0]) . '/$1', 
            $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[0]) . '/screenshot.png' => $wp_content_dir_name . '/' . $wp_plugin_dir_name . '/'.end(explode("/",plugins_url('',__FILE__))).'/screenshot.png',
            $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[0]) . '/style.css' => $wp_content_dir_name . '/themes/' . esc_attr($info_about_pseudo[0]) . '/style-theme.css'
            );
        $wp_rewrite->non_wp_rules+= $new_non_wp_rules;
    }
}

function guaven_pnh_callback($buffer) {
    if (!is_admin()){


    if (get_option('guaven_pnh_strip')!='')    $buffer=preg_replace('/<!--(.|\\s)*?-->/', '', $buffer);
    if(get_option('guaven_pnh_compress')!='') { 
        

$doc = new DOMDocument();
@$doc->loadHTML($buffer);
$xmtags = $doc->getElementsByTagName('script');
foreach ($xmtags as $xmtag) {
    $buffer=str_replace($xmtag->nodeValue, guaven_pnh_minify_js($xmtag->nodeValue),$buffer);     
}
$xmtags = $doc->getElementsByTagName('style');
foreach ($xmtags as $xmtag) {
    $buffer=str_replace($xmtag->nodeValue, guaven_pnh_minify_css($xmtag->nodeValue),$buffer);     
}

$buffer=guaven_pnh_minify_html($buffer);

         }

    $info_about_pseudo = guaven_pnh_real_and_pseudo();
    if ($info_about_pseudo[0]!=$info_about_pseudo[1]) {


        $buffer = str_replace('/' . $info_about_pseudo[0].'/', '/' . esc_attr($info_about_pseudo[1]).'/', $buffer);
        if ($info_about_pseudo[0] != $info_about_pseudo[2]) {
            $buffer = str_replace('/' . $info_about_pseudo[2].'/', '/' . esc_attr($info_about_pseudo[1] . '-child/'), $buffer);
        }

   
    }
}
    return $buffer;
}

function guaven_pnh_buffer_start() {
    ob_start("guaven_pnh_callback");
}
function guaven_pnh_buffer_end() {
    
    //    ob_end_flush();
    
    
}

add_action('after_setup_theme', 'guaven_pnh_buffer_start');
add_action('shutdown', 'guaven_pnh_buffer_end');

function guaven_pnh_additional_filterer($buffer) {
    $info_about_pseudo = guaven_pnh_real_and_pseudo();
    if ($info_about_pseudo[0]!=$info_about_pseudo[1]) {
        $buffer = str_replace('/' . $info_about_pseudo[0].'/', '/' . $info_about_pseudo[1].'/', $buffer);
        if ($info_about_pseudo[0] != $info_about_pseudo[2]) {
            $buffer = str_replace('/' . $info_about_pseudo[2].'/', '/' . $info_about_pseudo[1] . '-child/', $buffer);
        }
    }
    return $buffer;
}
add_filter('template_directory_uri', 'guaven_pnh_additional_filterer');
add_filter('stylesheet_directory_uri', 'guaven_pnh_additional_filterer');

function guaven_pnh_string_setting($par, $def) {
    if (!empty($_POST[$par])) {
       $k = $_POST[$par];
    } else $k=$def;
    update_option($par, $k);
}

function guaven_pnh_theme_switch() {
    delete_option('guaven_pnh_themename');
    flush_rewrite_rules();
}

add_action('switch_theme', 'guaven_pnh_theme_switch');




function guaven_pnh_make_uncommented_css() {
    
    $info_about_pseudo = guaven_pnh_real_and_pseudo();

    if (!empty($info_about_pseudo)) {
        
        $csscontent = file_get_contents(get_stylesheet_directory() . '/style.css');
        $csscontent = preg_replace('!/\*.*?\*/!s', '', $csscontent);
        $csscontent = preg_replace('/\n\s*\n/', "\n", $csscontent);
        $csscontent = preg_replace('/' . get_template() . '/', esc_attr($info_about_pseudo[1]), $csscontent);
        if (is_writable(get_stylesheet_directory())) {
            file_put_contents(get_stylesheet_directory() . '/style-theme.css', $csscontent);
        } 
        else {
            add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Can\'t activate the feature. Because, your active stylesheet folder is not writable, 
            please go to your theme folder(via ftp or control panel), and make it writable(or contact to your hosting provider) ', 'error');
            return 0;
        }
        
        $csscontent = file_get_contents(get_template_directory() . '/style.css');
        $csscontent = preg_replace('!/\*.*?\*/!s', '', $csscontent);
        $csscontent = preg_replace('/\n\s*\n/', "\n", $csscontent);
        if (is_writable(get_template_directory() )) {
            file_put_contents(get_template_directory() . '/style-theme.css', $csscontent);
        } 
        else {
            add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Can\'t activate the feature. Because, your active template folder is not writable, 
            please go to your theme folder(via ftp or control panel), and make it writable(or contact to your hosting provider)', 'error');
            return 0;
        }
        
        add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Success! Now first open your website and check its source code,
    then go to theme detector websites to check the result', 'updated');
        guaven_pnh_string_setting("guaven_pnh_themename", '');
        guaven_pnh_string_setting("guaven_pnh_compress", '');
        guaven_pnh_string_setting("guaven_pnh_strip", '');
        return 1;
    }
    add_settings_error('guaven_pnh_settings', esc_attr('settings_updated'), 'Some error happened. Re-save again', 'error');
    return -1;
}

function guaven_pnh_minify_html($input) {
    if(trim($input) === "") return $input;

    // Remove extra white-space(s) between HTML attribute(s)
   
    return preg_replace(
        array(
            // t = text
            // o = tag open
            // c = tag close
            // Keep important white-space(s) after self-closing HTML tag(s)
            '#<(img|input)(>| .*?>)#s',
            // Remove a line break and two or more white-space(s) between tag(s)
            '/\s{2,}/',
            '/[\t\n]/',
            '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
            '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
            '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
            '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
            '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
            
        ),
        array(
            '<$1$2</$1>',
           ' ',
            ' ',
            '$1$2$3',
            '$1$2$3$4$5',
            '$1$2$3$4$5$6$7',
            '$1$2$3',
            '<$1$2',
            '$1 ',
            '$1',
        
        ),
    $input);
}


function guaven_pnh_minify_css($input) {
    if(trim($input) === "") return $input;
    return preg_replace(
        array(
            // Remove comment(s)
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
            // Remove unused white-space(s)
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
            // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
            '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
            // Replace `:0 0 0 0` with `:0`
            '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
            // Replace `background-position:0` with `background-position:0 0`
            '#(background-position):0(?=[;\}])#si',
            // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
            '#(?<=[\s:,\-])0+\.(\d+)#s',
            // Minify string value
            
            '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
            // Minify HEX color code
            '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
            // Replace `(border|outline):none` with `(border|outline):0`
            '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
            // Remove empty selector(s)
            '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
        ),
        array(
            '$1',
            '$1$2$3$4$5$6$7',
            '$1',
            ':0',
            '$1:0 0',
            '.$1',
            
            '$1$2$4$5',
            '$1$2$3',
            '$1:0',
            '$1$2'
        ),
    $input);
}
// JavaScript Minifier
function guaven_pnh_minify_js($input) {
    if(trim($input) === "") return $input;
    return preg_replace(
        array(
            // Remove comment(s)
            '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            '#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
            // --ibid. From `foo['bar']` to `foo.bar`
            '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
        ),
        array(
            '$1',
            '$1$2',
            '}',
            '$1$3',
            '$1.$3'
        ),
    $input);
}