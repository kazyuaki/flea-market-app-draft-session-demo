<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'post_code' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください。',
            'name.string' => 'お名前を文字列で入力してください。',
            'name.max' => 'お名前を255文字以内で入力してください。',
            'post_code.required' => '郵便番号を入力してください。',
            'post_code.regex' => '郵便番号は「123-4567」の形式で入力してください。',
            'address.required' => '住所を入力してください。',
            'address.string' => '住所を文字列で入力してください。',
            'address.max' => '住所を255文字以内で入力してください。',
            'building_name.required' => '建物名を入力してください。',
            'building_name.string' => '建物名は文字列で入力してください。',
            'building_name.max' => '建物名を255文字以内で入力してください。',
            'profile_image.image' => '画像ファイルを選択してください。',
            'profile_image.mimes' => '画像形式はJPEGまたはPNGにしてください。',
        ];
    }
}
