<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations\models;

use craft\base\Model;
use DateTime;

class RecommendationModel extends Model
{
    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var string|null
     */
    public ?string $key = null;

    /**
     * @var string|null
     */
    public ?string $template = null;

    /**
     * @var string|null
     */
    public ?string $line = null;

    /**
     * @var string|null
     */
    public ?string $message = null;

    /**
     * @var string|null
     */
    public ?string $info = null;

    /**
     * @var DateTime|null
     */
    public ?DateTime $dateUpdated = null;
}
