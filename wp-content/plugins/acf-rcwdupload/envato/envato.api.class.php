<?php

/**
 * Envato API
 *
 * An PHP class to interact with the Envato Marketplace API
 *
 * @author		Philo Hermans
 * @copyright	Copyright (c) 2011 NETTUTS+
 * @link		http://net.tutsplus.com
 */
 
/**
 * envatoAPI
 *
 * Create our new class called "RcwdAcfUploadEnvatoAPI"
 */ 
class RcwdAcfUploadEnvatoAPI{
	
	private $api_url = 'http://marketplace.envato.com/api/edge/';
	private $api_set;
	private $username;
	private $api_key;
	
	public $cache_expires = 24;
	/**
 	* request()
 	*
 	* Request data from the API
 	*
 	* @access	public
 	* @param	void
 	* @return	 	array		
 	*/
	
	public function request(){

		$agent = 'RCWDWPPLUGIN';
		
		if(!empty($this->username) && !empty($this->api_key)){
			
			$request_is 	= 'private';
			$this->api_url .= $this->username . '/'.$this->api_key.'/'.$this->api_set . '.json';
			$cache_path 	= str_replace(':', '-', substr(strrchr($this->api_url, '/'), 1));

		}else{
			
			$request_is 	= 'public';
			$this->api_url .=  $this->api_set . '.json';
		
		}
		
		if ( $request_is == 'private' ){
			
			$ch = curl_init($this->api_url);
			
			curl_setopt( $ch, CURLOPT_USERAGENT, $agent );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 ); //The number of seconds to wait while trying to connect
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // Return the transfer as a string instead of outputting it out directly.
			
			$ch_data = curl_exec($ch);
			
			curl_close($ch);
	
			$data = json_decode( $ch_data, true );
			
			//return('We are unable to retrieve any information from the API.');

			return $data;	
						
		}else{

				$ch = curl_init($this->api_url);
				
				curl_setopt( $ch, CURLOPT_USERAGENT, $agent );
				curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				
				$ch_data = curl_exec($ch);
				
				curl_close($ch);
							
				if(!empty($ch_data))
					return json_decode( $ch_data, true );
				else
					return json_decode( array(), true );	
				
		}
		
	}
	
	/**
 	* set_api_set()
 	*
 	* Set the API set
 	*
 	* @access	public
 	* @param	string
 	* @return	 	void		
 	*/
	public function set_api_set($api_set)
	{
		$this->api_set = $api_set;
	}
	
	/**
 	* set_api_key()
 	*
 	* Set the API key
 	*
 	* @access	public
 	* @param	string
 	* @return	 	void		
 	*/
	public function set_api_key($api_key)
	{
		$this->api_key = $api_key;
	}
	
	/**
 	* set_username()
 	*
 	* Set the Username
 	*
 	* @access	public
 	* @param	string
 	* @return	 	void		
 	*/
	public function set_username($username)
	{
		$this->username = $username;
	}
		
	/**
 	* set_api_url()
 	*
 	* Set the API URL
 	*
 	* @access	public
 	* @param	string
 	* @return	 	void		
 	*/
	public function set_api_url($url)
	{
		$this->api_url = $url;
	}

	/**
 	* get_api_url()
 	*
 	* Return the API URL
 	*
 	* @access	public
 	* @return	 	string		
 	*/
	public function get_api_url()
	{
		return $this->api_url;
	}
	
}
?>