<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ba bang thiet ke WACO can ma ma nguon goc (truc) khong co.
 *
 * Nhung phan con lai cua trang chu deu dung duoc bo CMS san co:
 *   menus      - menu dau trang va 4 cot chan trang
 *   introduces - cac doan van ban theo tu khoa (tieu de hero, doan gioi thieu...)
 *   systems    - cau hinh (hotline, email, dia chi, mang xa hoi)
 *   widgets    - chon ban ghi cho khoi "He sinh thai san pham" va "Tin tuc"
 *   slides     - anh hero
 */
return new class extends Migration
{
    public function up(): void
    {
        // Cac khoi lap lai dang "icon + tieu de + mo ta" tren trang chu.
        // Thiet ke co ba cho dung dung mot dang nay nen gom vao MOT bang, phan
        // biet bang cot `group`, thay vi de moi cho mot bang rieng:
        //   hero_usp    - 4 diem manh duoi hero (Cong nghe Han Quoc, Nhap khau...)
        //   about_badge - 3 huy hieu khoi gioi thieu (Giay chung nhan doc quyen...)
        //   why_waco    - 6 ly do "Vi sao WACO duoc tin dung tai hon 70 quoc gia"
        //   stat        - 4 con so mang luoi (100+ dai ly, 63 tinh thanh...)
        Schema::create('home_features', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->comment('hero_usp | about_badge | why_waco | stat');
            $table->string('title');
            $table->string('description')->nullable();
            // Voi group = stat: `value` la con so hien to (100+, 63, 1000+, 20+)
            // con `title` la nhan ben duoi. Cac group khac de trong.
            $table->string('value', 50)->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->tinyInteger('publish')->default(2)->comment('2 = hien, 1 = an');
            $table->timestamps();

            $table->index(['group', 'publish', 'order']);
        });

        // He thong dai ly - nguon cho trang "He thong dai ly", cac ghim tren ban
        // do Viet Nam o trang chu, va de dem so tinh thanh da phu song.
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone', 50)->nullable();

            // Noi theo bang dia gioi san co trong CSDL, khong luu ten tinh dang
            // chuoi - de con loc va dem theo tinh cho chinh xac.
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('ward_id')->nullable();

            // Toa do de ghim len ban do. Nullable vi nhap lieu thuong co truoc,
            // toa do bo sung sau.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Thiet ke phan biet hai loai ghim tren ban do (chu thich goc duoi):
            // "Dai ly WACO" va "Trung tam ky thuat".
            $table->string('type', 30)->default('dealer')->comment('dealer | technical_center');

            $table->boolean('is_featured')->default(false);
            $table->tinyInteger('publish')->default(2);
            $table->unsignedInteger('order')->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index(['publish', 'order']);
            $table->index('province_id');
            $table->index('type');
        });

        // Don dang ky lam dai ly - form o khoi "Tro thanh dai ly phan phoi WACO".
        // De rieng chu khong nhet vao bang contacts: truong khac han (ten cong
        // ty, khu vuc kinh doanh) va day la luong kinh doanh chinh cua site nen
        // can loc, danh dau trang thai xu ly rieng.
        Schema::create('dealer_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('company')->nullable()->comment('Ten cong ty / cua hang');
            $table->string('business_area')->nullable()->comment('Dia chi khu vuc kinh doanh');
            $table->text('note')->nullable();

            $table->string('status', 30)->default('new')->comment('new | contacted | done | rejected');
            // Luu lai de biet don den tu dau khi sau nay co them form o trang khac.
            $table->string('source', 50)->nullable();

            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealer_registrations');
        Schema::dropIfExists('dealers');
        Schema::dropIfExists('home_features');
    }
};
