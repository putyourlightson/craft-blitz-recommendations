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
    public ?int $id;

    /**
     * @var string|null
     */
    public ?string $key;

    /**
     * @var string|null
     */
    public ?string $template;

    /**
     * @var string|null
     */
    public ?string $line;

    /**
     * @var string|null
     */
    public ?string $message;

    /**
     * @var string|null
     */
    public ?string $info;

    /**
     * @var DateTime|null
     */
    public ?DateTime $dateUpdated;
}
