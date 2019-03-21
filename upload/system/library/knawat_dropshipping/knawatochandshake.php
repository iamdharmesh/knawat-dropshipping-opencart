<?php
/**
 * Knawat Dropshipping OpenCart handshake class
 *
 * @link       http://knawat.com/
 * @since      1.0.0
 * @category   Class
 * @author 	   esl4m
 */


class KnawatOCHandshake
{
    private $registry;
    private $is_admin = false;

    /**
     * Knawat Constructor
     */
    public function __construct($registry)
    {

        $this->registry = $registry;

        if (false !== stripos(DIR_APPLICATION, 'admin')) {
            $this->is_admin = true;
        }

        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('module_knawat_dropshipping');

        if (!isset($this->model_extension_module_knawat_dropshipping) || empty($this->model_extension_module_knawat_dropshipping)) {
            $admin_dir = str_replace('system/', 'admin/', DIR_SYSTEM);
            require_once $admin_dir . "model/extension/module/knawat_dropshipping.php";
            $this->model_extension_module_knawat_dropshipping = new ModelExtensionModuleKnawatDropshipping($this->registry);
        }

        if ($settings) {
            $this->knawatValidate($settings['module_knawat_dropshipping_consumer_key'], $settings['module_knawat_dropshipping_consumer_secret']);
        }

        // Load get the current version
        $this->getOCVersion();
        $this->knawatIsConnected();
        $this->getLastSyncDate();
        $this->knawatCronIsConfingured();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->registry->get($name);
    }

    /**
     * Get OpenCart version
     */
    public function getOCVersion()
    {
        if (version_compare(VERSION, '3.0.0', '<')) {
            $this->log->write(VERSION);
        } else {
            $this->log->write(VERSION);
        }
    }

    /**
     * Get Knawat plugin version
     */
//    public function getKnawatPluginVersion()
//    {
//        //
//    }

    /**
     * Check Knawat is connected
     */
    public function knawatIsConnected()
    {
        $is_valid = $this->config->get('module_knawat_dropshipping_valid_token');
        if ($is_valid == '1') {
            $this->log->write('Is connected with Knawat.com');
        } else {
            $this->log->write('Not connected with Knawat.com');
        }
    }

    /**
     * Check Last sync Date
     */
    public function getLastSyncDate(){
//        $get_sql = "SELECT * FROM `" . DB_PREFIX . "knawat_metadata` WHERE `resource_type` = 'knawat_last_imported' LIMIT 1";
//        $result = $this->db->query( $get_sql );
//        if( isset( $result->num_rows ) && $result->num_rows > 0 && !empty( $result->rows ) ){
//            $timestamp = gmdate("F j, Y, g:i a T", $result->rows[0]['meta_value']);  // convert unix timestamp
//            $this->log->write($timestamp);
//        }

        $last_import_time = $this->model_extension_module_knawat_dropshipping->get_knawat_meta('8159', 'time', 'knawat_last_imported' );
        $timestamp = gmdate("F j, Y, g:i a T", $last_import_time);  // convert unix timestamp
        $this->log->write('Last sync Date: ' . $timestamp);
    }

    /**
     * Check Knawat cron is configured
     */
    public function knawatCronIsConfingured(){
        //check for product cron time
        $oldTime = $this->model_extension_module_knawat_dropshipping->get_knawat_meta('8162', 'time', 'cron_time' );
        $current_time = time();
        $difference = $current_time - (int)$oldTime;
        if($difference > 10800){
            $this->log->write('Cron is not configured');
        }
        else{
            $this->log->write('Cron is configured');
        }
    }

    /**
     * Knawat Validate
     * @param string $consumer_key Knawat Consumer Key
     * @param string $consumer_secret Knawat Consumer Secret
     */
    public function knawatValidate($consumer_key, $consumer_secret ){
        if( !empty( $consumer_key ) && !empty( $consumer_key ) ){
            $this->log->write('key: ' . $consumer_key);
            $this->log->write('secret: ' . $consumer_secret);
        }else{
            $this->log->write( $this->language->get('warning_apikey_needed') );
        }
    }
}
