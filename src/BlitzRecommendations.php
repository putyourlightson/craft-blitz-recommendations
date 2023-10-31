<?php

/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations;

use craft\base\Plugin;
use putyourlightson\blitzhints\BlitzRecommendations;

class BlitzRecommendations extends Plugin
{
    /**
     * @inheritdoc
     */
    public string $schemaVersion = '2.1.0';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        BlitzRecommendations::bootstrap();
    }
}
