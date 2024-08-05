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

class ExportSalesORder implements FromCollection, WithHeadings, WithStyles, WithEvents
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
        'id_order_confirmations' => $item->id_order_confirmations,
        'so_number' => $item->so_number,
        'date' => $item->date,
        'so_type' => $item->so_type,
        'customer' => $item->customer,
        'salesman' => $item->salesman,
        'reference_number' => $item->reference_number,
        'product_code' => $item->product_code,
        'description' => $item->description,
        'perforasi' => $item->perforasi,
        'progress' => "Due Date: {$item->due_date}\nQty: {$item->qty}",
        'status' => $item->status,
      ];
    });
  }

  public function headings(): array
  {
    return [
      'Order Confirmations',
      'SO Number',
      'Date',
      'SO Type',
      'Customer',
      'Salesman',
      'Reference Number',
      'Product Code',
      'Product Description',
      'Perforasi',
      'Progress',
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
        $cellRange = 'A1:L' . ($this->data->count() + 1); // Adjust the cell range as needed
        $styleArray = [
          'borders' => [
            'allBorders' => [
              'borderStyle' => Border::BORDER_THIN,
            ],
          ],
        ];

        $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

        // Auto size columns
        foreach (range('A', 'L') as $columnID) {
          $event->sheet->getDelegate()->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Wrap text for 'progress' column
        $event->sheet->getDelegate()->getStyle('K')->getAlignment()->setWrapText(true);
      },
    ];
  }
}
