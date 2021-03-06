<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="content-type" content="text/html; charset=<?php $this->options->charset(); ?>" />
<title><?php $this->archiveTitle(' &raquo; ', '', ' - '); ?><?php $this->options->title(); ?></title>

<!-- 使用url函数转换相关路径 -->
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" media="all" href="<?php $this->options->themeUrl('style.css'); ?>" />

<!-- 通过自有函数输出HTML头部信息 -->
<?php $this->header(); ?>
</head>

<body class="custom-font-enabled">

<div id="page" class="width">

<div id="header">
	<div class="logo">
	    <h1 class="yahei"><a href="<?php $this->options->siteUrl(); ?>">
        <?php $this->options->title() ?>
        </a></h1>
	    <p class="description"><?php $this->options->description() ?></p>
    </div>
</div><!-- end #header -->

<div id="nav_box" class="nav">
    
<ul>
    <li<?php if($this->is('index')): ?> class="current"<?php endif; ?>><a href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a></li>
    <?php $this->alump('Contents_Page_List')->to($pages); ?>
    <?php while($page = $pages->next()): ?>
    <li<?php if($this->is('page', $page->slug)): ?> class="current"<?php endif; ?>><a href="<?php $page->permalink(); ?>" title="<?php $page->title(); ?>"><?php $page->title(); ?></a></li>
    <?php endwhile; ?>
</ul>
</div>

<div class="wrapper">
