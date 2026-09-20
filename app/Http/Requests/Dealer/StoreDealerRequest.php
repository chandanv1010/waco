<?php

namespace App\Http\Requests\Dealer;

use App\Models\Dealer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDealerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'address' => 'nullable|string|max:191',
            'phone' => 'nullable|string|max:50',
            'province_code' => 'nullable|string|max:20',
            'district_code' => 'nullable|string|max:20',
            'ward_code' => 'nullable|string|max:20',
            // Gioi han theo bien do va kinh do that, de nhap nham thu tu hai o
            // la bao loi ngay thay vi de ghim bay sang chau Phi.
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'type' => ['required', Rule::in(array_keys(Dealer::LOAI))],
            'order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập tên điểm bán.',
            'name.max' => 'Tên điểm bán tối đa 191 ký tự.',
            'address.max' => 'Địa chỉ tối đa 191 ký tự.',
            'phone.max' => 'Số điện thoại tối đa 50 ký tự.',
            'latitude.numeric' => 'Vĩ độ phải là số.',
            'latitude.between' => 'Vĩ độ phải nằm trong khoảng -90 đến 90.',
            'longitude.numeric' => 'Kinh độ phải là số.',
            'longitude.between' => 'Kinh độ phải nằm trong khoảng -180 đến 180.',
            'type.required' => 'Bạn chưa chọn loại điểm bán.',
            'type.in' => 'Loại điểm bán không hợp lệ.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
        ];
    }
}
