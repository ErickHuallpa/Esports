<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Rol;
use App\Models\Persona;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\TipoPago;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Resena;
use App\Models\Envio;
use App\Models\Orden;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Desactivar restricciones de claves foráneas temporalmente de forma compatible
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // Limpiar todas las tablas
        DetalleVenta::truncate();
        Resena::truncate();
        if (Schema_has_table('envios')) Envio::truncate();
        if (Schema_has_table('ordens')) Orden::truncate();
        Venta::truncate();
        Pago::truncate();
        ProductoVariante::truncate();
        Producto::truncate();
        Proveedor::truncate();
        Categoria::truncate();
        User::truncate();
        Rol::truncate();
        Persona::truncate();
        TipoPago::truncate();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        // ==========================================
        // 1. ROLES
        // ==========================================
        $rolAdmin = Rol::create([
            'nombre' => 'admin',
            'descripcion' => 'Administrador del sistema con acceso total',
        ]);

        $rolCajero = Rol::create([
            'nombre' => 'cajero',
            'descripcion' => 'Personal de ventas y atención en caja',
        ]);

        $rolCliente = Rol::create([
            'nombre' => 'cliente',
            'descripcion' => 'Cliente de la tienda virtual',
        ]);

        // ==========================================
        // 2. PERSONAS Y USUARIOS
        // ==========================================
        // Admin
        $pAdmin = Persona::create([
            'nombre' => 'Alejandro',
            'apellidos' => 'Vargas Rojas',
            'ci' => '10203040',
            'telefono' => '71234567',
            'direccion' => 'Av. Arce #123, La Paz',
            'fecha_nacimiento' => '1990-05-15',
        ]);

        $userAdmin = User::create([
            'persona_id' => $pAdmin->id,
            'rol_id' => $rolAdmin->id,
            'email' => 'admin@esports.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'activo' => true,
            'ultimo_login' => Carbon::now(),
        ]);

        // Cajero
        $pCajero = Persona::create([
            'nombre' => 'Mariana',
            'apellidos' => 'Flores Mamani',
            'ci' => '50607080',
            'telefono' => '60123456',
            'direccion' => 'Calle Murillo #456, Potosí',
            'fecha_nacimiento' => '1995-10-20',
        ]);

        $userCajero = User::create([
            'persona_id' => $pCajero->id,
            'rol_id' => $rolCajero->id,
            'email' => 'cajero@esports.com',
            'username' => 'cajero',
            'password' => Hash::make('cajero123'),
            'activo' => true,
            'ultimo_login' => Carbon::now()->subHours(2),
        ]);

        // Clientes
        $clientesData = [
            [
                'nombre' => 'Erick',
                'apellidos' => 'Huallpa Calla',
                'ci' => '8947234',
                'telefono' => '78945612',
                'direccion' => 'Z. Central Calle Tarija #89',
                'email' => 'erick@gmail.com',
                'username' => 'erick',
            ],
            [
                'nombre' => 'Sofia',
                'apellidos' => 'Sánchez Paz',
                'ci' => '7845129',
                'telefono' => '65432109',
                'direccion' => 'Av. Melchor Pérez #777, Cochabamba',
                'email' => 'sofia@gmail.com',
                'username' => 'sofia',
            ],
            [
                'nombre' => 'Carlos',
                'apellidos' => 'Medina Ruiz',
                'ci' => '6985214',
                'telefono' => '71122334',
                'direccion' => 'Z. Sur Calle 21 #99, La Paz',
                'email' => 'carlos@gmail.com',
                'username' => 'carlos',
            ],
            [
                'nombre' => 'Marcela',
                'apellidos' => 'Peredo Justiniano',
                'ci' => '9658231',
                'telefono' => '72233445',
                'direccion' => 'Z. Equipetrol Calle 8 #20, Santa Cruz',
                'email' => 'marcela@gmail.com',
                'username' => 'marcela',
            ],
            [
                'nombre' => 'Juan Carlos',
                'apellidos' => 'Miranda Siles',
                'ci' => '4125369',
                'telefono' => '68899775',
                'direccion' => 'Z. San Gerardo Calle Chichas #15',
                'email' => 'juan@gmail.com',
                'username' => 'juan',
            ],
        ];

        $usersClientes = [];
        foreach ($clientesData as $c) {
            $p = Persona::create([
                'nombre' => $c['nombre'],
                'apellidos' => $c['apellidos'],
                'ci' => $c['ci'],
                'telefono' => $c['telefono'],
                'direccion' => $c['direccion'],
                'fecha_nacimiento' => Carbon::now()->subYears(rand(18, 40))->format('Y-m-d'),
            ]);

            $usersClientes[] = User::create([
                'persona_id' => $p->id,
                'rol_id' => $rolCliente->id,
                'email' => $c['email'],
                'username' => $c['username'],
                'password' => Hash::make('password'),
                'activo' => true,
                'ultimo_login' => Carbon::now()->subDays(rand(1, 10)),
            ]);
        }

        // ==========================================
        // 3. CATEGORÍAS
        // ==========================================
        $catTeclados = Categoria::create([
            'nombre' => 'Teclados Mecánicos',
            'descripcion' => 'Teclados mecánicos de alto rendimiento para gaming y oficina',
            'activo' => true,
        ]);

        $catRatones = Categoria::create([
            'nombre' => 'Ratones Gamer',
            'descripcion' => 'Ratones ergonómicos y ultra ligeros con sensores de alta precisión',
            'activo' => true,
        ]);

        $catAuriculares = Categoria::create([
            'nombre' => 'Auriculares Pro',
            'descripcion' => 'Auriculares con sonido envolvente y cancelación activa de ruido',
            'activo' => true,
        ]);

        $catSillas = Categoria::create([
            'nombre' => 'Sillas Ergonómicas',
            'descripcion' => 'Sillas gamer diseñadas para largas sesiones de juego y máximo confort',
            'activo' => true,
        ]);

        $catMonitores = Categoria::create([
            'nombre' => 'Monitores Gaming',
            'descripcion' => 'Monitores con alta tasa de refresco (144Hz+) y bajo tiempo de respuesta',
            'activo' => true,
        ]);

        // ==========================================
        // 4. PROVEEDORES
        // ==========================================
        $provRazer = Proveedor::create([
            'nombre_empresa' => 'Razer LATAM SA',
            'telefono' => '+541148902000',
            'email' => 'ventas@razer-latam.com',
            'contacto_nombre' => 'Esteban Quito',
            'direccion' => 'Av. Cabildo 1500, Belgrano',
            'ciudad' => 'Buenos Aires',
            'pais' => 'Argentina',
            'activo' => true,
        ]);

        $provLogitech = Proveedor::create([
            'nombre_empresa' => 'Logitech G Bolivia',
            'telefono' => '22791020',
            'email' => 'soporte@logitech-bo.com',
            'contacto_nombre' => 'Valeria Luna',
            'direccion' => 'Av. Montenegro #890, San Miguel',
            'ciudad' => 'La Paz',
            'pais' => 'Bolivia',
            'activo' => true,
        ]);

        $provCorsair = Proveedor::create([
            'nombre_empresa' => 'Corsair Gaming Imports',
            'telefono' => '+56223405000',
            'email' => 'importaciones@corsair.cl',
            'contacto_nombre' => 'Guillermo Tell',
            'direccion' => 'Av. Vitacura 2900',
            'ciudad' => 'Santiago',
            'pais' => 'Chile',
            'activo' => true,
        ]);

        $provRedragon = Proveedor::create([
            'nombre_empresa' => 'Redragon Distribuidora SRL',
            'telefono' => '4441029',
            'email' => 'info@redragon.com.bo',
            'contacto_nombre' => 'Roberto Gómez',
            'direccion' => 'Av. Virgen de Cotoca #2000',
            'ciudad' => 'Santa Cruz',
            'pais' => 'Bolivia',
            'activo' => true,
        ]);

        // ==========================================
        // 5. PRODUCTOS Y VARIANTES
        // ==========================================
        // 1. Razer Huntsman V3 Pro
        $prodHuntsman = Producto::create([
            'categoria_id' => $catTeclados->id,
            'proveedor_id' => $provRazer->id,
            'nombre' => 'Teclado Mecánico Razer Huntsman V3 Pro',
            'descripcion' => 'Teclado analógico para e-sports de tamaño completo con interruptores ópticos analógicos de 2ª generación Razer y Rapid Trigger.',
            'marca' => 'Razer',
            'precio_compra' => 1250.00,
            'precio_venta' => 1650.00,
            'imagen_url' => 'https://assets3.razerzone.com/gK-xS2XzF9Pq5wE9v_P7r7vF5dE=/600x600/https%3A%2F%2Fhybrismediaprod.blob.core.windows.net%2Fsys-master-phoenix-images-container%2Fh91%2Fhb5%2F9651508215838%2Fhuntsman-v3-pro-600x600.png',
            'visible' => true,
        ]);
        ProductoVariante::create(['producto_id' => $prodHuntsman->id, 'talla' => 'Full Size', 'color' => 'Negro', 'stock' => 15]);
        ProductoVariante::create(['producto_id' => $prodHuntsman->id, 'talla' => 'TKL', 'color' => 'Negro', 'stock' => 8]);
        ProductoVariante::create(['producto_id' => $prodHuntsman->id, 'talla' => 'Mini 60%', 'color' => 'Blanco', 'stock' => 5]);

        // 2. Logitech G Pro X Superlight 2
        $prodSuperlight = Producto::create([
            'categoria_id' => $catRatones->id,
            'proveedor_id' => $provLogitech->id,
            'nombre' => 'Ratón Logitech G Pro X Superlight 2',
            'descripcion' => 'El ratón para esports preferido por los profesionales. Ultra ligero de 60g, sensor HERO 2 con 32.000 DPI y conectividad LIGHTSPEED de latencia cero.',
            'marca' => 'Logitech G',
            'precio_compra' => 750.00,
            'precio_venta' => 1050.00,
            'imagen_url' => 'https://resource.logitechg.com/w_692,c_lpad,ar_16:9,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight-2/gallery/pro-x-superlight-2-wireless-mouse-black-gallery-1.png',
            'visible' => true,
        ]);
        ProductoVariante::create(['producto_id' => $prodSuperlight->id, 'talla' => 'Estándar', 'color' => 'Negro', 'stock' => 20]);
        ProductoVariante::create(['producto_id' => $prodSuperlight->id, 'talla' => 'Estándar', 'color' => 'Blanco', 'stock' => 12]);
        ProductoVariante::create(['producto_id' => $prodSuperlight->id, 'talla' => 'Estándar', 'color' => 'Rosa', 'stock' => 2]); // Stock crítico!

        // 3. Razer DeathAdder V3 Pro
        $prodDeathAdder = Producto::create([
            'categoria_id' => $catRatones->id,
            'proveedor_id' => $provRazer->id,
            'nombre' => 'Ratón Razer DeathAdder V3 Pro',
            'descripcion' => 'Ergonomía refinada en un diseño ultra ligero de 63g. Desarrollado en colaboración con profesionales de los esports.',
            'marca' => 'Razer',
            'precio_compra' => 680.00,
            'precio_venta' => 950.00,
            'imagen_url' => 'https://assets3.razerzone.com/p1_r0m4n_S0_0_J_J_L_L_L_L_L_L_L_L_L_L_L_L/600x600/deathadder-v3-pro-black-600x600.png',
            'visible' => true,
        ]);
        ProductoVariante::create(['producto_id' => $prodDeathAdder->id, 'talla' => 'Estándar', 'color' => 'Negro', 'stock' => 18]);
        ProductoVariante::create(['producto_id' => $prodDeathAdder->id, 'talla' => 'Estándar', 'color' => 'Blanco', 'stock' => 10]);

        // 4. Corsair HS80 RGB Wireless
        $prodHS80 = Producto::create([
            'categoria_id' => $catAuriculares->id,
            'proveedor_id' => $provCorsair->id,
            'nombre' => 'Auriculares Corsair HS80 RGB Wireless',
            'descripcion' => 'Auriculares premium para juegos con sonido envolvente Dolby Atmos y tecnología SLIPSTREAM WIRELESS de latencia ultra baja.',
            'marca' => 'Corsair',
            'precio_compra' => 620.00,
            'precio_venta' => 880.00,
            'imagen_url' => 'https://images.corsair.com/corsairfront/media/sys_master/images/images/h9e/ha8/9773644021790/CA-9011235-NA/Gallery/HS80_RGB_WIRELESS_CARBON_01.png',
            'visible' => true,
        ]);
        ProductoVariante::create(['producto_id' => $prodHS80->id, 'talla' => 'Estándar', 'color' => 'Negro Carbono', 'stock' => 14]);
        ProductoVariante::create(['producto_id' => $prodHS80->id, 'talla' => 'Estándar', 'color' => 'Blanco', 'stock' => 3]); // Stock bajo!

        // 5. Silla Corsair T3 Rush
        $prodT3Rush = Producto::create([
            'categoria_id' => $catSillas->id,
            'proveedor_id' => $provCorsair->id,
            'nombre' => 'Silla Gamer Corsair T3 Rush (Tejido)',
            'descripcion' => 'Inspirada en el automovilismo profesional. Fabricada en tejido suave y transpirable para mantener la frescura. Reposabrazos 4D.',
            'marca' => 'Corsair',
            'precio_compra' => 1400.00,
            'precio_venta' => 1950.00,
            'imagen_url' => 'https://images.corsair.com/corsairfront/media/sys_master/images/images/hec/hd5/9751996235806/CF-9010029-WW/Gallery/T3_RUSH_GREY_WHITE_01.png',
            'visible' => true,
        ]);
        ProductoVariante::create(['producto_id' => $prodT3Rush->id, 'talla' => 'Estándar', 'color' => 'Gris/Blanco', 'stock' => 6]);
        ProductoVariante::create(['producto_id' => $prodT3Rush->id, 'talla' => 'Estándar', 'color' => 'Gris/Carbón', 'stock' => 4]);

        // 6. Teclado Redragon K552 Kumara
        $prodKumara = Producto::create([
            'categoria_id' => $catTeclados->id,
            'proveedor_id' => $provRedragon->id,
            'nombre' => 'Teclado Mecánico Redragon K552 Kumara',
            'descripcion' => 'Teclado mecánico retroiluminado TKL. Resistente a salpicaduras, switch Red/Blue de alta durabilidad. El más vendido por su excelente calidad/precio.',
            'marca' => 'Redragon',
            'precio_compra' => 220.00,
            'precio_venta' => 350.00,
            'imagen_url' => 'https://redragon.es/content/uploads/2021/04/K552RGB-1.png',
            'visible' => true,
        ]);
        ProductoVariante::create(['producto_id' => $prodKumara->id, 'talla' => 'TKL', 'color' => 'Negro Switch Azul', 'stock' => 35]);
        ProductoVariante::create(['producto_id' => $prodKumara->id, 'talla' => 'TKL', 'color' => 'Blanco Switch Rojo', 'stock' => 25]);
        ProductoVariante::create(['producto_id' => $prodKumara->id, 'talla' => 'TKL', 'color' => 'Rosa Switch Rojo', 'stock' => 1]); // Crítico!

        // ==========================================
        // 6. TIPO PAGOS
        // ==========================================
        $tpQR = TipoPago::create(['nombre' => 'Transferencia QR', 'descripcion' => 'Pago mediante transferencia bancaria o código QR (Simple/Bancario)']);
        $tpCard = TipoPago::create(['nombre' => 'Tarjeta Crédito/Débito', 'descripcion' => 'Pago en línea con tarjeta Visa o MasterCard']);
        $tpEfectivo = TipoPago::create(['nombre' => 'Efectivo en Caja', 'descripcion' => 'Pago presencial en efectivo en el punto de venta (POS)']);

        // ==========================================
        // 7. PAGOS, VENTAS Y DETALLES (Últimos 30 días)
        // ==========================================
        $variantes = ProductoVariante::with('producto')->get();
        
        // Creamos unas 25 ventas realistas distribuidas en los últimos 30 días
        $numVentas = 28;
        for ($i = 0; $i < $numVentas; $i++) {
            $fecha = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $cliente = $usersClientes[rand(0, count($usersClientes) - 1)];
            
            // Decidir tipo de pago
            $tipoPago = rand(1, 3); // 1 = QR, 2 = Tarjeta, 3 = Efectivo
            $estadoPago = 'verificado';
            
            // Un par de ventas pendientes o rechazadas para realismo
            if ($i === 3) {
                $estadoPago = 'pendiente';
            } elseif ($i === 7) {
                $estadoPago = 'rechazado';
            }

            // Elegir entre 1 y 3 productos al azar
            $numItems = rand(1, 3);
            $itemsElegidos = $variantes->random($numItems);
            
            $subtotalVenta = 0;
            $itemsDetalle = [];
            
            foreach ($itemsElegidos as $var) {
                $cant = rand(1, 2);
                $sub = $cant * $var->producto->precio_venta;
                $subtotalVenta += $sub;
                
                $itemsDetalle[] = [
                    'producto_variante_id' => $var->id,
                    'cantidad' => $cant,
                    'precio_compra' => $var->producto->precio_compra,
                    'precio_venta' => $var->producto->precio_venta,
                    'subtotal' => $sub,
                ];
            }

            $descuento = 0;
            if (rand(0, 10) > 8) { // 20% probabilidad de descuento
                $descuento = round($subtotalVenta * 0.1, 2); // 10% de descuento
            }
            $totalVenta = $subtotalVenta - $descuento;

            // Crear el Pago primero
            $pago = Pago::create([
                'tipo_pago_id' => $tipoPago,
                'user_id' => $cliente->id,
                'monto' => $totalVenta,
                'estado' => $estadoPago,
                'comprobante_url' => $tipoPago === 1 ? 'comprobantes/qr_trans_' . md5($i) . '.png' : null,
                'motivo_rechazo' => $estadoPago === 'rechazado' ? 'Comprobante borroso o no válido' : null,
                'fecha_pago' => $fecha,
                'verificado_por' => $estadoPago === 'verificado' ? $userAdmin->id : null,
                'fecha_verificacion' => $estadoPago === 'verificado' ? $fecha->addMinutes(rand(10, 120)) : null,
                'observaciones' => $estadoPago === 'verificado' ? 'Pago verificado correctamente en el sistema' : null,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            // Crear la Venta
            $venta = Venta::create([
                'user_id' => $cliente->id,
                'pago_id' => $pago->id,
                'precio_total' => $totalVenta,
                'descuento_aplicado' => $descuento > 0 ? $descuento : null,
                'estado_venta' => $estadoPago === 'verificado' ? 'confirmada' : ($estadoPago === 'pendiente' ? 'pendiente' : 'cancelada'),
                'fecha_venta' => $fecha,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);

            // Crear los detalles
            foreach ($itemsDetalle as $item) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_variante_id' => $item['producto_variante_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario_compra' => $item['precio_compra'],
                    'precio_unitario_venta' => $item['precio_venta'],
                    'descuento_unitario' => 0,
                    'subtotal' => $item['subtotal'],
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);

                // Descontar stock si la venta está confirmada
                if ($venta->estado_venta === 'confirmada') {
                    $vModel = ProductoVariante::find($item['producto_variante_id']);
                    if ($vModel && $vModel->stock >= $item['cantidad']) {
                        $vModel->decrement('stock', $item['cantidad']);
                    }
                }
            }
        }

        // ==========================================
        // 8. RESEÑAS (Cargar opiniones realistas)
        // ==========================================
        $comentarios = [
            5 => [
                'Excelente teclado gamer, la respuesta es inmediata. Muy recomendado.',
                'El mejor ratón que he tenido, súper liviano y la batería dura un montón.',
                'Increíble calidad de sonido y el Dolby Atmos es espectacular para jugar.',
                'Muy cómoda para trabajar y jugar todo el día, el tejido es fresco.',
                'Relación calidad precio insuperable, switch muy agradable al tacto.',
            ],
            4 => [
                'Muy buen producto, aunque los interruptores son algo ruidosos.',
                'Súper cómodo y preciso, solo que el cable de carga es un poco rígido.',
                'Muy buen audio, pero las almohadillas presionan un poco al principio.',
                'Cómoda y robusta, aunque el armado tomó más tiempo del esperado.',
                'Muy buen teclado, pero las luces led podrían ser más brillantes.',
            ],
        ];

        // Añadir unas 8 reseñas al azar cuidando la regla de integridad unique(user_id, producto_id)
        $reseñasCreadas = 0;
        $intentos = 0;
        $maxIntentos = 100;
        
        $productos = Producto::all();
        
        while ($reseñasCreadas < 12 && $intentos < $maxIntentos) {
            $intentos++;
            $cliente = $usersClientes[rand(0, count($usersClientes) - 1)];
            $producto = $productos->random();
            
            // Verificar si ya existe reseña de este cliente para este producto
            $existe = Resena::where('user_id', $cliente->id)->where('producto_id', $producto->id)->exists();
            if (!$existe) {
                $calif = rand(4, 5);
                $comentario = $comentarios[$calif][rand(0, count($comentarios[$calif]) - 1)];
                
                Resena::create([
                    'user_id' => $cliente->id,
                    'producto_id' => $producto->id,
                    'calificacion' => $calif,
                    'comentario' => $comentario,
                    'fecha_resena' => Carbon::now()->subDays(rand(1, 15)),
                ]);
                $reseñasCreadas++;
            }
        }
    }
}

// Función auxiliar simple para verificar si una tabla existe en la base de datos
function Schema_has_table($table) {
    try {
        return \Illuminate\Support\Facades\Schema::hasTable($table);
    } catch (\Exception $e) {
        return false;
    }
}
