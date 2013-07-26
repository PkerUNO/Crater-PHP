<?php
class Setlist extends AppModel {
	public $hasMany = array(
		'Track' => array(
			'dependent' => true,
			'order' => array('Track.setlist_order ASC'),
			)
		);
	public $validate = array(
        'name' => array(
            'rule' => array('maxLength', 255),
            'required' => true,
            'allowEmpty' => false,
            'message' => 'Please keep your setlist name under 255 characters.'
        ),
        'author' => array(
            'rule' => array('maxLength', 255),
            'required' => true,
            'allowEmpty' => false,
            'message' => 'Please keep your author name under 255 characters.'
        ),
        'genre' => array(
        	'genre-rule1' => array(
        		'rule' => 'alphaNumeric',
        		'required' => true,
        		'allowEmpty' => true,
        		'message' => 'Please only use alphanumeric characters in your genre.'
        	),
        	'genre-rule2' => array(
        		'rule' => array('maxLength', 255),
        		'message' => 'Please keep your genre name under 255 characters.'
        	)
        ),
        'master_bpm' => array(
        	'master_bpm_start_rule1' => array(
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => true,
				'message' => 'Please input a number for the Master BPM.'
			),
			'master_bpm_start_rule2' => array(
				'rule' => array('maxLength', 3),
				'message' => 'Please input a 3-digit BPM.'
			)
		)
        //,
/*        'modified' => array(	// Not present in HTTP Request
        	'rule' => 
        ),*/
/*        'public_key' => array(	// Not implemented yet
        ),
        'private_key' => array(
        )*/
    );
}
?>