<x-layouts.app title="Edit Submission">
@include('admin.submissions.form', ['action' => route('admin.submissions.update', $submission), 'method' => 'PUT', 'submission' => $submission, 'profiles' => $profiles, 'submissionTypes' => $submissionTypes, 'isEdit' => true])
</x-layouts.app>
