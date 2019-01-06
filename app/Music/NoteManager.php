<?php

namespace Music;

class NoteManager{

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
			