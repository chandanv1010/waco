<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cho phep gan mot bai viet vao tung dong home_features.
 *
 * Dung truoc tien cho 3 huy hieu trong khoi gioi thieu: bam vao huy hieu thi
 * mo bai viet tuong ung. Khong dat khoa ngoai - bai viet bi xoa thi huy hieu
 * chi mat lien ket chu khong lam hong ca dong, va view da kiem tra bai viet
 * con ton tai truoc khi in the <a>.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_features', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id')->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('home_features', function (Blueprint $table) {
            $table->dropColumn('post_id');
        });
    }
};
