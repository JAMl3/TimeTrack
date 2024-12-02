<?php

namespace App\Services\Reports\Generators;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfGenerator
{
    public function generate(string $view, array $data, string $filename): Response
    {
        $pdf = Pdf::loadView($view, ['data' => $data]);
        return $pdf->download($filename . '.pdf');
    }
}
