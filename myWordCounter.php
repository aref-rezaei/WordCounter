<?php

/*
 Plugin Name: My Word Counter
 Description: Plugin to count each post words
 Author: Aref Rezaei
 Version: 1.0
*/

class MyWordCountPlugin{

    function __construct()
    {
        add_action('admin_menu',array($this, 'adminPage'));
        add_action('admin_init',array($this,'settings'));
        add_filter('the_content',array($this,'ifWrap'));
    }

    function ifWrap($content) {
        $option_check = (get_option('wcp_wordcount','1') OR get_option('wcp_charactercount','1') OR get_option('wcp_readtime',1));
        if (is_main_query() AND is_single() AND $option_check) {
            return $this->createHTML($content);
        }
        
        return $content;
    }

    function createHTML($content){

        $html = '<h3>'.get_option('wcp_headline','Post Statistics').'</h3><p>';

        if (get_option('wcp_wordcount','1') OR get_option('wcp_readtime','1')) {
            $wordCount = str_word_count(strip_tags($content));
        }
        
        if (get_option('wcp_wordcount','1')) {
           $html .= 'This post has ' . $wordCount . ' words.<br>';
        }

        if (get_option('wcp_charactercount','1')) {
            $html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>';
        }

        if (get_option('wcp_readtime','1')) {
            $html .= 'This post will take about ' . round($wordCount/255) . ' minute(s) to read.<br>';
        }

        $html .= '</p>';

        if (get_option('wcp_location','0') == '0') {
            return $html . $content;
        }

        return $content . $html;
    }

    function settings(){

        add_settings_section(
            'wcp_first_section',
            null,
            null,
            'word-count-setting-page'
        );

        add_settings_field('wcp_loation','Display Location',array($this,'locationHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordCountPlugin','wcp_location',array('sanitize_callback' => array($this, 'sanitizeLocation'),'default' => '0'));

        add_settings_field('wcp_headline','HeadLine Text',array($this,'headlineHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordCountPlugin','wcp_headline',array('sanitize_callback' => 'sanitize_textarea_field','default' => 'Post Statistics'));

        add_settings_field('wcp_wordcount','Word Count',array($this,'wordcountHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordCountPlugin','wcp_wordcount',array('sanitize_callback' => 'sanitize_textarea_field','default' => '1'));

        add_settings_field('wcp_charactercount','Character Count',array($this,'charactercountHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordCountPlugin','wcp_charactercount',array('sanitize_callback' => 'sanitize_textarea_field','default' => '1'));

        add_settings_field('wcp_readtime','Read Time',array($this,'readtimeHTML'),'word-count-setting-page','wcp_first_section');
        register_setting('wordCountPlugin','wcp_readtime',array('sanitize_callback' => 'sanitize_textarea_field','default' => '1'));
    }

    function sanitizeLocation($input){
        if($input != '0' AND $input != '1') {
            add_settings_error('wcp_location','wcp_location_error','Display location must be either beginning or end');
            return get_option('wcp_location');
        }
        return $input;
    }

    function readtimeHTML(){ ?>
        <input type="checkbox" name="wcp_readtime" value="1" <?php checked(get_option('wcp_readtime'),1)?>>
    <?php }

    function charactercountHTML(){ ?>
        <input type="checkbox" name="wcp_charactercount" value="1" <?php checked(get_option('wcp_charactercount'),1)?>>
    <?php }

    function wordcountHTML(){ ?>
        <input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option('wcp_wordcount'),1)?>>
    <?php }

    function headlineHTML(){ ?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline'))?>">
    <?php }

    function locationHTML(){ ?>

            <select name="wcp_location" >
                <option value="0" <?php selected(get_option('wcp_location','0'))?>>Beginning of post</option>
                <option value="1" <?php selected(get_option('wcp_location','1'))?>>End of post</option>
            </select>
    <?php }

    function adminPage(){

        add_options_page(
            'Word Count Settings',
            'Word Count',
            'manage_options',
            'word-count-setting-page',
            array($this,'ourHTML')
        );
    }
    
    function ourHTML(){ ?>
     
     <div class="wrap">
            <h1>Word Count Settings</h1>
            <form action="options.php" method="post">
                <?php 
                    settings_fields('wordCountPlugin');
                    do_settings_sections('word-count-setting-page');
                    submit_button();
                ?>
            </form>
        </div>
    
    <?php }
} 

$myWordCountPlugin = new MyWordCountPlugin();


