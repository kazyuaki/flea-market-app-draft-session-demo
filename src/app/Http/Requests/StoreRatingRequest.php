<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $transaction = $this->route('transaction');
        return $this->user() && $transaction &&
            ($transaction->seller_id === $this->user()->id || $transaction->buyer_id === $this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'score'   => ['required', 'integer', 'between:1,5'],
        ];
    }

    public function attributes()
    {
        return [
            'score' => '評価',
        ];
    }

    public function messages()
    {
        return [
            'score.required' => '評価を選択してください。',
            'score.integer'  => '評価は数値で指定してください。',
            'score.between'      => '評価は1以上5以下を選択してください。',

        ];
    }
}
