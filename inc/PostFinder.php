<?php
/**
 * Summary
 *
 * Description.
 *
 * @since 0.9.0
 *
 * @package
 * @subpackage 
 *
 * @author nguyenvanduocit
 */

namespace AdvancedCloner;


use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class PostFinder {
	private static $instance;
	protected $httpClient;
	protected $crawler;

	/**
	 * Summary.
	 *
	 * @since  0.9.0
	 * @see
	 * @return \AdvancedCloner\PostFinder
	 * @author nguyenvanduocit
	 */
	public static function getInstance(){
		if(static::$instance ===  null){
			static::$instance = new static();
		}
		return static::$instance;
	}
	protected function __construct(){
		$this->httpClient = new Client();
	}

	public function getLatestPost(){
		$links = $this->getAllPost();
		$result = new \stdClass();
		$lastLink = $links->first();
		$result->text = $lastLink->text();
		$result->href = $lastLink->attr('href');
		return $result;
	}
	public function getRandomPost(){
		$links = $this->getAllPost();
		$max = $links->count()-1;
		$index = rand(0, $max);
		$link = $links->eq($index);
		$result = new \stdClass();
		$result->text = $link->text();
		$result->href = $link->attr('href');
		return $result;
	}
	public function getAllPost(){
		$res = $this->httpClient->get('http://www.elegantthemes.com/blog/');
		$body          = $res->getBody();
		$this->crawler = new Crawler( $body->getContents() );
		return $this->crawler->filterXPath('//article[contains(@class, "entry")]/h2[@class="title"]/a');
	}
}