<?
class DOMTools{
	static function nodeHtml($node){
		$dom = new DOMDocument;
		$dom->appendChild($dom->importNode($node,true));
		return $dom->saveHTML();
	}
}
