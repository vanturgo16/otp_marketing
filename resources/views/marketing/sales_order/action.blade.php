<div class="btn-group" role="group">
    <button id="btnGroupDrop" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown"
        aria-expanded="false">
        Action <i class="mdi mdi-chevron-down"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop">
        @if ($data->status == 'Request' || $data->status == 'Un Posted')
            <li>
                <button class="dropdown-item drpdwn-scs" data-so-number="{{ $data->so_number }}"
                    data-status="{{ $data->status }}" onclick="showModal(this);"><span
                        class="mdi mdi-check-bold"></span>
                    | Posted</button>
            </li>
        @elseif ($data->status == 'Posted')
            @can('PPIC_unposted')
                <li>
                    <button class="dropdown-item drpdwn-wrn" data-so-number="{{ $data->so_number }}"
                        data-status="{{ $data->status }}" onclick="showModal(this);"><span
                            class="mdi mdi-arrow-left-top-bold"></span>
                        | Un Posted</button>
                </li>
            @endcan
        @endif
        {{-- @can('PPIC_unposted')
            @if ($data->status != 'Closed')
                <li>
                    <button
                        class="dropdown-item drpdwn-{{ $data->status == 'Request' || $data->status == 'Un Posted' ? 'scs' : 'wrn' }}"
                        data-so-number="{{ $data->so_number }}" data-status="{{ $data->status }}"
                        onclick="showModal(this);"><span
                            class="mdi {{ $data->status == 'Request' || $data->status == 'Un Posted' ? 'mdi-check-bold' : 'mdi-arrow-left-top-bold' }}"></span>
                        |
                        {{ $data->status == 'Request' || $data->status == 'Un Posted' ? 'Posted' : 'Un Posted' }}</button>
                </li>
            @endif
        @endcan --}}
        <li>
            {{-- <button class="dropdown-item drpdwn-scn" onclick="modalPDF('{{ encrypt($data->so_number) }}')"><span
                    class="mdi mdi-printer"></span> | Preview or Print</button> --}}
            <a class="dropdown-item drpdwn-scn"
                href="{{ route('marketing.salesOrder.print', encrypt($data->so_number)) }}" target="_blank"
                rel="noopener noreferrer"><span class="mdi mdi-printer"></span> | Print</a>
        </li>
        @if ($data->status == 'Request')
            <li>
                <button class="dropdown-item drpdwn-dgr" data-so-number="{{ $data->so_number }}"
                    data-status="{{ $data->status }}" onclick="showModal(this, 'Delete');"><span
                        class="mdi mdi-trash-can"></span>
                    | Delete</button>
            </li>
        @endif
        @if ($data->status == 'Request' || $data->status == 'Un Posted')

            <li>
                <a class="dropdown-item drpdwn-pri"
                    href="{{ route('marketing.salesOrder.edit', encrypt($data->so_number)) }}"><span
                        class="mdi mdi-circle-edit-outline"></span> | Edit
                    Data</a>
            </li>
            
        @elseif ($data->status == 'Posted' && $data->id_order_confirmations == null)
  @can('PPIC_unposted')
            <li>
                <a class="dropdown-item drpdwn-pri"
                    href="{{ route('marketing.salesOrder.edit', encrypt($data->so_number)) }}"><span
                        class="mdi mdi-circle-edit-outline"></span> | Edit Harga
                    </a>
            </li>
    @endcan
        @endif
        @if ($data->status == 'Posted')
            <li>
                <button class="dropdown-item drpdwn-cnl"
                    onclick="modalCancelQty('{{ $data->so_number }}', '{{ $data->qty }}')"><span
                        class="mdi mdi-archive-cancel"></span> | Cancel
                    Qty</button>
            </li>
            @can('PPIC_unposted')
                <li>
                    <button class="dropdown-item drpdwn-closed" data-so-number="{{ $data->so_number }}"
                        data-action="closed" onclick="showModal(this, 'Closed');"><span
                            class="mdi mdi-lock"></span>
                        | Closed</button>
                </li>
            @endcan
        @endif
        
        <li>
            <a class="dropdown-item drpdwn"
                href="{{ route('marketing.salesOrder.view', encrypt($data->so_number)) }}"><span
                    class="mdi mdi-eye"></span> | View Data</a>
        </li>
    </ul>
</div>
