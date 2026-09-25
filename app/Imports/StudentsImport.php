<?php

// app/Imports/StudentsImport.php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $guruhId;
    protected $courseId;

    public function __construct($guruhId, $courseId)
    {
        $this->guruhId = $guruhId;
        $this->courseId = $courseId;
    }

    public function model(array $row)
    {
        return new Student([
            'user_id'   => Auth::id(),
            'course_id' => $this->courseId,
            'guruh_id'  => $this->guruhId,
            'name'      => $row['name'],
            'photo'     => null, // rasm yo'q — view'da avatar avtomatik chiqadi
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
