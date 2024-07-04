<?php
/**
 * @author Bernardo Fuentes
 * @since 03/07/2024 
 */

require_once(__DIR_MODULES__."itivos_emailverification/classes/itivos_emailverification_codes.php");
class validateController extends ModulesFrontControllers
{
	function __construct()
    {
        $this->is_logged = false;
        $this->type_controller = "frontend";
        parent::__construct();
        $this->view->assign('page', "Validar mi cuenta");
    }
    public function index()
    {
        $module_obj = new itivosEmailVerification();
        $token = getValue('token');
        $isin = getValue('isin');
        $try_open = getValue('try_open_url');

        if (empty('token')) {
            $_SESSION['type_message'] = "danger";
            $_SESSION['message'] = "No se ha enviado el token";
            header("Location: ".__URI__."");
            exit();
        }
        if ($token != md5(__TOKEN__.$module_obj->key_module)) {
            $_SESSION['type_message'] = "danger";
            $_SESSION['message'] = "El token enviado no es valido";
            header("Location: ".__URI__."");
            exit();
        }
        $codes_obj = new emailverificationCodes($isin);
        if (empty($codes_obj->id)) {
            $_SESSION['type_message'] = "danger";
            $_SESSION['message'] = "El enlace no es valido o ya expiró";
            header("Location: ".__URI__."");
            exit();
        }
        $customer_obj = New Customers($codes_obj->id_customer);
        if (empty($customer_obj->id)) {
            $_SESSION['type_message'] = "danger";
            $_SESSION['message'] = "El usuario no es valido o no se encontró";
            header("Location: ".__URI__."");
            exit();
        }

        $customer_obj->status = "enabled";
        $customer_obj->save();
        
        $codes_obj->status = "validated";
        $codes_obj->save();

        $_SESSION['type_message'] = "success";
        $_SESSION['message'] = "Cuenta validada correctamente";
        if (!empty($try_open)) {
            header("Location: ".$try_open."");
            exit();
        }else {
            header("Location: ".__URI__."");
            exit();
        }
    }
}