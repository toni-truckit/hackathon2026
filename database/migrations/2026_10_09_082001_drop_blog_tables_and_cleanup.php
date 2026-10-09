<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $blogMorph = 'App\Models\Blog';
        $categoryMorph = 'App\Models\Category';

        DB::table('seo')->whereIn('model_type', [$blogMorph, $categoryMorph])->delete();
        DB::table('media')->where('model_type', $blogMorph)->delete();
        DB::table('views')->where('viewable_type', $blogMorph)->delete();

        Schema::dropIfExists('blog_category');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('categories');

        DB::table('static_pages')->where('name', 'blog')->delete();
    }

    public function down(): void
    {
        //
    }
};
