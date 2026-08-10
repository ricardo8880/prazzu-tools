<?php

namespace Tests\Feature\Verticals;

use Tests\TestCase;

final class CanonicalVerticalRoutingTest extends TestCase
{
    public function test_public_vertical_slugs_are_part_of_canonical_urls(): void
    {
        self::assertSame('http://localhost/tools/contabil', route('home', ['vertical' => 'contabil']));
        self::assertSame('http://localhost/tools/rh/ferramentas', route('tools.index', ['vertical' => 'rh']));
        self::assertSame('http://localhost/tools/rh/blog', route('blog.index', ['vertical' => 'rh']));
        self::assertSame('http://localhost/tools/rh/recursos', route('resources.index', ['vertical' => 'rh']));
    }

    public function test_rh_tool_is_not_available_under_accounting_vertical(): void
    {
        $this->get('/tools/rh/ferramentas/calculadora-turnover')->assertOk();
        $this->get('/tools/contabil/ferramentas/calculadora-turnover')->assertNotFound();
    }

    public function test_accounting_tool_is_not_available_under_rh_vertical(): void
    {
        $this->get('/tools/contabil/ferramentas/calculadora-salario-liquido')->assertOk();
        $this->get('/tools/rh/ferramentas/calculadora-salario-liquido')->assertNotFound();
    }

    public function test_only_plans_and_about_remain_global_content_pages(): void
    {
        self::assertSame('http://localhost/planos', route('plans'));
        self::assertSame('http://localhost/sobre', route('about'));
    }
}
