<?php
/**
 * @link      https://www.goldinteractive.ch
 * @copyright Copyright (c) 2018 Gold Interactive
 * @author Christian Ruhstaller
 * @license MIT
 */

namespace goldinteractive\publisher\controllers;

use Craft;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use craft\helpers\DateTimeHelper;
use craft\web\Controller;
use goldinteractive\publisher\elements\EntryPublish;
use goldinteractive\publisher\Publisher;
use yii\web\NotFoundHttpException;

/**
 * Class EntriesController
 *
 * @package goldinteractive\publisher\controllers
 */
class EntriesController extends Controller
{
    /**
     * Saves an EntryPublish.
     *
     * @throws ElementNotFoundException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionSave()
    {
        $this->requirePostRequest();

        $publishAt = Craft::$app->request->post('publisher_publishAt');
        $siteId = Craft::$app->request->post('publisher_sourceSiteId');
        // $draftId = Craft::$app->request->post('publisher_draftId');

        $versionId = Craft::$app->request->post('publisher_versionId');

        list($type, $tempId) = explode('_', $versionId);

        $draft = null;
        $revision = null;
        $entry = null;

        if ( $type === 'draft' ) {
            $draftId = $tempId;

            $draft = Entry::find()
                ->draftId($draftId)
                ->siteId($siteId)
                ->one();

            if ($draft === null) {
                throw new \Exception('Invalid entry draft ID: '.$draftId);
            }

            $entry = Craft::$app->entries->getEntryById($draft->getCanonicalId(), $siteId);
        } else {
            $revisionId = $tempId;

            $revision = Entry::find()
            ->revisionId($revisionId)
            ->siteId($siteId)
            ->one();

            if ($revision === null) {
                throw new \Exception('Invalid entry draft ID: '.$revisionId);
            }

            $entry = Craft::$app->entries->getEntryById($revision->getCanonicalId(), $siteId);
        }

        if ($entry === null) {
            throw new ElementNotFoundException("No element exists with the ID '{$draft->getCanonicalId()}'");
        }

        if (isset($draft) and $draft->enabled) {
            $this->requirePermission('publishEntries:'.$entry->section->uid);
        }

        if (isset($revision) and $revision->enabled) {
            $this->requirePermission('publishEntries:'.$entry->section->uid);
        }

        if ($publishAt !== null) {
            $publishAt = DateTimeHelper::toDateTime($publishAt, true);
        }

        $model = new EntryPublish();
        $model->sourceId = $entry->id;
        if (isset($draft) and !empty($draft)) {
            $model->publishDraftId = $draft->draftId;
        }
        if (isset($revision) and !empty($revision)) {
            $model->publishRevisionId = $revision->revisionId;
        }
        $model->publishAt = $publishAt;
        $model->sourceSiteId = $siteId;

        if (!Publisher::getInstance()->entries->saveEntryPublish($model)) {
            Craft::$app->getUrlManager()->setRouteParams(
                [
                    'publisherEntry' => $model,
                ]
            );
        }
    }


    /**
     * Deletes the EntryPublish.
     *
     * @return bool
     * @throws \Throwable
     */
    public function actionDelete()
    {
        $entriesService = Publisher::getInstance()->entries;
        $publishEntryId = Craft::$app->request->getQueryParam('sourceId');

        if ($publishEntryId === null) {
            throw new NotFoundHttpException('EntryPublish not found');
        }

        $entryPublish = $entriesService->getEntryPublishById($publishEntryId);

        if ($entryPublish !== null) {
            $entry = $entryPublish->getEntry();

            $entriesService->deleteEntryPublish($publishEntryId);
            $this->redirect($entry->getCpEditUrl());

            return true;
        }

        throw new NotFoundHttpException('EntryPublish not found');
    }
}
