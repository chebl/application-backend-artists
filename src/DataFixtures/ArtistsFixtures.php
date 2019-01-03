<?php

namespace App\DataFixtures;
use App\Entity\Artists;
use App\Entity\Albums;
use App\Entity\Songs;
use App\Utils\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArtistsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $content=file_get_contents('https://gist.github.com/fightbulc/9b8df4e22c2da963cf8ccf96422437fe/raw/8d61579f7d0b32ba128ffbf1481e03f4f6722e17/artist-albums.json');              
        $content = json_decode($content, 1);
	    $token=TokenGenerator::generate();
	    foreach($content as $artists) {
          /**Save Artist data **/
		  $token=TokenGenerator::generate();
	      $name=$artists['name'];
		  $artist = new Artists(); 
		  $artist->setToken($token);
		  $artist->setName($name);
		  $manager->persist($artist);
		 
		  /** Save related Albums**/
		  foreach($artists['albums'] as $albums){
		   $album=new Albums();
		   $token=TokenGenerator::generate();
	       $title=$albums['title'];
		   $description=$albums['description'];
	       $cover=$albums['cover'];
		   $album->setToken($token);
		   $album->setTitle($title);
		   $album->setDescription($description);
		   $album->setCover($cover);
		   $album->setArtist($artist);
		   $manager->persist($album);
		 
		  /** Save related Songs**/
		   foreach($albums['songs'] as $songs){
		    $title=$songs['title'];
            $length_minutes = $songs['length'];
            sscanf($length_minutes, "%d:%d:%d", $hours, $minutes, $seconds);
            $length_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
            $song=new Songs();
            $song->setTitle($title);
            $song->setLength($length_seconds);   			
 			$song->setAlbum($album);
		    $manager->persist($song);
		   }
		}			
        $manager->flush();
     }
	}	
}
