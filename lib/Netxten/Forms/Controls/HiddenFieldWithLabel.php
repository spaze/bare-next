<?php
namespace Netxten\Forms\Controls;

/**
 * Hidden form control used to store a non-displayed value but with label and optional text
 *
 * @author     Michal Špaček
 */
class HiddenFieldWithLabel extends \Nette\Forms\Controls\BaseControl
{

	/** @param  Html|string field text */
	protected $text;


	public function __construct($label = null, $value = null, $text = null)
	{
		parent::__construct($label);
		$this->control->type = 'hidden';
		$this->value = $value;
		$this->text = $text;
	}


	/**
	 * Generates control's HTML element.
	 * @return Nette\Utils\Html
	 */
	public function getControl()
	{
		$input = parent::getControl()
			->value($this->value === null ? '' : $this->value)
			->data('nette-rules', null);

		$container = \Nette\Utils\Html::el();
		if ($this->text !== null) {
			$container->add($this->text);
		}
		$container->add($input);
		return $container;
	}


}
