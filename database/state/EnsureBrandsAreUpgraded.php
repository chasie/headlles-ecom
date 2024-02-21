<?php

namespace HeadlessEcom\Database\State;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use HeadlessEcom\Models\Brand;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Product;

class EnsureBrandsAreUpgraded
{
    public function prepare()
    {
        if (! $this->canPrepare()) {
            return;
        }

        $legacyBrands = Product::query()->pluck('brand', 'id')->filter();

        if ($legacyBrands->isEmpty()) {
            return;
        }

        $brands = [];

        foreach ($legacyBrands as $productId => $brand) {
            if (empty($brands[$brand])) {
                $brands[$brand] = [];
            }

            $brands[$brand][] = $productId;
        }

        Storage::disk('local')->put('tmp/state/legacy_brands.json', json_encode($brands));
    }

    public function run()
    {
        if (! $this->canRun() || ! $this->shouldRun()) {
            return;
        }

        $brands = null;

        try {
            $brands = Storage::disk('local')->get('tmp/state/legacy_brands.json');
        } catch (FileNotFoundException $e) {
        }

        if ($brands) {
            $brands = json_decode($brands);

            foreach ($brands as $brandName => $productIds) {
                $brand = Brand::firstOrCreate([
                    'name' => $brandName,
                ]);

                Product::whereIn('id', $productIds)->update([
                    'brand_id' => $brand->id,
                ]);
            }
        }

        Storage::disk('local')->delete('tmp/state/legacy_brands.json');
    }

    protected function canPrepare()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        $hasBrandsTable = Schema::hasTable("{$prefix}brands");
        $hasProductsTable = Schema::hasTable("{$prefix}products");

        return ! $hasBrandsTable && $hasProductsTable && Language::count();
    }

    protected function canRun()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        $hasBrandsTable = Schema::hasTable("{$prefix}brands");
        $hasProductsTable = Schema::hasTable("{$prefix}products");

        return $hasBrandsTable && $hasProductsTable && Language::count();
    }

    protected function shouldRun()
    {
        return ! Product::has('brand')->count();
    }
}
