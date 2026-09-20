<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bang dealers luc dau khai province_id/district_id/ward_id la so nguyen, nhung
 * ba bang dia gioi cua du an dung khoa chinh la cot `code` kieu varchar(20)
 * ("01", "79", "00001"...). Ep so nguyen vao do se mat so 0 dung dau va khong
 * join lai duoc.
 *
 * Bang dang rong nen doi thang kieu cot, dat lai ten cho dung quy uoc _code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropIndex(['province_id']);
            $table->dropColumn(['province_id', 'district_id', 'ward_id']);
        });

        Schema::table('dealers', function (Blueprint $table) {
            $table->string('province_code', 20)->nullable()->after('phone')->index();
            $table->string('district_code', 20)->nullable()->after('province_code');
            $table->string('ward_code', 20)->nullable()->after('district_code');
        });
    }

    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropIndex(['province_code']);
            $table->dropColumn(['province_code', 'district_code', 'ward_code']);
        });

        Schema::table('dealers', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable()->after('phone')->index();
            $table->unsignedBigInteger('district_id')->nullable()->after('province_id');
            $table->unsignedBigInteger('ward_id')->nullable()->after('district_id');
        });
    }
};
