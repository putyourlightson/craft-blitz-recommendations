<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitztips\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use putyourlightson\blitztips\BlitzTips;

class TipsController extends Controller
{
    /**
     * Clears all tips.
     *
     * @return Response
     */
    public function actionClearAll(): Response
    {
        $this->requirePostRequest();

        BlitzTips::$plugin->tips->clearAll();

        return $this->redirectToPostedUrl();
    }

    /**
     * Clears a recommendation.
     *
     * @return Response
     */
    public function actionClear(): Response
    {
        $this->requirePostRequest();

        $id = Craft::$app->getRequest()->getRequiredBodyParam('id');

        BlitzTips::$plugin->tips->clear($id);

        return $this->redirectToPostedUrl();
    }
}
