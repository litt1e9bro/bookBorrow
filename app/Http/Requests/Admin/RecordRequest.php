<?php

namespace App\Http\Requests\Admin;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RecordRequest extends FormRequest
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
            //
            'user_id' => [
                'required',
                function($attribute, $value, $fail){
                    if(!User::find($value)){
                        $fail('用户不存在');
                        return;
                    }
                }
            ],
            'book_id' => [
                'required',
                function($attribute, $value, $fail){
                    if(!$book = Book::find($value)){
                        $fail('该书不存在');
                        return;
                    }
                    if($book->status){
                        $fail('该书已被借出');
                        return;
                    }
                }
            ],
            'borrow_date' => 'required|date',
            'return_deadline' => 'required|date',
        ];
    }
}
