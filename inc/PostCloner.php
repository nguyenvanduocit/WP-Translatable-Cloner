<?php
namespace AdvancedCloner;
use Stichoza\GoogleTranslate\TranslateClient;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
class PostCloner {
	protected $translator;
	protected $httpClient;
	protected $url;
	/** @var \Symfony\Component\DomCrawler\Crawler $crawler */
	protected $crawler;
	protected $post;

	public function __construct( $url, $processNow = FALSE ) {
		/**
		 * Init the translator
		 */
		$this->translator = new TranslateClient();
		$this->translator->setSource( 'en' );
		$this->translator->setTarget( 'vi' );
		/**
		 * Init the http client
		 */
		/** @var \GuzzleHttp\Client httpClient */
		$this->httpClient = new Client();
		$this->url        = $url;
		$this->post       = new \stdClass();
		if ( $processNow ) {
			$this->processPost();
		}
	}

	public function getPost() {
		if ( $this->post === NULL ) {
			$this->processPost();
		}

		return $this->post;
	}

	public function processPost() {
		$this->fetchBody();
		$this->processTitle();
		$this->processThumbnail();
		$this->processContent();
	}

	public function fetchBody() {
		$res           = $this->httpClient->get( $this->url );
		$body          = $res->getBody();
		$this->crawler = new Crawler( $body->getContents() );
	}

	public function processTitle() {
		$titleDOM = $this->crawler->filterXPath( '//article[contains(@class, "entry")]/h1[@class="title"]' );
		$this->post->title =  $this->translator->translate( $titleDOM->first()->text() );
	}

	public function processThumbnail() {
		$thumbnailDOM = $this->crawler->filterXPath( '//article[contains(@class, "entry")]/div[@class="post-thumbnail"]/img' );
		$this->post->thumbnail = $thumbnailDOM->attr( 'src' );
	}

	public function processNode(Crawler $node){
		$newNode = null;
		$handlerName = 'process_'.ucfirst($node->nodeName());
		if(method_exists($this, $handlerName))
		{
			$newNode = call_user_func_array(array($this, $handlerName), array(&$node));
		}
		return $newNode;
	}

	public function processContent() {
		$entryDOM  = $this->crawler->filterXPath( '//article[contains(@class, "entry")]' );
		$this->post->content = '';
		$entryDOM->children()->each( function ( Crawler $node, $i ) {
			$newNode = $this->processNode( $node );
			if ( $newNode !== NULL ) {
				if($newNode->nodeName !=='a' && isset( $newNode->text)) {
					$nodeName = trim( $newNode->nodeName );
					$this->post->content .= '<' . $nodeName . '>' . $newNode->text . '</' . $nodeName . '>';
				}
			}
		} );
		$this->post->content = $this->translator->translate($this->post->content);
	}

	/**
	 * Summary.
	 *
	 * @since  0.9.0
	 *
	 * @param \Symfony\Component\DomCrawler\Crawler $node
	 *
	 * @return \stdClass
	 *
	 * @author nguyenvanduocit
	 */
	public function process_A(Crawler &$node){
		$newNode = new \stdClass();
		$newNode->nodeName = $node->nodeName();
		$newNode->text = $node->text();
		$newNode->href = $node->attr('href');
		$newNode->title = $node->attr('title');
		return $newNode;
	}

	/**
	 * process image.
	 *
	 * @since  0.9.0
	 *
	 * @param \Symfony\Component\DomCrawler\Crawler $node
	 *
	 * @return \stdClass
	 *
	 * @author nguyenvanduocit
	 */
	public function process_Img(Crawler &$node){
		$newNode = new \stdClass();
		$newNode->nodeName = $node->nodeName();
		$newNode->src = $node->attr('src');
		return $newNode;
	}

	/**
	 * process iframe.
	 *
	 * @since  0.9.0
	 *
	 * @param \Symfony\Component\DomCrawler\Crawler $node
	 *
	 * @return \stdClass
	 *
	 * @author nguyenvanduocit
	 */
	public function process_Iframe(Crawler &$node){
		$newNode = new \stdClass();
		$newNode->nodeName = $node->nodeName();
		return $newNode;
	}

	/**
	 * Process p node.
	 *
	 * @since  0.9.0
	 *
	 * @param \Symfony\Component\DomCrawler\Crawler $node
	 *
	 * @return \stdClass
	 *
	 * @author nguyenvanduocit
	 */
	public function process_P(Crawler &$node){
		if(strlen($node->text()) === 0){
			return null;
		}
		$newNode = new \stdClass();
		$newNode->nodeName = $node->nodeName();
		$newNode->text = trim($node->text());
		return $newNode;
	}

	/**
	 * Process general node.
	 *
	 * @since  0.9.0
	 *
	 * @param \Symfony\Component\DomCrawler\Crawler $node
	 *
	 * @return \stdClass
	 *
	 * @author nguyenvanduocit
	 */
	public function process_General(Crawler &$node){
		$newNode = new \stdClass();
		$newNode->nodeName = $node->nodeName();
		$newNode->text = $node->text();
		return $newNode;
	}
}