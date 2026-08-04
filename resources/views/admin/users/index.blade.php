@extends('layouts.admin')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <div>
                <h1>Foydalanuvchilar</h1>
                <p class="page-sub">Barcha o'qituvchi, o'quvchi va administratorlarni boshqarish — qo'shish, tahrirlash,
                    rolini o'zgartirish va o'chirish.</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-lg"></i> Yangi foydalanuvchi
            </button>
        </div>

        @if (session('success'))
            <div class="empty-hint" style="background:var(--mint-soft);color:var(--mint);border-color:var(--mint);margin-bottom:16px;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Ism yoki email bo'yicha qidirish..." class="filter-input">
            <select name="role" class="filter-select" onchange="this.form.submit()">
                <option value="">Barcha rollar</option>
                @foreach ($roles as $roleOption)
                    <option value="{{ $roleOption }}" {{ request('role') === $roleOption ? 'selected' : '' }}>{{ $roleOption }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="filter-reset">Tozalash</a>
            @endif
        </form>

        <div class="card fade-up" style="animation-delay:.05s;">
            @if ($users->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hech qanday foydalanuvchi topilmadi.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ism</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Ro'yxatdan o'tgan</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="cell-strong" data-label="Ism">{{ $user->name }}</td>
                                    <td data-label="Email">{{ $user->email }}</td>
                                    <td data-label="Rol"><span class="badge-role">{{ $user->roles->pluck('name')->first() ?? '—' }}</span></td>
                                    <td class="cell-muted" data-label="Sana">{{ $user->created_at->format('d.m.Y') }}</td>
                                    <td class="actions-cell">
                                        <button type="button" class="btn btn-outline btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                            data-role="{{ $user->roles->pluck('name')->first() }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $users->links() }}
            @endif
        </div>
    </div>

    {{-- Create modal --}}
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Yangi foydalanuvchi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Ism</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Parol</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div>
                            <label class="form-label">Rol</label>
                            <select name="role" class="form-select" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Saqlash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit modal --}}
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Foydalanuvchini tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Ism</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Yangi parol (ixtiyoriy)</label>
                            <input type="password" name="password" class="form-control" minlength="8"
                                placeholder="O'zgartirmaslik uchun bo'sh qoldiring">
                        </div>
                        <div>
                            <label class="form-label">Rol</label>
                            <select name="role" id="edit_role" class="form-select" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Yangilash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('editUserModal').addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const form = document.getElementById('editUserForm');
            form.action = '{{ url('admin/users') }}/' + btn.dataset.id;
            document.getElementById('edit_name').value = btn.dataset.name;
            document.getElementById('edit_email').value = btn.dataset.email;
            document.getElementById('edit_role').value = btn.dataset.role;
        });
    </script>
@endsection
