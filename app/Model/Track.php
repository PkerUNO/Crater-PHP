<?php
class Track extends AppModel {
	public $belongsTo = array(
		'Setlist',
		'KeyStart' => array(
			'className' => 'Key',
			'foreignKey' => 'key_start'
		),
		'KeyEnd' => array(
			'className' => 'Key',
			'foreignKey' => 'key_end'
		)
	);
	public $helpers = array('Time');
	public $recursive = -1;
	public $validate = array(
		'setlist_order' => array(
			'rule' => 'naturalNumber',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Missing setlist_order, this should not appear.'
		),
		'title' => array(
			'rule' => array('maxLength', 255),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please input a track title between 1 and 255 characters.'
		),
		'artist' => array(
			'rule' => array('maxLength', 255),
			'required' => true,
			'allowEmpty' => true,
			'message' => 'Please keep the artist name under 255 characters.'
		),
		'label' => array(
			'rule' => array('maxLength', 255),
			'required' => true,
			'allowEmpty' => true,
			'message' => 'Please keep the label name under 255 characters.'
		),
		'length' => array(
			'rule' => array('custom', '/^([0-5]?[0-9]:[0-5]?[0-9]|[0-5]?[0-9])$/'),	// Captures mm:ss or just ss
			'required' => true,
			'allowEmpty' => true,
			'message' => 'Please provide track length in mm:ss or ss format.'
		),
		'bpm_start' => array(
			'bpm_start_rule1' => array(
				'rule' => '/(?:\A\d{1,3}\.\d{1,2}\z|\A\d{1,3}\z)/',
				'required' => true,
				'allowEmpty' => true,
				'message' => 'Please enter a BPM between 1 and 999.99, maximum 2 decimal places.'
			),
			'bpm_start_rule2' => array(
				'rule' => array('maxLength', 6),
				'message' => 'Please enter a BPM below 999.99.'
			)
		),
		'bpm_end' => array(
			'bpm_end_rule1' => array(
				'rule' => '/(?:\A\d{1,3}\.\d{1,2}\z|\A\d{1,3}\z)/',
				'required' => false,
				'allowEmpty' => true,
				'message' => 'Please enter a BPM between 1 and 999.99, maximum 2 decimal places.'
			),
			'bpm_end_rule2' => array(
				'rule' => array('maxLength', 6),
				'message' => 'Please enter a BPM below 999.99.'
			)
		),
		'key_start' => array(
			'rule' => array('inList', array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24')),
			'required' => true,
			'allowEmpty' => true,
			'message' => 'Please select a valid key from the drop-down.'
		),
		'key_end' => array(
			'rule' => array('inList', array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24')),
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please select a valid key from the drop-down.'
		)
	);
	
	protected $_currentTrack = 1;	// Used to keep tabs on current track during beforeSave() operations

	public function getKeyNotation($key = null) {	// Returns a key notation to display according to user preference
		switch (strtoupper($key)) {
			case "1A":
				return "A♭";
				break;
			case "1B":
				return "B";
				break;
			case "2A":
				return "E♭m";
				break;
			case "2B":
				return "F♯";
				break;
			case "3A":
				return "B♭m";
				break;
			case "3B":
				return "D♭";
				break;
			case "4A":
				return "Fm";
				break;
			case "4B":
				return "A♭";
				break;
			case "5A":
				return "Cm";
				break;
			case "5B";
				return "E♭";
				break;
			case "6A":
				return "Gm";
				break;
			case "6B":
				return "B♭";
				break;
			case "7A":
				return "Dm";
				break;
			case "7B":
				return "F";
				break;
			case "8A":
				return "Am";
				break;
			case "8B":
				return "C";
				break;
			case "9A":
				return "Em";
				break;
			case "9B":
				return "G";
				break;
			case "10A":
				return "Bm";
				break;
			case "10B":
				return "D";
				break;
			case "11A":
				return "F♯m";
				break;
			case "11B":
				return "A";
				break;
			case "12A":
				return "D♭m";
				break;
			case "12B":
				return "E";
				break;
			default:
				return '';
				break;
		}
	}
	
	public function getKeyCode($key = null) {	// Returns a Camelot Key Code for each given key
		$replaceFrom = array('♯', '♭');
		$replaceTo = array('#', 'b');
		
		$key = str_replace($replaceFrom, $replaceTo, $key);
		switch (strtolower($key)) {
			case "abm":
				return "1A";
				break;
			case "b":
				return "1B";
				break;
			case "ebm":
				return "2A";
				break;
			case "f#":
				return "2B";
				break;
			case "gb":
				return "2B";
				break;
			case "bbm":
				return "3A";
				break;
			case "db":
				return "3B";
				break;
			case "fm":
				return "4A";
				break;
			case "ab":
				return "4B";
				break;
			case "g#";
				return "4B";
				break;
			case "cm":
				return "5A";
				break;
			case "eb":
				return "5B";
				break;
			case "gm":
				return "6A";
				break;
			case "bb":
				return "6B";
				break;
			case "dm":
				return "7A";
				break;
			case "f":
				return "7B";
				break;
			case "am":
				return "8A";
				break;
			case "c":
				return "8B";
				break;
			case "em":
				return "9A";
				break;
			case "g":
				return "9B";
				break;
			case "bm":
				return "10A";
				break;
			case "d":
				return "10B";
				break;
			case "f#m":
				return "11A";
				break;
			case "gbm":
				return "11A";
				break;
			case "a":
				return "11B";
				break;
			case "dbm":
				return "12A";
				break;
			case "e":
				return "12B";
				break;
			default:
				return "";
				break;
		}
	}
	
	public function beforeSave($options = array()) {	// Turns 00:00 user input into 00:00:00 for database
		if ($this->data['Track']['length'] && (strlen($this->data['Track']['length']) != 8)) {
			$this->data['Track']['length'] = '00:' . $this->data['Track']['length'];
			$this->data['Track']['length'] = date('H:i:s', strtotime($this->data['Track']['length']));
		}
		
		if (!is_numeric(substr($this->data['Track']['key_start'], 0, 1))) {	// If first character of Key isn't numeric, we need to convert from key notation to a Camelot key code
			$this->data['Track']['key_start'] = $this->getKeyCode($this->data['Track']['key_start']);
		}
		else {	// Key is numeric, therefore valid Camelot Key Code, but let's make sure it's uppercase
			$this->data['Track']['key_start'] = strtoupper($this->data['Track']['key_start']);
		}
		
		$this->data = $this->bsSetSetlistOrder($this->data);
		
		if ($this->validateTrackID($this->data)) {
			return true;
		}
		
		return false;
	}
	
	public function bsSetSetlistOrder($data) {	// Sets each track's setlist_order according to their submission order
		$data['Track']['setlist_order'] = $this->_currentTrack;
		$this->_currentTrack++;
		return $data;
	}
	
	public function afterFind($results, $primary = false) {	// Turns database 00:00:00 into 00:00 for user input
//		debug($results);
		$results = $this->afConvertTrackLength($results);
		
		return $results;
	}
	
	public function calculateBPMDifference($track, $masterBPM) {
		if (isset($track['bpm_start'])) {
			if ($track['bpm_start'] == 0) {
				$track['bpm_difference'] = 0;
			}
			else {
				$track['bpm_difference'] = round((($masterBPM - $track['bpm_start']) / $track['bpm_start']) * 100, 2);	
			}
		}
		else {
			$track['bpm_difference'] = false;
		}
		return $track;
	}
	
	public function calculateKeyDifference($track = null) {
		if (($track['bpm_difference'] || $track['bpm_difference'] === (float)0) && $track['key_start']) {
			$roundedBPMDifference = intval($track['bpm_difference']);
			
			if ($roundedBPMDifference >= 3) {	// Tone goes up
				$toneDifference = intval(($roundedBPMDifference + 3) / 6);
				
				$newKey = (($track['key_start'] + (14 * $toneDifference)) % 24);
				if ($newKey == 0) {
					$newKey = 24;
				}
				$track['key_start_modified'] = $newKey;
			}
			elseif ($roundedBPMDifference <= -3) {	// Tone goes down
				$toneDifference = abs(intval(($roundedBPMDifference - 3) / 6));
				
				$newKey = (($track['key_start'] - (14 * $toneDifference)) % 24);
				if ($newKey < 0) {
					$newKey += 24;
				}
				
				if ($newKey == 0) {
					$newKey = 24;
				}
				$track['key_start_modified'] = $newKey;
			}
			else {
				$track['key_start_modified'] = $track['key_start'];
			}
			
			return $track;
		}
		elseif ($track) {
			$track['key_start_modified'] = 0;
			
			return $track;
		}
		else {
			return 0;
		}
	}
	
	public function validateTrackID($check = null) {
		if (isset($check['Track']['id']) && isset($check['Track']['setlist_id'])) {
			$track = $this->find('first', array(
				'conditions' => array('Track.id' => $check['Track']['id'])));
			
			if (!empty($track) && ($track['Track']['setlist_id'] == $check['Track']['setlist_id'])) {
				return true;	// TrackID belongs to stated SetlistID
			}
		} elseif (empty($check['Track']['id'])) {
			return true;	// New track
		}
		
		return false;	// TrackID was forged
	}
	
	protected function afConvertTrackLength($results) {
		foreach ($results as $i => $result) {
			if (isset($result['Track']['length'])) {
				$results[$i]['Track']['length'] = date('i:s', strtotime($result['Track']['length']));
			}
		}
		
		return $results;
	}
}