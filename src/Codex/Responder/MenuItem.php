<?php namespace Codex\Responder;


class MenuItem {

	public $items = [];
	public $label;
	public $icon;

	public function __construct($label = '', $icon = '') {
		$this->label = $label;
	}

	public function addList($label, $icon, $url) {
		$item = $this->addItem($label, $icon, 'list');
		$item[ 'options' ][ 'url' ] = $url;
		$this->items[] = $item;
		return $this;
	}

	protected function addItem($label, $icon, $action) {
		return [ 'label' => $label, 'icon' => $icon, 'action' => $action, 'options' => [] ];
	}

	public function get() {
		return [
			'label' => $this->label,
			'icon'  => $this->icon,
			'items' => $this->items,
		];
	}

};
