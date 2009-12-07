<?php
/**
 * See docs/AUTHORS and docs/COPYRIGHT for relevant info.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 *
 * @version $Id$
 * @author Matthew McNaney <mcnaney at gmail dot com>
 * @package
 * @license http://opensource.org/licenses/gpl-3.0.html
 */
require_once 'PEAR/Exception.php';

class Tag {
    /**
     * Tag type (e.g. p, b, div, etc.)
     * @var string
     */
    private $type = null;


    /**
     * Tag identifier
     * @var string
     */
    protected $id = null;

    /**
     * Inline style definition for tag
     * @var string
     */
    protected $style = null;

    /**
     * Class definition of tag
     * @var string
     */
    protected $class = null;

    /**
     * If true, the tag is open (e.g. <p></p>), closed (e.g. <br />) otherwise)
     * @var boolean
     */
    protected $open = true;

    /**
     * Value of the tag. Output of value determined by open status
     * @var string
     */
    private $value = null;

    /**
     * If there was an error, it will be listed here
     * @var array
     */
    private $error = array();

    protected function setType($type)
    {
        if (preg_match('/[^a-z]/', $type)) {
            throw new PEAR_Exception(dgettext('core', 'Tag type must be alphabetic characters only'));
        }
        $this->type = $type;
    }

    public function __toString()
    {
        $data[] = "<$this->type";

        $tag_parameters = get_object_vars($this);
        unset($tag_parameters['open']);
        unset($tag_parameters['type']);
        unset($tag_parameters['error']);
        if (!empty($tag_parameters)) {
            foreach ($tag_parameters as $pname=>$param) {
                if (is_null($param)) {
                    continue;
                }
                if (is_string($param)) {
                    $param = htmlentities($param, ENT_COMPAT);
                }
                $data[] = sprintf('%s="%s"', $pname, $param);
            }
        }
        $result = implode(' ', $data);

        if($this->open) {
            $result .= ">$this->value</$this->type>";
        } else {
            $result .= " />";
        }

        return $result;
    }

    public function listErrors()
    {
        return $this->error;
    }

    public function setClass($class)
    {
        if (preg_match('/[^\w\-]/', $class)) {
            trigger_error(dgettext('core', 'Improper class name'));
        }
        $this->class = $class;
    }

    public function setStyle($style)
    {
        if (preg_match('/[^\w\-;\s:!]/', $style)) {
            trigger_error(dgettext('core', 'Improperly formatted style settings'));
        }
        $this->style = $style;
    }
}

?>