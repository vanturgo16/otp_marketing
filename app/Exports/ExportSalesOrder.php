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
        return $this->data->map(function ($item, $index) {
            return [
                'no' => $index + 1, // Menambahkan nomor urut mulai dari 1
                'id_order_confirmations' => $item->id_order_confirmations,
                'so_number' => $item->so_number,
                'date' => $item->date,
                'so_type' => $item->so_type,
                'so_category' => $item->so_category,
                'customer' => $item->customer,
                // 'address' => $item->address,
                'salesman' => $item->salesman,
                'reference_number' => $item->reference_number,
                'due_date' => $item->due_date,
                // 'color' => $item->color,
                // 'non_invoiceable' => $item->non_invoiceable,
                // 'remarks' => $item->remarks,
                // 'type_product' => $item->type_product,
                'product_code' => $item->product_code,
                'description' => $item->description,
                'perforasi' => $item->perforasi,
                'price' => $item->price,
                'qty' => $item->qty,
                'unit_code' => $item->unit_code,
                'kg' => ($item->weight != 0 && $item->weight != '')
                    ? number_format((float)$item->qty * (float)$item->weight, 2, ',', '.')
                    : '0,00',
                'total_price' => $item->total_price,
                // Bulatkan hasil pembagian ke integer, jika weight 0 maka set jadi 0
                // 'based_price' => ($item->weight != 0 && $item->weight != '') ? round((float)$item->price / (float)$item->weight) : 0,
                // 'ppn' => $item->ppn,
                // 'term_payment' => $item->term_payment,
                'status' => $item->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Order Confirmations',
            'SO Number',
            'Date',
            'SO Type',
            'SO Category',
            'Customer',
            // 'Customer Address',
            'Salesman',
            'Reference Number (PO)',
            'Due Date',
            // 'Color',
            // 'Non Invoiceable',
            // 'Remarks',
            // 'Type Product',
            'Product Code',
            'Product Description',
            'Perforasi',
            'Price',
            'Qty',
            'Unit',
            'Jumlah KG',
            'Total Price',
            // 'Based Price',
            // 'Ppn',
            // 'Term Payment',
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
                $cellRange = 'A1:S' . ($this->data->count() + 1); // Adjust the cell range as needed
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ];

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

                // Auto size columns
                foreach (range('A', 'S') as $columnID) {
                    $event->sheet->getDelegate()->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Wrap text for 'progress' column
                $event->sheet->getDelegate()->getStyle('K')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
