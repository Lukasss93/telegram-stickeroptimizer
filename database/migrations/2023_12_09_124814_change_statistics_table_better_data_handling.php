<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->index('category');
        });

        echo "\n\nAdded index to category\n";

        DB::table('statistics')
            ->where('category', 'command')
            ->update(['action' => DB::raw("concat('command.', action)")]);

        echo "Updated where category is command set action to command.action\n";

        DB::table('statistics')
            ->where('category', 'handler')
            ->update(['action' => DB::raw("concat('handler.', action)")]);

        echo "Updated where category is handler set action to handler.action\n";

        DB::table('statistics')
            ->where('category', 'donation')
            ->update(['action' => 'settings.news']);

        echo "Updated where category is donation set action to settings.news\n";

        Schema::table('statistics', function (Blueprint $table) {
            $table->string('action')->index()->change();
        });

        echo "Changed action column to index\n";

        DB::table('statistics')
            ->where('action', 'chat.blocked')
            ->update(['action' => 'user.status.blocked']);

        echo "Updated where action is chat.blocked set action to user.status.blocked\n";

        DB::table('statistics')
            ->where('action', 'chat.unblocked')
            ->update(['action' => 'user.status.unblocked']);

        echo "Updated where action is chat.unblocked set action to user.status.unblocked\n";

        DB::table('statistics')
            ->where('action', 'sticker')
            ->update(['action' => 'sticker.optimized']);

        echo "Updated where action is sticker set action to sticker.optimized\n";

        DB::table('statistics')
            ->where('action', 'donation')
            ->update(['action' => 'donate.success']);

        echo "Updated where action is donation set action to donate.success\n";

        DB::table('statistics')
            ->where('action', 'precheckout')
            ->update(['action' => 'donate.precheckout']);

        echo "Updated where action is precheckout set action to donate.precheckout\n";

        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        echo "Dropped category column\n";
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
