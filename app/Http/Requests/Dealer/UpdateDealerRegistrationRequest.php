<?php

namespace App\Http\Requests\Dealer;

use App\Models\DealerRegistration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDealerRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(DealerRegistration::TRANG_THAI))],
            'note' => 'nullable|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Bạn chưa chọn trạng thái xử lý.',
            'status.in' => 'Trạng thái xử lý không hợp lệ.',
            'note.max' => 'Ghi chú tối đa 5000 ký tự.',
        ];
    }
}
