<?php

namespace App\Observers;

use App\Company;
use Illuminate\Validation\ValidationException;

class CompanyObserver
{
    public function creating(Company $company){
        if ($company->doc == '123456'){
            throw new \Exception('Invalid doc id');
        }
    }
}
