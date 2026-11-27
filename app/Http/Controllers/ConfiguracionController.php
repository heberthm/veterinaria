<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ConfiguracionController extends Controller
{
    /** Módulos (recursos) de la aplicación sobre los que se asignan permisos. */
    public const MODULOS = [
        'inicio'          => 'Inicio (tablero)',
        'agenda'          => 'Agenda / Citas',
        'clientes'        => 'Clientes',
        'mascotas'        => 'Mascotas',
        'historias'       => 'Historias clínicas',
        'vacunacion'      => 'Vacunación',
        'desparasitacion' => 'Desparasitación',
        'ventas'          => 'Ventas / POS',
        'facturas'        => 'Facturas',
        'inventario'      => 'Inventario',
        'compras'         => 'Compras',
        'reportes'        => 'Reportes',
        'usuarios'        => 'Usuarios',
        'configuracion'   => 'Configuración',
    ];

    /** Acciones por módulo. El permiso se llama "<acción>-<módulo>", p. ej. ver-facturas. */
    public const ACCIONES = [
        'ver'      => 'Ver',
        'crear'    => 'Crear',
        'editar'   => 'Editar',
        'eliminar' => 'Eliminar',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        // Solo administradores (por columna rol, rol de Spatie o super admin)
        $this->middleware(function ($request, $next) {
            $u = auth()->user();
            $nombresAdmin = ['admin', 'administrador', 'administrator', 'super admin', 'superadmin', 'super-admin'];
            $rolColumna   = mb_strtolower(trim((string) ($u->rol ?? '')));

            $esAdmin = $u && (
                in_array($rolColumna, $nombresAdmin, true)
                || (bool) ($u->es_super_admin ?? false)
                || (method_exists($u, 'getRoleNames')
                    && $u->getRoleNames()->contains(fn ($r) => in_array(mb_strtolower($r), $nombresAdmin, true)))
            );
            abort_unless($esAdmin, 403, 'Solo los administradores pueden cambiar la configuración.');

            return $next($request);
        })->except('logo');
    }

    /* ===================================================================== */
    /*  Utilidades                                                            */
    /* ===================================================================== */

    private function tenantId(): int
    {
        return (int) (auth()->user()->tenant_id ?? 1);
    }

    private function cfg(): Configuracion
    {
        return Configuracion::deTenant($this->tenantId());
    }

    private function nombresPermisos(): array
    {
        $n = [];
        foreach (array_keys(self::MODULOS) as $m) {
            foreach (array_keys(self::ACCIONES) as $a) {
                $n[] = "$a-$m";
            }
        }
        return $n;
    }

    /** Roles que se pueden editar (el Admin siempre tiene todo). */
    private function rolesEditables(): array
    {
        return array_values(array_diff(UserController::ROLES, ['Admin']));
    }

    private function falla(string $campo, string $mensaje)
    {
        throw ValidationException::withMessages([$campo => [$mensaje]]);
    }

    /* ===================================================================== */
    /*  Pantalla                                                              */
    /* ===================================================================== */

    public function index()
    {
        $cfg = $this->cfg();

        return view('configuracion', [
            'cfg'      => $cfg,
            'horario'  => $cfg->horarioCompleto(),
            'dias'     => Configuracion::DIAS,
            'tipos'    => Catalogo::TIPOS,
            'modulos'  => self::MODULOS,
            'acciones' => self::ACCIONES,
            'roles'    => $this->rolesEditables(),
        ]);
    }

    /* ===================================================================== */
    /*  General / Facturación / Horario                                       */
    /* ===================================================================== */

    public function guardarGeneral(Request $request)
    {
        $data = $request->validate([
            'nombre_clinica' => 'required|string|max:120',
            'razon_social'   => 'nullable|string|max:150',
            'nit'            => 'nullable|string|max:30',
            'direccion'      => 'nullable|string|max:255',
            'ciudad'         => 'nullable|string|max:100',
            'telefono'       => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:150',
            'sitio_web'      => 'nullable|string|max:150',
            'logo'           => 'nullable|image|max:2048',
        ], [
            'nombre_clinica.required' => 'El nombre de la clínica es obligatorio.',
            'email.email'             => 'El correo no es válido.',
            'logo.image'              => 'El logo debe ser una imagen (JPG, PNG, WEBP...).',
            'logo.max'                => 'El logo no puede pesar más de 2 MB.',
        ]);

        $cfg = $this->cfg();
        unset($data['logo']);

        if ($request->boolean('quitar_logo') && $cfg->logo) {
            Storage::disk('public')->delete($cfg->logo);
            $data['logo'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($cfg->logo) {
                Storage::disk('public')->delete($cfg->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $cfg->update($data);

        return response()->json([
            'success'  => true,
            'message'  => 'Datos de la clínica guardados.',
            'logo_url' => $cfg->fresh()->logo_url,
        ]);
    }

    /** Sirve el logo de la clínica (visible para cualquier usuario autenticado). */
    public function logo()
    {
        $cfg  = Configuracion::deTenant($this->tenantId());
        $disk = Storage::disk('public');

        abort_unless($cfg && $cfg->logo && $disk->exists($cfg->logo), 404);

        return response()->file($disk->path($cfg->logo), [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function guardarFacturacion(Request $request)
    {
        $data = $request->validate([
            'iva_porcentaje'    => 'required|numeric|min:0|max:100',
            'prefijo_factura'   => ['required', 'string', 'max:10', 'regex:/^[A-Za-z0-9\-_]+$/'],
            'moneda'            => ['required', Rule::in(['COP', 'USD'])],
            'formato_impresion' => ['required', Rule::in(['ticket', 'carta'])],
            'pie_factura'       => 'nullable|string|max:500',
        ], [
            'iva_porcentaje.required' => 'Indica el porcentaje de IVA.',
            'iva_porcentaje.max'      => 'El IVA no puede superar 100%.',
            'prefijo_factura.regex'   => 'El prefijo solo puede tener letras, números, guion y guion bajo.',
            'prefijo_factura.required' => 'El prefijo de la factura es obligatorio.',
        ]);

        $this->cfg()->update($data);

        return response()->json(['success' => true, 'message' => 'Parámetros de facturación guardados.']);
    }

    public function guardarHorario(Request $request)
    {
        $request->validate([
            'duracion_cita_min' => 'required|integer|min:5|max:240',
        ], [
            'duracion_cita_min.required' => 'Indica la duración de la cita.',
            'duracion_cita_min.min'      => 'La duración mínima de una cita es 5 minutos.',
            'duracion_cita_min.max'      => 'La duración máxima de una cita es 240 minutos.',
        ]);

        $entrada = (array) $request->input('horario', []);
        $horario = [];

        foreach (Configuracion::DIAS as $dia => $etiqueta) {
            $fila    = (array) ($entrada[$dia] ?? []);
            $abierto = !empty($fila['abierto']) && $fila['abierto'] !== '0';
            $desde   = (string) ($fila['desde'] ?? '08:00');
            $hasta   = (string) ($fila['hasta'] ?? '18:00');

            $formato = '/^([01]\d|2[0-3]):[0-5]\d$/';
            if (!preg_match($formato, $desde) || !preg_match($formato, $hasta)) {
                $this->falla('horario', "El horario de {$etiqueta} no tiene un formato de hora válido.");
            }
            if ($abierto && $desde >= $hasta) {
                $this->falla('horario', "{$etiqueta}: la hora de cierre debe ser mayor a la de apertura.");
            }

            $horario[$dia] = ['abierto' => $abierto, 'desde' => $desde, 'hasta' => $hasta];
        }

        $this->cfg()->update([
            'duracion_cita_min' => (int) $request->duracion_cita_min,
            'horario'           => $horario,
        ]);

        return response()->json(['success' => true, 'message' => 'Horario guardado.']);
    }

    /* ===================================================================== */
    /*  Catálogos                                                             */
    /* ===================================================================== */

    private function tipoValido(string $tipo): array
    {
        abort_unless(isset(Catalogo::TIPOS[$tipo]), 404, 'Tipo de catálogo no válido.');
        return Catalogo::TIPOS[$tipo];
    }

    public function catalogoIndex(string $tipo)
    {
        $meta = $this->tipoValido($tipo);
        $t    = $this->tenantId();

        $padres = $meta['padre']
            ? Catalogo::delTenant($t)->where('tipo', $meta['padre'])->orderBy('nombre')->get(['id', 'nombre'])
            : collect();

        $nombresPadre = $padres->pluck('nombre', 'id');

        $items = Catalogo::delTenant($t)
            ->where('tipo', $tipo)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($c) => [
                'id'          => $c->id,
                'nombre'      => $c->nombre,
                'descripcion' => $c->descripcion,
                'valor'       => $c->valor,
                'padre_id'    => $c->padre_id,
                'padre'       => $nombresPadre[$c->padre_id] ?? null,
                'activo'      => $c->activo,
            ]);

        return response()->json(['success' => true, 'items' => $items, 'padres' => $padres]);
    }

    private function validarCatalogo(Request $request, string $tipo, ?Catalogo $actual = null): array
    {
        $meta = $this->tipoValido($tipo);
        $t    = $this->tenantId();

        $reglas = [
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'activo'      => 'nullable|boolean',
        ];
        if ($meta['valor']) {
            $reglas['valor'] = 'nullable|numeric|min:0|max:999999999';
        }
        if ($meta['padre']) {
            $reglas['padre_id'] = [
                'required',
                Rule::exists('catalogos', 'id')->where(fn ($q) => $q->where('tenant_id', $t)->where('tipo', $meta['padre'])),
            ];
        }

        $data = $request->validate($reglas, [
            'nombre.required'   => 'El nombre es obligatorio.',
            'padre_id.required' => 'Selecciona la ' . strtolower(Catalogo::TIPOS[$meta['padre']]['singular'] ?? 'opción') . '.',
            'padre_id.exists'   => 'La opción seleccionada no es válida.',
            'valor.numeric'     => 'El valor debe ser un número.',
        ]);

        // Sin duplicados dentro del mismo tipo (y del mismo padre)
        $duplicado = Catalogo::delTenant($t)
            ->where('tipo', $tipo)
            ->whereRaw('LOWER(nombre) = ?', [mb_strtolower(trim($data['nombre']))])
            ->when($meta['padre'], fn ($q) => $q->where('padre_id', $data['padre_id'] ?? null))
            ->when($actual, fn ($q) => $q->where('id', '!=', $actual->id))
            ->exists();

        if ($duplicado) {
            $this->falla('nombre', 'Ya existe un registro con ese nombre.');
        }

        $data['nombre'] = trim($data['nombre']);
        $data['activo'] = $request->boolean('activo', true);

        return $data;
    }

    public function catalogoStore(Request $request)
    {
        $tipo = (string) $request->input('tipo');
        $data = $this->validarCatalogo($request, $tipo);

        Catalogo::create($data + ['tenant_id' => $this->tenantId(), 'tipo' => $tipo]);

        return response()->json(['success' => true, 'message' => Catalogo::TIPOS[$tipo]['singular'] . ' creado correctamente.']);
    }

    public function catalogoUpdate(Request $request, int $id)
    {
        $item = Catalogo::delTenant($this->tenantId())->findOrFail($id);
        $data = $this->validarCatalogo($request, $item->tipo, $item);

        $item->update($data);

        return response()->json(['success' => true, 'message' => 'Registro actualizado correctamente.']);
    }

    public function catalogoDestroy(int $id)
    {
        $item = Catalogo::delTenant($this->tenantId())->findOrFail($id);

        if (Catalogo::delTenant($this->tenantId())->where('padre_id', $item->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar: tiene registros que dependen de él (por ejemplo, razas).',
            ], 422);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Registro eliminado.']);
    }

    /** Carga los valores sugeridos del tipo (omite los que ya existen). */
    public function catalogoSugeridos(string $tipo)
    {
        $meta = $this->tipoValido($tipo);
        $t    = $this->tenantId();

        $existentes = Catalogo::delTenant($t)->where('tipo', $tipo)->pluck('nombre')
            ->map(fn ($n) => mb_strtolower($n))->all();

        $nuevos = 0;
        foreach ($meta['sugeridos'] as $i => $nombre) {
            if (in_array(mb_strtolower($nombre), $existentes, true)) {
                continue;
            }
            Catalogo::create([
                'tenant_id' => $t, 'tipo' => $tipo, 'nombre' => $nombre, 'orden' => $i, 'activo' => true,
            ]);
            $nuevos++;
        }

        return response()->json([
            'success' => true,
            'message' => $nuevos ? "Se agregaron {$nuevos} valores sugeridos." : 'Ya tenías todos los valores sugeridos.',
        ]);
    }

    /* ===================================================================== */
    /*  Roles y permisos                                                      */
    /* ===================================================================== */

    public function permisos(string $rol)
    {
        abort_unless(in_array($rol, $this->rolesEditables(), true), 404, 'Rol no válido.');

        $role = Role::where('name', $rol)->where('guard_name', 'web')->first();

        return response()->json([
            'success'  => true,
            'permisos' => $role ? $role->permissions->pluck('name')->values() : [],
        ]);
    }

    public function guardarPermisos(Request $request, string $rol)
    {
        abort_unless(in_array($rol, $this->rolesEditables(), true), 404, 'Rol no válido.');

        $request->validate([
            'permisos'   => 'nullable|array',
            'permisos.*' => 'string',
        ]);

        $validos = $this->nombresPermisos();
        $pedidos = array_values(array_intersect((array) $request->input('permisos', []), $validos));

        // "ver" es la base: si hay crear/editar/eliminar de un módulo, también se da ver
        foreach ($pedidos as $p) {
            [$accion, $modulo] = explode('-', $p, 2);
            if ($accion !== 'ver' && !in_array("ver-$modulo", $pedidos, true)) {
                $pedidos[] = "ver-$modulo";
            }
        }
        $pedidos = array_values(array_unique($pedidos));

        // Se crean los permisos que falten
        foreach ($validos as $nombre) {
            Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
        }

        // El Admin siempre conserva todos los permisos
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web'])->syncPermissions($validos);

        Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web'])->syncPermissions($pedidos);

        return response()->json([
            'success'  => true,
            'message'  => "Permisos de {$rol} guardados.",
            'permisos' => $pedidos,
        ]);
    }
}