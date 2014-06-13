<?
///Used to read and write to a dababase row (can cross multiple tables)
class RowHandler{
	function __construct($rowSelector){
		/*
		array(
			table => where
		)
			
		
			
		*/
		
	}
	
	
	static private $included;///<an array of included files along with other arguments
	///used to factor out common functionality
	static function __callStatic($name,$arguments){
		self::$currentInclude = array(
				'file'=>$arguments[0],
				'globals'=>$arguments[1],
				'vars'=>$arguments[2],
				'type'=>$name
			);
		return call_user_func_array(array('self',$name),$arguments);
	}
	///include a file
	/**
	@param	file	file path
	@param	globalize	list of strings representing variables to globalize for the included file
	@param	vars	variables to extract for use by the file
	@param	extract	variables to extract from the included file to be returned
	@return	true or extracted varaibles if file successfully included, else false
	*/
	private static function inc($_file,$_globalize=null,$_vars=null,$_extract=null){
		
}


