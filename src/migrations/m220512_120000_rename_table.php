<?php

namespace putyourlightson\blitzrecommendations\migrations;

use craft\db\Migration;
use putyourlightson\blitzhints\records\HintRecord;

class m220512_120000_rename_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $oldTableName = '{{%blitz_recommendations}}';

        if (!$this->db->tableExists(HintRecord::tableName())) {
            $this->renameTable($oldTableName, HintRecord::tableName());
        }

        $this->dropTableIfExists($oldTableName);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo self::class . " cannot be reverted.\n";

        return false;
    }
}
