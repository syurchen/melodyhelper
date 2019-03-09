<?php
  
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Music\Note;
use Music\Scale;
use Music\NoteManager;
use Music\Midi;
use Music\MidiParser;

class BasicMusicTest extends TestCase
{

    public function testMidiBasic()
    {
	    $midi = new Midi();
	    $this->assertNotFalse($midi->importMid(__DIR__ . '/beethoven1.mid'));
	    $noteList = $midi->getNoteList();
	    $this->assertTrue(is_array($noteList));
	    $melody = MidiParser::parseNoteList($noteList, true, false);

	    $this->assertTrue(is_array($melody));
	    $this->assertTrue($melody[0] instanceof Note);
	    return $melody;
    }

    /**
     * @depends testMidiBasic
     */
    public function testNotesBasic()
    {
	    $letter = 'G';
	    $note = new Note($letter);
	    
	    $this->assertNotFalse($note);
	    $hr1 = $note->getHumanFriendly();
	    $note->setSharp(); 
	    $hr2 = $note->getHumanFriendly();
	    $this->assertFalse($hr1 == $hr2);
	    $note2 = new Note($letter, true);
	    $this->assertTrue(NoteManager::CheckEqual($note, $note2));
	    $note->setSharp(); 
	    return $note;

    }

    /**
     * @depends testNotesBasic
     */
    public function testScaleChordBasic($note)
    {
	    $scale = new Scale($note, 'major');
	    $this->assertNotFalse($scale);
	    $this->assertEquals(count($scale->getNotes()), 7);
	    $notes = $scale->getNotes();
	    echo "\n\nScale of {$scale->getFriendlyName()}\n";
	    foreach ($notes as $tempNote){
		    echo "\n> " . $tempNote->getHumanFriendly() ;
	    }

	    foreach($scale->getChords() as $chord){
		    $this->assertNotFalse($chord);
		    $notes = $chord->getNotes();
		    echo "\n\nChord {$chord->getFriendlyName()}\n";
		    foreach ($notes as $tempNote){
			    echo "\n> " . $tempNote->getHumanFriendly() ;
		    }
	    }
    }

    /**
     * @depends testMidiBasic
     * @depends testScaleChordBasic
     */
    public function testFindScale($melody)
    {
	    $manager = new NoteManager();
	    $melodyOld = array(
		    new Note('C'),
		    new Note('D'),
		    new Note('E'),
		    new Note('F'),
	    );
	    echo "\n_____\nMelody:\n";
	    foreach($melody as $note){
		    echo "{$note->getHumanFriendly()}\n";
	    }
	    $result = $manager->findMelodyScale($melody);
	    $this->assertTrue(is_array($result));
	    $this->assertTrue(!empty($result));
	    echo "\n\n";
	    foreach ($result as $scale){
		    echo "\n{$scale->getFriendlyName()}\n";
	    }

    }

}