<?php

namespace Music;
use Illuminate\Support\Facades\Redis;

class NoteManager{
	const SCALECACHEKEY = 'scale';

	public function __construct(){
		$this->cacheScales();
	}

	public function cacheScales(): void {
		$scaleCount = Note::NOTECOUNT * count(Scale::formulas) - 1;

		if (!Redis::get(self::SCALECACHEKEY . $scaleCount)){ /* We need to cache scales */
			$scaleCount = 0;
			foreach (Scale::formulas as $scaleFormula){
				$letterI = 0;
				$sharp = false;
				/* foreach note */
				for($i = 0; $i < Note::NOTECOUNT; $i ++){
					$note = new Note(Note::LETTERS[$letterI], $sharp);
					if (Note::DISTANCE[$letterI] && !$sharp){
						$sharp = true;
					} else {
						$letterI++;
						$sharp = false;
					}
					$scale = new Scale($note, $scaleFormula['name']);
					print_r($scale->asArray());
					Redis::set(self::SCALECACHEKEY . $scaleCount, json_encode($scale->asArray()));
					$scaleCount++;

				}
			}

		} 
	}

	private function cacheGetScales(): array {
		$scaleCount = Note::NOTECOUNT * count(Scale::formulas) - 1;
		$scales = array();
		for ($i = 0; $i <= $scaleCount; $i++){
			$scale = Redis::get(self::SCALECACHEKEY . $i);
			$scales[] = json_decode($scale, true);
		}
		return $scales;
	}

	public function findMelodyScale(array $melody, bool $returnFull = true): ?array {
		$scales = $this->cacheGetScales();
		foreach ($melody as $note){
			foreach ($scales as $id => $scale){
				if (!in_array($note->getHumanFriendly(), $scale['notes'])){
					unset($scales[$id]);
				}
				if (empty($scales))
					break 2;
			}
		}
		if($returnFull){
			foreach($scales as $id => $scale){
				$baseNote = new Note($scale['baseNote'], $scale['baseNoteSharp']);
				$newScale = new Scale($baseNote, $scale['formula']);
				$scales[$id] = $newScale;
			}
		}
		return $scales;
	}

	public static function checkEqual(Note $note1, Note $note2, bool $checkOctave = false): bool {
		$result = $note1->letter == $note2->letter && $note1->sharp == $note2->sharp && $note1->flat == $note2->flat;
		if ($checkOctave && $result)
			$result = ($note1->octave == $note2->octave);
		return $result;
	}

	public static function belongsToScale(Scale $scale, Note $note): bool {
		foreach ($scale->getNotes() as $scaleNote){
			if (self::checkEqual($note, $scaleNote)){
				return true;
			}
		}
		return false;
	}
}
			