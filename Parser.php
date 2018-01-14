<?php
class Parser implements IParser { 

	public $html;

	function __construct($url) {
		$this->html = new simple_html_dom();
		$this->html->load_file($url);
	}

	function getMainImage($id){
		$img = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=image_frame]',0)->find('div[class=image_wrapper]',0)->find('a',0)->find('img',0)->src;
		return trim(str_replace('&nbsp;', ' ', $img));
	}
	function getTitle($id){
		$title = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-title]',0)->find('h3',0)->find('a',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $title));
	}
	function getDescription($id){
		$desc = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-excerpt]',0)->find('span',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $desc));
	}
	function getAuthor($id){
		$author = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-head]',0)->find('span[class=fn]',0)->find('a',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $author));
	}
	function getDate($id){
		$date = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-head]',0)->find('span[class=post-date]',0)->plaintext;
		$time = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-head]',0)->find('span[class=post-date]',1)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $date))." ".trim(str_replace('&nbsp;', ' ', $time));
	}
	function getCategory($id){
		$cat = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-head]',0)->find('div[class=category]',0)->find('a',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $cat));
	}
	function getLikes($id){
		$likes = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-footer]',0)->find('div[class=button-love]',0)->find('span[class=vortex-p-like-counter]',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $likes));
	}
	function getDislikes($id){
		$dislikes = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-footer]',0)->find('div[class=button-love]',0)->find('span[class=vortex-p-dislike-counter]',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $dislikes));
	}
	function getComments($id){
		$comments = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-footer]',0)->find('div[class=post-links]',0)->find('a',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $comments));
	}
	function getViews($id){
		$views = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-footer]',0)->find('div[class=post-views]',0)->find('span[class=post-views-count]',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $views));
	}
	function getLink($id){
		$link = $this->html->find('div[class=posts_group]',0)->find('div[class=post-item]',$id)->find('div[class=post-desc-wrapper]',0)->find('div[class=post-desc]',0)->find('div[class=post-title]',0)->find('h3',0)->find('a',0)->href;
		return trim(str_replace('&nbsp;', ' ', $link));
	}
	function getFullText($link){
		$lnk = new simple_html_dom();
		$lnk->load_file($link);
		//$full = $lnk->find('div[class=image_wrapper]',0)->find('img',0);
		$full = $lnk->find('div[class=the_content]',0)->plaintext;
		return trim(str_replace('&nbsp;', ' ', $full));
	}

	function getBest($id) {
		$best_link = $this->html->find('div[class=Recent_posts]',0)->find('li[class=post]',$id)->find('a',0)->href;
		$best_image = $this->html->find('div[class=Recent_posts]',0)->find('li[class=post]',$id)->find('img',0)->src;
		$best_desc = $this->html->find('div[class=Recent_posts]',0)->find('li[class=post]',$id)->find('h6',0)->plaintext;
		$best_date = $this->html->find('div[class=Recent_posts]',0)->find('li[class=post]',$id)->find('span[class=date]',0)->plaintext;
		$arr = array(	'link' => trim(str_replace('&nbsp;', ' ', $best_link)),
					'image' => trim(str_replace('&nbsp;', ' ', $best_image)),
					'desc' => trim(str_replace('&nbsp;', ' ', $best_desc)),
					'date' => trim(str_replace('&nbsp;', ' ', $best_date))
				);
		return $arr;
	}
}
