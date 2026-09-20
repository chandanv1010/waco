<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Nhan yeu cau tu van / bao gia gui tu popup o trang san pham.
 *
 * Popup gui bang fetch nen tra ve JSON: nguoi dung thay ket qua ngay trong popup,
 * khong phai tai lai ca trang va mat vi tri dang xem.
 */
class ConsultController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:190',
            'phone' => 'required|string|max:50',
            'message' => 'nullable|string|max:2000',
            'product' => 'nullable|string|max:190',
            'type' => 'nullable|string|max:30',
        ], [
            'name.required' => 'Bạn chưa nhập họ và tên.',
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
            'phone.max' => 'Số điện thoại quá dài.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $data = $validator->validated();

        // Ghi ro san pham va loai yeu cau vao loi nhan de nguoi truc don biet
        // khach dang hoi ve may nao, xin tu van hay xin bao gia.
        $loaiYeuCau = ($data['type'] ?? '') === 'quote' ? 'Yêu cầu báo giá' : 'Yêu cầu tư vấn';
        $loiNhan = $loaiYeuCau;

        if (!empty($data['product'])) {
            $loiNhan .= ' - sản phẩm: ' . $data['product'];
        }

        if (!empty($data['message'])) {
            $loiNhan .= "\n" . $data['message'];
        }

        try {
            DB::beginTransaction();
            Contact::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                // Cot email khong cho NULL, popup khong hoi email nen luu chuoi rong.
                'email' => '',
                'message' => $loiNhan,
                'publish' => 1,
                'type' => 2,
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'ok' => false,
                'errors' => ['Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại hoặc gọi hotline.'],
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Đã gửi yêu cầu thành công. WACO sẽ liên hệ lại với bạn trong thời gian sớm nhất.',
        ]);
    }
}
