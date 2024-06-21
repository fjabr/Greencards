<?php
namespace App\Utility;

use PDF;


class PdfUtils{

    public static function generate($view, $data) {
        $pdf = null;
        try{
            $pdf = PDF::loadView($view, [
                'data' => (object) $data,
                'font_family' => "'Roboto','sans-serif'",
            ], [], []);
        }catch (\Throwable $th) {
        }

        return !$pdf ? null : $pdf->output();
    }
}
