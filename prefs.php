<?php
class PrefsManager {
	public $prefs = array();
	public $file = "";
	public $tweets = array();
	public $screens = array();
	function __construct($file = "prefs.txt") {
		if(!file_exists($file)) {
			$this->prefs = array();
		} else {
			$tmp = @unserialize(file_get_contents($file));
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
		file_put_contents($this->file,serialize($this->prefs));
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
}
