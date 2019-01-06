<?php

namespace Music;

class Scale{ 
	
	/* Scale formulas as constants */
	const formulas = array(
		'minor' => array(
			'name' => 'minor',
			'f' => array(
				1, 0, 1, 1, 0, 1
			)
		),

		'major' => array(
			'name' => 'major',
			'f' => array(
				1, 1, 0, 1, 1, 1
			)
		),
	);

	public function __get($scale){
		return false;
	}

	protected $data;

	protected $baseNote;

	protected $notes = array();

	protected $chords = array();

	public function __construct(Note $note, string $scale){

		if (isset(self::formulas[$scale]))
			$this->data = self::formulas[$scale];
		else
			return false;

		$this->baseNote = $note;
		$this->buildSelf();
	}

	public function buildSelf(){
		
		$formula = $this->data['f'];
		$this->notes = array($this->baseNote);
		/* filling notes array */
		foreach ($formula as $step){
			$note = clone(end($this->notes));
			if ($step)
				$note->upTone();
			else
				$note->upHalfTone();
			$this->notes[] = clone($note);
		}
		/* filling chords array */
		foreach ($this->notes as $note){
			foreach (array_keys(Chord::formulas) as $chordName){
				$chord = new Chord($note, $chordName);
				$add = true;
				foreach ($chord->getNotes() as $tempNote){
					if (!NoteManager::belongsToScale($this, $tempNote)){
						$add = false;
						break;
					}
				}
				if ($add)
					$this->chords[] = clone($chord);
			}
		}
	}

	public function getNotes(): array {
		return $this->notes;
	}

	public function getChords(): array {
		return $this->chords;
	}

	public function getFriendlyName(): string {
		$string = $this->baseNote->getHumanFriendly();
		$string .= " " . __("music.{$this->data['name']}");
		return $string;
	}

	public function getFriendlyScale(): array {

	}
}