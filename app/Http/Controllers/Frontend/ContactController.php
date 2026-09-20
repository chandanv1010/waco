<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Trang "Lien he" va xu ly form lien he.
 *
 * Thong tin hien tren trang (dia chi, hotline, email, ban do) deu lay tu bang
 * systems de quan tri sua duoc, khong ghi cung trong giao dien.
 */
class ContactController extends FrontendController
{
    public function index()
    {
        $system = $this->system;

        $seo = [
            'meta_title' => 'Liên hệ - ' . ($system['homepage_brand'] ?? 'WACO Việt Nam'),
            'meta_description' => 'Liên hệ ' . ($system['homepage_company'] ?? ''),
            'meta_keyword' => '',
            'meta_image' => $system['seo_meta_images'] ?? '',
            'canonical' => write_url('lien-he'),
        ];

        return view('frontend.contact.index', [
            'config' => ['js' => [], 'css' => []],
            'system' => $system,
            'seo' => $seo,
        ]);
    }

    /**
     * Nhan form lien he.
     *
     * Dung tui loi rieng ten 'contact' vi trang nay con mot form dang ky dai ly
     * nua; chung tui thi loi cua form nay se hien ca o form kia.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:190',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:190',
            'address' => 'nullable|string|max:190',
            'message' => 'nullable|string|max:5000',
        ], [
            'name.required' => 'Bạn chưa nhập họ và tên.',
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
            'email.email' => 'Địa chỉ email chưa đúng định dạng.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator, 'contact')
                ->withFragment('lien-he');
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            Contact::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                // Cot email khong cho NULL nen luu chuoi rong khi khach bo trong.
                'email' => $data['email'] ?? '',
                'address' => $data['address'] ?? null,
                'message' => $data['message'] ?? null,
                'publish' => 1,
                'type' => 1,
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'Có lỗi xảy ra khi gửi thông tin. Vui lòng thử lại hoặc gọi hotline.'], 'contact')
                ->withFragment('lien-he');
        }

        return redirect()->back()
            ->with('contact_success', 'Đã gửi thông tin thành công. WACO sẽ liên hệ lại với bạn trong thời gian sớm nhất.')
            ->withFragment('lien-he');
    }
}
