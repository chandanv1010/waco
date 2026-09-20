<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Nhan don dang ky lam dai ly tu form "Tro thanh dai ly phan phoi WACO".
 *
 * Day la luong kinh doanh chinh cua website: toan bo nut keu goi tren trang deu
 * dan ve day, nen don phai luu duoc ke ca khi co truc trac o cac buoc phu.
 */
class DealerRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // Dung tui loi rieng ten 'dealer'. Trang lien he co hai form tren cung
        // mot trang; neu dung tui mac dinh thi loi cua form nay se hien ca o
        // form kia.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'company' => 'nullable|string|max:255',
            'business_area' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Bạn chưa nhập họ và tên.',
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
            'phone.max' => 'Số điện thoại quá dài.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator, 'dealer')
                ->withFragment('dang-ky-dai-ly');
        }

        $validated = $validator->validated();

        try {
            DB::table('dealer_registrations')->insert([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'company' => $validated['company'] ?? null,
                'business_area' => $validated['business_area'] ?? null,
                'status' => 'new',
                // Ghi lai trang gui don de biet nut keu goi o dau dang hieu qua.
                'source' => substr((string) $request->input('source', 'homepage'), 0, 190),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại hoặc gọi hotline.'], 'dealer')
                ->withFragment('dang-ky-dai-ly');
        }

        // Quay lai dung khoi form thay vi dau trang, de nguoi gui nhin thay ngay
        // thong bao ket qua.
        return redirect()->back()
            ->with('dealer_success', 'Đã gửi yêu cầu thành công. WACO sẽ liên hệ lại với bạn trong thời gian sớm nhất.')
            ->withFragment('dang-ky-dai-ly');
    }
}
