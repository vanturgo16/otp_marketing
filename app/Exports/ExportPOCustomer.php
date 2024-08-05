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

class ExportPOCustomer implements FromCollection, WithHeadings, WithStyles, WithEvents
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
    // return $this->data;
    // Extract only the desired fields
    return $this->data->map(function ($item) {
      return [
        'id_order_confirmations' => $item->id_order_confirmations,
        'so_number' => $item->so_number,
        'date' => $item->date,
        'so_type' => $item->so_type,
        'so_category' => $item->so_category,
        'customer' => $item->customer,
        'salesman' => $item->salesman,
        'reference_number' => $item->reference_number,
        'product_code' => $item->product_code,
        'description' => $item->description,
        'perforasi' => $item->perforasi,
        'price' => $item->price,
        'qty' => $item->qty,
        'total_price' => $item->total_price,
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
      'SO Category',
      'Customer',
      'Salesman',
      'Reference Number',
      'Product Code',
      'Product Description',
      'Perforasi',
      'Price',
      'Qty',
      'Total Price',
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
        $cellRange = 'A1:O' . ($this->data->count() + 1); // Adjust the cell range as needed
        $styleArray = [
          'borders' => [
            'allBorders' => [
              'borderStyle' => Border::BORDER_THIN,
            ],
          ],
        ];

        $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

        // Auto size columns
        foreach (range('A', 'O') as $columnID) {
          $event->sheet->getDelegate()->getColumnDimension($columnID)->setAutoSize(true);
        }
      },
    ];
  }
}
