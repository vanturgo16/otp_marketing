<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportOrderConfirmation implements FromCollection, WithHeadings, WithStyles, WithEvents
{
  protected $data;

  public function __construct(Collection $data)
  {
    $this->data = $data;
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return $this->data->map(function ($item) {
      return [
        'oc_number' => $item->oc_number,
        'date' => $item->date,
        'customer' => $item->customer,
        'salesman' => $item->salesman,
        'total_price' => $item->total_price,
        'ppn' => $item->ppn,
        'status' => $item->status,
      ];
    });
  }

  public function headings(): array
  {
    return [
      'OC Number',
      'Date',
      'Customer',
      'Salesman',
      'Total Price',
      'PPN',
      'Status',
    ];
  }

  public function styles(Worksheet $sheet)
  {
    return [
      // Style the first row as bold text.
      1 => ['font' => ['bold' => true]],
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $cellRange = 'A1:G' . ($this->data->count() + 1); // Adjust the cell range as needed
        $styleArray = [
          'borders' => [
            'allBorders' => [
              'borderStyle' => Border::BORDER_THIN,
            ],
          ],
        ];

        $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

        // Auto size columns
        foreach (range('A', 'G') as $columnID) {
          $event->sheet->getDelegate()->getColumnDimension($columnID)->setAutoSize(true);
        }
      },
    ];
  }
}
