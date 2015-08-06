<?php

namespace BinaryThinking\LastfmBundle\Lastfm\Client\Method;

use BinaryThinking\LastfmBundle\Lastfm\Client\LastfmAPIClient;
use BinaryThinking\LastfmBundle\Lastfm\Model as LastfmModel;

/**
 * ArtistMethodsClient
 *
 * @author Karol Sójko <karolsojko@gmail.com>
 */
class ArtistMethodsClient extends LastfmAPIClient
{
    /**
     * Use the last.fm corrections data to check whether the supplied artist has a correction to a canonical artist
     * 
     * @param string $artist the artist name to correct
     */
    public function getCorrection($artist)
    {
        $response = $this->call(array(
            'method' => 'artist.getCorrection',
            'artist' => $artist
        ));
        
        $corrections = array();
        if(!empty($response->corrections->correction)){
            foreach($response->corrections->correction as $correction){
                $corrections[] = LastfmModel\Artist::createFromResponse($correction->artist);
            }
        }
        
        return $corrections;
    }
    
    /**
     * Get a list of upcoming events for this artist. Easily integratable into calendars, using the ical standard (see feeds section below).
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param int $limit the number of results to fetch per page. Defaults to 50
     * @param int $page the page number to fetch. Defaults to first page
     * @param bool $autocorrect transform misspelled artist names into correct artist names, returning the correct version instead. The corrected artist name will be returned in the response
     * @param bool $festivalsOnly whether only festivals should be returned, or all events
     */
    public function getEvents($artist, $mbid = null, $limit = null, $page = null, $autocorrect = true, $festivalsOnly = false)
    {
        $respone = $this->call(array(
            'method' => 'artist.getEvents',
            'artist' => $artist,
            'mbid' => $mbid,
            'limit' => $limit,
            'page' => $page,
            'autocorrect' => $autocorrect,
            'festivalsonly' => $festivalsOnly
        ));
        
        $events = array();
        if(!empty($respone->events->event)){
            foreach($respone->events->event as $event){
                $events[] = LastfmModel\Event::createFromResponse($event);
            }
        }
        
        return $events;
    }
    
    /**
     * Get the metadata for an artist. Includes biography.
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param string $lang the language to return the biography in, expressed as an ISO 639 alpha-2 code
     * @param string $username the username for the context of the request. If supplied, the user's playcount for this artist is included in the response
     * @param bool $autocorrect transform misspelled artist names into correct artist names, returning the correct version instead. The corrected artist name will be returned in the response
     * @param string $saveXmlFilePath path to save raw xml response
     */
    public function getInfo($artist, $mbid = null, $lang = null, $username = null, $autocorrect = true, $saveXmlFilePath = '')
    {
        $response = $this->call(array(
            'method' => 'artist.getInfo',
            'artist' => $artist,
            'mbid' => $mbid,
            'lang' => $lang,
            'username' => $username,
            'autocorrect' => $autocorrect
        ));

        if(!empty($response->artist)){
            if(!empty($saveXmlFilePath)){
                try {
                    $response->saveXML($saveXmlFilePath);
                } catch (\Exception $e) {
                    throw new \Exception('Unable to save xml response');
                }
            }
            $artist = LastfmModel\Artist::createFromResponse($response->artist);
        }
        
        return $artist;
    }
    
    /**
     * Get a paginated list of all the events this artist has played at in the past.
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param int $limit the number of results to fetch per page. Defaults to 50
     * @param int $page the page number to fetch. Defaults to first page
     * @param bool $autocorrect transform misspelled artist names into correct artist names, returning the correct version instead. The corrected artist name will be returned in the response
     */
    public function getPastEvents($artist, $mbid = null, $limit = null, $page = null, $autocorrect = true)
    {
        $respone = $this->call(array(
            'method' => 'artist.getPastEvents',
            'artist' => $artist,
            'mbid' => $mbid,
            'limit' => $limit,
            'page' => $page,
            'autocorrect' => $autocorrect
        ));
        
        $events = array();
        if(!empty($respone->events->event)){
            foreach($respone->events->event as $event){
                $events[] = LastfmModel\Event::createFromResponse($event);
            }
        }
        
        return $events;        
    }
    
    /**
     * Get shouts for this artist. Also available as an rss feed.
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param int $limit the number of results to fetch per page. Defaults to 50
     * @param int $page the page number to fetch. Defaults to first page
     * @param bool $autocorrect transform misspelled artist names into correct artist names, returning the correct version instead. The corrected artist name will be returned in the response
     */
    public function getShouts($artist, $mbid = null, $limit = null, $page = null, $autocorrect = true)
    {
        $response = $this->call(array(
            'method' => 'artist.getShouts',
            'artist' => $artist,
            'mbid' => $mbid,
            'limit' => $limit,
            'page' => $page,
            'autocorrect' => $autocorrect
        ));
        
        $shouts = array();
        if(!empty($response->shouts->shout)){
            foreach($response->shouts->shout as $artistShout){
                $shout = LastfmModel\Shout::createFromResponse($artistShout);
                $shouts[] = $shout;
            }
        }
        
        return $shouts;
    }
    
    /**
     * Get the tags applied by an individual user to an artist on Last.fm.
     * 
     * @param string $artist the artist name
     * @param string $user If called in non-authenticated mode you must specify the user to look up
     * @param string $mbid the musicbrainz id for the artist
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     */
    public function getTags($artist, $user, $mbid = null, $autocorrect = null)
    {
        $response = $this->call(array(
            'method' => 'artist.getTags',
            'artist' => $artist,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect,
            'user' => $user
        ));
        
        $tags = array();
        if(!empty($response->tags->tag)){
            foreach($response->tags->tag as $artistTag){
                $tag = LastfmModel\Tag::createFromResponse($artistTag);
                $tags[$tag->getName()] = $tag;
            }
        }
        
        return $tags;
    }
    
    /**
     * Get the top tags for an artist on Last.fm, ordered by popularity.
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     */
    public function getTopTags($artist, $mbid = null, $autocorrect = true)
    {
        $response = $this->call(array(
            'method' => 'artist.getTopTags',
            'artist' => $artist,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect
        ));
        
        $tags = array();
        if(!empty($response->toptags->tag)){
            foreach($response->toptags->tag as $artistTag){
                $tag = LastfmModel\Tag::createFromResponse($artistTag);
                $tags[$tag->getName()] = $tag;
            }
        }
        
        return $tags;        
    }
    
    /**
     * Get all the artists similar to this artist.
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param int $limit Limit the number of similar artists returned
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     * @param string $saveXmlFilePath path to save raw xml response
     */
    public function getSimilar($artist, $mbid = null, $limit = null, $autocorrect = true, $saveXmlFilePath = '')
    {
        $response = $this->call(array(
            'method' => 'artist.getSimilar',
            'artist' => $artist,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect,
            'limit' => $limit
        ));
        
        $artists = array();
        if (!empty($response->similarartists->artist)) {
            if(!empty($saveXmlFilePath)){
                try {
                    $response->saveXML($saveXmlFilePath);
                } catch (\Exception $e) {
                    throw new \Exception('Unable to save xml response');
                }
            }
            foreach ($response->similarartists->artist as $similarArtist) {
                $artists[] = LastfmModel\Artist::createFromResponse($similarArtist);
            }
        }
        
        return $artists;
    }
    
    /**
     * Get the top albums for an artist on Last.fm, ordered by popularity.
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param int $limit the number of results to fetch per page. Defaults to 50
     * @param int $page the page number to fetch. Defaults to first page
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     */
    public function getTopAlbums($artist, $mbid = null, $limit = null, $page = null, $autocorrect = true)
    {
        $response = $this->call(array(
            'method' => 'artist.getTopAlbums',
            'artist' => $artist,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect,
            'limit' => $limit,
            'page' => $page
        ));
        
        $albums = array();
        if (!empty($response->topalbums->album)) {
            foreach ($response->topalbums->album as $topAlbum) {
                $topAlbumAttributes = $topAlbum->attributes();
                $topAlbum = LastfmModel\Album::createFromResponse($topAlbum);
                $albums[(int) $topAlbumAttributes->rank] = $topAlbum;
            }
        }
        
        return $albums;        
    }

    /**
     * Get the top tracks by an artist on Last.fm, ordered by popularity
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param int $limit the number of results to fetch per page. Defaults to 50
     * @param int $page the page number to fetch. Defaults to first page
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     * @param string $saveXmlFilePath path to save raw xml response
     */
    public function getTopTracks($artist, $mbid = null, $limit = null, $page = null, $autocorrect = true, $saveXmlFilePath = '')
    {
        $response = $this->call(array(
            'method' => 'artist.getTopTracks',
            'artist' => $artist,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect,
            'limit' => $limit,
            'page' => $page
        ));
        
        $tracks = array();
        if (!empty($response->toptracks->track)) {
            if(!empty($saveXmlFilePath)){
                try {
                    $response->saveXML($saveXmlFilePath);
                } catch (\Exception $e) {
                    throw new \Exception('Unable to save xml response');
                }
            }
            foreach ($response->toptracks->track as $topTrack) {
                $topTrackAttribues = $topTrack->attributes();
                $topTrack = LastfmModel\Track::createFromResponse($topTrack);
                $tracks[(int) $topTrackAttribues->rank] = $topTrack;
            }
        }
        
        return $tracks;
    }
    
    /**
     * Search for an artist by name. Returns artist matches sorted by relevance.
     * 
     * @param string $artist the artist name
     * @param int $limit the number of results to fetch per page. Defaults to 50
     * @param int $page the page number to fetch. Defaults to first page
     */
    public function search($artist, $limit = null, $page = null)
    {
        $response = $this->call(array(
            'method' => 'artist.search',
            'artist' => $artist,
            'limit' => $limit,
            'page' => $page
        ));
        
        $artists = array();
        if (!empty($response->results->artistmatches->artist)) {
            foreach ($response->results->artistmatches->artist as $artist) {
                $artists[] = LastfmModel\Artist::createFromResponse($artist);
            }
        }
        
        return $artists;
    }
    
    /**
     * Get the top fans for an artist on Last.fm, based on listening data.
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     */
    public function getTopFans($artist, $mbid = null, $autocorrect = true)
    {
        $response = $this->call(array(
            'method' => 'artist.getTopFans',
            'artist' => $artist,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect
        ));
        
        $fans = array();
        if (!empty($response->topfans->user)) {
            foreach ($response->topfans->user as $fan) {
                $fans[] = LastfmModel\User::createFromResponse($fan);
            }
        }
        
        return $fans;
    }

    /**
     * Get a podcast of free mp3s based on an artist
     * 
     * @param string $artist the artist name
     * @param string $mbid the musicbrainz id for the artist
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     */    
    public function getPodcast($artist, $mbid = null, $autocorrect = true)
    {
        $response = $this->call(array(
            'method' => 'artist.getPodcast',
            'artist' => $artist,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect
        ));
        
        $channels = array();
        if (!empty($response->rss->channel)) {
            foreach ($response->rss->channel as $channel) {
                $channels[] = LastfmModel\Channel::createFromResponse($channel);
            }
        }
        
        return $channels;        
    }
    
}
