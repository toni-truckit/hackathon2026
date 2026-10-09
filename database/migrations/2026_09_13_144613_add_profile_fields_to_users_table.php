<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $blueprint): void {
            $blueprint->string('slug')->nullable()->unique()->after('name');
            $blueprint->string('job_title')->nullable()->after('slug');
            $blueprint->text('bio')->nullable()->after('job_title');
            $blueprint->string('website')->nullable()->after('bio');
        });

        DB::table('users')->select('id', 'name', 'slug')->orderBy('id')->each(function (object $user): void {
            if (filled($user->slug)) {
                return;
            }

            $slug = Str::slug($user->name) ?: 'author-'.$user->id;
            $base = $slug;
            $suffix = 2;

            while (DB::table('users')->where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                $slug = $base.'-'.$suffix;
                $suffix++;
            }

            DB::table('users')->where('id', $user->id)->update(['slug' => $slug]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint): void {
            $blueprint->dropColumn(['slug', 'job_title', 'bio', 'website']);
        });
    }
};
