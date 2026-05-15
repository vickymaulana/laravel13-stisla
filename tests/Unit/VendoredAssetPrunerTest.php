<?php

namespace Tests\Unit;

use App\Support\Assets\VendoredAssetPruner;
use PHPUnit\Framework\TestCase;

class VendoredAssetPrunerTest extends TestCase
{
    public function test_runtime_assets_are_kept_while_vendor_development_artifacts_are_pruned(): void
    {
        $pruner = new VendoredAssetPruner;

        $this->assertTrue($pruner->shouldPrune('bootstrap-social/node_modules/bootstrap/dist/css/bootstrap.css'));
        $this->assertTrue($pruner->shouldPrune('gmaps/examples/basic.html'));
        $this->assertTrue($pruner->shouldPrune('owl.carousel/docs_src/templates/pages/docs/api.hbs'));
        $this->assertTrue($pruner->shouldPrune('bootstrap-tagsinput/dist/bootstrap-tagsinput.zip'));
        $this->assertTrue($pruner->shouldPrune('chart.js/dist/chart.min.js.map'));
        $this->assertTrue($pruner->shouldPrune('select2/package.json'));
        $this->assertTrue($pruner->shouldPrune('dropzone/README.md'));

        $this->assertFalse($pruner->shouldPrune('fullcalendar/dist/fullcalendar.min.js'));
        $this->assertFalse($pruner->shouldPrune('bootstrap-daterangepicker/daterangepicker.css'));
        $this->assertFalse($pruner->shouldPrune('jquery.nicescroll/dist/jquery.nicescroll.min.js'));
        $this->assertFalse($pruner->shouldPrune('weathericons/font/weathericons-regular-webfont.woff2'));
        $this->assertFalse($pruner->shouldPrune('datatables/media/images/sort_asc.png'));
    }
}
