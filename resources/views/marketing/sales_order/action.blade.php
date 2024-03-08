<div class="btn-group" role="group">
    <button id="btnGroupDrop" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown"
        aria-expanded="false">
        Action <i class="mdi mdi-chevron-down"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop">
        <li>
            <button class="dropdown-item drpdwn-{{ $data->status == 'Request' ? 'scs' : 'wrn' }}"
                data-so-number="{{ $data->so_number }}" data-status="{{ $data->status }}"
                onclick="showModal(this);"><span
                    class="mdi {{ $data->status == 'Request' ? 'mdi-check-bold' : 'mdi-arrow-left-top-bold' }}"></span>
                |
                {{ $data->status == 'Request' ? 'Posted' : 'Un Posted' }}</button>
        </li>
        <li>
            {{-- <button class="dropdown-item drpdwn-scn" onclick="modalPDF('{{ encrypt($data->so_number) }}')"><span
                    class="mdi mdi-printer"></span> | Preview or Print</button> --}}
            <a class="dropdown-item drpdwn-scn"
                href="{{ route('marketing.salesOrder.print', encrypt($data->so_number)) }}" target="_blank" rel="noopener noreferrer"><span
                    class="mdi mdi-printer"></span> | Print</a>
        </li>
        @if ($data->status == 'Request')
            <li>
                <button class="dropdown-item drpdwn-dgr" data-so-number="{{ $data->so_number }}"
                    data-status="{{ $data->status }}" onclick="showModal(this, 'Delete');"><span
                        class="mdi mdi-trash-can"></span>
                    | Delete</button>
            </li>
            <li>
                <a class="dropdown-item drpdwn-pri"
                    href="{{ route('marketing.salesOrder.edit', encrypt($data->so_number)) }}"><span
                        class="mdi mdi-circle-edit-outline"></span> | Edit
                    Data</a>
            </li>
        @endif
        <li>
            <a class="dropdown-item drpdwn"
                href="{{ route('marketing.salesOrder.view', encrypt($data->so_number)) }}"><span
                    class="mdi mdi-eye"></span> | View Data</a>
        </li>
    </ul>
</div>
