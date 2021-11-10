<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations\console\controllers;

use craft\console\Controller;
use craft\helpers\Console;
use yii\console\ExitCode;
use putyourlightson\blitzrecommendations\BlitzRecommendations;

/**
 * Allows you to manage recommendations.
 */
class RecommendationsController extends Controller
{
    /**
     * Clears all recommendations.
     *
     * @return int
     */
    public function actionClear(): int
    {
        $this->stdout('Clearing recommendations... ');
        BlitzRecommendations::$plugin->recommendations->clearAll();
        $this->stdout('done' . PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }
}
