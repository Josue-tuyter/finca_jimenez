<?php

namespace App\Exports;

use App\Models\Worker;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Auth;



class WorkersExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return Worker::select('name', 'cedula', 'email', 'phone')->get();
    }

    public function headings(): array
    {
        return ["Nombre", "Cédula", "Correo", "Teléfono"];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Title
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'Finca Jiménez - Reporte de Trabajadores');
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // User who generated report
                $user = Auth::user()->name ?? 'Desconocido';
                $sheet->setCellValue('A2', "Generado por: " . $user);
                $sheet->getStyle('A2')->getFont()->setItalic(true);

                // Style headers
                $sheet->getStyle('A3:D3')->getFont()->setBold(true);
                $sheet->getStyle('A3:D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3:D3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
