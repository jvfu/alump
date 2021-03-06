<?php
class ALump_Comment extends ALump_Model {
	
	function __construct($row){
		parent::__construct($row);
		$this->id = $this->get('id');
		$this->post_id = $this->get('post_id');
		$this->created = $this->get('created');
		$this->author = $this->get('author');
		$this->author_id = $this->get('author_id');
		$this->nickname = $this->get('nickname');
		$this->mail = $this->get('mail');
		$this->url = $this->get('url');
		$this->ip = $this->get('ip');
		$this->agent = $this->get('agent');
		$this->content = $this->get('content');
		$this->type = $this->get('type');
		$this->status = $this->get('status');
		$this->parent_id = $this->get('parent_id');
	}
	
  
    public $id = 0;
	public $post_id = 0;
	public $created = 0;
	public $author = 0;
	public $author_id = 0;
	public $mail = 0;
	public $url = 0;
	public $ip = 0;
	public $agent = 0;
	public $content = 0;
	public $type = 0;
	public $status = 0;
	public $parent_id = 0;
	

	public static function getCommentsPage($postid, $commentsPageSize = False, $pageno = 0){
		$db = ALump_Db::getInstance();
		$ordersql = "`created` ".ALump::$options->commentsOrder;
		
		$count = $db->count(ALump_Common::getTabName("comments"),null,array("where"=>" post_id='$postid' and parent_id=0"));
		
		$comments = new ALump_Array($count, $commentsPageSize);
		
		if(!empty($pageno)){
			
			$comments->pageNav->setPageno($pageno);
		}
		
		$db->select(ALump_Common::getTabName("comments"), null, array(
				"where" => " post_id='$postid' and `parent_id`=0 ",
				"order" => $ordersql,
				"limit" => $comments->pageNav->limitSql()));
		
		
		$rows = $db->fetch_array();
		
		foreach($rows as $row){
            $row['content'] = ALump_Common::deEscape($row['content']);
			$comments->add(new ALump_Comment($row));
		}
		return $comments;
	}
    
    public static function getCommentsPageByStatus(){
        $status = ALump::$request->get("s");
    
        switch($status){
            case 'adopt':
                $status = ALump_Common::$ADOPT;
                break;
            case 'audit':
                $status = ALump_Common::$AUDIT;
                break;
            case 'trash':
                $status = ALump_Common::$TRASH;
                break;
            default:
                $status = ALump_Common::$ADOPT;
        }
        
        $keyword = ALump::$request->get('k');
        $key_sql = '';
        if(!empty($keyword)){
            $key_sql = " and (`author` like '%$keyword%' or `ip` like '%$keyword%' or `content` like '%$keyword%')";
        }
        
		$db = ALump_Db::getInstance();
		$ordersql = "`created` desc";
		
		$count = $db->count(ALump_Common::getTabName("comments"),null,array("where"=>' `status`='.$status.$key_sql));
		
		$comments = new ALump_Array($count);
		
		
		$db->select(ALump_Common::getTabName("comments"), null, array(
				"where" => "`status`=$status ".$key_sql,
				"order" => $ordersql,
				"limit" => $comments->pageNav->limitSql()));
		
		
		$rows = $db->fetch_array();
		
		foreach($rows as $row){
            $row['content'] = ALump_Common::deEscape($row['content']);
			$comments->add(new ALump_Comment($row));
		}
		return $comments;
	}
    
    public static function getCommentCountByStatus($status){
        
		$db = ALump_Db::getInstance();
		
		$count = $db->count(ALump_Common::getTabName("comments"),null,array("where"=>' `status`='.$status));
        
        return $count;
		
	}
	
	public function nickName(){
		echo $this->nickname;
	}
	
	public static function save($comment){
		$db = ALump_Db::getInstance();
	
		if($db->insert(ALump_Common::getTabName("comments"), $comment->toArray(array("id")))){
			//ALump_Post::addCommentNum($comment->post_id);
		};
	
		return $db->insert_id();
	}
	
	public static function canComment($ip){
		$db = ALump_Db::getInstance();
		$tarTab = ALump_Common::getTabName("comments");
		$db->query("select max(`created`) as cmax from $tarTab where `ip`='$ip'");
		$row = $db->fetch_one();
		if($row){
			$maxCreated = $row['cmax'];
			if((time() - $maxCreated) > ALump::$options->commentsPostTimeout){
				return True;
			}
		}
		
		return False;
		
	}
	
	public static function getCommentByAuthor($author){
		$db = ALump_Db::getInstance();
		$db->select(ALump_Common::getTabName("comments"), null, array("where" => "`author`='$author'"));
		$row = $db->fetch_one();
		$comment = False;
		if($row){
            $row['content'] = ALump_Common::deEscape($row['content']);
			$comment = new ALump_Comment($row);
		}
		
		return $comment;
	}
    
    public static function getCommentById($id){
		$db = ALump_Db::getInstance();
		$db->select(ALump_Common::getTabName("comments"), null, array("where" => "`id`='$id'"));
		$row = $db->fetch_one();
		$comment = False;
		if($row){
            $row['content'] = ALump_Common::deEscape($row['content']);
			$comment = new ALump_Comment($row);
		}
		
		return $comment;
	}
	
	public static function getChildComments($commentid){
		$db = ALump_Db::getInstance();
		$ordersql = "`created` ".ALump::$options->commentsOrder;
		$db->select(ALump_Common::getTabName("comments"), null, array(
				"where" => " `parent_id`='$commentid'",
				"order" => $ordersql
				));
		
		
		$comments = new ALump_Array();
		$rows = $db->fetch_array();
		
		foreach($rows as $row){
            $row['content'] = ALump_Common::deEscape($row['content']);
			$comments->add(new ALump_Comment($row));
		}
		return $comments;
		
	}
	
	public static function getCommentCount($postid=False){
		$db = ALump_Db::getInstance();
        if(empty($postid)){
            return $db->count(ALump_Common::getTabName("comments"), 'id', array());
        }
		return $db->count(ALump_Common::getTabName("comments"), 'id', array("where"=>"`post_id`='$postid'"));
	}
	
	public static function getCommentsRecent($commentsListSize){
		$db = ALump_Db::getInstance();
		$ordersql = "`created` DESC";
		$comments = new ALump_Array();
		
		$db->select(ALump_Common::getTabName("comments"), null, array(
				"order" => $ordersql,
				"limit" => $commentsListSize));
		
		
		$rows = $db->fetch_array();
		
		foreach($rows as $row){
            $row['content'] = ALump_Common::deEscape($row['content']);
			$comments->add(new ALump_Comment($row));
		}
		return $comments;
	}
    
    public static function changeStatus($id, $status){
        if(empty($id)) return;
        $db = ALump_Db::getInstance();
        $db->update(ALump_Common::getTabName("comments"), array('status'=>$status)," `id`='$id'");
    }
    
    public static function remove($id){
        $db = ALump_Db::getInstance();
        $db->remove(ALump_Common::getTabName("comments"), "`id`='$id'");
    }
    
    public static function update($comment){
        $db = ALump_Db::getInstance();
        
		$db->update(ALump_Common::getTabName("comments"), $comment->toArray(array("id", "created", "status")), "`id`='$comment->id'");
    }
	
	//得到指定的ID的评论是在指定的POST的第几页
	private function _getCommentPageNo(){
		$db = ALump_Db::getInstance();
		$ordersql = "`created` ".ALump::$options->commentsOrder;
		
		$db->select(ALump_Common::getTabName("comments"),array('id'),array("where"=>" post_id='$this->post_id' and parent_id=0", "order" => $ordersql));
		$rows = $db->fetch_array();
       
		$pageSize = ALump::$options->commentsPageSize;
		
		$count = 0;
		foreach($rows as $row){
			$count++;
			$pageno = intval(($count + $pageSize)  / $pageSize);
			if($row['id'] == $this->id || $row['id'] == $this->parent_id){
				return $pageno;
			}
		}
		
		return 1;
		
		
	}
	
	public function author($isReturn = True){
		if(!$isReturn){
			echo $this->author;
		}else{
			return $this->author;
		}
	}
	
	public function getPermalink(){
		$post = ALump_Post::getPostById($this->post_id);
		//<a href="http://localhost/typecho/archives/47.html/comment-page-3#comment-40">admin</a>
		
		$this->permalink = $post->getPermalink().'/comment-page-'.$this->_getCommentPageNo().'#comment-'.$this->id;
		return $this->permalink;
	}
	
	public function permalink(){
		echo $this->getPermalink();
	}
	
	public function excerpt($excerpt, $Prompt){
		$excStr = ALump_Common::deEscape($this->content);
		$dom = str_get_html($excStr);
		if(!empty($dom)){
			echo ALump_Common::subStr($dom->root->text(), 0, $excerpt, $Prompt);
		}
	
	}
	
	
	
}
?>