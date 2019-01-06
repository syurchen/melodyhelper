<?php
  
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BasicMusicTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testMidiBasic()
    {
	    $midi = new \Music\Midi();
	    $this->assertNotFalse($midi->importMid(__DIR__ . '/beethoven1.mid'));
	    $noteList = $midi->getNoteList();
	    $this->assertTrue(is_array($noteList));
    }

    public function testNotesBasic()
    {
	    $letter = 'G';
	    $note = new \Music\Note($letter);
	    
	    $this->assertNotFalse($note);
	    $hr1 = $note->getHumanFriendly();
	    $note->setSharp();
	    $hr2 = $note->getHumanFriendly();
	    $this->assertFalse($hr1 == $hr2);
	    $note2 = new \Music\Note($letter, true);
	    $this->assertTrue(\Music\NoteManager::CheckEqual($note, $note2));
	    return $note;

    }

    /**
     * @depends testNotesBasic
     */
    public function testScaleChordBasic($note)
    {
	    $scale = new \Music\Scale($note, 'minor');
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


}