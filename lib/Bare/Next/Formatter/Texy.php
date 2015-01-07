<?php
namespace Bare\Next\Formatter;

class Texy
{

	const TEXY_NAMESPACE = 'TexyFormatted';

	/**
	 * @var Nette\Caching\IStorage
	 */
	protected $cacheStorage;


	public function __construct(\Nette\Caching\IStorage $cacheStorage)
	{
		$this->cacheStorage = $cacheStorage;
	}


	/**
	 * @return \Texy
	 */
	protected function getTexy()
	{
		$texy = new \Texy();
		$texy->encoding = 'utf-8';
		$texy->allowedTags = \Texy::NONE;
		$this->addHandlers($texy);
		return $texy;
	}


	/**
	 * @param \Texy $texy
	 */
	protected function addHandlers(\Texy $texy)
	{
		// Intentionally empty, ready to override
	}


	/**
	 * @return \Nette\Utils\Html
	 */
	public function format($text)
	{
		if (empty($text)) {
			return $text;
		}

		$cache = new \Nette\Caching\Cache($this->cacheStorage, self::TEXY_NAMESPACE);

		// Nette Cache itself generates the key by hashing the key passed in load() so we can use whatever we want
		$formatted = $cache->load($text, function() use ($text) {
			$texy = $this->getTexy();
			return preg_replace('~^\s*<p[^>]*>(.*)</p>\s*$~s', '$1', $texy->process($text));
		});
		return \Nette\Utils\Html::el()->setHtml($formatted);;
	}


}