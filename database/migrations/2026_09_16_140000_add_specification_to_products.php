<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Them cot thong so ky thuat cho san pham.
 *
 * Trang chi tiet co hai the: "Thong tin chi tiet" (cot content) va "Thong so ky
 * thuat" (cot nay). Truoc day khong co cho luu thong so nen the thu hai khong co
 * gi de hien.
 *
 * Quan tri nhap moi dong mot thong so dang "Ten: Gia tri", giao dien tu dung
 * thanh bang hai cot.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->longText('specification')->nullable()->after('iframe');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('specification');
        });
    }
};
