<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitztips\utilities;

use Craft;
use craft\base\Utility;
use putyourlightson\blitztips\BlitzTips;

class TipsUtility extends Utility
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('blitz-tips', 'Blitz Tips');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'blitz-tips';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        $iconPath = Craft::getAlias('@vendor/putyourlightson/craft-blitz-tips/src/icon-mask.svg');

        if (!is_string($iconPath)) {
            return null;
        }

        return $iconPath;
    }

    /**
     * @inheritdoc
     */
    public static function badgeCount(): int
    {
        return BlitzTips::$plugin->tips->getTotal();
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('blitz-tips/_utility', [
            'tips' => BlitzTips::$plugin->tips->getAll(),
        ]);
    }
}

