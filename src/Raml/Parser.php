<?php

namespace Raml;

class Parser extends \Symfony\Component\Yaml\Yaml {

	protected $file;
	protected $raml;
	protected $includes = [];

	public function __construct($file) {
		$this->file = $file;
		$this->includes = [];
		if (file_exists($this->file)) {
			$contents = file_get_contents($this->file);
			$this->raml = $this->parse($contents);
			$this->processIncludes();
		} else {
			throw new \Exception("File not found $file");
		}
	}

	protected function processIncludes() {
		array_walk_recursive($this->raml, function(&$val, $index) {

			if (!is_string($val)) {
				return;
			}
			if (strpos($val, "!include") !== 0) {
				return;
			}

			$file = trim(substr($val, 9));
			if (!array_key_exists($file, $this->includes)) {

				$dir = dirname($this->file);
				$includefile = $dir.'/'.$file;
				if (!file_exists($includefile)) {
					throw new \Exception("Include file not found $includefile");
				}
				$contents = file_get_contents($includefile);
				$ext = pathinfo($file, PATHINFO_EXTENSION);

				// YAML and RAML files will be parsed, others included as strings
				if ($ext === 'yaml' || $ext === 'raml') {
					$this->includes[$file] = $this->parse($contents);
				} else {
					$this->includes[$file] = $contents;
				}
			}

			$val = $this->includes[$file];
		});
	}

	public function getRaml() {
		return $this->raml;
	}

	/**
	 * Get an associative array of the named schemas
	 */
	public function getSchemas() {
		if (!array_key_exists('schemas', $this->raml)) {
			return [];
		}
		return self::sequenceToAssociativeArray($this->raml['schemas']);
	}

	public function getResourceTypes() {
		if (!array_key_exists('resourceTypes', $this->raml)) {
			return [];
		}
		return self::sequenceToAssociativeArray($this->raml['resourceTypes']);
	}

	public function getTraits() {
		if (!array_key_exists('traits', $this->raml)) {
			return [];
		}
		return self::sequenceToAssociativeArray($this->raml['traits']);
	}

	public function getResources($includeSubresources = false) {
		$result = [];
		foreach ($this->raml as $key => $val) {
			if (strpos($key, '/') === 0) {
				$result[$key] = $val;
			}
		}
		return $result;
	}

	/**
	 * Collapse a sequence to a PHP associative array.
	 *
	 * The YAML parser turns a sequence like this:
	 *
	 * thing:
	 *   - one: 1
	 *   - two: 2
	 *
	 * into this:
	 *
	 * [ ["one" => 1], ["two" => 2] ]
	 *
	 * This function collapses this to:
	 *
	 * ["one" => 1, "two" => 2]
	 *
	 * @param array $sequence
	 * @return array
	 */
	protected static function sequenceToAssociativeArray($sequence) {
		$result = [];
		foreach ($sequence as $arr) {
			$result += $arr;
		}

		return $result;
	}



}