<?php

namespace Music;

class Chord {

	/* Chord formulas as constants 
	 * Formulas are lists of distances in halftones between notes in chord
	 */
	const formulas = array(
		'minor' => array(
			'name' => 'minor',
			'f' => array(
				2, 3
			)
		),

		'major' => array(
			'name' => 'major',
			'f' => array(
				3, 2
			)
		),

		'maj7' => array(
			'name' => 'maj7',
			'f' => array(
				3, 2, 3
			)
		),

		'm7' => array(
			'name' => 'm7',
			'f' => array(
				2, 3, 2
			)
		),

		'dim' => array(
			'name' => 'dim',
			'f' => array(
				2, 2
			)
		),

		'dim7' => array(
			'name' => 'dim7',
			'f' => array(
				2, 2, 2
			)
		),

		'sus4' => array(
			'name' => 'sus4',
			'f' => array(
				4, 1
			)
		),

		'sus2' => array(
			'name' => 'sus2',
			'f' => array(
				1, 4
			)
		)
	);

	public function __get($chord){
		return false;
	}

	protected $data;

	protected $baseNote;

	protected $notes = array();

	public function __construct(Note $note, string $chord){

		if (isset(self::formulas[$chord]))
			$this->data = self::formulas[$chord];
		else
			return false;

		$this->baseNote = $note;
		$this->buildSelf();
	}

	public function buildSelf(){
		
		$formula = $this->data['f'];
		$this->notes = array($this->baseNote);
		foreach ($formula as $step){
			$note = clone(end($this->notes));
			for ($i =0; $i <= $step; $i++)
				$note->upHalfTone();
			$this->notes[] = clone($note);
		}

	}

	public function getNotes(): array {
		return $this->notes;
	}

	public function getFriendlyName(): string {
		$string = $this->baseNote->getHumanFriendly();
		/* A check in case of no local available */
		if (__("music.{$this->data['name']}") == "music.{$this->data['name']}")
			$string .= " {$this->data['name']}";
		else
			$string .= " " . __("music.{$this->data['name']}");

		return $string;
	}
}