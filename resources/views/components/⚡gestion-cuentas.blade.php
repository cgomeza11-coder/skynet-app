<?php

use Livewire\Component;
use App\Models\Cuenta;
use App\Models\Cliente;

new class extends Component
{
    public $cuenta_id, $cliente_id, $numero_cuenta, $tipo_cuenta = 'Ahorro', $saldo_actual = 0;
    public $isEdit = false;

    public function with(): array
    {
        return [
            'cuentas' => Cuenta::with('cliente')->latest()->get(),
            'clientes' => Cliente::orderBy('nombre_completo')->get()
        ];
    }

    public function resetInput()
    {
        $this->reset(['cuenta_id', 'cliente_id', 'numero_cuenta', 'isEdit']);
        $this->tipo_cuenta = 'Ahorro';
        $this->saldo_actual = 0;
    }

    public function store()
    {
        $this->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'numero_cuenta' => 'required|string|unique:cuentas,numero_cuenta,' . $this->cuenta_id,
            'tipo_cuenta' => 'required|string',
            'saldo_actual' => 'required|numeric|min:0',
        ]);

        Cuenta::updateOrCreate(['id' => $this->cuenta_id], [
            'cliente_id' => $this->cliente_id,
            'numero_cuenta' => $this->numero_cuenta,
            'tipo_cuenta' => $this->tipo_cuenta,
            'saldo_actual' => $this->saldo_actual,
        ]);

        session()->flash('message', $this->isEdit ? 'Cuenta actualizada correctamente.' : 'Cuenta registrada correctamente.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $cuenta = Cuenta::findOrFail($id);
        $this->cuenta_id = $id;
        $this->cliente_id = $cuenta->cliente_id;
        $this->numero_cuenta = $cuenta->numero_cuenta;
        $this->tipo_cuenta = $cuenta->tipo_cuenta;
        $this->saldo_actual = $cuenta->saldo_actual;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Cuenta::find($id)->delete();
        session()->flash('message', 'Cuenta eliminada correctamente.');
    }
};
?>

<div>
    <h3 class="mb-4">Gestión de Cuentas</h3>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulario -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    {{ $isEdit ? 'Editar Cuenta' : 'Nueva Cuenta' }}
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="form-label">Cliente Asociado *</label>
                            <select class="form-select" wire:model="cliente_id">
                                <option value="">-- Seleccione un cliente --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre_completo }}</option>
                                @endforeach
                            </select>
                            @error('cliente_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Número de Cuenta *</label>
                            <input type="text" class="form-control" wire:model="numero_cuenta" placeholder="Ej. CTA-001">
                            @error('numero_cuenta') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Cuenta *</label>
                            <select class="form-select" wire:model="tipo_cuenta">
                                <option value="Ahorro">Ahorro</option>
                                <option value="Monetaria">Monetaria</option>
                                <option value="Crédito">Crédito</option>
                            </select>
                            @error('tipo_cuenta') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Saldo Actual (Q) *</label>
                            <input type="number" step="0.01" class="form-control" wire:model="saldo_actual">
                            @error('saldo_actual') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                {{ $isEdit ? 'Actualizar' : 'Guardar' }}
                            </button>
                            @if($isEdit)
                                <button type="button" wire:click="resetInput" class="btn btn-secondary">Cancelar</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Cuentas -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Listado de Cuentas</div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>No. Cuenta</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Saldo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cuentas as $cuenta)
                                <tr>
                                    <td>{{ $cuenta->id }}</td>
                                    <td><strong>{{ $cuenta->numero_cuenta }}</strong></td>
                                    <td>{{ $cuenta->cliente->nombre_completo }}</td>
                                    <td><span class="badge bg-secondary">{{ $cuenta->tipo_cuenta }}</span></td>
                                    <td>Q {{ number_format($cuenta->saldo_actual, 2) }}</td>
                                    <td>
                                        <button wire:click="edit({{ $cuenta->id }})" class="btn btn-sm btn-warning">Editar</button>
                                        <button wire:click="delete({{ $cuenta->id }})" wire:confirm="¿Seguro que deseas eliminar esta cuenta?" class="btn btn-sm btn-danger">Eliminar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay cuentas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>