<?php
class getBestOfMonth extends Parser{ 

	public $parser;

	function __construct($u) {
		$this->parser = new Parser($u);
	}

	function get_xml($id) {
		$dom=new DOMDocument('1.0', 'utf-8');
		$dom->formatOutput=true;
		$post=$dom->createElement('best');
		$val=$dom->appendChild($post);
		$val=$dom->createElement('id',$id);
		$post->appendChild($val);

		foreach($this->parser->getBest($id) as $key => $value) {
			$val=$dom->createElement($key,$value);
			$post->appendChild($val);
		}

		return $dom->saveXML();
	}
}