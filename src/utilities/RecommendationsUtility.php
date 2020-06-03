<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations\utilities;

use Craft;
use craft\base\Utility;
use putyourlightson\blitzrecommendations\BlitzRecommendations;

class RecommendationsUtility extends Utility
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('blitz-recommendations', 'Blitz Recommendations');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'blitz-recommendations';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        $iconPath = Craft::getAlias('@vendor/putyourlightson/craft-blitz-recommendations/src/icon-mask.svg');

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
        return BlitzRecommendations::$plugin->recommendations->getTotal();
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('blitz-recommendations/_utility', [
            'recommendations' => BlitzRecommendations::$plugin->recommendations->getAll(),
        ]);
    }
}

