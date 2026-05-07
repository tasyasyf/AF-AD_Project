<x-layouts.app title="Create Submission">
@include('admin.submissions.form', ['action' => route('admin.submissions.store'), 'method' => null, 'submission' => null, 'profiles' => $profiles, 'submissionTypes' => $submissionTypes, 'isEdit' => false])
</x-layouts.app>
