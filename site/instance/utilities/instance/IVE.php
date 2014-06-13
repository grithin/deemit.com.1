<?
//input validators extended
class IVE{
	static $in = array(
			'title' => 'f:toRegex|@[^a-z_\-0-9 \']@i,f:trim,!v:filled',
			'text' => array(
				array('f:stripTags',
					'a,blockquote,center,h1,h2,h3,h4,h5,h6,h7,h8,h9,ul,ol,li,u,i,em,p,strong,span,caption,strike,img,br',
						'title,target,href,src,alt,height,width,style,align',
						array(IVE,'restrictStyle')
				),
				'IVE:cleanText','f:trim','v:filled'),	
		);
	///remove root elements (they aren't needed) and ensure decent html submitted
	static function cleanText(&$value){
		$value = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
		
		$dom = new DOMDocument;
		@$dom->loadHTML($value);
		$root = $dom->documentElement;
		while($root->childNodes->length == 1){
			$root = $root->childNodes->item(0);
		}
		$nodes = $root->childNodes;
		if($nodes){
			for($i = 0, $html = ''; $i < $nodes->length; $i++){
				$node = $nodes->item($i);
				$html .= DOMTools::nodeHtml($node);
			}
		}else{
			$html = $root->nodeValue;
		}
		$value = preg_replace('@\&#13;@','',$html);
	}
	
	static $acceptableStyles = array(
				'text-decoration'=>true,
				'font-weight'=>true,
				'color'=>true
			);
	///clear out problematic styling
	static function restrictStyle(&$tag,&$attributes=null){
		if($attributes){
			foreach($attributes as &$attribute){
				preg_match('@([a-z\-]*)=([\'"])(.+?)\2@i',$attribute,$match);
				if(strtolower($match[1]) == 'style'){
					$okStyles = array();
					$style = $match[3];
					$styles = preg_split('@\s*;\s*@',$style);
					foreach($styles as $style){
						list($name,$value) = preg_split('@\s*:\s*@',$style);
						$name = strtolower($name);
						if(self::$acceptableStyles[$name]){
							$okStyles[] = $name.':'.$value;
						}
					}
					if($okStyles){
						$attribute = 'style="'.implode(';',$okStyles).'"';
					}else{
						$attribute = null;
					}
				}
			}
			unset($attribute);
		}
	}
}
