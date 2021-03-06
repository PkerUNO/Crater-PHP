<?php
App::uses('Setlist', 'Model');
App::uses('Track', 'Model');

class TrackTest extends CakeTestCase {
	public $fixtures = array('app.setlist', 'app.track', 'app.key');
	
	public function setUp() {
		parent::setUp();
		$this->Setlist = ClassRegistry::init('Setlist');
		$this->Track = ClassRegistry::init('Track');
	}
	
	public function tearDown() {
		unset($this->Setlist);
		unset($this->Track);

		parent::tearDown();
	}
	
	public function testGetKeyNotation() {
		$this->assertEquals('B', $this->Track->getKeyNotation('1B'));
		$this->assertEquals('A♭', $this->Track->getKeyNotation('4B'));
		$this->assertEquals('F♯m', $this->Track->getKeyNotation('11A'));
		$this->assertEquals('E', $this->Track->getKeyNotation('12b'));	// Lowercase letter
		$this->assertEquals('', $this->Track->getKeyNotation());
	}
	
	public function testGetKeyCode() {
		$this->assertEquals('2A', $this->Track->getKeyCode('Ebm'));
		$this->assertEquals('2B', $this->Track->getKeyCode('F#'));
		$this->assertEquals('6B', $this->Track->getKeyCode('b♭'));
		$this->assertEquals('10A', $this->Track->getKeyCode('bM'));
		$this->assertEquals('', $this->Track->getKeyCode());
	}
	
	public function testCalculateBPMDifference() {
		$trackGroupB = $this->Setlist->find('first', array(
			'conditions' => array('Setlist.id' => 2),
			'recursive' => 1));
			
	//	debug($trackGroupB);
		
		$testTrackArray0 = $this->Track->calculateBPMDifference($trackGroupB['Track'][0], $trackGroupB['Setlist']['master_bpm']);
		$testTrackArray1 = $this->Track->calculateBPMDifference($trackGroupB['Track'][1], $trackGroupB['Setlist']['master_bpm']);
		$testTrackArray4 = $this->Track->calculateBPMDifference($trackGroupB['Track'][4], $trackGroupB['Setlist']['master_bpm']);
		
		$this->assertArrayHasKey('bpm_difference', $testTrackArray0);
		$this->assertEquals(0, $testTrackArray0['bpm_difference']);		// 180 -> 180 BPM
		$this->assertEquals(5.88, $testTrackArray1['bpm_difference']);	// 170 -> 180 BPM
		$this->assertEquals(-2.17, $testTrackArray4['bpm_difference']);	// 184 -> 180 BPM
	}
	
	/**
	 * @depends testCalculateBPMDifference
	 */
	
	public function testCalculateKeyDifference() {
		$trackGroupE = $this->Setlist->find('first', array(
			'conditions' => array('Setlist.id' => 5),
			'contain' => array(
        		'Track',
        		'Track.KeyStart',
        		'Track.KeyEnd'
				)
        	)
        );
			
	//	debug($trackGroupE);
		
		$testTrackArray0 = $this->Track->calculateBPMDifference($trackGroupE['Track'][0], $trackGroupE['Setlist']['master_bpm']);
	//	debug($testTrackArray0);
		$testTrackArray0 = $this->Track->calculateKeyDifference($testTrackArray0);
	//	debug($testTrackArray0);
		
		$testTrackArray1 = $this->Track->calculateBPMDifference($trackGroupE['Track'][1], $trackGroupE['Setlist']['master_bpm']);
		$testTrackArray1 = $this->Track->calculateKeyDifference($testTrackArray1);
		
		$testTrackArray2 = $this->Track->calculateBPMDifference($trackGroupE['Track'][2], $trackGroupE['Setlist']['master_bpm']);
		$testTrackArray2 = $this->Track->calculateKeyDifference($testTrackArray2);
		
		$testTrackArray3 = $this->Track->calculateBPMDifference($trackGroupE['Track'][3], $trackGroupE['Setlist']['master_bpm']);
		$testTrackArray3 = $this->Track->calculateKeyDifference($testTrackArray3);
		
		$testTrackArray4 = $this->Track->calculateBPMDifference($trackGroupE['Track'][4], $trackGroupE['Setlist']['master_bpm']);
		$testTrackArray4 = $this->Track->calculateKeyDifference($testTrackArray4);
		
		$testTrackArray5 = $this->Track->calculateBPMDifference($trackGroupE['Track'][5], $trackGroupE['Setlist']['master_bpm']);
		$testTrackArray5 = $this->Track->calculateKeyDifference($testTrackArray5);
		
		$testTrackArray6 = $this->Track->calculateBPMDifference($trackGroupE['Track'][6], $trackGroupE['Setlist']['master_bpm']);
		$testTrackArray6 = $this->Track->calculateKeyDifference($testTrackArray6);
		
		$this->assertArrayHasKey('key_start_modified', $testTrackArray0);
		$this->assertEquals(19, $testTrackArray0['key_start_modified']);	// 156 -> 162 BPM, one tone higher
		$this->assertEquals(10, $testTrackArray1['key_start_modified']);	// 161 -> 162 BPM, no change
		$this->assertEquals(13, $testTrackArray2['key_start_modified']);	// 162 -> 162 BPM, no change
		$this->assertEquals(4, $testTrackArray3['key_start_modified']);	// 168 -> 162 BPM, one tone lower
		$this->assertEquals(17, $testTrackArray4['key_start_modified']);	// 180 -> 162 BPM, two tones lower
		$this->assertEquals(0, $testTrackArray5['key_start_modified']);	// No BPM given, empty result
		$this->assertEquals(0, $this->Track->calculateKeyDifference());	// Invalid input, empty result
		$this->assertEquals(2, $testTrackArray6['key_start_modified']);	// Test wrap-around issue
	}
	
	public function testValidateTrackID() {
		$trackGroupB = $this->Setlist->find('first', array(
			'conditions' => array('Setlist.id' => 1),
			'recursive' => 1));
		
		$testTrackArray7['Track'] = $trackGroupB['Track'][0];
		$testTrackArray8['Track'] = $trackGroupB['Track'][1];
		$testTrackArray9['Track'] = $trackGroupB['Track'][2];
		$testTrackArray10['Track'] = $trackGroupB['Track'][3];
		
		$testTrackArray8['Track']['id'] = '8';	// Track 8 does not belong to Setlist 1
		$testTrackArray9['Track']['id'] = '573';	// Track 573 does not exist yet
		unset($testTrackArray10['Track']['id']);	// Pretending this is a new track
			
		$this->assertTrue($this->Track->validateTrackID($testTrackArray7));		// Existing track
		$this->assertFalse($this->Track->validateTrackID($testTrackArray8));	// Forged TrackID
		$this->assertFalse($this->Track->validateTrackID($testTrackArray9));	// Nonexistant TrackID
		$this->assertTrue($this->Track->validateTrackID($testTrackArray10));	// New track
	}
	
	public function testBsSetSetlistOrder() {
		$trackGroupG = $this->Setlist->find('first', array(
			'conditions' => array('Setlist.id' => 7),
			'recursive' => 1));
			
		$trackGroupG;
			
		foreach ($trackGroupG['Track'] as $key => $track) {
			$dataGroupG[$key]['Track'] = $track;
			$resultGroupG[$key] = $this->Track->bsSetSetlistOrder($dataGroupG[$key]);
			debug($dataGroupG[$key]);
			debug($resultGroupG[$key]);
		}
			
		$this->assertEquals(1, $resultGroupG[0]['Track']['setlist_order']);
		$this->assertEquals(2, $resultGroupG[1]['Track']['setlist_order']);
		$this->assertEquals(3, $resultGroupG[2]['Track']['setlist_order']);
	}
}
?>