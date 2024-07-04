<?php
/**
 * @author Bernardo Fuentes
 * @since 03/07/2024
 */

class itivos_emailverification_codes extends Model
{
	public $id;
	public $id_customer;
	public $code;
	public $date_created;
	public $status;

	function __construct($id = null)
	{
		if ($id != null) {
			if (is_string($id)) {
				$data = self::getByCode($id);
			}else {
				$data = self::select($id);
			}
			if (!empty($data)) {
				$this->loadPropertyValues($data);
			}
		}
	}
	public static function getByCode($code)
	{
		$query = "SELECT c.* 
					FROM ".__DB_PREFIX__."itivos_emailverification_codes c
				  WHERE c.code = '".$code."' AND 
				  		c.status = 'waiting' 
				  ";
		return connect::execute($query, "select", true);
	}
}
class_alias("itivos_emailverification_codes","emailverificationCodes");