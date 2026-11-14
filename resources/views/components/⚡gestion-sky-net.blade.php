<?php

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Cuenta;

new class extends Component
{

    public $cliente_id;
    public $nombre_completo;
    public $correo;
    public $telefono;
    public $direccion;
    public $editandoCliente = false;


    public $cuenta_id;
    public $cliente_asociado_id;
    public $numero_cuenta;
    public $tipo_cuenta = 'Ahorro';
    public $saldo_actual = 0;
    public $editandoCuenta = false;

    public function guardarCliente()
    {
        $this->validate([
            'nombre_completo' => 'required|string|max:255',
            'correo' => 'required|email|unique:clientes,correo,' . $this->cliente_id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingresa un formato de correo válido.',
            'correo.unique' => 'Este correo ya está registrado con otro cliente.',
        ]);

        Cliente::updateOrCreate(
            ['id' => $this->cliente_id],
            [
                'nombre_completo' => $this->nombre_completo,
                'correo' => $this->correo,
                'telefono' => $this->telefono,
                'direccion' => $this->direccion,
            ]
        );

        session()->flash('mensaje_cliente', $this->editandoCliente ? 'Cliente actualizado correctamente.' : 'Cliente registrado correctamente.');
        $this->limpiarCliente();
    }

    public function editarCliente($id)
    {
        $cliente = Cliente::findOrFail($id);
        $this->cliente_id = $cliente->id;
        $this->nombre_completo = $cliente->nombre_completo;
        $this->correo = $cliente->correo;
        $this->telefono = $cliente->telefono;
        $this->direccion = $cliente->direccion;
        $this->editandoCliente = true;
    }

    public function eliminarCliente($id)
    {
        Cliente::findOrFail($id)->delete();
        session()->flash('mensaje_cliente', 'Cliente eliminado.');
    }

    public function limpiarCliente()
    {
        $this->reset(['cliente_id', 'nombre_completo', 'correo', 'telefono', 'direccion', 'editandoCliente']);
        $this->resetValidation();
    }

  
    public function guardarCuenta()
    {
        $this->validate([
            'cliente_asociado_id' => 'required|exists:clientes,id',
            'numero_cuenta' => 'required|string|unique:cuentas,numero_cuenta,' . $this->cuenta_id,
            'tipo_cuenta' => 'required|string',
            'saldo_actual' => 'required|numeric|min:0',
        ], [
            'cliente_asociado_id.required' => 'Debes seleccionar un cliente.',
            'numero_cuenta.required' => 'El número de cuenta es obligatorio.',
            'numero_cuenta.unique' => 'Ese número de cuenta ya existe.',
            'saldo_actual.required' => 'El saldo inicial es obligatorio.',
            'saldo_actual.numeric' => 'El saldo debe ser un número válido.',
        ]);

        Cuenta::updateOrCreate(
            ['id' => $this->cuenta_id],
            [
                'cliente_id' => $this->cliente_asociado_id,
                'numero_cuenta' => $this->numero_cuenta,
                'tipo_cuenta' => $this->tipo_cuenta,
                'saldo_actual' => $this->saldo_actual,
            ]
        );

        session()->flash('mensaje_cuenta', $this->editandoCuenta ? 'Cuenta actualizada correctamente.' : 'Cuenta asociada correctamente.');
        $this->limpiarCuenta();
    }

    public function editarCuenta($id)
    {
        $cuenta = Cuenta::findOrFail($id);
        $this->cuenta_id = $cuenta->id;
        $this->cliente_asociado_id = $cuenta->cliente_id;
        $this->numero_cuenta = $cuenta->numero_cuenta;
        $this->tipo_cuenta = $cuenta->tipo_cuenta;
        $this->saldo_actual = $cuenta->saldo_actual;
        $this->editandoCuenta = true;
    }

    public function eliminarCuenta($id)
    {
        Cuenta::findOrFail($id)->delete();
        session()->flash('mensaje_cuenta', 'Cuenta eliminada.');
    }

    public function limpiarCuenta()
    {
        $this->reset(['cuenta_id', 'cliente_asociado_id', 'numero_cuenta', 'saldo_actual', 'editandoCuenta']);
        $this->tipo_cuenta = 'Ahorro';
        $this->resetValidation();
    }

    public function with(): array
    {
        return [
            'clientes' => Cliente::with('cuentas')->latest()->get(),
            'cuentas' => Cuenta::with('cliente')->latest()->get(),
        ];
    }
};
?>

<div class="row g-4">
    <!-- MÓDULO DE CLIENTES -->
    <div class="col-lg-6">
        <div class="sketch-card mb-4">
            <div class="p-3 sketch-header-blue d-flex justify-content-between align-items-center">
                <span class="sketch-title">
                    {{ $editandoCliente ? '✏️ Editar Cliente' : '👤 Registrar Nuevo Cliente' }}
                </span>
                <span wire:loading wire:target="guardarCliente, editarCliente, eliminarCliente" class="small text-muted">
                    Guardando... ⏳
                </span>
            </div>
            <div class="p-3">
                @if (session()->has('mensaje_cliente'))
                    <div class="sketch-alert py-1 px-3 mb-3 small fw-bold">
                        ✔ {{ session('mensaje_cliente') }}
                    </div>
                @endif

                <form wire:submit="guardarCliente">
                    <div class="mb-2">
                        <label class="form-label mb-1 fw-bold">Nombre completo *</label>
                        <input type="text" wire:model="nombre_completo" class="form-control form-control-sm sketch-input" placeholder="Ej. Juan Pérez">
                        @error('nombre_completo') <span class="text-danger small fw-bold">⚠ {{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1 fw-bold">Correo electrónico *</label>
                        <input type="email" wire:model="correo" class="form-control form-control-sm sketch-input" placeholder="correo@ejemplo.com">
                        @error('correo') <span class="text-danger small fw-bold">⚠ {{ $message }}</span> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label mb-1 fw-bold">Teléfono (opcional)</label>
                            <input type="text" wire:model="telefono" class="form-control form-control-sm sketch-input" placeholder="Ej. 5555-1234">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label mb-1 fw-bold">Dirección (opcional)</label>
                            <input type="text" wire:model="direccion" class="form-control form-control-sm sketch-input" placeholder="Ciudad / Zona">
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm sketch-btn btn-sketch-blue px-3">
                            {{ $editandoCliente ? '✔ Actualizar Cliente' : '+ Guardar Cliente' }}
                        </button>
                        @if($editandoCliente)
                            <button type="button" wire:click="limpiarCliente" class="btn btn-sm sketch-btn btn-sketch-gray px-3">
                                Cancelar
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="sketch-card">
            <div class="p-3 sketch-header-plain d-flex justify-content-between align-items-center">
                <span class="sketch-title">📋 Listado de Clientes</span>
                <span class="sketch-badge small">{{ $clientes->count() }} total</span>
            </div>
            <div class="table-responsive p-2">
                <table class="table table-sm sketch-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th class="text-center">Cuentas</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $c)
                        <tr wire:key="cliente-{{ $c->id }}">
                            <td>
                                <strong>{{ $c->nombre_completo }}</strong><br>
                                <small class="text-muted">{{ $c->direccion ?? 'Sin dirección' }}</small>
                            </td>
                            <td>
                                <small>{{ $c->correo }}</small><br>
                                <small class="text-muted">{{ $c->telefono ?? 'N/A' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="sketch-badge">{{ $c->cuentas->count() }}</span>
                            </td>
                            <td class="text-end">
                                <button wire:click="editarCliente({{ $c->id }})" class="btn btn-sm sketch-btn btn-sketch-yellow py-0 px-2">Editar</button>
                                <button wire:click="eliminarCliente({{ $c->id }})" class="btn btn-sm sketch-btn btn-sketch-red py-0 px-2">X</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No hay clientes registrados aún.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MÓDULO DE CUENTAS -->
    <div class="col-lg-6">
        <div class="sketch-card mb-4">
            <div class="p-3 sketch-header-green d-flex justify-content-between align-items-center">
                <span class="sketch-title">
                    {{ $editandoCuenta ? '✏️ Editar Cuenta' : '💳 Asociar Nueva Cuenta' }}
                </span>
                <span wire:loading wire:target="guardarCuenta, editarCuenta, eliminarCuenta" class="small text-muted">
                    Procesando... ⏳
                </span>
            </div>
            <div class="p-3">
                @if (session()->has('mensaje_cuenta'))
                    <div class="sketch-alert py-1 px-3 mb-3 small fw-bold">
                        ✔ {{ session('mensaje_cuenta') }}
                    </div>
                @endif

                <form wire:submit="guardarCuenta">
                    <div class="mb-2">
                        <label class="form-label mb-1 fw-bold">Cliente Asociado *</label>
                        <select wire:model="cliente_asociado_id" class="form-select form-select-sm sketch-input">
                            <option value="">-- Seleccione un cliente --</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre_completo }}</option>
                            @endforeach
                        </select>
                        @error('cliente_asociado_id') <span class="text-danger small fw-bold">⚠ {{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1 fw-bold">Número de Cuenta *</label>
                        <input type="text" wire:model="numero_cuenta" class="form-control form-control-sm sketch-input" placeholder="Ej. CTA-001">
                        @error('numero_cuenta') <span class="text-danger small fw-bold">⚠ {{ $message }}</span> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label mb-1 fw-bold">Tipo de Cuenta *</label>
                            <select wire:model="tipo_cuenta" class="form-select form-select-sm sketch-input">
                                <option value="Ahorro">Ahorro</option>
                                <option value="Monetaria">Monetaria</option>
                                <option value="Crédito">Crédito</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label mb-1 fw-bold">Saldo Actual (Q) *</label>
                            <input type="number" step="0.01" wire:model="saldo_actual" class="form-control form-control-sm sketch-input">
                            @error('saldo_actual') <span class="text-danger small fw-bold">⚠ {{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm sketch-btn btn-sketch-green px-3">
                            {{ $editandoCuenta ? '✔ Actualizar Cuenta' : '+ Guardar Cuenta' }}
                        </button>
                        @if($editandoCuenta)
                            <button type="button" wire:click="limpiarCuenta" class="btn btn-sm sketch-btn btn-sketch-gray px-3">
                                Cancelar
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Cuentas -->
        <div class="sketch-card">
            <div class="p-3 sketch-header-plain d-flex justify-content-between align-items-center">
                <span class="sketch-title">🏦 Cuentas Registradas</span>
                <span class="sketch-badge small">{{ $cuentas->count() }} activas</span>
            </div>
            <div class="table-responsive p-2">
                <table class="table table-sm sketch-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>No. Cuenta</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Saldo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuentas as $cta)
                        <tr wire:key="cuenta-{{ $cta->id }}">
                            <td><strong>{{ $cta->numero_cuenta }}</strong></td>
                            <td>{{ $cta->cliente->nombre_completo }}</td>
                            <td>{{ $cta->tipo_cuenta }}</td>
                            <td class="fw-bold">Q {{ number_format($cta->saldo_actual, 2) }}</td>
                            <td class="text-end">
                                <button wire:click="editarCuenta({{ $cta->id }})" class="btn btn-sm sketch-btn btn-sketch-yellow py-0 px-2">Editar</button>
                                <button wire:click="eliminarCuenta({{ $cta->id }})" class="btn btn-sm sketch-btn btn-sketch-red py-0 px-2">X</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No hay cuentas registradas aún.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>