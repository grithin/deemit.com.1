<?
//display helper
class DH{
	static function roundTo($value,$round=2,$type='',$default=''){
		if($value == '-' || $value == null){
			return $default;
		}else{
			if($type == '%'){
				$value = $value * 100;
			}
			
			$return = number_format(round($value,$round),$round);
			
			if($type){
				if($type == '%'){
					$return = $return.'%';
				}else{
					$return = '$'.$return;
				}
			}
			return $return;
		}
	}
	static function limit($text,$wordSize=35,$totalText=null){
		///see php manual "Once-only subpatterns" for (.?.>.) explanation
		while(preg_match('@((?>[^\s]{'.$wordSize.'}))([^\s])@',$text)){
			$text = preg_replace('@((?>[^\s]{'.$wordSize.'}))([^\s])@','$1 $2',$text,1);
		}
		if($totalText && strlen($text) > $totalText){
			$text = '<span class="shortened" title="'.htmlspecialchars($text).'">'.htmlspecialchars(substr($text,0,$totalText)).'</span>';
		}
		return $text;
	}
	static function entity($name,$id){
		return '<a href="/entity/read/'.$id.'">'.self::limit($name).'</a>';
	}
	static function entityRelation($name,$id){
		return '<a href="/entity/relation/read/'.$id.'">'.self::limit($name).'</a>';
	}
	static function forFactor($value){
		return '<span class="forFactor">'.$value.'</span>';
	}
	static function controlFactor($value){
		return '<span class="controlFactor">'.$value.'</span>';
	}
	static function voteOn(){
		return '<a class="voteOn" title="vote on">[Vote]</a>';
	}
	static function voteOnComment($id){
		return '<a class="voteOnComment" data-commentId="'.$id.'" title="vote on">[Vote]</a>';
	}
	static function significance($significance,$type,$vote=true){
		return '<span class="significance" data-type="'.$type.'">'.$significance.'</span> '.($vote ? DH::voteOn() : '');
		
	}
	
	static function user($name,$id){
		return '<a href="/user/read/'.$id.'">'.self::limit($name).'</a>';
	}
	static function time($time){
		#return '<span class="time">'.i()->Time($time)->format('Y-n-j@G:i e').'';
		return '<span class="time">'.i()->Time($time)->unix().'</span>';
	}
	static function dialogIncludes(){
		$prefix = '/public/resource/MooDialog-0.8/Source/';
		Display::addCss(
				$prefix.'css/MooDialog.css'
			);
		Display::addTopJs(
				$prefix.'Overlay.js',
				$prefix.'MooDialog.js',
				$prefix.'MooDialog.Fx.js',
				'dialog.js',
				'vote.js'
			);
	}
	static function editorIncludes(){
		$prefix = '/public/resource/mooeditable/';
		Display::addCss(
				$prefix.'Assets/MooEditable/MooEditable.css',
				$prefix.'Assets/MooEditable/MooEditable.Extras.css',
				$prefix.'Assets/MooEditable/MooEditable.SilkTheme.css'
			);
		Display::addTopJs(
				$prefix.'Source/MooEditable/MooEditable.js',
				$prefix.'Source/MooEditable/MooEditable.UI.MenuList.js',
				$prefix.'Source/MooEditable/MooEditable.Extras.js',
				'editor.js'
			);
	}
}
