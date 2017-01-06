<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\fields\data;

use craft\base\Serializable;
use craft\helpers\Json;

/**
 * Multi-select option field data class.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class MultiOptionsFieldData extends \ArrayObject implements Serializable
{
    // Properties
    // =========================================================================

    /**
     * @var
     */
    private $_options;

    // Public Methods
    // =========================================================================

    /**
     * Returns the options.
     *
     * @return array|null
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets the options.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->_options = $options;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($value)
    {
        $value = (string)$value;

        foreach ($this as $selectedValue) {
            if ($value == $selectedValue->value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return Json::encode($this->getArrayCopy());
    }
}
