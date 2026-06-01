<?php

use App\Http\Requests\Admin\StoreProductoRequest;
use Illuminate\Support\Facades\Validator;

/**
 * PRUEBAS DE INTEGRACIÓN — Reglas de Negocio y Validaciones de Productos
 * 
 * Verifica que las nuevas políticas del sistema E-Sports funcionen correctamente:
 * - Nombre sin números ni símbolos especiales.
 * - Stock no negativo en las variantes.
 * - Precio Venta >= Precio Compra.
 * - Marca no puede ser puramente símbolos/líneas (-------, ______).
 */

// Función de ayuda para validar un campo específico con las reglas de StoreProductoRequest
function validateField(string $field, mixed $value, array $additionalData = []): \Illuminate\Validation\Validator
{
    $request = new StoreProductoRequest();
    $rules = $request->rules();
    
    // Solo tomamos la regla del campo que queremos testear
    $fieldRules = [$field => $rules[$field] ?? []];
    
    // Si testeamos variante_stock.*, estructuramos la data como array
    if ($field === 'variante_stock.*') {
        $data = ['variante_stock' => is_array($value) ? $value : [$value]];
    } else {
        $data = array_merge([$field => $value], $additionalData);
    }
    
    $validator = Validator::make($data, $fieldRules, $request->messages());
    
    // Mock del verificador de presencia de base de datos para evitar llamadas a SQLite
    $presenceVerifier = Mockery::mock(\Illuminate\Validation\PresenceVerifierInterface::class);
    $presenceVerifier->shouldReceive('getCount')->andReturn(0);
    $validator->setPresenceVerifier($presenceVerifier);
    
    return $validator;
}

/*
|--------------------------------------------------------------------------
| 1. Pruebas para el Nombre del Producto
|--------------------------------------------------------------------------
*/

it('acepta nombres de producto válidos puramente alfabéticos', function () {
    $nombresValidos = ['Zapatillas Deportivas', 'Polera Puma', 'Camiseta Oficial'];
    
    foreach ($nombresValidos as $nombre) {
        $validator = validateField('nombre', $nombre);
        expect($validator->passes())->toBeTrue();
    }
});

it('rechaza nombres de producto con números o caracteres especiales', function () {
    $nombresInvalidos = ['Polera 123', 'Adidas@#$', 'Puma_Store!'];
    
    foreach ($nombresInvalidos as $nombre) {
        $validator = validateField('nombre', $nombre);
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->first('nombre'))->toContain('solo puede contener letras, espacios y guiones');
    }
});

/*
|--------------------------------------------------------------------------
| 2. Pruebas para la Comparación de Precios (Venta >= Compra)
|--------------------------------------------------------------------------
*/

it('acepta precio de venta mayor o igual al costo de compra', function () {
    // Caso Venta > Compra
    $validator1 = validateField('precio_venta', 150.00, ['precio_compra' => 100.00]);
    expect($validator1->passes())->toBeTrue();

    // Caso Venta == Compra
    $validator2 = validateField('precio_venta', 100.00, ['precio_compra' => 100.00]);
    expect($validator2->passes())->toBeTrue();
});

it('rechaza precio de venta menor al costo de compra', function () {
    // Caso Venta < Compra
    $validator = validateField('precio_venta', 80.00, ['precio_compra' => 100.00]);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('precio_venta'))->toBe('El precio de venta (PVP) debe ser mayor o igual al costo de compra.');
});

it('rechaza precios de compra o venta negativos o iguales a cero', function () {
    // Compra negativa
    $validator1 = validateField('precio_compra', -10.00);
    expect($validator1->fails())->toBeTrue();

    // Compra cero
    $validator2 = validateField('precio_compra', 0.00);
    expect($validator2->fails())->toBeTrue();

    // Venta negativa
    $validator3 = validateField('precio_venta', -5.00, ['precio_compra' => 10.00]);
    expect($validator3->fails())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| 3. Pruebas para el Stock de Variantes
|--------------------------------------------------------------------------
*/

it('acepta stocks de variantes enteros no negativos', function () {
    $validator1 = validateField('variante_stock.*', 10);
    expect($validator1->passes())->toBeTrue();

    $validator2 = validateField('variante_stock.*', 0);
    expect($validator2->passes())->toBeTrue();
});

it('rechaza stocks de variantes negativos', function () {
    $validator = validateField('variante_stock.*', -5);
    expect($validator->fails())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| 4. Pruebas para la Marca de Fabricación
|--------------------------------------------------------------------------
*/

it('acepta marcas válidas que contienen letras o números', function () {
    $marcasValidas = ['Adidas', 'Nike-123', 'Puma Bolivia', 'Under_Armour'];
    
    foreach ($marcasValidas as $marca) {
        $validator = validateField('marca', $marca);
        expect($validator->passes())->toBeTrue();
    }
});

it('rechaza marcas que consisten puramente en símbolos o líneas repetidas', function () {
    $marcasBasura = ['-------', '_______', '.....', '@@@@@'];
    
    foreach ($marcasBasura as $marca) {
        $validator = validateField('marca', $marca);
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->first('marca'))->toBe('La marca de fabricación debe contener letras o números y no puede consistir únicamente en símbolos o líneas.');
    }
});

it('acepta que la descripción del producto sea opcional', function () {
    $validator = validateField('descripcion', null);
    expect($validator->passes())->toBeTrue();
});
