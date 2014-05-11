<?php

/**
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <franksp@internl.net> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Frank Spijkerman
 * ----------------------------------------------------------------------------
 *
 * @package savonet
 * @copyright 2008, Frank Spijkerman
 *  for the Savonet Project
 */

class savonet {
	/**
   * The hostname of the liquidsoap
   * server
   * @var string
   */
	private $hostname;

	/**
   * The post of the liquidsoap server
   * @var integer
   */
	private $port;

  /**
	 * Array for storage of the stream list,
	 * this is used for caching.
   * @var array
	 */
	private $streams = array();

	/**
	 * Constructor
	 *
   * Prepares some settings
	 *
	 * @param string $hostname
	 *   The hostname of liquidsoap
	 * @param integer $port
   *   The port of liquidsoap, usualy 1234
	 */
	function __construct($hostname = 'localhost', $port = 1234)
	{
		$this->hostname = $hostname;
		$this->port = $port;
	}

	/**
   * Destructor
	 *
   * Disconnects the connection if there is any.
	 */
	function __destruct()
	{
		// disconnect
		$this->disconnect();
	}

	/**
	 * This function makes the connection to savonet
	 */
	public function connect()
	{
		// Make this socket
		$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($this->sock == FALSE)
			throw new Exception("Unable to create socket: " .
				socket_strerror(socket_last_error())
			);

		// Connect
		if (!socket_connect($this->sock, $this->hostname, $this->port))
			throw new Exception("Unable to connect to $hostname:$port" .
				socket_strerror(socket_last_error())
			);
	}

	/**
	 * Disconnect from liquidsoap ;(
	 */
	public function disconnect()
	{
		// say good bye politely, then close the socket.
		if ($this->sock) {
			$this->write("bye");
			socket_close($this->sock);
		}
	}

	/**
   * queue_list
   *
   * Returns a list with the current items in the queue
   *
   * @param string $name
   *   The name of the queue
	 * @return array
   */
	public function queue_list($name)
	{
		// check for the name
		if (empty($name))
			return;

		// Check if the stream is available
		if (($stream = $this->checkStreamType($name, "queue.")) === FALSE)
			throw new Exception("No such stream $name, or of the wrong type");

		$data = $this->write($name . ".queue");

		// Return an empty array when nothing in the queue
		if (empty($data[0]))
			return array();

		// Enrich the data with metadata
		$rv = array();
		foreach(explode(" ", $data[0]) as $i)
			$rv[] = $this->get_metadata($i);

		return $rv;
	}

	/**
   * queue_push
	 *
   * Adds an URI to the queue
   * @param string $name
   * @param string $uri
	 */
	public function queue_push($name, $uri)
	{
		// gimme some input
		if (empty($name) && empty($uri))
			return;

		// Check if the stream is available
		if (($stream = $this->checkStreamType($name, "queue.")) === FALSE)
			throw new Exception("No such stream $name, or of the wrong type");

		$data = $this->write($name . ".push " . $uri);

		// Check if valid uri
		if (empty($data[0]))
			return array();

		// Return the metadata aswel.
		return $this->get_metadata($data[0]);
	}

	/**
   * queue_move
   *
   * Moves a rid in the queue to a new position.
   *
   * @param string $name
   *   Name of the queue
   * @param string $rid
   *   Record id
   * @param string $pos
   *   New position
	 */
	public function queue_move($name, $rid, $pos)
	{
		// Gimme
		if (empty($name) && !is_numeric($rid) &&
				!is_numeric($pos))
			return;

		// Check if the stream is available
		if (($stream = $this->checkStreamType($name, "queue.")) === FALSE)
			throw new Exception("No such stream $name, or of the wrong type");

		return $this->write($name . ".move $rid $pos");
	}

	/**
	 * queue_insert
	 *
	 * Inserts a rid at a specific pos.
	 *
	 * @param string $name
	 *   The name of the queue
	 * @param string $pos
	 *   The position
	 * @param string $uri
	 *   The uri
	 */
	public function queue_insert($name, $pos, $uri)
	{
		// Check input
		if (empty($name) && !is_numeric($pos) && empty($uri))
			return;

		// Check if the stream is available
		if (($stream = $this->checkStreamType($name, "queue.")) === FALSE)
			throw new Exception("No such stream $name, or of the wrong type");

		return $this->write($name . ".insert $pos $uri");
	}

	/**
   * queue_remove
	 *
   * Removes a rid from the queue
	 *
   * @param string $name
   * @param integer $rid
	 * @return array
	 */
	public function queue_remove($name, $rid)
	{
		if (empty($name) && !is_numeric($rid))
			return;

		// Check if the stream is available
		if (($stream = $this->checkStreamType($name, "queue.")) === FALSE)
			throw new Exception("No such stream $name, or of the wrong type");

		return $this->write($name . ".remove " . $rid);
	}

	/**
	 * stream_list
	 *
	 * Fetches the list of available streams
	 * and formats it in a nice array and returns that.
   *
   * @return array
	 */
	public function stream_list()
	{
		$retval = $this->write("list");

		$list = array();
		foreach ($retval as $rv)
			$list[] = explode(" : ", $rv);

		// save it for caching
		$this->streams = $list;

		return $list;
	}

	/**
   * now_playing
   *
   * returns a formated string with the current
   * thats playing.
   *
   * @return string $song
   */
	public function now_playing()
	{
		$id = $this->on_air();
		$md = $this->get_metadata($id);
		return $md['artist'] . " - " . $md['title'];
	}

	/**
   * on_air
   *
   * @return int $current_song
	 *   return the 'rid' of the current song
	 */
	public function on_air()
	{
		$tmp = $this->write("on_air"); $tmp = $tmp[0];
		return $tmp;
	}

	/**
	 * checkStreamType
	 *
   * Checks if the stream if of the correct type
	 *
   * @param string $name
	 *   The name of the stream
   * @param string $type
   *   The type of the stream, like output. or editable.
	 */
	private function checkStreamType($name, $type)
	{
		// Get the cache if empty
		if (empty($this->streams))
			$this->stream_list();

		foreach ($this->streams as $stream)
		{
				// search for the name and check the type
				if ($stream[0] == $name && strstr($stream[1], $type))
					return$stream;
		}

		return FALSE;
	}

	/**
   * Skip
	 *
   * Skip is only available on a few stream types.
	 * so it has to check first it its allowed.
	 *
   * @param string $name
	 *   The name of the stream
	 */
	public function skip($name)
	{
		// Check the stream
		if (($stream = $this->checkStreamType($name, "output.")) === FALSE)
			throw new Exception("No such stream $name, or of the wrong type");

		$this->write($name . ".skip");
	}

	/**
   * Remaining
	 *
   * Remaining is only available on a few stream types.
	 * so it has to check first it its allowed.
	 *
   * @param string $name
	 *   The name of the stream
	 */
	public function remaining($name)
	{
		// Check the stream
		if (($stream = $this->checkStreamType($name, "output.")) === FALSE)
			throw new Exception("No such stream $name, or of the wrong type");

		$tmp = $this->write($name . ".remaining"); $tmp = $tmp[0];
		if($tmp == "(undef)") $tmp="0";
		return $tmp;
		}


	/**
   * get_metadata
	 *
 	 * @param integer $rid
	 *   This id of the song.
   * @return Object
	 *   An object filled with all the info.
   */
	public function get_metadata($rid)
	{
		if (empty($rid))
			return;

		$retval = $this->write("metadata " . $rid);

		// No such request
		if ($retval[0] == 'No such request.')
			return;

		// Lets parse it
		$md = array();
		foreach ($retval as $rv)
		{
			list($key, $value) = split('=', $rv, 2);
			// Remove the quotes
			$value = trim($value, '"');
			$md[$key] = $value;
		}
		return $md;
	}

	/**
   * is_alive
	 *
   * Connects to the savonet server and returns alive
   *
   * @return string
   *  Active Queue, 0 = none.
	 */
	public function is_alive()
	{
		$tmp = $this->write("alive"); $tmp = $tmp[0];
		return $tmp;
	}

	/**
	 * write
	 *
   * Writes and reads stuff from savo
	 * Because we are lazy.
   *
   * @param string $what
   *   The msg thats getting passed to savonet.
	 */
	public function write($what)
	{
		$this->is_connected();

		// Write to socket
		if (@socket_write($this->sock, $what . "\n") == FALSE)
			throw @Exception("Unable to write to socket: " .
				@socket_strerror(socket_last_error())
			);

		// wait until END then return it
		$retval = array();

		while ($buffer = @socket_read($this->sock, 512, PHP_NORMAL_READ))
		{
			if ($buffer == "END\n")
				break;
			$retval[] = trim($buffer);
		}

		return $retval;

	}

	/**
   * is_connected
	 *
	 * Check if the connection is connected otherwise
   * throws an exception if $throw is true.
   *
	 * @params $throw bool
	 *   throws an exception or not. default true.
	 * @return bool
	 *   returns 1 if connected. 0 if not.
   *   only returns 0 when $throw is false
	 */
	private function is_connected($throw = true)
	{
		if ($this->sock == FALSE)
			if ($throw)
				throw new Exception("Socket is not connected.");
			else
				return 0;

		return 1;
	}
}
?>
<?php
/*
	File: twitter.php

	Description:
		The following class is for using twitter.com

		This class was based off the work of Antonio Lupetti. Original Work can be found at:
			http://woork.blogspot.com/2007/10/twitter-send-message-from-php-page.html

	Contributing Author( s):
		Antonio Lupetti < antonio.lupetti@gmail.com >
		Scott Sloan < scott@aimclear.com >

	Date: January 4th, 2008
	License: Creative Commons

*/
class twitter {

	private $user;
	private $pass;
	private $ch;
	private $twitterHost = "http://twitter.com/";

	public function __construct($username, $passwd) {
		$this->user = $username;
		$this->pass = $passwd;

		/* Create and setup  the curl Session */
		$this->ch = curl_init();

		@curl_setopt($this->ch, CURLOPT_VERBOSE, 0);
		@curl_setopt($this->ch, CURLOPT_NOBODY, 1);
		@curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($this->ch, CURLOPT_USERPWD, "$this->user:$this->pass");
		@curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		@curl_setopt($this->ch, CURLOPT_POST, 1);
	}

	public function __destruct() {
		/*clean Up */
		@curl_close($this->ch);
	}

	public function setStatus($stat) {

		if(strlen($stat) < 1)
			return false;

		/*Set the host information for the curl session */
		$this->twitterHost .= "statuses/update.xml?status=". urlencode(stripslashes(urldecode($stat)));

		@curl_setopt($this->ch, CURLOPT_URL, $this->twitterHost);

		/* Execute and get the http code to see if it was succesfull or not*/
		$result = @curl_exec($this->ch);
		$resultArray = @curl_getinfo($this->ch);

		if ($resultArray['http_code'] == 200) ;
			return true;

		return false;
	}
}
?>
<?
 $file = file("/etc/podradio/content/current.m3u");

if(@file_exists(trim($file[6])) && !@file_exists("/tmp/in_live")) {
 $sav = new savonet();
 $sav->connect();
 $sav->write("scheduler.push ".$file[5]);
 $sav->write("scheduler.push ".$file[6]);
 $sav->write("scheduler.push ".$file[7]);
 $sav->disconnect();

 if($podcast = @file("/etc/podradio/content/podcasts/podcast_".substr(trim($file[1]), 1, strlen(trim($file[1]))-1))) {
	$name = trim($podcast[0]);
	$twitter = new twitter("podradio_fr", "swag");
	if(strlen($name) > 5) {
		$twitter->setStatus(str_replace(array("é", "è", "à", "ê"), array("é  ", "è  ", "à  ", "ê  "), "Tout de suite sur podradio : ".htmlentities($name)." // http://www.podradio.fr/radio/ ou installez l'application iPhone podradio : http://app.podradio.fr/"));
 	}
 }
}
?>
