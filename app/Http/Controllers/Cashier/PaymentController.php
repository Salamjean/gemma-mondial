<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $admissionController;

    public function __construct()
    {
        $this->admissionController = new AdmissionController();
    }

    public function today()
    {
        return $this->admissionController->today();
    }

    public function valid()
    {
        return $this->admissionController->valid();
    }

    public function history()
    {
        return $this->admissionController->history();
    }
}
