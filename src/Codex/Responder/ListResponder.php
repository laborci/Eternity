<?php namespace Codex\Responder;

use Eternity\Response\Responder\JsonResponder;

class ListResponder extends JsonResponder {

	protected function respond() {
		/** @var \Codex\AdminDescriptor $adminDescriptor */
		$adminDescriptorClass = $this->getArgumentsBag()->get('admin');
		$adminDescriptor = new $adminDescriptorClass();

		$listHandler = $adminDescriptor->getListHandler();
		$sorting = $this->getJsonParamBag()->get('sorting');
		$filter = $this->getJsonParamBag()->get('filter');
		$page = $this->getJsonParamBag()->get('page');
		return $listHandler->get($page, $sorting, $filter);
	}

}


