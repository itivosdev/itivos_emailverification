<?php 
/**
 * @author Bernardo Fuentes
 * @since 03/07/2024
 */

require_once(__DIR_MODULES__."itivos_emailverification/classes/itivos_emailverification_codes.php");
class itivosEmailVerification extends modules
{
	public $html = "";
    public function __construct()
    {
        $this->name ='itivos_emailverification';
        $this->displayName = "Email Verification";
        $this->description = "Verfica cada cuenta nueva con un enlace";
        $this->category  ='front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Bernardo Fuentes';
        $this->versions_compliancy = array('min'=>'1.0.0', 'max'=> __SYSTEM_VERSION__);
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');
        $this->template_dir = __DIR_MODULES__."itivos_emailverification/views/back/";
        $this->template_dir_front = __DIR_MODULES__."itivos_emailverification/views/front/";
        parent::__construct();

        $this->key_module = "ff2fb4172ccb59d8b2b5e5898ee2c2b5";
        $this->crontLink = __URI__.__ADMIN__."/module/".$this->name."/crontab?key=".$this->key_module."";
    }
    public function install()
    {
        if(!$this->registerHook("actionCustomerNewAccountCreated") ||
           !$this->installDb()){
            return false;
        }
        return true;
    }
    public function installDb()
    {
        $return = true;
        $return &= connect::execute('
            CREATE TABLE IF NOT EXISTS `'.__DB_PREFIX__.$this->name.'_codes` (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `id_customer` varchar(12) NOT NULL,
                `code` varchar(36) NOT NULL, 
                `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` set("waiting", "validated") DEFAULT "waiting",
                PRIMARY key (id)
            ) ENGINE ='.__MYSQL_ENGINE__.' DEFAULT CHARSET=utf8 ;'
        );
        if (!$return) {
            $return = true;
        }
        return $return;
    }
    public function uninstall($drop = true)
    {
    	$return = true;
        $return &= connect::execute("DELETE FROM ".__DB_PREFIX__. "configuration WHERE module = '".$this->name."'");
        if ($drop == true) {
            $return &= connect::execute("DROP TABLE IF EXISTS ".__DB_PREFIX__. $this->name."_codes");
        }
        return $return;
    }
    public function hookActionCustomerNewAccountCreated($params = null)
    {
        $title = "Enlace para validar tu cuenta";
        $email = new ItivosMailer();
        
        $code = isin();
        $email_verification_obj = new emailverificationCodes();
        $email_verification_obj->id_customer = $params['id_customer'];
        $email_verification_obj->code = $code;
        $email_verification_obj->save();

        $data_mail = array();
        $data_mail['fullname'] = $params['firstname']. " ".$params['lastname'];
        $data_mail['email'] = $params['email'];
        
        $uri = __URI__.$this->lang."/module/itivos_emailverification/validate?";
        $uri .= "isin={$code}&token=". md5(__TOKEN__.$this->key_module);
        if (isset($params['try_open_url'])) {
            $uri .= "&try_open_url=".$params['try_open_url'];
        }

        $data_mail['validation_link'] = $uri;
        $email->sendMail(
            $params['email'],
            $title, 
            $data_mail, 
            "email_verification"
        );
    }
}