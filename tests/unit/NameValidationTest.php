<?php

use PHPUnit\Framework\TestCase;

final class NameValidationTest extends TestCase
{
    private function getValidation(): \CodeIgniter\Validation\Validation
    {
        // CI4 Services may not be initialized in plain PHPUnit run; load framework bootstrap if needed.
        // But in CodeIgniter's phpunit.xml, the bootstrap is configured, so Services should work.
        $validation = \Config\Services::validation();
        $rules = [
            'first_name' => [
                'rules' => "required|min_length[1]|max_length[100]|regex_match[/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*$/u]"
            ],
            'middle_name' => [
                'rules' => "permit_empty|max_length[100]|regex_match[/^([\\p{L}\\p{M}]\\.?|[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*)$/u]"
            ],
            'last_name' => [
                'rules' => "required|min_length[1]|max_length[100]|regex_match[/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s\\p{Pd}’'\\-]*$/u]"
            ],
            'suffix' => [
                'rules' => "permit_empty|max_length[20]|regex_match[/^[A-Za-z0-9\.\\sIVXLCDMivxlcdm]{1,20}$/]"
            ],
        ];
        $validation->setRules($rules);
        return $validation;
    }

    public function testHappyPathWithHyphenAndCurlyApostrophe(): void
    {
        $v = $this->getValidation();
        $data = [
            'first_name' => 'Anne-Marie',
            'middle_name' => 'J.',
            'last_name' => "O’Connor",
            'suffix' => 'III',
        ];
        $this->assertTrue($v->run($data));
    }

    public function testMiddleInitialWithoutPeriod(): void
    {
        $v = $this->getValidation();
        $data = [
            'first_name' => 'John',
            'middle_name' => 'J',
            'last_name' => 'Smith',
            'suffix' => '',
        ];
        $this->assertTrue($v->run($data));
    }

    public function testSuffixWithOrdinal(): void
    {
        $v = $this->getValidation();
        $data = [
            'first_name' => 'Maria',
            'middle_name' => '',
            'last_name' => 'Lopez',
            'suffix' => '2nd',
        ];
        $this->assertTrue($v->run($data));
    }

    public function testRejectInvalidCharactersInFirstName(): void
    {
        $v = $this->getValidation();
        $data = [
            'first_name' => 'John!',
            'middle_name' => '',
            'last_name' => 'Doe',
            'suffix' => '',
        ];
        $this->assertFalse($v->run($data));
    }

    public function testRejectInvalidMiddleWithDoublePeriod(): void
    {
        $v = $this->getValidation();
        $data = [
            'first_name' => 'John',
            'middle_name' => 'J..',
            'last_name' => 'Doe',
            'suffix' => '',
        ];
        $this->assertFalse($v->run($data));
    }

    public function testRejectEmojiInSuffix(): void
    {
        $v = $this->getValidation();
        $data = [
            'first_name' => 'Jane',
            'middle_name' => '',
            'last_name' => 'Roe',
            'suffix' => 'III 🔥',
        ];
        $this->assertFalse($v->run($data));
    }
}
