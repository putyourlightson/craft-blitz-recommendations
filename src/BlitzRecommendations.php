<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations;

use craft\base\Plugin;
use putyourlightson\blitzhints\BlitzHints;

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

        BlitzHints::bootstrap();
    }
}
