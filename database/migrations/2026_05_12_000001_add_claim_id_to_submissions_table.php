<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('claim_id')->nullable()->after('profile_id')->constrained()->nullOnDelete();
        });

        DB::table('submissions')
            ->whereNull('claim_id')
            ->orderBy('profile_id')
            ->get(['id', 'profile_id'])
            ->groupBy('profile_id')
            ->each(function ($submissions, $profileId) {
                $claimId = DB::table('claims')
                    ->where('profile_id', $profileId)
                    ->latest('created_at')
                    ->latest('id')
                    ->value('id');

                if ($claimId) {
                    DB::table('submissions')
                        ->whereIn('id', $submissions->pluck('id'))
                        ->update(['claim_id' => $claimId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('claim_id');
        });
    }
};
