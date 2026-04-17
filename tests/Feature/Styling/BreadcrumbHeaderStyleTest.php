<?php

namespace Tests\Feature\Styling;

use Tests\TestCase;

class BreadcrumbHeaderStyleTest extends TestCase
{
    public function test_section_header_breadcrumb_styles_render_a_centered_slash_separator(): void
    {
        $primaryCss = file_get_contents(public_path('css/style.css'));
        $mirroredCss = file_get_contents(public_path('assets/style.css'));

        $linkSelector = '.section .section-header .section-header-breadcrumb .breadcrumb-item a';
        $adjacentItemSelector = '.section .section-header .section-header-breadcrumb .breadcrumb-item + .breadcrumb-item';
        $separatorSelector = '.section .section-header .section-header-breadcrumb .breadcrumb-item + .breadcrumb-item::before';

        $this->assertIsString($primaryCss);
        $this->assertIsString($mirroredCss);

        $this->assertStringContainsString($linkSelector, $primaryCss);
        $this->assertStringContainsString($adjacentItemSelector, $primaryCss);
        $this->assertStringContainsString($separatorSelector, $primaryCss);
        $this->assertStringContainsString('padding-left: 0;', $primaryCss);
        $this->assertStringContainsString('content: "/";', $primaryCss);
        $this->assertStringContainsString('display: inline-block;', $primaryCss);
        $this->assertStringContainsString('padding: 0 6px;', $primaryCss);
        $this->assertStringContainsString('text-decoration: none;', $primaryCss);
        $this->assertStringNotContainsString('margin-left: 8px;', $primaryCss);
        $this->assertStringNotContainsString('content: " / ";', $primaryCss);

        $this->assertStringContainsString($linkSelector, $mirroredCss);
        $this->assertStringContainsString($adjacentItemSelector, $mirroredCss);
        $this->assertStringContainsString($separatorSelector, $mirroredCss);
        $this->assertStringContainsString('padding-left: 0;', $mirroredCss);
        $this->assertStringContainsString('content: "/";', $mirroredCss);
        $this->assertStringContainsString('display: inline-block;', $mirroredCss);
        $this->assertStringContainsString('padding: 0 6px;', $mirroredCss);
        $this->assertStringContainsString('text-decoration: none;', $mirroredCss);
        $this->assertStringNotContainsString('margin-left: 8px;', $mirroredCss);
        $this->assertStringNotContainsString('content: " / ";', $mirroredCss);
    }
}
