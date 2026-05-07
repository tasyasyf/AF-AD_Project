<x-layouts.app title="Create Certificate">
@include('admin.certificates.form', ['action' => route('admin.certificates.store'), 'method' => null, 'certificate' => null, 'profiles' => $profiles, 'isEdit' => false])
</x-layouts.app>
