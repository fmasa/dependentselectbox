<?php

/**
 * @author Daniel Robenek
 * @license MIT
 */

namespace DependentSelectBox;

use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\InvalidStateException;

class JsonDependentSelectBox extends DependentSelectBox
{

	public static $jsonResoponseItems = array();

	public function submitButtonHandler($button)
	{
		parent::submitButtonHandler($button);
		if ($this->lookup("\Nette\Application\UI\Presenter")->isAjax())
			$this->addJsonResponseItem($this);
	}

	protected function addJsonResponseItem($selectBox)
	{
		self::$jsonResoponseItems[] = $selectBox;
		if ($selectBox instanceof DependentSelectBox)
			foreach ($selectBox->childs as $child)
				$child->addJsonResponseItem($child);
	}

	public static function tryJsonResponse(Presenter $presenter)
	{
		if (empty(self::$jsonResoponseItems))
			return;

		$payload = array(
			"type" => "JsonDependentSelectBoxResponse",
			"items" => array()
		);
		foreach (self::$jsonResoponseItems as $item) {
			$payload["items"][$item->getHtmlId()] = array(
				"selected" => $item->getValue(),
				"items" => $item->getItems()
			);
		}
		$response = new JsonResponse($payload);
		$presenter->sendResponse($response);
	}

}
