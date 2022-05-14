<?php

namespace putyourlightson\blitzrecommendations\migrations;

use craft\db\Migration;

class m220512_120000_reinstall extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->dropTableIfExists('{{%blitz_recommendations}}');

        return (new Install())->safeUp();
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
