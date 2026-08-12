<?php

declare(strict_types=1); namespace App\Tools\IssCalculator\Tests\Feature; use Tests\TestCase; final class ToolPageTest extends TestCase { public function test_page_loads():void{$this->get('/tools/contabil/ferramentas/calculadora-iss')->assertOk()->assertSee('Calculadora de ISS');} public function test_calculation_returns_result():void{$this->post('/tools/contabil/ferramentas/calculadora-iss',['municipality'=>'São Paulo/SP','service'=>'Consultoria','value'=>'5.000,00','rate'=>'5'])->assertOk()->assertSee('ISS estimado');}}
