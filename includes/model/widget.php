<?php

namespace ACFWidgets\model;

class Widget {

    public $slug;
    public $index;

	/**
	 * @var [] Magic fields
	 */
	private $fields;

	/**
     * Widget constructor.
     * @param $data
     * @param $index
     */
    public function __construct($data, $index) {
        $this->index = $index;
        $this->slug = $data['acf_fc_layout'];

        foreach($data as $k => $v) {
        	$this->$k = $v;
		}
    }

    public function isFirst() {
        return $this->index == 0;
    }


	/**
	 * The extra magic get function
	 * @param $name
	 * @return object|string|boolean
	 */
	public function __get($name) {
		if (isset( $this->fields[$name])) {
			return $this->fields[$name];
		}
		// This is where the MAGIC happens
		return ($this->$name = get_field($name, get_the_ID()));
	}

	public function __set($name, $value) {
		$this->fields[$name] = $value;
	}

	/**
	 * Make sure our empty() and isset() functions behave correctly
	 * @param $name
	 * @return bool
	 */
	public function __isset($name) {
		if (!isset($this->fields[$name])) {
			$this->$name = get_field($name, get_the_ID());
		}
		return isset($this->fields[$name]);
	}

}
