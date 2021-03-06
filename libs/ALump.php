<?php
class ALump {
	public static $options = null;
	public static $request = null;
	
	private $_funcName = "";
	private $_param = "";
	
	private function __construct($name, $param){
		$this->_funcName = self::toFunctionName($name);
		$this->_param = $param;
		
	}
	
	public static function  Lump($name, $param=False){
		return new ALump($name, $param);
	}
	
	private static function toFunctionName($lumpName){
		return implode("", explode("_", $lumpName));
	}
	
	public function to(&$attr){
		$fname = $this->_funcName;
		$params = $this->_getParams();
		return $attr = $this->$fname($params);
	}
	/**
	 * 获得分类列表
	 */
	public function MetasCategoryListAdmin(){
		return ALump_Meta::getCategorys();
	}
	/**
	 * 获得已发布文章列表
	 * @return ALump_Array
	 */
	public function ContentsPostPublishAdmin(){
		return ALump_Post::getPostsAdmin(ALump_Common::$PUBLISH);
	}
	/**
	 * 获得草稿状态的文章列表
	 * @return ALump_Array
	 */
	public function ContentsPostDraftAdmin(){
		return ALump_Post::getPostsAdmin(ALump_Common::$DRAFT);
	}
	/**
	 * 获得已发布的页面列表
	 */
	public function ContentsPageAdmin(){
		return ALump_Post::getPagesAdmin();
	}
	
	public function ContentsPageList(){
		return ALump_Post::getPages();
	}
	/**
	 * 获得编辑文章
	 */
	public function ContentPostEditAdmin(){
		return ALump_Post::getPostById(ALump::$request->get('id'));
	}
	/**
	 * 获得编辑页面
	 */
	public function ContentPageEditAdmin(){
		return ALump_Post::getPageById(ALump::$request->get('id'));
	}
	/**
	 * 获得分类列表
	 * @return ALump_Array
	 */
	public function MetasCategoryAdmin(){
		return ALump_Meta::getCategorys();
	}
	/**
	 * 获得标签列表
	 * @return ALump_Array
	 */
	public function MetasTagsAdmin(){
		return ALump_Meta::getTags();
	}
	/**
	 * 获得编辑的分类
	 * @return ALump_Meta
	 */
	public function MetaEditCategory(){
		
		return ALump_Meta::getMetaById(ALump::$request->get('cateid'));
	}
	
	/**
	 * 取得POST列表，列表元素个数根据postsListSize配置
	 * @return Ambigous <NULL, ALump_Array>
	 */
	public function ContentsPostRecent(){
		return ALump_Post::getRecentPosts(null, ALump::$options->postsListSize);
	}
	
	/**
	 * 取得类别列表，列表元素个数根据postsListSize配置
	 * @return Ambigous <NULL, ALump_Array>
	 */
	public function MetasCategoryList(){
		return ALump_Meta::getCategorys(ALump::$options->postsListSize);
	}
	
	private function _getParams(){
		if(empty($this->_param)){
			return False;
		}
		$params = explode("&", $this->_param);
		$paramsArr = array();
		foreach($params as $p){
			$ps = explode("=", $p);
			$paramsArr[$ps[0]] = $ps[1];
		}
		
		return $paramsArr;
	}
	
	public function ContentsPostDate($params){
		return ALump_Archive::getArchive($params['format'],  $params['type'], ALump::$options->postsListSize);
		
	}
	
	public function CommentsPage($params){ 
		return ALump_Comment::getCommentsPage($params['pageid'],  ALump::$options->commentsPageSize, $params['pageno']);
	
	}
	
	
	public function CommentsRecent(){
		return ALump_Comment::getCommentsRecent(ALump::$options->commentsListSize);
	}
    
    public function LogsListAdmin(){
		return ALump_Log::getLogList();
	}
    
    public function CommentsListAdmin(){
        return ALump_Comment::getCommentsPageByStatus();
    }
    
    public function AttachListAdmin(){
        return ALump_Post::getAttachListAdmin();
    }
	
	
	
}

?>