<?php

class Atom
{
	private $xpath;

	public function __construct($feed)
	{
		$dom = new DOMDocument();
		$dom->loadXML($feed);
		$this->xpath = new DOMXpath($dom);
		$this->xpath->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
	}

	public function url()
	{
		$url = array();
		$items = $this->xpath->evaluate('//atom:entry/atom:link/@href');
		foreach ($items as $item) {
			$url[] = $item->value;
		}

		return $url;
	}
}
