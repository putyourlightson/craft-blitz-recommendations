<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzhintz;

use craft\base\Plugin;
use putyourlightson\blitzhints\BlitzHints;

class BlitzHintz extends Plugin
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
