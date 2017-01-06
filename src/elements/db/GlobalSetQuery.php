<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\elements\db;

use Craft;
use craft\db\QueryAbortedException;
use craft\elements\GlobalSet;
use craft\helpers\Db;

/**
 * GlobalSetQuery represents a SELECT SQL statement for global sets in a way that is independent of DBMS.
 *
 * @method GlobalSet[]|array all($db = null)
 * @method GlobalSet|array|null one($db = null)
 * @method GlobalSet|array|null nth($n, $db = null)
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class GlobalSetQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    // General parameters
    // -------------------------------------------------------------------------

    /**
     * @var bool Whether to only return global sets that the user has permission to edit.
     */
    public $editable;

    /**
     * @var string|string[] The handle(s) that the resulting global sets must have.
     */
    public $handle;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($elementType, array $config = [])
    {
        // Default orderBy
        if (!isset($config['orderBy'])) {
            $config['orderBy'] = 'name';
        }

        parent::__construct($elementType, $config);
    }

    /**
     * Sets the [[editable]] property.
     *
     * @param bool $value The property value (defaults to true)
     *
     * @return static self reference
     */
    public function editable($value = true)
    {
        $this->editable = $value;

        return $this;
    }

    /**
     * Sets the [[handle]] property.
     *
     * @param string|string[] $value The property value
     *
     * @return static self reference
     */
    public function handle($value)
    {
        $this->handle = $value;

        return $this;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function beforePrepare()
    {
        $this->joinElementTable('globalsets');

        $this->query->select([
            'globalsets.name',
            'globalsets.handle',
            'globalsets.fieldLayoutId',
        ]);

        if ($this->handle) {
            $this->subQuery->andWhere(Db::parseParam('globalsets.handle', $this->handle));
        }

        $this->_applyEditableParam();

        return parent::beforePrepare();
    }

    // Private Methods
    // =========================================================================

    /**
     * Applies the 'editable' param to the query being prepared.
     *
     * @throws QueryAbortedException
     */
    private function _applyEditableParam()
    {
        if ($this->editable) {
            // Limit the query to only the global sets the user has permission to edit
            $editableSetIds = Craft::$app->getGlobals()->getEditableSetIds();
            $this->subQuery->andWhere(['elements.id' => $editableSetIds]);
        }
    }
}
