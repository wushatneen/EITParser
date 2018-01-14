<?php
class getShortInfoById extends Parser{ 

	public $parser;

	function __construct($u) {
		$this->parser = new Parser($u);
	}

	function get_xml($id) {
		$dom=new DOMDocument('1.0', 'utf-8');
		$dom->formatOutput=true;
		$post=$dom->createElement('post');
		$val=$dom->appendChild($post);
		$val=$dom->createElement('id',$id);
		$post->appendChild($val);
		$val=$dom->createElement('image',$this->parser->getMainImage($id));
		$post->appendChild($val);
		$val=$dom->createElement('title',$this->parser->getTitle($id));
		$post->appendChild($val);
		$val=$dom->createElement('description',$this->parser->getDescription($id));
		$post->appendChild($val);
		$val=$dom->createElement('author',$this->parser->getAuthor($id));
		$post->appendChild($val);
		$val=$dom->createElement('date',$this->parser->getDate($id));
		$post->appendChild($val);
		$val=$dom->createElement('category',$this->parser->getCategory($id));
		$post->appendChild($val);
		$val=$dom->createElement('likes',$this->parser->getLikes($id));
		$post->appendChild($val);
		$val=$dom->createElement('dislikes',$this->parser->getDislikes($id));
		$post->appendChild($val);
		$val=$dom->createElement('comments',$this->parser->getComments($id));
		$post->appendChild($val);
		$val=$dom->createElement('views',$this->parser->getViews($id));
		$post->appendChild($val);
		$val=$dom->createElement('link',$this->parser->getLink($id));
		$post->appendChild($val);
		return $dom->saveXML();
	}
}