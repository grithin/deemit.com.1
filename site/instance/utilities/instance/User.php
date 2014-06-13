<?
class User{
//+	basic login stuff {
	static function login($id,$name){
		Session::create();
		Session::$other = array('user_id'=>$id);
		$_SESSION['userId'] = $id;
		$_SESSION['displayName']=$name;
		
		$userIpId = Db::row('user_ip',array(
				'user_id' => $id,
				'ip' => $_SERVER['REMOTE_ADDR']
			),'id');
		if($userIpId){
			Db::update('user_ip',array('time_login' =>  i()->Time()->datetime()),$userIpId);
		}else{
			Db::insert('user_ip',array(
				'user_id' => $id,
				'ip' => $_SERVER['REMOTE_ADDR'],
				'time_created' => i()->Time()->datetime(),
				'time_login' =>  i()->Time()->datetime(),
			));
		}
		
		Db::update('user',array('time_last_login'=>i()->Time()->datetime()),$id);
	}
	static function logout(){
		//log action
		session_destroy();
	}
	
	///ensure user is logged in
	static function required(){
		if(!$_SESSION['userId']){
			Cookie::set('url',$_SERVER['REQUEST_URI']);
			Http::redirect('/user/login');
		}
	}
	///returns id of current user - easier to remember than the session variable
	static function id(){
		return $_SESSION['userId'];
	}
//+	}
//+	basic user and group privilege handling{
	static function isAdmin(){
		return $_SESSION['isAdmin'];
	}
	static $userGroups;
	static $userGroupPrivileges;
	static $userPrivileges;
	static function getUserGroups($userId=null){
		$userId = $userId ? $userId : $_SESSION['userId'];
		if(!isset(self::$userGroups[$userId])){
			self::$userGroups[$userId] = Db::column('user_group_user',array('user_id'=>$userId),'group_id');
		}
		return self::$userGroups[$userId];
	}
	static $groupIds;
	static function inGroup($name,$user=null){
		if(!self::$groupIds[$name]){
			self::$groupIds[$name] = Db::row('user_group',array('name'=>$name),'id');
		}
		$groups = self::getUserGroups($user);
		return in_array(self::$groupIds[$name],$groups);
	}
	///Check if user has a privilege
	/**
	@param	privilege	either int id or string name
	@param	userId	the user to check.  Defaults to current user
	*/
	static function hasPrivilege($privilege,$userId=null){
		if(!$userId){
			$userId = $_SESSION['userId'];
		}
		//privilege can be given as either the id or the name
		if(!Tool::isInt($privilege)){
			$privilege = self::getPrivilege($privilege);
		}
		
		if(!isset($userPrivileges[$userId][$privilege])){
			$groups = self::getUserGroups();
			if($groups){
				//check the users groups to see if they give user the privilege
				foreach($groups as $group){
					if(!isset(self::$userGroupPrivileges[$group])){
						self::$userGroupPrivileges[$group] = Db::columnKey('privilege_id','user_group_privilege',array('group_id'=>$group),'id,privilege_id');
					}
					if(self::$userGroupPrivileges[$group][$privilege]){
						$userPrivileges[$userId][$privilege] = true;
						break;
					}
				}
			}
		}
		//check if the privilege is given directly to the user
		if(!isset($userPrivileges[$userId][$privilege])){
			$userPrivileges[$userId][$privilege] = Db::row('user_privilege',array('user_id'=>$userId,'privilege_id'=>$privilege),'1');
		}
		
		return $userPrivileges[$userId][$privilege];
	}
	static $privileges;
	static function getPrivilege($name){
		if(!self::$privileges[$name]){
			self::$privileges[$name] = Db::row('user_privilege_type',array('name'=>$name),'id');
		}
		return self::$privileges[$name];
	}
	static function requirePrivilege($privilege){
		if(!self::hasPrivilege($privilege)){
			die('Not authorized: '.$privilege);
		}
	}
//+	}
//+	basic user action logging {
	static $tables;
	///Check if user has a privilege
	/**
	@param	table	name of table
	@param	type	insert, delete, update
	@param	rowData	the data used on update, insert
	@param	$rowId	the row id
	*/
	static function logTableChange($table,$type,$rowData=null,$rowId=null){
		if(!$rowId){
			$rowId = $rowData['id'];
		}
		$tableId = self::getTableId($table);
		$typeId = Db::row('user_log_table_change_type',array('name'=>$type),'id');
		
		Db::insert('user_log_table_change',array(
			'user_id' => $_SESSION['userId'],
			'table_id' => $tableId,
			'row_id' => $rowId,
			'type_id' => $typeId,
			'time' => i()->Time()->datetime(),
			'change' => serialize($rowData)
		));
	}
	static function getTableId($name){
		$tableId = Db::row('table_type',array('name'=>$name),'id');
		if(!$tableId){
			$display = ucwords(preg_replace('@_@',' ',$name));
			$tableId = Db::insert('table_type',array('name'=>$name,'display'=>$display));
		}
		return $tableId;
	}
	static function logAction($action,$data=null,$user=null){
		if(!$user){
			$user = $_SESSION['userId'];
		}
		$action = self::getAction($action);
		
		Db::insert('user_log_action',array(
				'user_id' => $user,
				'action_id' => $action,
				'time' => i()->Time()->datetime(),
				'data' => $data,
			));
	}
	static $actions;
	static function getAction($name){
		if(!self::$actions[$name]){
			self::$actions[$name] = Db::row('user_log_action_type',array('name'=>$name),'id');
		}
		return self::$actions[$name];
	}
//+	}
//+	basic user account functions
	static function changePassword($current,$new){
		if(!Db::row('select 1 from user where password = '.Db::quote(md5($current)).' and id = '.Db::quote($_SESSION['userId']))){
			Page::$errors[] = 'Incorrect password entered';
		}else{
			Db::update('user',array('password'=>md5($new)),$_SESSION['userId']);
			Page::$messages[] = 'Password updated';
		}
	}
	static function disable($userId){
		Db::delete('session',array('user_id'=>$userId));
		Db::update('user',array('is_disabled'=>1),$userId);
	}
//+	}
}
