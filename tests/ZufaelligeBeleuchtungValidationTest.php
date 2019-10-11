<?php

declare(strict_types=1);
include_once __DIR__ . '/stubs/Validator.php';
class ZufaelligeBeleuchtungValidationTest extends TestCaseSymconValidation
{
    public function testValidateZufaelligeBeleuchtung(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }
    public function testValidateZufaelligeBeleuchtungModule(): void
    {
        $this->validateModule(__DIR__ . '/../ZufaelligeBeleuchtung');
    }
}