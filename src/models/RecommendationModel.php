<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitztips\models;

use craft\base\Model;
use DateTime;

class RecommendationModel extends Model
{
    /**
     * @var int|null
     */
    public $id;

    /**
     * @var string|null
     */
    public $key;

    /**
     * @var string|null
     */
    public $template;

    /**
     * @var string|null
     */
    public $message;

    /**
     * @var string|null
     */
    public $info;

    /**
     * @var DateTime
     */
    public $dateUpdated;
}
