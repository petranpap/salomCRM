<?php

namespace App\Http\Controllers;

use App\Support\TempPdf;
use Illuminate\Http\Response;

class TempPdfController extends Controller
{
    public function show(string $token): Response
    {
        $contents = TempPdf::retrieve($token);

        abort_if($contents === null, 404, 'This preview link has expired — please generate it again.');

        return response($contents, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }
}
