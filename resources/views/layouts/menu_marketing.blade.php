@can('Marketing')
<li class="{{ request()->is('marketing/*') ? 'mm-active' : '' }}">
    <a href="javascript: void(0);" class="has-arrow">
        <i data-feather="shopping-cart"></i>
        <span>Marketing</span>
    </a>
    <ul class="sub-menu" aria-expanded="false">
        @can('Marketing_inputPOCust')
        <li><a href="{{ route('marketing.inputPOCust.index') }}" class="{{ request()->is('marketing/inputPOCust/*') ? 'active' : '' }}"><i data-feather="edit"></i>PO Customer</a></li>
        @endcan

        @can('Marketing_orderConfirmation')
        <li><a href="{{ route('marketing.orderConfirmation.index') }}" class="{{ request()->is('marketing/orderConfirmation/*') ? 'active' : '' }}"><i data-feather="clipboard"></i>Order Confirmation</a></li>
        @endcan

        @can('Marketing_salesOrder')
        <li><a href="{{ route('marketing.salesOrder.index') }}" class="{{ request()->is('marketing/salesOrder/*') ? 'active' : '' }}"><i data-feather="file-text"></i>Sales Order</a></li>
        @endcan
   
    </ul>
</li>
@endcan
