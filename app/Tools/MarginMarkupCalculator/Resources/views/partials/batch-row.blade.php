@php
$defaults = ['name'=>'','code'=>'','category'=>'','base_cost'=>'','additional_costs'=>'0,00','freight_cost'=>'0,00','packaging_cost'=>'0,00','fixed_expenses'=>'0,00','desired_margin'=>'30','taxes_percentage'=>'0','commission_percentage'=>'0','card_fees_percentage'=>'0','marketplace_fees_percentage'=>'0'];
$product = array_merge($defaults, $product ?? []);
@endphp
<tr>
    <td><input class="form-check-input" type="checkbox" data-select aria-label="Selecionar produto"></td>
    @foreach(['name'=>'Nome','code'=>'Código','category'=>'Categoria','base_cost'=>'0,00','additional_costs'=>'0,00','freight_cost'=>'0,00','packaging_cost'=>'0,00','fixed_expenses'=>'0,00','desired_margin'=>'30','taxes_percentage'=>'0','commission_percentage'=>'0','card_fees_percentage'=>'0','marketplace_fees_percentage'=>'0'] as $field => $placeholder)
        <td><input class="form-control form-control-sm {{ in_array($field, ['name','code','category'], true) ? '' : 'text-end' }}" style="min-width: {{ in_array($field, ['name','category'], true) ? '140px' : '95px' }}" name="products[{{ $index }}][{{ $field }}]" data-field="{{ $field }}" value="{{ $product[$field] }}" placeholder="{{ $placeholder }}" {{ in_array($field, ['name','base_cost','desired_margin'], true) ? 'required' : '' }} {{ str_contains($field, 'percentage') || $field === 'desired_margin' ? 'type=number step=0.000001 min=0 max=99.999999' : '' }}></td>
    @endforeach
</tr>
