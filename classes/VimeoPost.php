<?php

namespace Brafton\Vimeo;

class VimeoPost {
	public $videoPath;
	public $braftonId;
	public $userUri;

	public function __construct($id,$uri){
		$this->braftonId = $id;
		$this->userUri = $uri;
	}

	/**
     * Post Brafton video to Vimeo user account
     *
     * @param json $obj video data to be posted
     * 
     **/
	public function postVideo($obj){
		$crl = curl_init();
		curl_setopt($crl, CURLOPT_URL, $this->buildPostUrl());
		curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($crl, CURLOPT_POSTFIELDS, $obj);                                                                                                        
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($crl, CURLOPT_HTTPHEADER, array(  
				'Authorization: Bearer TOKEN',                                                                       
			    'Content-Type: application/json',
			    'Accept: application/vnd.vimeo.*+json;version=3.4'
			)                                                                                                                                
		);
		curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, true);     
		$result = curl_exec($crl);
		$this->videoPath = $result->uri;
		$this->addTag();
	}

	/**
     * Add brafton id as a tag to newly created Vimeo video
     * 
     **/
	public function addTag(){
		$crl = curl_init();
		curl_setopt($crl, CURLOPT_URL, $this->buildTagUrl() );
		curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($crl, CURLOPT_POSTFIELDS, $obj);                                                                                                        
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($crl, CURLOPT_HTTPHEADER, array(  
				'Authorization: Bearer TOKEN',                                                                       
			    'Content-Type: application/json',
			    'Accept: application/vnd.vimeo.*+json;version=3.4'
			)                                                                                                                                
		);
		curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, true);     
		$result = curl_exec($crl);
	}
	/**
     * Retrieve list of user's videos and search for brafton id in tag list
     * @return $test boolean 
     **/
	public function checkVideos(){
		$crl = curl_init();
		curl_setopt($crl, CURLOPT_URL, $this->buildPostUrl());
		curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($crl, CURLOPT_HTTPHEADER, array(  
				'Authorization: Bearer TOKEN',                                                                       
			    'Content-Type: application/json',
			    'Accept: application/vnd.vimeo.*+json;version=3.4'
			)                                                                                                                                
		);
		curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, true);     
		$result = curl_exec($crl);
		$fixed = json_decode($result);
		$videos = $fixed->data;
		$test = false;
		foreach ($videos as $video) {
			foreach($video->tags as $tag){
				if($tag->name == $this->braftonId) {
					$test = true;
					continue;
				}
			}
		}
		return $test;
	}

	/**
     * Build url endpoint for posting a video to a particular Vimeo user account
     *
     * @param string $link users home url /users/xxxxxx/
     * @return string /users/xxxxxx/videos
     **/
	public function buildPostUrl(){
		return $this->userUri.'/videos';
	}

	/**
     * Build url endpoint for adding a tag to an existing video 
     *
     * @param string $userId users account id 
     * @return string /videos/{video-id}/tags/{string}
     **/
	public function buildTagUrl($path,$braftonId){
		return $this->videoPath.'/tags/'.$this->braftonId;
	}

}
