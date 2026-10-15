<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Excel template for the student import: one bold header row and one sample row.
 */
class UserImportTemplate
{
    // --- Columns with their sample value, in sheet order ---

    private const COLUMNS = [
        // --- Core Fields ---
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'role' => 'student',
        'is_active' => '1',

        // --- Student Info ---
        'nis' => '12345678',
        'nisn' => '1234567890',
        'prodi' => 'Computer Science',
        'sub_prodi' => 'Software Engineering',
        'student_year' => '2023',
        'major' => 'Computer Science',
        'is_graduate' => '0',
        'cgpa' => '3.85',
        'edu_level' => 'Bachelor',

        // --- Personal Data ---
        'gender' => 'Male',
        'birth_date' => '2000-01-01',
        'birth_city' => 'Jakarta',
        'religion' => 'Islam',
        'citizenship' => 'Indonesian',
        'citizenship_no' => '3201010101000001',

        // --- Contact Info - Primary ---
        'address' => 'Jl. Example No. 123',
        'address_city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'country' => 'Indonesia',
        'zip_code' => '12345',
        'phone_number' => '021-1234567',
        'mobile_number' => '0812-3456-7890',

        // --- Contact Info - Secondary ---
        'address2' => '',
        'address_city2' => '',
        'province2' => '',
        'country2' => '',
        'zip_code2' => '',
        'phone_number2' => '',
        'mobile_number2' => '',

        // --- Social Media ---
        'whatsapp' => '0812-3456-7890',
        'bbm' => '',
        'line' => '',
        'facebook' => '',
        'twitter' => '',
        'instagram' => '',

        // --- Academic History ---
        'academic_advisor' => 'Dr. Jane Smith',
        'previous_school_name' => 'SMA Example',
        'school_city' => 'Jakarta',
        'previous_edu_level' => 'High School',
        'start_year' => '2018',
        'end_year' => '2021',
        'score' => '85.5',

        // --- Certificates ---
        'certificate_no_1' => '',
        'certificate_date_1' => '',
        'certificate_no_2' => '',
        'certificate_date_2' => '',

        // --- Father Data - Basic ---
        'father_name' => 'John Doe Sr.',
        'father_birth_city' => 'Jakarta',
        'father_birthday' => '1970-01-01',
        'father_citizenship' => 'Indonesian',
        'father_citizenship_no' => '3201010170000001',
        'father_passport_no' => '',
        'father_npwp_no' => '',
        'father_religion' => 'Islam',
        'father_bpjs_no' => '',

        // --- Father Data - Contact ---
        'father_address' => 'Jl. Example No. 123',
        'father_address_city' => 'Jakarta',
        'father_phone' => '021-1111111',
        'father_mobile' => '0811-1111-1111',
        'father_email' => 'father@example.com',
        'father_bbm' => '',

        // --- Father Data - Education & Work ---
        'father_education' => 'Bachelor',
        'father_education_major' => 'Business',
        'father_profession' => 'Entrepreneur',
        'father_business_name' => 'ABC Company',
        'father_business_address' => 'Jl. Business St.',
        'father_business_phone' => '021-9999999',
        'father_business_line' => 'Trading',
        'father_business_title' => 'CEO',
        'father_business_revenue' => '> 1B',
        'father_special_need' => '',

        // --- Mother Data - Basic ---
        'mother_name' => 'Jane Doe',
        'mother_birth_city' => 'Jakarta',
        'mother_birthday' => '1972-01-01',
        'mother_citizenship' => 'Indonesian',
        'mother_citizenship_no' => '3201010172000002',
        'mother_passport_no' => '',
        'mother_npwp_no' => '',
        'mother_religion' => 'Islam',
        'mother_bpjs_no' => '',

        // --- Mother Data - Contact ---
        'mother_address' => 'Jl. Example No. 123',
        'mother_address_city' => 'Jakarta',
        'mother_phone' => '021-2222222',
        'mother_mobile' => '0822-2222-2222',
        'mother_email' => 'mother@example.com',
        'mother_bbm' => '',

        // --- Mother Data - Education & Work ---
        'mother_education' => 'Bachelor',
        'mother_education_major' => 'Education',
        'mother_profession' => 'Teacher',
        'mother_business_name' => 'XYZ School',
        'mother_business_address' => 'Jl. School St.',
        'mother_business_phone' => '021-8888888',
        'mother_business_line' => 'Education',
        'mother_business_title' => 'Principal',
        'mother_business_revenue' => '500M - 1B',
        'mother_special_need' => '',

        // --- Graduation Data ---
        'final_project_indonesia' => '',
        'final_project_english' => '',
        'cum_credits' => '',
        'predicate' => '',
        'judicium_date' => '',
        'document_no' => '',
        'document_date' => '',
        'graduate_period' => '',
        'class_semester' => 'Semester 1',
        'form_no' => '',
        'official_email' => 'john.doe@student.university.edu',
        'current_status' => 'Active Student',
        'start_date' => '2023-09-01',
        'end_date' => '2027-06-30',
        'business_name' => '',
        'business_line' => '',
        'business_title' => '',
    ];

    public function download(): BinaryFileResponse
    {
        $headers = array_keys(self::COLUMNS);
        $sampleData = array_values(self::COLUMNS);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'1', $header);
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
            $col++;
        }

        $col = 'A';
        foreach ($sampleData as $value) {
            $sheet->setCellValue($col.'2', $value);
            $col++;
        }

        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $fileName = 'users_import_template_'.date('Y-m-d').'.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
