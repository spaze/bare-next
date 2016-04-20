<?php
namespace Netxten\Formatter;

class Texy
{

	/** @var string */
	const DEFAULT_NAMESPACE = 'TexyFormatted';

	/** @var string */
	private $namespace;

	/** @var array $event => $callback */
	private $handlers = array();

	/** @var Nette\Caching\IStorage */
	protected $cacheStorage;


	public function __construct(\Nette\Caching\IStorage $cacheStorage, $namespace = self::DEFAULT_NAMESPACE)
	{
		$this->cacheStorage = $cacheStorage;
		$this->namespace = $namespace;
	}


	/**
	 * @return \Texy\Texy
	 */
	protected function getTexy()
	{
		$texy = new \Texy\Texy();
		$texy->encoding = 'utf-8';
		$texy->allowedTags = $texy::NONE;
		foreach ($this->handlers as $event => $callback) {
			$texy->addHandler($event, $callback);
		}
		return $texy;
	}


	/**
	 * @param string $event
	 * @param callable $callback
	 */
	protected function addHandler($event, $callback)
	{
		$this->handlers = array_merge($this->handlers, [$event => $callback]);
	}


	/**
	 * @var string
	 * @var callable
	 * @return \Nette\Utils\Html
	 */
	private function cache($text, callable $callback)
	{
		$cache = new \Nette\Caching\Cache($this->cacheStorage, $this->namespace);

		// Nette Cache itself generates the key by hashing the key passed in load() so we can use whatever we want
		$formatted = $cache->load($text, $callback);
		return \Nette\Utils\Html::el()->setHtml($formatted);
	}


	/**
	 * Format string and strip surrounding P element.
	 *
	 * Suitable for "inline" strings like headers.
	 *
	 * @return \Nette\Utils\Html|false
	 */
	public function format($text)
	{
		return (empty($text) ? false : $this->cache("{$text}|" . __FUNCTION__, function() use ($text) {
			$texy = $this->getTexy();
			return preg_replace('~^\s*<p[^>]*>(.*)</p>\s*$~s', '$1', $texy->process($text));
		}));
	}


	/**
	 * Format string.
	 *
	 * @return \Nette\Utils\Html|false
	 */
	public function formatBlock($text)
	{
		return (empty($text) ? false : $this->cache("{$text}|" . __FUNCTION__, function() use ($text) {
			return $this->getTexy()->process($text);
		}));
	}


}
