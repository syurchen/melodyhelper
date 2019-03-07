<?php

namespace Music;

use Music\Note;

class MidiParser{
	static function parseNoteList(array $noteList, bool $onlyUnique = false, bool $octaveSpecific = true): array {
		$result = array();
		foreach($noteList as $string){
			$string = trim($string);
			$letter = $string[0];
			$sharp = (strpos('s', $string) == 1);
			preg_match('!\d+!', $string, $octave);
			if (isset($octave[0]) && $octaveSpecific)
				$octave = $octave[0];
			else
				$octave = 1;
			$note = new Note($letter, $sharp, false, $octave);
			if ($onlyUnique && in_array($note, $result))
				continue;
			$result[] = $note;
		}
		return $result;
	}
}