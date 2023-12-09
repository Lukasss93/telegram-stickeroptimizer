<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::table('statistics')
            ->update([
                'action' => DB::raw("case
                    when category='command' then concat('command.', action)
                    when category='handler' then concat('handler.', action)
                    when action='chat.blocked' then 'user.status.blocked'
                    when action='chat.unblocked' then 'user.status.unblocked'
                    when action='sticker' then 'sticker.optimized'
                    when category='settings' then 'settings.news'
                    when action='donation' then 'donate.success'
                    when action='precheckout' then 'donate.precheckout'
                    else action
                end"),
            ]);

        Schema::table('statistics', function (Blueprint $table) {
            $table->string('action')->index()->change();
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropIndex(['action']);
            $table->dropColumn('action');
            $table->string('category');
        });
    }
};
