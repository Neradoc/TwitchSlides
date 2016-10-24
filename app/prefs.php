<?php
class PrefsManager {
	public $prefs = array();
	public $file = "";
	public $tweets = array();
	public $screens = array();
	function __construct($file = "prefs.json") {
		if(!file_exists($file)) {
			$this->prefs = array();
		} else {
			$tmp = @json_decode(file_get_contents($file),true);
			if(!is_array($tmp)) {
				$this->prefs = array();
			} else {
				$this->prefs = $tmp;
			}
		}
		$this->file = $file;
		// init
		if(isset($this->prefs['tweets']) && is_array($this->prefs['tweets'])) {
			$this->tweets = $this->prefs['tweets'];
		}
		if(isset($this->prefs['screens']) && is_array($this->prefs['screens'])) {
			$this->screens = $this->prefs['screens'];
		}
	}
	function save() {
		// ne garder que les 10 derniers tweets
		$this->prefs['tweets'] = array_slice($this->tweets,-10,10);
		$this->prefs['screens'] = $this->screens;
		file_put_contents($this->file,json_encode($this->prefs));
	}
	function get($key,$defaut=null) {
		if(isset($this->prefs[$key])) {
			return $this->prefs[$key];
		} else {
			return $defaut;
		}
	}
	function set($key,$value) {
		$this->prefs[$key] = $value;
	}
	function poll_embed() {
		$strawpoll_page = $this->get("strawpoll","");
		if(preg_match('`(\d\d+)`',$strawpoll_page,$m)) {
			$numpoll = $m[1];
			return "http://www.strawpoll.me/embed_1/$numpoll/r";
		}
		return "";
	}
	function screenFile($screenNum) {
		if(isset($this->screens[$screenNum])) {
			if(isset($this->screens[$screenNum]['file'])) {
				if($this->screens[$screenNum]['file'] != "") {
					return $this->screens[$screenNum]['file'];
				}
			}
		}
		return "";
	}
	// setScreenFile()
	function screenPos($screenNum) {
		$pos = [0,0,0];
		if(isset($this->screens[$screenNum])) {
			if(isset($this->screens[$screenNum]['file'])) {
				if($this->screens[$screenNum]['file'] != "") {
					if(isset($this->screens[$screenNum]['top'])) {
						$pos[1] = $this->screens[$screenNum]['top'];
					}
					if(isset($this->screens[$screenNum]['left'])) {
						$pos[0] = $this->screens[$screenNum]['left'];
					}
					if(isset($this->screens[$screenNum]['zoom'])) {
						$pos[2] = $this->screens[$screenNum]['zoom'];
					}
				}
			}
		}
		return $pos;
	}
}
