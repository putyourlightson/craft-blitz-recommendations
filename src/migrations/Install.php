<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzrecommendations\migrations;

use craft\db\Migration;
use putyourlightson\blitzrecommendations\records\RecommendationRecord;
use Throwable;

class Install extends Migration
{
    /**
     * @return boolean
     */
    public function safeUp(): bool
    {
        if (!$this->db->tableExists(RecommendationRecord::tableName())) {
            $this->createTable(RecommendationRecord::tableName(), [
                'id' => $this->primaryKey(),
                'key' => $this->string()->notNull(),
                'template' => $this->string(),
                'message' => $this->text(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);

            $this->createIndex(null, RecommendationRecord::tableName(), ['key', 'template'], true);
        }

        return true;
    }

    /**
     * @return boolean
     * @throws Throwable
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists(RecommendationRecord::tableName());

        return true;
    }
}
