<?php

class ALump_PageController extends Alump_BaseController {

	
	public static $PARAMS_COUNT = 4;
	
	private $_comments = null;
	
	function __construct(){
		parent::__construct();
	}
	
	public  function p($slug=False, $action = False){
		if(empty($slug)){
		    $this->say404();
		}
		$commable = false;
	   
		$this->getModuleUrl('p/'.$slug.$this->options->suffix, 'comment-page-');
		
		$this->_curPost = ALump_Post::getPageBySlug($slug);
        ALump_Post::updateViewCount($this->_curPost);
		if(empty($this->_curPost->id)){
		    $this->say404();
		}
		if($action == "comment"){
			$this->doComment($this->_curPost->id);
		}else if(strpos($action, 'comment-page-') !== False){
			$this->_comment_no = substr($action, strrpos($action, '-') + 1);
		}
		$this->setArchiveTitle(array($this->_curPost->title));
		$this->view("page.php");
	
	}
	
	
	public function permalink(){
		$this->_curPost->permalink();
		
	}
    
    public function category(){
		$category = $this->_curPost->category();
		if(!empty($category)){
			$permalink = $category->getPermalink();
			echo '<a href="'.$permalink.'">'.$category->name.'</a>';
		}
		
	}
    
	
	public function title(){
		$this->_curPost->title();
	}
	
	public function author(){
		$author = $this->_curPost->author();
		if(!empty($author)){
			echo $author->name;
		}
		
	}
	
	public function date($fmt){
		echo ALump_Date::format($this->_curPost->created, $fmt);
	}
	
	
	
	public function commentsNum($no, $hasOne, $moreFmt){
		$comment_count = ALump_Comment::getCommentCount($this->_curPost->id);
		
		if(empty($comment_count)){
			echo $no;
		}else{
			if($comment_count == 1){
				echo $hasOne;
			}else{
				echo sprintf($moreFmt, $comment_count);
			}
		}
		
	}
	
	public function comments(){
		$alump = ALump::Lump("Comments_Page", "pageid=".$this->_curPost->id.'&pageno='.$this->_comment_no);
		$alump->to($this->_comments);
		
		return $this->_comments;
	
	}
	
	public function content($Prompt=""){
		echo ALump_Common::deEscape($this->_curPost->content);
	}
	
	public function excerpt($excerpt, $Prompt){
		$excStr = ALump_Common::deEscape($this->_curPost->content);
		$dom = str_get_html($excStr);
		if(!empty($dom)){
			echo ALump_Common::subStr($dom->root->text(), 0, $excerpt, $Prompt);
		}
		
	}
	
	public function allow($power){
		$attr = "allow_".$power;
	    return $this->_curPost->$attr;
	}
	
	
	
	
	public function commentUrl(){
		echo ALump_Common::url("/comment", $this->_curPost->getPermalink());
	}
	
	public function pageNav($prev = '&laquo;', $next = '&raquo;', $splitPage = 3, $splitWord = '...'){
		$this->_commentPageNav($this->_comments, $prev, $next, $splitPage, $splitWord, $this->_comment_no);
	}

}

?>