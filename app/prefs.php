<?php
class PrefsManager {
	// données de prefs
	public $prefs = array();
	// fichier de prefs
	public $file = "";
	// listes initialisées
	public $tweets = array();
	public $screens = array();
	public $scores = array();
	public $stars = array();
	// réglages de config
	public $twitterMessages = array();
	//
	function __construct($Nscreens = 0, $file = "data/prefs.json") {
		global $twitterMessages;
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
		if(isset($this->prefs['Nscreens'])) {
			if(intval($this->prefs['Nscreens'])>0) {
				$Nscreens = $this->prefs['Nscreens'];
			}
		}
		if(isset($this->prefs['tweets']) && is_array($this->prefs['tweets'])) {
			$this->tweets = $this->prefs['tweets'];
		}
		if(isset($this->prefs['screens']) && is_array($this->prefs['screens'])) {
			$this->screens = $this->prefs['screens'];
			// limiter le nombre de screens à $Nscreens
			if($Nscreens>0) {
				$this->screens = array_filter($this->screens,
					function($n) use ($Nscreens) {
						return $n>0 && $n<=$Nscreens;
					},
					ARRAY_FILTER_USE_KEY
				);
			}
		}
		if(isset($this->prefs['scores']) && is_array($this->prefs['scores'])) {
			$this->scores = $this->prefs['scores'];
		}
		if(isset($this->prefs['stars']) && is_array($this->prefs['stars'])) {
			$this->stars = $this->prefs['stars'];
		}
		if(isset($this->prefs['twitterMessages'])
			&& is_array($this->prefs['twitterMessages'])
			&& !empty($this->prefs['twitterMessages'])) {
			$this->twitterMessages = $this->prefs['twitterMessages'];
		} else {
			$this->twitterMessages = $twitterMessages;
		}
	}
	function save() {
		$this->prefs['scores'] = $this->scores;
		$this->prefs['screens'] = $this->screens;
		$this->prefs['twitterMessages'] = $this->twitterMessages;
		// ne garder que les 10 derniers tweets
		$this->prefs['tweets'] = array_slice($this->tweets,-10,10);
		// vérifier que les fichiers star existent
		$this->prefs['stars'] = array_filter($this->stars,function($is,$star) {
			return file_exists(SOURCES_DIR.$star) && $is;
		},ARRAY_FILTER_USE_BOTH);
		// sauvegarder dans le fichier
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
	function del($key) {
		unset($this->prefs[$key]);
	}
	function poll_embed() {
		$strawpoll_page = $this->get("strawpoll","");
		$strawpoll_page = trim($strawpoll_page);
		$strawpoll_page = preg_replace(',^https?:?/*,i','',$strawpoll_page);
		if(preg_match('`^(www.)?strawpoll.me/(.*)`i',$strawpoll_page,$m)) {
			$strawpoll_page = $m[2];
			$strawpoll_page = preg_replace(',/r?$,i','',$strawpoll_page);
			if(preg_match('`/?([a-z0-9]+)$`',$strawpoll_page,$m)) {
				$numpoll = $m[1];
				return "https://www.strawpoll.me/embed_1/$numpoll/r";
			}
		}
		return "";
	}
	function setScreen($screenNum,$file,$top,$left,$zoom,$stamp=0) {
		if($stamp == 0) {
			$stamp = time();
		}
		if(isset($this->screens[$screenNum])) {
			$screen = $this->screens[$screenNum];
		} else {
			$screen = array(
				"file" => $file,
				"top" => 0,
				"left" => 0,
				"zoom" => 0,
				"stamp" => 0,
			);
		}
		if($top != null) $screen['top'] = $top;
		if($left != null) $screen['left'] = $left;
		if($zoom != null) $screen['zoom'] = $zoom;
		if($stamp != null) $screen['stamp'] = $stamp;
		$this->screens[$screenNum] = $screen;
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
	function screenOn($screenNum) {
		if(isset($this->screens[$screenNum]['file'])) {
			if(isset($this->screens[$screenNum]['on'])) {
				return $this->screens[$screenNum]['on'];
			}
		}
		return true;
	}
	function screenTime($screenNum) {
		if(isset($this->screens[$screenNum])) {
			if(isset($this->screens[$screenNum]['file'])) {
				if($this->screens[$screenNum]['file'] != "") {
					if(isset($this->screens[$screenNum]['stamp'])) {
						return $this->screens[$screenNum]['stamp'];
					}
				}
			}
		}
		return 0;
	}
	function effacer_screen($screen) {
		$file = $this->screenFile($screen);
		if($file != "") {
			$this->screens[$screen] = array(
				'file' => "",
				'top' => 0,
				'left' => 0,
				'zoom' => 0,
				'stamp' => 0,
			);
			$this->save();
			// n'effacer que si l'image n'est pas dans un autre screen
			foreach($this->screens as $screen) {
				if($screen['file'] == $file) {
					return;
				}
			}
			if(file_exists(SCREENS_DIR.$file)) {
				unlink(SCREENS_DIR.$file);
			}
		}
	}
	function sortedScores() {
		$scores = $this->scores;
		usort($scores,function($a,$b) {
			if($b['score'] != $a['score']) return $b['score'] - $a['score'];
			if($b['stamp'] != $a['stamp']) return $b['stamp'] - $a['stamp'];
			return strcmp($a['nom'],$b['nom']);
		});
		return $scores;
	}
}
