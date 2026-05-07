<x-layouts.app title="Edit Certificate">
@include('admin.certificates.form', ['action' => route('admin.certificates.update', $certificate), 'method' => 'PUT', 'certificate' => $certificate, 'profiles' => $profiles, 'isEdit' => true])
</x-layouts.app>
