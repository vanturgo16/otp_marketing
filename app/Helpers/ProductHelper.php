<?php

use Illuminate\Support\Facades\DB;
// app/Helpers/ProductHelper.php

if (!function_exists('getProductName')) {
    function getProductName($typeProduct, $idMasterProduct)
    {
        // Gantilah dengan logika pengambilan data produk dari database atau sumber data lainnya
        $combinedDataProducts = DB::table('master_product_fgs')
            ->select('id', 'product_code', 'description', 'id_master_units', 'perforasi', DB::raw("'FG' as type_product"), 'group_sub_code')
            ->where('status', 'Active')
            ->unionAll(
                DB::table('master_wips')
                    ->select('id', 'wip_code as product_code', 'description', 'id_master_units', 'perforasi', DB::raw("'WIP' as type_product"), DB::raw("'' as group_sub_code"))
                    ->where('status', 'Active')
            )
            ->unionAll(
                DB::table('master_raw_materials')
                    ->select('id', 'rm_code as product_code', 'description', 'id_master_units', DB::raw("'' as perforasi"), DB::raw("'RM' as type_product"), DB::raw("'' as group_sub_code"))
                    ->where('status', 'Active')
            )
            ->unionAll(
                DB::table('master_tool_auxiliaries')
                    ->select('id', 'code as product_code', 'description', 'id_master_units', DB::raw("'' as perforasi"), DB::raw("'AUX' as type_product"), DB::raw("'' as group_sub_code"))
            )
            ->get();

        $filteredProduct = collect($combinedDataProducts)->first(function ($item) use ($typeProduct, $idMasterProduct) {
            return $item->type_product == $typeProduct && $item->id == $idMasterProduct;
        });

        $perforasi = $filteredProduct->perforasi === '' ? '' : ($filteredProduct->perforasi === null ? ' | Perforasi : -' : ' | Perforasi : ' . $filteredProduct->perforasi);
        $group_sub_code = $filteredProduct->group_sub_code === '' ? '' : ($filteredProduct->group_sub_code === null ? ' | Group Sub : -' : ' | Group Sub : ' . $filteredProduct->group_sub_code);

        return $filteredProduct ? $filteredProduct->description . $perforasi . $group_sub_code  : 'Product Not Found';
    }
}
