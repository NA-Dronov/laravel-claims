<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimCreateRequest extends FormRequest
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
            'subject' => ['required', 'min:5', 'max:256'],
            'body' => ['required', 'min:5', 'max:10000'],
            'attachments.*' => ['file', 'max:3000'],
        ];
    }

    /**
     * Custom messages for request validation errors
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'subject.required' => 'Поле "Загловок" обязательно для заполнения',
            'subject.min' => 'Минимальная длина заголовка :min символов',
            'subject.max' => 'Максимальная длина заголовка :max символов',
            'body.required' => 'Поле "Текст заявления" обязательно для заполнения',
            'body.min' => 'Минимальная длина заявления :min символов',
            'body.max' => 'Максимальная длина заявления :max символов',
            'attachments.*.max' => 'Максимальный размер файла не должен превышать :max килобайт',
        ];
    }
}
