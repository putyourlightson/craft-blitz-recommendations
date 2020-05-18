<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use putyourlightson\blitzrecommendations\BlitzRecommendations;

class RecommendationsController extends Controller
{
    /**
     * Clears all recommendations.
     *
     * @return Response
     */
    public function actionClearAll(): Response
    {
        $this->requirePostRequest();

        BlitzRecommendations::$plugin->recommendations->clearAll();

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

        BlitzRecommendations::$plugin->recommendations->clear($id);

        return $this->redirectToPostedUrl();
    }
}
