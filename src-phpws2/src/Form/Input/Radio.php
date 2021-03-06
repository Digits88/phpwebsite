<?php

namespace phpws2\Form\Input;

/**
 *
 * @author Matthew McNaney <mcnaney at gmail dot com>
 * @package phpws2
 * @subpackage Form
 * @license http://opensource.org/licenses/lgpl-3.0.html
 */
class Radio extends \phpws2\Form\Input {

    /**
     * Indicates label should print after the input tag.
     * @var integer
     */
    protected $label_location = 1;
    /**
     * When true, adds the "checked" parameter to the radio input tag.
     * @var boolean
     */
    protected $checked;
    /**
     * Radio is a closed tag.
     * @var boolean
     */
    protected $open = false;

    /**
     * If true, set show the "checked" status of this input.
     * @param boolean $selection
     */
    public function setSelection($selection)
    {
        $this->checked = (bool) $selection;
    }

    /**
     * Alternate for setSelection
     * @param boolean $checked
     */
    public function setChecked($checked)
    {
        $this->setSelection($checked);
    }

}
