<?php

namespace App\Http\Requests\HomeFeature;

use App\Models\HomeFeature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHomeFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group' => ['required', Rule::in(array_keys(HomeFeature::NHOM))],
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:191',
            'value' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:191',
            'order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'group.required' => 'Bạn chưa chọn khối hiển thị.',
            'group.in' => 'Khối hiển thị không hợp lệ.',
            'title.required' => 'Bạn chưa nhập tiêu đề.',
            'title.max' => 'Tiêu đề tối đa 191 ký tự.',
            'description.max' => 'Mô tả tối đa 191 ký tự.',
            'value.max' => 'Con số tối đa 50 ký tự.',
            'icon.max' => 'Đường dẫn icon tối đa 191 ký tự.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
        ];
    }
}
