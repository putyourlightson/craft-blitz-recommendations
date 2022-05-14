<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations\migrations;

use Craft;
use craft\db\Migration;
use putyourlightson\blitzhints\migrations\Install as HintsInstall;

class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        return (new HintsInstall())->safeUp();
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        // Don't remove table if Blitz is installed.
        if (Craft::$app->getPlugins()->isPluginInstalled('blitz')) {
            return true;
        }

        return (new HintsInstall())->safeDown();
    }
}
