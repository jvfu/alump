<?php
class ALump_User extends ALump_Model {
	/**
	 * 用户组
	 * 编辑者，管理者
	 * 用户初始化的时候的用户即为管理者
	 * 管理者可以拥有编辑权限，管理用户权限
	 * 编辑者由管理者指定，可以编辑网站内容
	 * @var unknown_type
	 */
	public static $GROUPS = array(
			"editor", "admin"
			);
	
	
	function __construct($row){
		parent::__construct($row);
		$this->id = $this->get('id');
		$this->name = $this->get('name');
		$this->password = $this->get('password');
		$this->mail = $this->get('mail');
		$this->url = $this->get('url');
        $this->created = $this->get('created');
        $this->visited = $this->get('visited');
		$this->nickname = $this->get('nickname');
		$this->group = $this->get('group');
	}
	
	public $id = 0;
	public $name = "";
	public $password = "";
	public $mail = "";
	public $url = "";
	public $nickname = "";
	public $created = "";
    public $visited = "";
	public $group = "editor";
	
   
	function __toString(){
		return sprintf("{%s,%s,%s,%s,%s}",
				$this->name,
				$this->mail,
		        $this->url,
		        $this->nickname,
		        $this->group
				);
	}
    
	
	public static function getUserById($id){
		$db = ALump_Db::getInstance();
		$db->select(ALump_Common::getTabName("users"), null, array('where' => " id='$id'"));
		$user = new ALump_User($db->fetch_one());
		return $user;
		
	}
    
    public static function updateVisitTime($username){
        $db = ALump_Db::getInstance();
        $db->update(ALump_Common::getTabName("users"),array("visited"=>time()), " `name`='$username'" );
    }
	
	public static function getUserByName($name){
		$db = ALump_Db::getInstance();
		$db->select(ALump_Common::getTabName("users"), null, array('where' => " name='$name'"));
		$user = new ALump_User($db->fetch_one());
        if(empty($user->mail)){
            $user->mail = "admin@admin.com";
        }
        if(empty($user->url)){
            $user->url = "http://xiaolan.tk";
        }
		return $user;
	
	}
    
    public static function updateUserPassword($userId, $newPassword){
        $db = ALump_Db::getInstance();
        return $db->update(ALump_Common::getTabName("users"),array("password"=>$newPassword), " `id`='$userId'" );
    }
    
    public static function update($user){
        $db = ALump_Db::getInstance();
        return $db->update(ALump_Common::getTabName("users"),
                array("nickname"=>$user->nickname,"url"=>$user->url,"mail"=>$user->mail),
                " `id`='$user->id'" );
        
    }
	
	public function nickName(){
		echo $this->nickname;
	}
	
	
	
	
}
?>