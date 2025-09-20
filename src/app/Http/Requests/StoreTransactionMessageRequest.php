<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $transaction = $this->route('transaction');
        $user = $this->user();

        if (!$user) {
            return false;
        }
        return $user->can('message', $transaction);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['required', 'string', 'max:400'],
            'image' => ['nullable', 'mimes:jpeg,png', 'max:4096'], 
        ];
    }

    public function attributes()
    {
        return [
            'body' => '本文',
            'image' => '画像',
        ];
    }

    public function messages()
    {
        return [
            'body.required' => '本文を入力してください',
            'body.max'      => '本文は400文字以内で入力してください',
            'image.mimes'   => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max'     => '画像サイズは4MB以内でアップロードしてください',
        ];
    }
}
