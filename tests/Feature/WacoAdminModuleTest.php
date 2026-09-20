<?php

namespace Tests\Feature;

use App\Models\Dealer;
use App\Models\HomeFeature;
use App\Models\User;
use Tests\TestCase;

/**
 * Kiem tra ba man hinh admin moi mo duoc va luu duoc.
 *
 * Khong dung RefreshDatabase: bo test nay chay tren chinh CSDL dang phat trien
 * de doi chieu voi du lieu that. Ban ghi nao tao ra trong test deu tu xoa o
 * cuoi moi ham.
 */
class WacoAdminModuleTest extends TestCase
{
    private function quanTri(): User
    {
        return User::orderBy('id')->firstOrFail();
    }

    public function test_mo_duoc_ba_man_hinh_danh_sach(): void
    {
        $user = $this->quanTri();

        $this->actingAs($user)->get('/home-feature/index')->assertOk();
        $this->actingAs($user)->get('/dealer/index')->assertOk();
        $this->actingAs($user)->get('/dealer/registration/index')->assertOk();
    }

    public function test_loc_theo_nhom_tra_ve_dung_muc(): void
    {
        $user = $this->quanTri();

        $response = $this->actingAs($user)->get('/home-feature/index?group=stat');
        $response->assertOk();

        // Loc nhom "stat" thi khong duoc lan muc cua nhom khac.
        $mucNhomKhac = HomeFeature::where('group', 'why_waco')->value('title');
        if ($mucNhomKhac) {
            $response->assertDontSee($mucNhomKhac, false);
        }
    }

    public function test_mo_duoc_man_hinh_them_va_sua(): void
    {
        $user = $this->quanTri();

        $this->actingAs($user)->get('/home-feature/create')->assertOk();
        $this->actingAs($user)->get('/dealer/create')->assertOk();

        $feature = HomeFeature::orderBy('id')->firstOrFail();
        $this->actingAs($user)->get("/home-feature/{$feature->id}/edit")->assertOk();
        $this->actingAs($user)->get("/home-feature/{$feature->id}/delete")->assertOk();
    }

    public function test_them_sua_xoa_mot_muc_trang_chu(): void
    {
        $user = $this->quanTri();

        $this->actingAs($user)->post('/home-feature/store', [
            'group' => 'stat',
            'title' => 'Muc thu nghiem tu dong',
            'description' => 'Mo ta thu',
            'value' => '99+',
            'icon' => '',
            'order' => 99,
            'publish' => 2,
        ])->assertRedirect(route('home.feature.index'));

        $muc = HomeFeature::where('title', 'Muc thu nghiem tu dong')->first();
        $this->assertNotNull($muc, 'Khong luu duoc muc moi');
        $this->assertSame('99+', $muc->value);
        $this->assertSame('stat', $muc->group);

        $this->actingAs($user)->post("/home-feature/{$muc->id}/update", [
            'group' => 'stat',
            'title' => 'Muc thu nghiem da sua',
            'description' => '',
            'value' => '100+',
            'icon' => '',
            'order' => 98,
            'publish' => 1,
        ])->assertRedirect(route('home.feature.index'));

        $muc->refresh();
        $this->assertSame('Muc thu nghiem da sua', $muc->title);
        $this->assertSame('100+', $muc->value);
        $this->assertSame(1, (int) $muc->publish);

        $this->actingAs($user)->delete("/home-feature/{$muc->id}/destroy")
            ->assertRedirect(route('home.feature.index'));

        $this->assertNull(HomeFeature::find($muc->id), 'Khong xoa duoc muc');
    }

    public function test_tieu_de_giu_nguyen_ky_tu_xuong_dong(): void
    {
        $user = $this->quanTri();

        // Tieu de kieu "Cong nghe\nHan Quoc" phai giu nguyen cho ngat dong,
        // vi trang chu dua vao do de tach chu thanh hai hang.
        $this->actingAs($user)->post('/home-feature/store', [
            'group' => 'hero_usp',
            'title' => "Dong mot\nDong hai",
            'order' => 97,
            'publish' => 2,
        ])->assertRedirect(route('home.feature.index'));

        $muc = HomeFeature::where('order', 97)->where('group', 'hero_usp')->first();
        $this->assertNotNull($muc);
        $this->assertStringContainsString("\n", $muc->title, 'Ky tu xuong dong bi mat khi luu');

        $muc->delete();
    }

    public function test_diem_ban_luu_duoc_toa_do_va_chan_toa_do_sai(): void
    {
        $user = $this->quanTri();

        $this->actingAs($user)->post('/dealer/store', [
            'name' => 'Diem ban thu nghiem tu dong',
            'address' => '12 Duong Test',
            'phone' => '0900000000',
            'province_code' => '01',
            'latitude' => '21.0278',
            'longitude' => '105.8342',
            'type' => 'dealer',
            'order' => 0,
            'publish' => 2,
        ])->assertRedirect(route('dealer.index'));

        $dealer = Dealer::where('name', 'Diem ban thu nghiem tu dong')->first();
        $this->assertNotNull($dealer, 'Khong luu duoc diem ban');
        $this->assertSame('21.0278000', (string) $dealer->latitude);
        $this->assertSame('01', $dealer->province_code);

        // Vi do 200 nam ngoai khoang -90..90 -> phai bi chan lai.
        $this->actingAs($user)->post('/dealer/store', [
            'name' => 'Diem ban toa do sai',
            'latitude' => '200',
            'longitude' => '105',
            'type' => 'dealer',
        ])->assertSessionHasErrors('latitude');

        $this->assertNull(Dealer::where('name', 'Diem ban toa do sai')->first());

        $dealer->forceDelete();
    }

    public function test_o_toa_do_de_trong_thi_luu_null_chu_khong_phai_0(): void
    {
        $user = $this->quanTri();

        $this->actingAs($user)->post('/dealer/store', [
            'name' => 'Diem ban khong toa do',
            'latitude' => '',
            'longitude' => '',
            'type' => 'dealer',
        ])->assertRedirect(route('dealer.index'));

        $dealer = Dealer::where('name', 'Diem ban khong toa do')->firstOrFail();

        // Luu 0 thi ghim se roi xuong vinh Guinea thay vi bi bo qua.
        $this->assertNull($dealer->latitude);
        $this->assertNull($dealer->longitude);

        $dealer->forceDelete();
    }
}
