<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'identification' => $row[0],
            'name' => $row[1],
            'email' => $row[2],
            'password' => Hash::make($row[1])
        ]);
    }

    function rules(): array
    {
        return [
            '0' => 'required|digits:8|unique:users,identification',
            '1' => "required|string|max:100",
            '2' => "nullable|string|lowercase|email|max:100|unique:users,email"
        ];
    }
}
