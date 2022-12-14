<?php
/**
 * @link      https://www.goldinteractive.ch
 * @copyright Copyright (c) 2018 Gold Interactive
 * @author    Christian Ruhstaller
 * @license   MIT
 */

namespace goldinteractive\publisher\elements;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\models\EntryDraft;
use craft\validators\DateTimeValidator;
use goldinteractive\publisher\elements\db\EntryPublishQuery;
use yii\validators\BooleanValidator;

/**
 * Class EntryPublish
 *
 * @package goldinteractive\publisher\elements
 */
class EntryPublish extends Element
{
    /**
     * @var int
     */
    public $sourceId;

    /**
     * @var int
     */
    public $sourceSiteId;

    /**
     * @var int
     */
    public $publishDraftId;

    /**
     * @var int
     */
    public $publishRevisionId;

    /**
     * @var \DateTime
     */
    public $publishAt;

    /**
     * @var bool
     */
    public $expire;

    /**
     * @var Entry
     */
    protected $_entry;

    /**
     * @var EntryDraft
     */
    protected $_draft;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('publisher', 'Entry Publish');
    }

    /**
     * @inheritdoc
     */
    public static function refHandle(): string
    {
        return 'entrypublish';
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        $names = parent::extraFields();
        $names[] = 'entry';
        $names[] = 'draft';

        return $names;
    }

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        $attributes = parent::datetimeAttributes();
        $attributes[] = 'publishAt';

        return $attributes;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['sourceId', 'sourceSiteId', 'publishDraftId', 'publishRevisionId'], 'number', 'integerOnly' => true];
        $rules[] = [['publishAt'], DateTimeValidator::class];
        $rules[] = [['expire'], BooleanValidator::class];

        return $rules;
    }

    /**
     * @return EntryPublishQuery
     */
    public static function find(): ElementQueryInterface
    {
        return new EntryPublishQuery(static::class);
    }

    /**
     * Returns the entry draft.
     *
     * @return Entry|null
     */
    public function getDraft(): ?Entry
    {
        $draft = $this->_draft;

        if ($draft !== null) {
            if ($draft === false) {
                $draft = null;
            }
        } elseif ($this->publishDraftId !== null) {
            $draft = Entry::find()
                ->draftId($this->publishDraftId)
                ->siteId($this->sourceSiteId)
                ->one();

            if ($draft === null) {
                $this->_draft = false;
            }
        }

        return $draft;
    }

    /**
     * Returns the entry revision.
     *
     * @return Entry|null
     */
    public function getRevision(): ?Entry
    {

        $revision = Entry::find()
            ->revisionId($this->publishRevisionId)
            ->siteId($this->sourceSiteId)
            ->one();

        return $revision;
    }

    /**
     * Returns the entry.
     *
     * @return Entry|null
     */
    public function getEntry(): ?Entry
    {
        $entry = $this->_entry;

        if ($entry !== null) {
            if ($entry === false) {
                $entry = null;
            }
        } elseif ($this->sourceId !== null) {
            $entry = Craft::$app->entries->getEntryById($this->sourceId, $this->sourceSiteId);

            if ($entry === null) {
                $this->_entry = false;
            }
        }

        return $entry;
    }
}
