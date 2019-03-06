<?php

namespace Music;

class Note {

	const LETTERS = array ('C', 'D', 'E', 'F', 'G', 'A', 'B');

	/* tones and half-tones between notes */
	const DISTANCE = array (1, 1, 0, 1, 1, 1, 0);

	const NOTECOUNT = 12;

	const OCTAVEMAX = 10;

	protected $flat = false;

	protected $sharp = false;

	protected $letter;

	protected $octave;

	public function __construct(string $letter, bool $sharp = false, bool $flat = false, int $octave = 1){
		if (in_array($letter, self::LETTERS)){
			if ($sharp)
				$this->setSharp();
			if($flat)
				$this->setFlat();
			$this->letter = $letter;
			$this->octave = $octave;
		} else
			return false;
	}

	public function __get ($name) {

		return $this->$name ?? null;
	}
	public function setFlat(){
		$this->flat = !$this->flat;
		$this->sharp = false;
	}

	public function setSharp(){
		$this->sharp = !$this->sharp;
		$this->flat = false;
	}

	public function octaveUp(){
		if ($this->octave < self::OCTAVEMAX){
			$this->octave++;
		} else 
			return false;
	}

	public function octaveDown(){
		if ($this->octave > 0){
			$this->octave--;
		} else 
			return false;
	}


	public function getHumanFriendly(bool $withOctave = false, bool $full = false): string {

		$string = $this->letter;

		if ($this->sharp){
			if ($full)
				$string .=  " " . __('music.sharp');
			else
				$string .= "#";
		}

		if ($this->flat){
			if ($full)
				$string .=  " " . __('music.flat');
			else
				$string .= "♭";
		}

		if ($withOctave){
			if ($full)
				$string .= " " . __('music.octave') . " {$this->octave}";
			else
				$string .= " {$this->octave}";
		}

		return $string;
	}

	public function upHalfTone(){
		$index = array_search($this->letter, self::LETTERS);
		$distance = self::DISTANCE[$index];
		if ($distance){
			if ($this->sharp){
				$index++;
				$this->setSharp();
			} else {
				if ($this->flat){
					$this->setFlat();
				} else {
					$this->setSharp();
				}
			}
		} else {
			if ($this->sharp){
				$index += 2;
			} else {
				if ($this->flat){
					$this->setFlat();
				} else {
					$index++;
				}
			}
		}
		if ($index >= count(self::LETTERS)){
			$index = $index - count(self::LETTERS);
			/* octave change here */
			$this->octaveUp();
		}
		$this->letter = self::LETTERS[$index];
	}

	public function downHalfTone(){
		if ($this->sharp)
			$this->setSharp();
		else {
			$index = array_search($this->letter, self::LETTERS);
			$index--;
			if (!$index)
				$index = count(self::LETTERS);
			$distance = self::DISTANCE[$index];
			if ($distance){
				$index--;
				if ($this->flat){
					$this->setFlat();
				} else {
					$this->setSharp();
					/* this also unflats the note */
				}
			} else {
				if ($this->flat){
					$this->setSharp();
					$index -= 2;
				} else {
					$index--;
				}
			}
			if ($index < 0){
				$index = count(self::LETTERS) - $index;
				/* octave change here */
				$this->octaveDown();
			}
			$this->letter = self::LETTERS[$index];
		}
	}

	public function downTone(){
		$this->downHalfTone();
		$this->downHalfTone();
	}

	public function upTone(){
		$this->upHalfTone();	
		$this->upHalfTone();	
	}
}